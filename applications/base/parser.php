<?php
/**
 * @package     Molajo
 * @subpackage  Base
 * @copyright   Copyright (C) 2012 Amy Stephen. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */
defined('MOLAJO') or die;

/**
 * Parser
 *
 * @package     Molajo
 * @subpackage  Base
 * @since       1.0
 */
class MolajoParser
{
    /**
     * $instance
     *
     * Parser static instance
     *
     * @var    object
     * @since  1.0
     */
    protected static $instance;

    /**
     * $parameters
     *
     * Parameters used by Theme and Page View
     *
     * @var string
     * @since 1.0
     */
    protected $parameters = array();

    /**
     * $sequence
     *
     * System defined order for processing renderers
     * stored in the sequence.xml file
     *
     * @var array
     * @since 1.0
     */
    protected $sequence = array();

    /**
     * $final
     *
     * Indicator of final processing for renderers
     * (which means clean up of unfound includes can take place)
     *
     * @var boolean
     * @since 1.0
     */
    protected $final = false;

    /**
     * $renderer_requests
     *
     * Include Statement Renderer requests extracted from the
     * theme (initially) and then the rendered output
     *
     * @var array
     * @since 1.0
     */
    protected $renderer_requests = array();

    /**
     * $rendered_output
     *
     * @var string
     * @since 1.0
     */
    protected $rendered_output = array();

    /**
     * $renderers
     *
     * Parsing process retrieves input:renderer statements from the theme and
     * rendered output, loading the requests for renderers (and associated attributes)
     * in this array
     *
     * @var string
     * @since 1.0
     */
    protected $renderers = array();

    /**
     * getInstance
     *
     * @return bool|object
     * @since  1.0
     */
    public static function getInstance()
    {
        if (empty(self::$instance)) {
            self::$instance = new MolajoParser();
        }
        return self::$instance;
    }

    /**
     * __construct
     *
     * @return boolean
     * @since  1.0
     */
    public function __construct()
    {
        $this->process();
    }

    /**
     * process
     *
     * Retrieve sequence.xml file and load into array for use in determining renderer processing order
     *
     * Invoke Theme Renderer to load page metadata, and theme language and media resources
     *
     * Retrieve Theme and Page View to initiate the iterative process of parsing rendered output
     * for <include:renderer/> statements and then looping through all renderer requests
     *
     * When no more <include:renderer/> statements are found in the rendered output,
     * process sets the Responder body and completes
     *
     * @return  object
     * @since  1.0
     */
    public function process()
    {
        /**
         *  Body Renderers: processed recursively until no more <include: found
         *      for the set of includes defined in the renderers-page.xml
         */
        $formatXML = '';
        if ($formatXML == '') {
            $formatXML = MOLAJO_APPLICATIONS . '/options/renderers-page.xml';
        }

        if (Services::File()->exists($formatXML)) {
        } else {
            //error
            return false;
        }

        $sequence = simplexml_load_file($formatXML, 'SimpleXMLElement');
        foreach ($sequence->renderer as $next) {
            $this->sequence[] = (string)$next;
        }

        /** Theme Parameters */
        $this->parameters = new Registry;
        $this->parameters->loadArray(
            array(
                'theme' => Molajo::Request()->get('theme_name'),
                'theme_path' => Molajo::Request()->get('theme_path') . '/' . 'index.php',
                'page' => Molajo::Request()->get('page_view_include'),
                'parameters' => Molajo::Request()->get('theme_parameters')
            )
        );

        $helperFile = Molajo::Request()->get('theme_path')
            . '/helpers/theme.php';

        if (file_exists($helperFile)) {
            require_once $helperFile;

            $helperClass = 'Molajo' .
                ucfirst(Molajo::Request()->get('theme_name'))
                . 'ThemeHelper';
        }

        /** Before Event */
        // Services::Dispatcher()->notify('onBeforeRender');

        $this->final = false;
        $body = $this->_renderLoop();

        /**
         *  Final Renderers: Now, the theme, head, messages, and defer renderers run
         *      and any cleanup of unfound <include values can take place
         */
        $formatXML = '';
        if ($formatXML == '') {
            $formatXML = MOLAJO_APPLICATIONS . '/options/renderers-final.xml';
        }

        if (Services::File()->exists($formatXML)) {
        } else {
            //error
            return false;
        }

        $this->sequence = array();
        $sequence = simplexml_load_file($formatXML, 'SimpleXMLElement');
        foreach ($sequence->renderer as $next) {
            if ($next == 'message') {
                $messages = Services::Message()->get();
                if (count($messages) == 0) {
                } else {
                    $this->sequence[] = (string)$next;
                }
            } else {
                $this->sequence[] = (string)$next;
            }
        }

        /** theme: load template media and language files */
        if (class_exists('MolajoThemeRenderer')) {
            $rc = new MolajoThemeRenderer ('theme');
            $results = $rc->process();

        } else {
            echo 'failed renderer = ' . 'MolajoThemeRenderer' . '<br />';
            // ERROR
        }

        $this->final = true;
        $body = $this->_renderLoop($body);

        /**
         *  Set the Response Body
         */
        Molajo::Responder()->setContent($body);

        /** after rendering */
        //        Services::Dispatcher()->notify('onAfterRender');

        return;
    }

