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
     * Request Array
     *
     * @var array
     * @since 1.0
     */
    protected $requestArray = null;

    /**
     * Template Parameters
     *
     * @var string
     * @since 1.0
     */
    protected $parameters = null;

    /**
     * Template
     *
     * @var string
     * @since 1.0
     */
    protected $_template = array();

    /**
     * Holds renderer set defined within the template and associated attributes
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
     *
     * @since  1.0
     */
    public function __construct($requestArray = array())
    {
        /*
                        echo '<pre>';
                        var_dump($requestArray);
                        '</pre>';
        */
        $formatXML = MOLAJO_EXTENSIONS_CORE . '/core/formats/'.$requestArray['format'].'.xml';
        if (JFile::exists($formatXML)) {
        } else {
            //error
            return false;
        }
        $sequence = simplexml_load_file($formatXML, 'SimpleXMLElement');
        foreach ($sequence->renderer as $next) {
            $this->sequence[] = (string)$next;
        }

        /** Set Class Properties */
        $this->requestArray = $requestArray;

        /** Request */
        $this->_render();
    }

    /**
     * Render the Template
     *
     * @return  object
     * @since  1.0
     */
    protected function _render()
    {
        /** Initialize */
        $template_include = '';

        if (file_exists(MOLAJO_EXTENSIONS_TEMPLATES . '/' . $this->requestArray['template_name'] . '/' . 'index.php')) {
            $template_include = MOLAJO_EXTENSIONS_TEMPLATES . '/' . $this->requestArray['template_name'] . '/' . 'index.php';
        } else {
            $this->requestArray['template_name'] = 'system';
            $template_include = MOLAJO_EXTENSIONS_TEMPLATES . '/system/index.php';
        }

        $template_path = MOLAJO_EXTENSIONS_TEMPLATES . '/' . $this->requestArray['template_name'];
        $template_page_include = $this->requestArray['page_path'] . '/index.php';

        $this->parameters = array(
            'template' => $this->requestArray['template_name'],
            'template_path' => $template_path,
            'page' => $template_page_include,
            'parameters' => $this->requestArray['template_parameters']

        );

        /** Before Event */
        //        MolajoController::getApplication()->triggerEvent('onBeforeRender');

        /** Media */

        /** Application-specific CSS and JS in => media/[application]/css[js]/XYZ.css[js] */
        $filePath = MOLAJO_SITE_FOLDER_PATH_MEDIA . '/' . MOLAJO_APPLICATION;
        $urlPath = MOLAJO_SITE_FOLDER_PATH_MEDIA_URL . '/' . MOLAJO_APPLICATION;
        MolajoController::getApplication()->loadMediaCSS($filePath, $urlPath);
        MolajoController::getApplication()->loadMediaJS($filePath, $urlPath);

        /** Load Language Files */
        $this->_loadLanguageTemplate();

        ob_start();
        require $template_include;
        $this->_template = ob_get_contents();
        ob_end_clean();

        $this->_parseTemplate();

        $body = $this->_renderTemplate();

        MolajoController::getApplication()->setBody($body);

        /** Template-specific CSS and JS in => template/[template-name]/css[js]/XYZ.css[js] */
        $filePath = MOLAJO_EXTENSIONS_TEMPLATES . '/' . $this->requestArray['template_name'];
        $urlPath = MOLAJO_EXTENSIONS_TEMPLATES_URL . '/' . $this->requestArray['template_name'];
        MolajoController::getApplication()->loadMediaCSS($filePath, $urlPath);
        MolajoController::getApplication()->loadMediaJS($filePath, $urlPath);

        /** After Rendering */
        MolajoController::getApplication()->triggerEvent('onAfterRender');

        return;
    }

    /**
     * _loadLanguageTemplate
     *
     * Loads Language Files
     *
     * @return  boolean  True, if the file has successfully loaded.
     * @since   1.0
     */
    protected function _loadLanguageTemplate()
    {
        MolajoController::getLanguage()->load($this->requestArray['template_name'],
            MOLAJO_EXTENSIONS_TEMPLATES . '/' . $this->requestArray['template_name'],
            MolajoController::getLanguage()->getDefault(), false, false);
    }

    /**
     * _parseTemplate
     *
     * Parse the template and extract renderers and associated attributes
     *
     * @return  The parsed contents of the template
     */
    protected function _parseTemplate()
    {
        /** initialise */
        $matches = array();
        $this->_renderers = array();
        $i = 0;

        /** parse template for renderers */
        preg_match_all('#<include:(.*)\/>#iU', $this->_template, $matches);

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

        /** echo '<pre>';var_dump($this->_renderers);echo '</pre>'; */
    }

    /**
     * _renderTemplate
     *
     * Render pre-parsed template
     *
     * @return string rendered template
     */
    protected function _renderTemplate()
    {
        $replace = array();
        $with = array();

        foreach ($this->sequence as $nextSequence) {

            /** request:component */
            if (stripos($nextSequence, ':')) {
                $includeName = substr($nextSequence, 0, strpos($nextSequence, ':'));
                $rendererName = substr($nextSequence, strpos($nextSequence, ':') + 1, 999);
            } else {
                $includeName = $nextSequence;
                $rendererName = $nextSequence;
            }

            /** Primary Component <include:request attr1=x /> */
            if ($includeName == 'request') {
                $this->requestArray['primary_request'] = true;
            } else {
                $this->requestArray['primary_request'] = false;
            }

            for ($i=0; $i < count($this->_renderers); $i++) {

                $rendererArray = $this->_renderers[$i];

                if ($includeName == $rendererArray['name']) {

                    /** extract attribute pairs into an array */
                    if (isset($rendererArray['attributes'])) {
                        $attributes = $rendererArray['attributes'];
                    } else {
                        $attributes = array();
                    }

                    /** store value for replacement */
                    $replace[] = "<include:" . $rendererArray['replace'] . "/>";

                    /** load renderer class */
                    $class = 'Molajo' . ucfirst($rendererName) . 'Renderer';
                    if (class_exists($class)) {
                        $rendererClass = new $class ($rendererName, $this->requestArray);
                    } else {
                        echo 'failed renderer = ' . $class . '<br />';
                        // ERROR
                    }
                    $with[] = $rendererClass->render($attributes);
                }
            }
        }
        return str_replace($replace, $with, $this->_template);
    }

    /**
     * Load a Favicon
     *
     * @return bool
     */
    protected function _loadFavicon()
    {
        $path = MOLAJO_EXTENSIONS_TEMPLATES . '/' . $this->requestArray['template_name'] . '/images/';

        if (file_exists($path . 'favicon.ico')) {
            $urlPath = MOLAJO_EXTENSIONS_TEMPLATES_URL . '/' . $this->requestArray['template_name'] . '/images/favicon.ico';
            MolajoController::getApplication()->addFavicon($urlPath);
            return true;
        }
        return false;
    }
}