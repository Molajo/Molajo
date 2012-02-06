<?php
/**
 * @package     Molajo
 * @subpackage  Controller
 * @copyright   Copyright (C) 2012 Amy Stephen. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */
defined('MOLAJO') or die;

/**
 * Parser
 *
 * @package     Molajo
 * @subpackage  Controller
 * @since       1.0
 */
class MolajoParserController
{
    /**
     * Application static instance
     *
     * @var    object
     * @since  1.0
     */
    protected static $instance;

    /**
     * $_sequence
     *
     * Renderers Processing Sequence
     *
     * @var array
     * @since 1.0
     */
    protected $_sequence = array();

    /**
     * $parameters
     *
     * Parameters used by Theme and Page View
     *
     * @var string
     * @since 1.0
     */
    public $parameters = array();

    /**
     * $_theme
     *
     * @var string
     * @since 1.0
     */
    protected $_theme = array();

    /**
     * $_renderers
     *
     * Parsing process retrieves input:renderer statements from the theme and
     * rendered output, loading the requests for renderers (and associated attributes)
     * in this array
     *
     * @var string
     * @since 1.0
     */
    protected $_renderers = array();

    /**
     * Configuration
     *
     * @var    object
     * @since  1.0
     */
    protected static $_config = null;

    /**
     * getInstance
     *
     * Returns a reference to the global Application object,
     *  only creating it if it doesn't already exist.
     *
     * @static
     * @param  null $id
     * @param  JInput|null $input
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
            self::$instance = new MolajoParserController($config);
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

        /** sequence of renderer processing */
        $formatXML = '';
        if (array_key_exists('xml', $this->_config)) {
            $formatXML = $this->_config['xml'];
            if (JFile::exists($formatXML)) {
            } else {
                $formatXML = '';
            }
        }
        if ($formatXML == '') {
            $formatXML = MOLAJO_EXTENSIONS_CORE . '/core/renderers/sequence.xml';
        }

        if (JFile::exists($formatXML)) {
        } else {
            //error
            return false;
        }

        $sequence = simplexml_load_file($formatXML, 'SimpleXMLElement');
        foreach ($sequence->renderer as $next) {
            $this->_sequence[] = (string)$next;
        }

        $this->_processTheme();