    /**
     *  _renderLoop
     *
     * Theme Views can contain <include:renderer statements in the same manner that the
     *  Theme include files use these statements. For that reason, this method parses
     *  the initial theme include, renders the output for the <include:renderer statements
     *  found, and then parses that output again, over and over, until no more <include:renderer
     *  statements are found. Potential endless loop stopped by MOLAJO_STOP_LOOP value.
     *
     * @return string  Rendered output for the Response Head and Body
     * @since  1.0
     */
    protected function _renderLoop($body = null)
    {
        /** initial run: include the theme and page */
        if ($body == null) {
            ob_start();
            require $this->parameters->get('theme_path');
            $this->rendered_output = ob_get_contents();
            ob_end_clean();
        } else {
            /* final run: get message, head and defer */
            $this->rendered_output = $body;
        }

        /** process all buffered input for include: statements  */
        $complete = false;
        $loop = 0;
        while ($complete === false) {

            /** count looping */
            $loop++;

            /** parse theme (initially) and rendered output for include statements */
            $this->_extractRendererRequests();

            /** if no include statements found, processing is complete */
            if (count($this->renderer_requests) == 0) {
                break;
            } else {
                /** invoke renderers for new include statements */
                $this->rendered_output = $this->_callRenderer();
            }

            if ($loop > MOLAJO_STOP_LOOP) {
                break;
            }
            /** look for new include statements in just rendered output */
            continue;
        }
        return $this->rendered_output;
    }

    /**
     * _extractRendererRequests
     *
     * Parse the theme (first) and then rendered output (subsequent calls)
     * in search of include statements in order to extract renderers
     * and associated attributes
     *
     * @return  array
     * @since   1.0
     */
    protected function _extractRendererRequests()
    {
        /** initialise */
        $matches = array();
        $this->renderer_requests = array();
        $i = 0;

        /** parse theme for renderers */
        preg_match_all('#<include:(.*)\/>#iU',
            $this->rendered_output,
            $matches
        );

        if (count($matches) == 0) {
            return;
        }

        /** store renderers in array */
        foreach ($matches[1] as $includeStatement) {

            /** initialise for each renderer */
            $parts = array();
            $parts = explode(' ', $includeStatement);
            $rendererType = '';

            foreach ($parts as $part) {

                /** 1st part is the Renderer Command */
                if ($rendererType == '') {
                    $rendererType = $part;
                    $this->renderer_requests[$i]['name'] = $rendererType;
                    $this->renderer_requests[$i]['replace'] = $includeStatement;

                    /** Renderer Attributes */
                } else {
                    $attributes = str_replace('"', '', $part);

                    if (trim($attributes) == '') {
                    } else {

                        /** Associative array of attributes */
                        $pair = array();
                        $pair = explode('=', $attributes);
                        $this->renderer_requests[$i]['attributes'][$pair[0]] = $pair[1];
                    }
                }
            }
            $i++;
        }
    }

    /**
     * _callRenderer
     *
     * Invoke extension-specific renderer for include statement
     *
     * @return  string rendered output
     * @since   1.0
     */
    protected function _callRenderer()
    {
        $replace = array();
        $with = array();

        /** 1. process renderers in order defined by the sequence.xml file */
        foreach ($this->sequence as $sequence) {

            /** 2. if necessary, split renderer name from include name     */
            /** (ex. request:component or defer:head) */
            if (stripos($sequence, ':')) {
                $includeName = substr($sequence, 0, strpos($sequence, ':'));
                $rendererName = substr($sequence, strpos($sequence, ':') + 1, 999);
            } else {
                $includeName = $sequence;
                $rendererName = $sequence;
            }

            /** 3. loop thru parsed include requests for matching renderer */
            for ($i = 0; $i < count($this->renderer_requests); $i++) {

                $parsedRequests = $this->renderer_requests[$i];

                if ($includeName == $parsedRequests['name']) {

                    /** 4. place attribute pairs into variable */
                    if (isset($parsedRequests['attributes'])) {
                        $attributes = $parsedRequests['attributes'];
                    } else {
                        $attributes = array();
                    }

                    /** 5. store the "replace this" value */
                    $replace[] = "<include:" . $parsedRequests['replace'] . "/>";

                    /** 6. call the renderer class */
                    $class = 'Molajo' . ucfirst($rendererName) . 'Renderer';
                    if (class_exists($class)) {
                        $rc = new $class ($rendererName, $includeName);
                    } else {
                        echo 'failed renderer = ' . $class . '<br />';
                        die;
                        // ERROR
                    }

                    /** 7. render output and store results as "replace with" */
                    $with[] = $rc->process($attributes);
                }
            }
        }

        /** 8. replace it */
        $this->rendered_output = str_replace($replace, $with, $this->rendered_output);

        /** 9. make certain all <include:xxx /> literals are removed on final */
        if ($this->final === true) {
            $replace = array();
            $with = array();
            for ($i = 0; $i < count($this->renderer_requests); $i++) {
                $replace[] = "<include:" . $this->renderer_requests[$i]['replace'] . "/>";
                $with[] = '';
            }

            $this->rendered_output = str_replace($replace, $with, $this->rendered_output);
        }

        return $this->rendered_output;
    }
}
