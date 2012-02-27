<?php
/**
 * @package     Molajo
 * @subpackage  Base
 * @copyright   Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @copyright   Copyright (C) 2012 Amy Stephen. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */
defined('MOLAJO') or die;

/**
 * Responder
 *
 * @package     Molajo
 * @subpackage  Base
 * @since       1.0
 */
class MolajoResponder
{
    /**
     * Responder static instance
     *
     * @var    object
     * @since  1.0
     */
    protected static $instance;

    /**
     * Response Mimetype
     *
     * @var    string
     * @since  1.0
     */
    protected $mimetype = 'text/html';

    /**
     * Response Object
     *
     * @var    object
     * @since  1.0
     */
    public $response;

    /**
     * getInstance
     *
     * Returns a reference to the global Responder object,
     *  creating it if it doesn't already exist.
     *
     * @static
     * @return object
     * @since  1.0
     */
    public static function getInstance()
    {
        if (empty(self::$instance)) {
            self::$instance = new MolajoResponder();
        }
        return self::$instance;
    }

    /**
     * __construct
     *
     * Class constructor.
     *
     * @return null
     * @since  1.0
     */
    public function __construct()
    {
        $this->response = new stdClass;
        $this->response->cachable = false;
        $this->response->headers = array();
        $this->response->body = array();

        $this->metadata = array();
    }

    /**
     * setMimeEncoding
     *
     * Sets the document MIME encoding that is sent to the browser.
     *
     * This usually will be text/html because most browsers cannot yet
     * accept the proper mimetype settings for XHTML: application/xhtml+xml
     * and to a lesser extent application/xml and text/xml. See the W3C note
     * ({@link http://www.w3.org/TR/xhtml-media-types/
     * http://www.w3.org/TR/xhtml-media-types/}) for more details.
     *
     * @param   string  $format
     * @param   bool    $sync  Should the type be synced with HTML?
     *
     * @return  void
     * @since   1.0
     */
    public function setMimeEncoding($format = 'text/html', $sync = true)
    {
        $this->mimetype = strtolower($format);
        if ($sync) {
            $this->set_metadata('content-type', $format, true, false);
        }
    }

    /**
     * getMimeEncoding
     *
     * Return the document MIME encoding that is sent to the browser.
     *
     * @return  string
     * @since   1.0
     */
    public function getMimeEncoding()
    {
        return $this->mimetype;
    }

