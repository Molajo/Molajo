<?php
/**
 * @package     Molajo
 * @subpackage  Controller
 * @copyright   Copyright (C) 2012 Amy Stephen. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */
defined('MOLAJO') or die;

/**
 * Responder
 *
 * @package     Molajo
 * @subpackage  Controller
 * @since       1.0
 */
class MolajoResponderController
{
    /**
     * Responder static instance
     *
     * @var    object
     * @since  1.0
     */
    protected static $instance;

    /**
     * Configuration
     *
     * @var    object
     * @since  1.0
     */
    protected static $_config = null;

    /**
     * Response
     *
     * @var    object
     * @since  1.0
     */
    protected $_response = array();

    /**
     * getInstance
     *
     * Returns a reference to the global Responder object,
     *  only creating it if it doesn't already exist.
     *
     * @static
     * @param  JRegistry|null $config
     *
     * @return bool|object
     * @since  1.0
     */
    public static function getInstance(JRegistry $config = null)
    {
        if (empty(self::$instance)) {
            if ($config instanceof JRegistry) {
            } else {
                $config = new JRegistry;
            }
            self::$instance = new MolajoResponderController($config);
        }
        return self::$instance;
    }

    /**
     * __construct
     *
     * Class constructor.
     *
     * @return boolean
     * @since  1.0
     */
    public function __construct(JRegistry $config)
    {
        if ($config instanceof JRegistry) {
            $this->_config = $config;
        }

        $this->_response = new stdClass;
        $this->_response->cachable = false;
        $this->_response->headers = array();
        $this->_response->body = array();
        $this->_response->last_modified = null;

        return true;
    }

    /**
     * respond
     *
     * Retrieves Theme and begins the process of first parsing the Theme and Page View
     * for input:renderer statements, looping through the renderers for the statements found,
     * and then continuing the process by parsing the rendered output for additional input
     * statements until no more are found.
     *
     * @return  object
     * @since  1.0
     */
    public function respond()
    {
//        $this->triggerEvent('onBeforeRespond');

        // If gzip compression is enabled in configuration and the server is compliant, compress the output.
        if (Molajo::Application()->get('gzip')) {
            if (ini_get('zlib.output_compression')) {
            } elseif (ini_get('output_handler') == 'ob_gzhandler') {
            } else {
                $this->_compress();
            }
        }

        // Send the content-type header.
        $this->setHeader('Content-Type', Molajo::Application()->getMimeEncoding() . '; charset=utf-8');

        if ($this->_response->cachable === true) {
            $this->setHeader('Expires', gmdate('D, d M Y H:i:s', time() + 900) . ' GMT');
            if ($this->_response->last_modified instanceof JDate) {
                $this->setHeader('Last-Modified', $this->_response->last_modified->format('D, d M Y H:i:s'));
            }
        } else {
            $this->setHeader('Expires', 'Fri, 6 Jan 1989 00:00:00 GMT', true);
            $this->setHeader('Last-Modified', gmdate('D, d M Y H:i:s') . ' GMT', true);
            $this->setHeader('Cache-Control', 'no-store, no-cache, must-revalidate, post-check=0, pre-check=0', false);
            // HTTP 1.0
            $this->setHeader('Pragma', 'no-cache');
        }

        $this->_sendHeaders();

        echo $this->getBody();

//        $this->triggerEvent('onAfterRespond');

        return;
    }

    /**
     * Checks the accept encoding of the browser and compresses the data before
     * sending it to the client if possible.
     *
     * @return  void
     *
     * @since   1.0
     */
    protected function _compress()
    {
        // Supported compression encodings.
        $supported = array(
            'x-gzip' => 'gz',
            'gzip' => 'gz',
            'deflate' => 'deflate'
        );

        // Get the supported encoding.
        $encodings = array_intersect($this->_client->encodings, array_keys($supported));

        // If no supported encoding is detected do nothing and return.
        if (empty($encodings)) {
            return;
        }

        // Verify that headers have not yet been sent, and that our connection is still alive.
        if ($this->_checkHeadersSent() || !$this->_checkConnectionAlive()) {
            return;
        }

        // Iterate through the encodings and attempt to compress the data using any found supported encodings.
        foreach ($encodings as $encoding)
        {
            if (($supported[$encoding] == 'gz') || ($supported[$encoding] == 'deflate')) {
                // Verify that the server supports gzip compression before we attempt to gzip encode the data.
                // @codeCoverageIgnoreStart
                if (!extension_loaded('zlib') || ini_get('zlib.output_compression')) {
                    continue;
                }
                // @codeCoverageIgnoreEnd

                // Attempt to gzip encode the data with an optimal level 4.
                $data = $this->getBody();

                $gzdata = gzencode($data, 4, ($supported[$encoding] == 'gz') ? FORCE_GZIP : FORCE_DEFLATE);

                // If there was a problem encoding the data just try the next encoding scheme.
                // @codeCoverageIgnoreStart
                if ($gzdata === false) {
                    continue;
                }
                // @codeCoverageIgnoreEnd

                // Set the encoding headers.
                $this->setHeader('Content-Encoding', $encoding);
                $this->setHeader('X-Content-Encoded-By', 'Molajo');

                // Replace the output with the encoded data.
                $this->setBody($gzdata);

                // Compression complete, let's break out of the loop.
                break;
            }
        }
    }

