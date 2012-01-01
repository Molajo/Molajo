<?php
/**
 * @package     Molajo
 * @subpackage  HTML
 * @copyright   Copyright (C) 2012 Amy Stephen. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */
defined('MOLAJO') or die;

/**
 * Render HTML Format
 *
 * @package     Molajo
 * @subpackage  HTML
 * @since       1.0
 */
class MolajoHtmlFormat
{
    /**
     *  Sequence in which renderers are to be processed
     *
     * @var array
     * @since 1.0
     */
    protected $rendererProcessingSequence = array();

    /**
     *  Config
     *
     * @var array
     * @since 1.0
     */
    protected $config = null;

    /**
     *  Message
     *
     * @var string
     * @since 1.0
     */
    protected $message = null;

    /**
     *  Template folder name
     *
     * @var string
     * @since 1.0
     */
    protected $template = null;

    /**
     *  Page include file
     *
     * @var string
     * @since 1.0
     */
    protected $page = null;

    /**
     *  View include file
     *
     * @var string
     * @since 1.0
     */
    protected $view = null;

    /**
     *  Wrap for View
     *
     * @var string
     * @since 1.0
     */
    protected $wrap = null;

    /**
     *  Template Parameters
     *
     * @var string
     * @since 1.0
     */
    protected $parameters = null;

    /**
     *  Template
     *
     * @var string
     * @since 1.0
     */
    protected $_template = array();

    /**
     *  Holds set of renderers defined within the template and associated attributes
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
     * @param   null    $config from MolajoExtensions
     *
     * @return boolean
     *
     * @since  1.0
     */
    public function __construct($config = array())
    {
                echo '<pre>';
                var_dump($config);
                '</pre>';

        $sequence = simplexml_load_file(MOLAJO_EXTENSIONS_CORE . '/core/formats/sequence.xml', 'SimpleXMLElement');
        foreach ($sequence->format as $format) {
            if ($format->name == 'html') {
                foreach ($format->renderer as $renderer) {
                    $this->rendererProcessingSequence[] = (string)$renderer[0];
                }
                break;
            }
        }

        /** set class properties */
        $this->config = $config;
        if (isset($config['message'])) {
            $this->message = $config['message'];
        } else {
            $this->message ='';
        }
        $this->template = $config['template_name'];
        $this->page = $config['page'];
        $this->view = $config['view'];
        $this->wrap = $config['wrap'];

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
        /** Query */
        $templates = MolajoExtensionHelper::get(MOLAJO_ASSET_TYPE_EXTENSION_TEMPLATE, $this->template);

        /** Initialize */
        $template_name = '';
        $template_include = '';
        $template_parameters = '';

        /* Process Query Results */
        if (count($templates) > 0) {
            foreach ($templates as $template) {

                $registry = new JRegistry;
                $registry->loadJSON($template_parameters);
                $template_parameters = $registry;

                if (file_exists(MOLAJO_EXTENSIONS_TEMPLATES . '/' . $template->title . '/' . 'index.php')) {
                    $template_include = MOLAJO_EXTENSIONS_TEMPLATES . '/' . $template->title . '/' . 'index.php';
                    $template_name = $template->title;
                }
            }
        }

        if ($template_name == '') {
            $template_include = MOLAJO_EXTENSIONS_TEMPLATES . '/system/index.php';
            $template_name = 'system';
        }

        $template_path = MOLAJO_EXTENSIONS_TEMPLATES . '/' . $template_name;

        $template_page_include = $template_path . '/pages/'.$this->page.'/index.php';

        $this->parameters = array(
            'template' => $template_name,
            'template_path' => $template_path,
            'page' => $template_page_include,
            'parameters' => $template_parameters

        );

        /** Before Event */
        MolajoController::getApplication()->triggerEvent('onBeforeRender');

        /** Media */

        /** Application-specific CSS and JS in => media/[application]/css[js]/XYZ.css[js] */
        $filePath = MOLAJO_SITE_FOLDER_PATH_MEDIA . '/' . MOLAJO_APPLICATION;
        $urlPath = JURI::root() . 'sites/' . MOLAJO_SITE . '/media/' . MOLAJO_APPLICATION;
        MolajoController::getApplication()->loadMediaCSS($filePath, $urlPath);
        MolajoController::getApplication()->loadMediaJS($filePath, $urlPath);

        /** Template-specific CSS and JS in => template/[template-name]/css[js]/XYZ.css[js] */
        $filePath = MOLAJO_EXTENSIONS_TEMPLATES . '/' . $template_name;
        $urlPath = JURI::root() . 'extensions/templates/' . $template_name;
        MolajoController::getApplication()->loadMediaCSS($filePath, $urlPath);
        MolajoController::getApplication()->loadMediaJS($filePath, $urlPath);

        /** Language */
        $lang = MolajoController::getLanguage();
        $lang->load($template_name, MOLAJO_EXTENSIONS_TEMPLATES . '/' . $template_name, $lang->getDefault(), false, false);

        ob_start();
        require $template_include;
        $this->_template = ob_get_contents();
        ob_end_clean();

        $this->_parseTemplate();

        $body = $this->_renderTemplate();

        MolajoController::getApplication()->setBody($body);

        /** After Event */
        MolajoController::getApplication()->triggerEvent('onAfterRender');

        return;
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

        //        echo '<pre>';var_dump($this->_renderers);echo '</pre>';
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

        foreach ($this->rendererProcessingSequence as $nextRenderer) {

            /** load renderer class */
            $class = 'Molajo' . ucfirst($nextRenderer) . 'Renderer';
            if ($class == 'MolajoHeadRenderer') {
            } elseif (class_exists($class)) {
                $rendererClass = new $class ($nextRenderer, $this->config);
                echo $class . '<br />';
            } else {
                echo 'failed renderer = ' . $class . '<br />';
                // ERROR
            }

            if ($class == 'MolajoHeadRenderer') {
            } else {
                foreach ($this->_renderers as $index => $rendererArray) {

                    if ($nextRenderer == $rendererArray['name']) {

                        $renderer = $rendererArray['name'];

                        if (isset($rendererArray['attributes'])) {
                            $attributes = $rendererArray['attributes'];
                        } else {
                            $attributes = array();
                        }

                        $replace[] = "<include:" . $rendererArray['replace'] . "/>";
                        $with[] = $rendererClass->render($attributes);
                    }
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
        $path = MOLAJO_EXTENSIONS_TEMPLATES . '/' . $this->template . '/images/';

        if (file_exists($path . 'favicon.ico')) {
            $urlPath = JURI::root() . 'extensions/templates/' . $this->template . '/images/favicon.ico';
            MolajoController::getApplication()->addFavicon($urlPath);
            return true;
        }

        return false;
    }
}