        return true;
    }

    /**
     * _processTheme
     *
     * Retrieves Theme and begins the process of first parsing the Theme and Page View
     * for input:renderer statements, looping through the renderers for the statements found,
     * and then continuing the process by parsing the rendered output for additional input
     * statements until no more are found.
     *
     * @return  object
     * @since  1.0
     */
    protected function _processTheme()
    {
        /** Theme Parameters */
        $this->parameters = new JRegistry;
        $this->parameters->loadArray(
            array(
                'theme' => Molajo::Request()->get('theme_name'),
                'theme_path' => Molajo::Request()->get('theme_path'),
                'page' => Molajo::Request()->get('page_view_include'),
                'parameters' => Molajo::Request()->get('theme_parameters')
            )
        );

        /** Before Event */
        // Molajo::Application()->triggerEvent('onBeforeRender');

        /** process theme include, and then all rendered output, for <include statements */
        $body = $this->_renderLoop();

        /** theme: load template media and language files, does not renderer template output */
        if (class_exists('MolajoThemeRenderer')) {
            $rc = new MolajoThemeRenderer ('theme');
            $results = $rc->render();

        } else {
            echo 'failed renderer = ' . 'MolajoThemeRenderer' . '<br />';
            // ERROR
        }

        /** set response body */
        Molajo::Responder()->setBody($body);

        /** after rendering */
//        Molajo::Application()->triggerEvent('onAfterRender');

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
    protected function _renderLoop()
    {
        /** include the theme and page */
        ob_start();
        require Molajo::Request()->get('theme_path');
        $this->_theme = ob_get_contents();
        ob_end_clean();

        /** process all buffered input for include: statements  */
        $complete = false;
        $loop = 0;
        while ($complete === false) {

            /** count looping */
            $loop++;

            /** parse $this->theme for include statements */
            $this->_parseTheme();

            /** if no more include statements found, processing is complete */
            if (count($this->_renderers) == 0) {
                break;
            } else {
                /** invoke renderers for new include statements */
                $this->_theme = $this->_renderTheme();
            }

            if ($loop > MOLAJO_STOP_LOOP) {
                break;
            }
            /** look for new include statements in just rendered output */
            continue;
        }

        return $this->_theme;
    }

    /**
     * _parseTheme
     *
     * Parse the theme and extract renderers and associated attributes
     *
     * @return  Parsed contents of the theme
     * @since   1.0
     */
    protected function _parseTheme()
    {
        /** initialise */
        $matches = array();
        $this->_renderers = array();
        $i = 0;

        /** parse theme for renderers */
        preg_match_all('#<include:(.*)\/>#iU', $this->_theme, $matches);

        if (count($matches) == 0) {
            return;
        }

        /** store renderers in array */
        foreach ($matches[1] as $includeString) {

            /** initialise for each renderer */
            $includeArray = array();
            $includeArray = explode(' ', $includeString);
            $rendererType = '';

            foreach ($includeArray as $rendererCommand) {

                /** Type of Renderer */
                if ($rendererType == '') {
                    $rendererType = $rendererCommand;
                    $this->_renderers[$i]['name'] = $rendererType;
                    $this->_renderers[$i]['replace'] = $includeString;

                    /** Renderer Attributes */
                } else {
                    $rendererAttributes = str_replace('"', '', $rendererCommand);

                    if (trim($rendererAttributes) == '') {
                    } else {

                        /** Associative array of named pairs */
                        $splitAttribute = array();
                        $splitAttribute = explode('=', $rendererAttributes);
                        $this->_renderers[$i]['attributes'][$splitAttribute[0]] = $splitAttribute[1];
                    }
                }
            }
            $i++;
        }
    }

    /**
     * _renderTheme
     *
     * Render pre-parsed theme
     *
     * @return  string rendered theme
     * @since   1.0
     */
    protected function _renderTheme()
    {
        $replace = array();
        $with = array();

        /** 1. process every renderer in the format file in defined order */
        foreach ($this->_sequence as $nextSequence) {

            /** 2. if necessary, split renderer name from include name (ex. request:component) */
            if (stripos($nextSequence, ':')) {
                $includeName = substr($nextSequence, 0, strpos($nextSequence, ':'));
                $rendererName = substr($nextSequence, strpos($nextSequence, ':') + 1, 999);
            } else {
                $includeName = $nextSequence;
                $rendererName = $nextSequence;
            }

            /** 4. loop thru all extracted include values to find match */
            for ($i = 0; $i < count($this->_renderers); $i++) {

                $rendererArray = $this->_renderers[$i];

                if ($includeName == $rendererArray['name']) {

                    /** 5. place attribute pairs into variable */
                    if (isset($rendererArray['attributes'])) {
                        $attributes = $rendererArray['attributes'];
                    } else {
                        $attributes = array();
                    }

                    /** 6. store the "replace this" value */
                    $replace[] = "<include:" . $rendererArray['replace'] . "/>";

                    /** 7. load the renderer class */
                    $class = 'Molajo' . ucfirst($rendererName) . 'Renderer';
                    if (class_exists($class)) {
                        $rc = new $class ($rendererName, $includeName);
                    } else {
                        echo 'failed renderer = ' . $class . '<br />';
                        // ERROR
                    }
                    /** 8. render output and store results as "replace with" */
                    $with[] = $rc->render($attributes);
                }
            }
        }

        /** 9. replace it */
        $this->_theme = str_replace($replace, $with, $this->_theme);

        /** 10. make certain all <include:xxx /> literals are removed */
        $replace = array();
        $with = array();
        for ($i = 0; $i < count($this->_renderers); $i++) {
            $replace[] = "<include:" . $this->_renderers[$i]['replace'] . "/>";
            $with[] = '';
        }

        return str_replace($replace, $with, $this->_theme);
    }
}