    /**
     * Set/get cachable state for the response.  If $allow is set, sets the cachable state of the
     * response.  Always returns the current state.
     *
     * @param   boolean  $allow  True to allow browser caching.
     *
     * @return  boolean
     *
     * @since   1.0
     */
    public function allowCache($allow = null)
    {
        if ($allow !== null) {
            $this->_response->cachable = (bool)$allow;
        }

        return $this->_response->cachable;
    }

    /**
     * Method to set a response header.  If the replace flag is set then all headers
     * with the given name will be replaced by the new one.  The headers are stored
     * in an internal array to be sent when the site is sent to the browser.
     *
     * @param   string   $name     The name of the header to set.
     * @param   string   $value    The value of the header to set.
     * @param   boolean  $replace  True to replace any headers with the same name.
     *
     * @return  Application  Instance of $this to allow chaining.
     *
     * @since   1.0
     */
    public function setHeader($name, $value, $replace = false)
    {
        $name = (string)$name;
        $value = (string)$value;

        // If the replace flag is set, unset all known headers with the given name.
        if ($replace) {
            foreach ($this->_response->headers as $key => $header)
            {
                if ($name == $header['name']) {
                    unset($this->_response->headers[$key]);
                }
            }

            // Clean up the array as unsetting nested arrays leaves some junk.
            $this->_response->headers = array_values($this->_response->headers);
        }

        // Add the header to the internal array.
        $this->_response->headers[] = array('name' => $name, 'value' => $value);

        return $this;
    }

    /**
     * Method to get the array of response headers to be sent when the response is sent
     * to the client.
     *
     * @return  array
     *
     * @since   1.0
     */
    public function getHeaders()
    {
        return $this->_response->headers;
    }

    /**
     * Method to clear any set response headers.
     *
     * @return  Application  Instance of $this to allow chaining.
     *
     * @since   1.0
     */
    public function clearHeaders()
    {
        $this->_response->headers = array();

        return $this;
    }

    /**
     * Send the response headers.
     *
     * @return  Application  Instance of $this to allow chaining.
     *
     * @since   1.0
     */
    protected function _sendHeaders()
    {
        if ($this->_checkHeadersSent()) {
        } else {
            foreach ($this->_response->headers as $header) {
                if ('status' == strtolower($header['name'])) {
                    // 'status' headers indicate an HTTP status, and need to be handled slightly differently
                    $this->header(ucfirst(strtolower($header['name'])) . ': ' . $header['value'], null, (int)$header['value']);
                } else {
                    $this->header($header['name'] . ': ' . $header['value']);
                }
            }
        }
        return $this;
    }

    /**
     * setBody
     *
     * Set body content.  If body content already defined, this will replace it.
     *
     * @param   string  $content  The content to set as the response body.
     *
     * @return  Application  Instance of $this to allow chaining.
     * @since   1.0
     */
    public function setBody($content)
    {
        $this->_response->body = array((string)$content);

        return $this;
    }

    /**
     * Prepend content to the body content
     *
     * @param   string  $content  The content to prepend to the response body.
     *
     * @return  Application  Instance of $this to allow chaining.
     *
     * @since   1.0
     */
    public function prependBody($content)
    {
        array_unshift($this->_response->body, (string)$content);

        return $this;
    }

    /**
     * Append content to the body content
     *
     * @param   string  $content  The content to append to the response body.
     *
     * @return  Application  Instance of $this to allow chaining.
     *
     * @since   1.0
     */
    public function appendBody($content)
    {
        array_push($this->_response->body, (string)$content);

        return $this;
    }

    /**
     * Return the body content
     *
     * @param   boolean  $asArray  True to return the body as an array of strings.
     *
     * @return  mixed  The response body either as an array or concatenated string.
     *
     * @since   1.0
     */
    public function getBody($asArray = false)
    {
        if ($asArray === true) {
            return $this->_response->body;
        } else {
            return implode('', $this->_response->body);
        }
    }

    /**
     * Method to check the current client connection status to ensure that it is alive.  We are
     * wrapping this to isolate the connection_status() function from our code base for testing reasons.
     *
     * @return  boolean  True if the connection is valid and normal.
     *
     * @codeCoverageIgnore
     * @see     connection_status()
     * @since   1.0
     */
    protected function _checkConnectionAlive()
    {
        return (connection_status() === CONNECTION_NORMAL);
    }

    /**
     * Method to check to see if headers have already been sent.  We are wrapping this to isolate the
     * headers_sent() function from our code base for testing reasons.
     *
     * @return  boolean  True if the headers have already been sent.
     *
     * @codeCoverageIgnore
     * @see     headers_sent()
     * @since   1.0
     */
    protected function _checkHeadersSent()
    {
        return headers_sent();
    }

    /**
     * Method to send a header to the client.  We are wrapping this to isolate the header() function
     * from our code base for testing reasons.
     *
     * @param   string   $string   The header string.
     * @param   boolean  $replace  The optional replace parameter indicates whether the header should
     *                             replace a previous similar header, or add a second header of the same type.
     * @param   integer  $code     Forces the HTTP response code to the specified value. Note that
     *                             this parameter only has an effect if the string is not empty.
     *
     * @return  void
     *
     * @codeCoverageIgnore
     * @see     header()
     * @since   1.0
     */
    protected function header($string, $replace = true, $code = null)
    {
        header($string, $replace, $code);
    }
}