    /**
     * respond
     *
     * @return  object
     * @since  1.0
     */
    public function respond()
    {
        //        Services::Dispatcher()->notify('onBeforeRespond');

        // If gzip compression is enabled in configuration and the server is compliant, compress the output.
        if (Services::Configuration()->get('gzip')) {
            if (ini_get('zlib.output_compression')) {
            } elseif (ini_get('output_handler') == 'ob_gzhandler') {
            } else {
                $this->compress();
            }
        }

        // Send the content-type header.
        $this->setHeader('Content-Type', $this->getMimeEncoding() . '; charset=utf-8');

        if ($this->response->cachable === true) {
            $this->setHeader('Expires', gmdate('D, d M Y H:i:s', time() + 900) . ' GMT');
            if ($this->last_modified instanceof JDate) {
                $this->setHeader('Last-Modified', $this->last_modified->format('D, d M Y H:i:s'));
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

        //        Services::Dispatcher()->notify('onAfterRespond');

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
        $encodings = array_intersect(
            $this->client->encodings,
            array_keys($supported)
        );

        // If no supported encoding is detected do nothing and return.
        if (empty($encodings)) {
            return;
        }

        // Verify that headers have not yet been sent, and that our connection is still alive.
        if ($this->_checkHeadersSent()
            || !$this->checkConnectionAlive()
        ) {
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
            $this->response->cachable = (bool)$allow;
        }

        return $this->response->cachable;
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
     * @return  Responder  Instance of $this to allow chaining.
     *
     * @since   1.0
     */
    public function setHeader($name, $value, $replace = false)
    {
        $name = (string)$name;
        $value = (string)$value;

        // If the replace flag is set, unset all known headers with the given name.
        if ($replace) {
            foreach ($this->response->headers as $key => $header)
            {
                if ($name == $header['name']) {
                    unset($this->response->headers[$key]);
                }
            }

            // Clean up the array as unsetting nested arrays leaves some junk.
            $this->response->headers = array_values($this->response->headers);
        }

        // Add the header to the internal array.
        $this->response->headers[] = array('name' => $name, 'value' => $value);

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
        return $this->response->headers;
    }

    /**
     * Method to clear any set response headers.
     *
     * @return  Responder  Instance of $this to allow chaining.
     *
     * @since   1.0
     */
    public function clearHeaders()
    {
        $this->response->headers = array();

        return $this;
    }

    /**
     * Send the response headers.
     *
     * @return  Responder  Instance of $this to allow chaining.
     *
     * @since   1.0
     */
    protected function _sendHeaders()
    {
        if ($this->_checkHeadersSent()) {
        } else {
            foreach ($this->response->headers as $header) {
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
     * @return  Responder  Instance of $this to allow chaining.
     * @since   1.0
     */
    public function setBody($content)
    {
        $this->response->body = array((string)$content);

        return $this;
    }

    /**
     * Prepend content to the body content
     *
     * @param   string  $content  The content to prepend to the response body.
     *
     * @return  Responder  Instance of $this to allow chaining.
     *
     * @since   1.0
     */
    public function prependBody($content)
    {
        array_unshift($this->response->body, (string)$content);

        return $this;
    }

    /**
     * Append content to the body content
     *
     * @param   string  $content  The content to append to the response body.
     *
     * @return  Responder  Instance of $this to allow chaining.
     *
     * @since   1.0
     */
    public function appendBody($content)
    {
        array_push($this->response->body, (string)$content);

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
            return $this->response->body;
        } else {
            return implode('', $this->response->body);
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

    /**
     * Redirect to the URL for a specified pageRequest value
     *
     * URL PHP Constants set in root index.php =>
     * MOLAJO_BASE_URL - protocol, host and path + / (ex. http://localhost/molajo/)
     * MOLAJO_APPLICATION_URL_PATH - slug for application (ex. administrator or '' for site)
     * .'/'.
     * MOLAJO_PAGE_REQUEST - remaining (ex. index.php?option=articles&view=display or edit)
     *
     * If the headers have not been sent the redirect will be accomplished using a "301 Moved Permanently"
     * or "303 See Other" code in the header pointing to the new location. If the headers have already been
     * sent this will be accomplished using a JavaScript statement.
     *
     * @param   string   $url    The URL to redirect to. Can only be http/https URL
     * @param   boolean  $moved  True if the page is 301 Permanently Moved, otherwise 303 See Other is assumed.
     *
     * 301 - Permanent move
     * 303 - Other
     *
     * @return  void
     *
     * @since   1.0
     */
    public function redirect($pageRequest, $code = 303)
    {
        /** sef url options */
        if (Services::Configuration()->get('sef', 1) == 1) {
            if (Services::Configuration()->get('sef_rewrite', 0) == 0) {
                $url = MOLAJO_BASE_URL . MOLAJO_APPLICATION_URL_PATH . 'index.php/' . $pageRequest;
            } else {
                $url = MOLAJO_BASE_URL . MOLAJO_APPLICATION_URL_PATH . $pageRequest;
            }

            if ((int)Services::Configuration()->get('sef_suffix', 0) == 1) {
                $url .= '.html';
            }
        }
        if (Services::Configuration()->get('debug', 0) == 1) {
            debug(' ');
            debug('MolajoResponderer::save Success redirect to: ' . $url);
        }
        /** validate code */
        if ($code == 301) {
        } else {
            $code = 303;
        }

        $exception = false;

        /** IE */
        if (stripos($_SERVER['HTTP_USER_AGENT'], 'MSIE') !== false
            || stripos($_SERVER['HTTP_USER_AGENT'], 'Trident') !== false
        ) {
            $exception = 'trident';
        }
        if (stripos($_SERVER['HTTP_USER_AGENT'], 'AppleWebKit') !== false
            || stripos($_SERVER['HTTP_USER_AGENT'], 'blackberry') !== false
        ) {
            $exception = 'webkit';
        }

        if ($this->_checkHeadersSent()) {
            echo "<script>document.location.href='$url';</script>\n";
        } else {

            /** IE and UTF8 URLs */
            if (($exception == 'trident') && !utf8_is_ascii($url)) {
                $html = '<html><head>';
                $html .= '<meta http-equiv="content-type" content="text/html; charset=utf-8" />';
                $html .= '<script>document.location.href=\'' . $url . '\';</script>';
                $html .= '</head><body></body></html>';

                echo $html;
            }
            /*
             * For WebKit based browsers do not send a 303, as it causes subresource reloading.  You can view the
             * bug report at: https://bugs.webkit.org/show_bug.cgi?id=38690
             */
            elseif ($exception == 'webkit' && $code == 303)
            {
                $html = '<html><head>';
                $html .= '<meta http-equiv="refresh" content="0; url=' . $url . '" />';
                $html .= '<meta http-equiv="content-type" content="text/html; charset=utf-8" />';
                $html .= '</head><body></body></html>';

                echo $html;

            } else {

                /** normal */
                $this->header($code ? 'HTTP/1.1 301 Moved Permanently' : 'HTTP/1.1 303 See other');
                $this->header('Location: ' . $url);
                $this->header('Content-Type: text/html; charset=utf-8');
            }
        }

        /** close after redirect */
        $this->close();
    }

    /**
     * Exit the application.
     *
     * @param   integer  $code  The exit code (optional; default is 0).
     *
     * @return  void
     *
     * @codeCoverageIgnore
     * @since   1.0
     */
    public function close($code = 0)
    {
        exit($code);
    }
}
