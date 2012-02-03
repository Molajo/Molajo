<?php
/**
 * @package     Molajo
 * @subpackage  Document
 * @copyright   Copyright (C) 2012 Amy Stephen. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */
defined('MOLAJO') or die;

/**
 * Document
 *
 * @package     Molajo
 * @subpackage  Document
 * @since       1.0
 */
class MolajoDocument
{
    /**
     * Rendering Sequence
     *
     * @var array
     * @since 1.0
     */
    protected $sequence = array();

    /**
     * Request
     *
     * @var object
     * @since 1.0
     */
    public $request = null;

    /**
     * Theme Parameters
     *
     * @var string
     * @since 1.0
     */
    public $parameters = null;

    /**
     * Theme
     *
     * @var string
     * @since 1.0
     */
    protected $_theme = array();

    /**
     * Holds renderer set defined within the theme and associated attributes
     *
     * @var string
     * @since 1.0
     */
    protected $_renderers = array();

    /**
     * __construct
     *
     * Class constructor.
     *
     * @param   null    $requestArray from MolajoRequests
     *
     * @return boolean
     * @since  1.0
     */
    public function __construct($request = array())
    {
        $this->request = $request;

        $formatXML = MOLAJO_EXTENSIONS_CORE . '/core/renderers/sequence.xml';
        if (JFile::exists($formatXML)) {
        } else {
            //error
            return false;
        }
        $sequence = simplexml_load_file($formatXML, 'SimpleXMLElement');
        foreach ($sequence->renderer as $next) {
            $this->sequence[] = (string)$next;
        }
        /** Request */
        $this->_render();
    }

    /**
     * Render the Theme
     *
     * @return  object
     * @since  1.0
     */
    protected function _render()
    {
        $parameters = array(
            'theme' => $this->request->get('theme_name'),
            'theme_path' => $this->request->get('theme_path'),
            'page' => $this->request->get('page_view_include'),
            'parameters' => $this->request->get('theme_parameters')
        );

        /** Theme Parameters */
        $this->parameters = new JRegistry;
        $this->parameters->loadArray($parameters);

        /** Before Event */
        // MolajoController::getApplication()->triggerEvent('onBeforeRender');

        /** process theme include, and then all rendered output, for <include statements */
        $body = $this->_renderLoop();

        /** theme: load template media and language files, does not renderer template output */
        if (class_exists('MolajoRendererTheme')) {
            $tmp = array();
            $rendererClass = new MolajoRendererTheme ('theme', $this->request);
            $results = $rendererClass->render($tmp);
        } else {
            echo 'failed renderer = ' . MolajoRendererTheme . '<br />';
            // ERROR
        }

        /** set response body */
        MolajoController::getApplication()->setBody($body);

        /** after rendering */
        MolajoController::getApplication()->triggerEvent('onAfterRender');

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
        require $this->request->get('theme_path');
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
     * @return  The parsed contents of the theme
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
        foreach ($this->sequence as $nextSequence) {

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

                    /** 7. load the renderer class and send in requestArray */
                    $class = 'Molajo' . 'Renderer' . ucfirst($rendererName);
                    if (class_exists($class)) {
                        $rendererClass = new $class ($rendererName, $this->request, $includeName);
                    } else {
                        echo 'failed renderer = ' . $class . '<br />';
                        // ERROR
                    }
                    /** 8. render output and store results as "replace with" */
                    $with[] = $rendererClass->render($attributes);
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
