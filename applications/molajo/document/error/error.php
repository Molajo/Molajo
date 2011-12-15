<?php
/**
 * @package     Molajo
 * @subpackage  Document
 * @copyright   Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @copyright   Copyright (C) 2012 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die;

/**
 * DocumentError class
 *
 * Parse and display an error page
 *
 * @package     Molajo
 * @subpackage  Document
 * @since       1.0
 */
class MolajoDocumentError extends MolajoDocument
{
    /**
     * Error Object
     *
     * @var    object
     * @since  1.0
     */
    var $_error;

    /**
     * __construct
     *
     * Class constructor
     *
     * @param   string  $type        Either HTML or text
     * @param   array   $attributes  Associative array of attributes
     *
     * @since   11.1
     */
    public function __construct($options = array())
    {
        parent::__construct($options);

        //set mime type
        $this->_mime = 'text/html';

        //set document type
        $this->_type = 'error';
    }

    /**
     * setError
     *
     * Set error object
     *
     * @param   object  $error  Error object to set
     *
     * @return  boolean  True on success
     *
     * @since   11.1
     */
    public function setError($error)
    {
        if (MolajoError::isError($error)) {
            $this->_error = & $error;
            return true;
        } else {
            return false;
        }
    }

    /**
     * render
     *
     * Render the document
     *
     * @param   boolean  $cache    If true, cache the output
     * @param   array    $parameters   Associative array of attributes
     *
     * @return  string   The rendered data
     *
     * @since   11.1
     */
    public function render($cache = false, $parameters = array())
    {
        // If no error object is set return null
        if (isset($this->_error)) {
        } else {
            return;
        }

        //Set the status header
        JResponse::setHeader('status', $this->_error->getCode() . ' ' . str_replace("\n", ' ', $this->_error->getMessage()));
        $file = 'error.php';

        // check template
        $directory = isset($parameters['directory']) ? $parameters['directory'] : 'templates';
        $template = isset($parameters['template']) ? JFilterInput::getInstance()->clean($parameters['template'], 'cmd')
                : 'system';

        if (file_exists($directory . '/' . $template . '/' . $file)) {
        } else {
            $template = 'system';
        }

        //set variables
        $this->baseurl = JURI::base(true);
        $this->template = $template;
        $this->debug = isset($parameters['debug']) ? $parameters['debug'] : false;
        $this->error = $this->_error;

        // load
        $data = $this->_loadTemplate($directory . '/' . $template, $file);

        parent::render();
        return $data;
    }

    /**
     * _loadTemplate
     *
     * Load a template file
     *
     * @param   string  $template   The name of the template
     * @param   string  $filename   The actual filename
     *
     * @return  string  The contents of the template
     *
     * @since   11.1
     */
    function _loadTemplate($directory, $filename)
    {
        $contents = '';

        // Check to see if we have a valid template file
        if (file_exists($directory . '/' . $filename)) {
            // Store the file path
            $this->_file = $directory . '/' . $filename;

            // Get the file content
            ob_start();
            require_once $directory . '/' . $filename;
            $contents = ob_get_contents();
            ob_end_clean();
        }

        return $contents;
    }

    /**
     * renderBacktrace
     *
     * Render the backtrace
     *
     * @return  string  The contents of the backtrace
     *
     * @since   11.1
     */
    function renderBacktrace()
    {
        $contents = null;
        $backtrace = $this->_error->getTrace();
        if (is_array($backtrace)) {
            ob_start();
            $j = 1;
            echo    '<table cellpadding="0" cellspacing="0" class="Table">';
            echo    '	<tr>';
            echo    '		<td colspan="3" class="TD"><strong>Call stack</strong></td>';
            echo    '	</tr>';
            echo    '	<tr>';
            echo    '		<td class="TD"><strong>#</strong></td>';
            echo    '		<td class="TD"><strong>Function</strong></td>';
            echo    '		<td class="TD"><strong>Location</strong></td>';
            echo    '	</tr>';
            for ($i = count($backtrace) - 1; $i >= 0; $i--)
            {
                echo    '	<tr>';
                echo    '		<td class="TD">' . $j . '</td>';
                if (isset($backtrace[$i]['class'])) {
                    echo    '	<td class="TD">' . $backtrace[$i]['class'] . $backtrace[$i]['type'] . $backtrace[$i]['function'] . '()</td>';
                } else {
                    echo    '	<td class="TD">' . $backtrace[$i]['function'] . '()</td>';
                }
                if (isset($backtrace[$i]['file'])) {
                    echo    '		<td class="TD">' . $backtrace[$i]['file'] . ':' . $backtrace[$i]['line'] . '</td>';
                } else {
                    echo    '		<td class="TD">&#160;</td>';
                }
                echo    '	</tr>';
                $j++;
            }
            echo    '</table>';
            $contents = ob_get_contents();
            ob_end_clean();
        }
        return $contents;
    }
}
