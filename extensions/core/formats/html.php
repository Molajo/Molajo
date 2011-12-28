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
     *  Layout include file
     *
     * @var string
     * @since 1.0
     */
    protected $layout = null;

    /**
     *  Wrap for Layout
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
     *  Template Tags
     *
     * @var string
     * @since 1.0
     */
    protected $_template_tags = array();

    /**
     * Array of buffered output
     *
     * @var    mixed (depends on the renderer)
     */
    protected $_buffer = null;

    /**
     * __construct
     *
     * Class constructor.
     *
     * @param   null    $request
     * @param   null    $page
     *
     * @return boolean
     *
     * @since  1.0
     */
    public function __construct($config = array())
    {
//        echo '<pre>';
//        var_dump($config);
//        '</pre>';

        /** set class properties */
        $this->config = $config;
        $this->message = $config->message;
        $this->template = $config->template;
        $this->page = $config->page;
        $this->layout = $config->layout;
        $this->wrap = $config->wrap;

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
        $templates = MolajoExtensionHelper::getExtensions(MOLAJO_ASSET_TYPE_EXTENSION_TEMPLATE, $this->template);

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

        $template_path = MOLAJO_EXTENSIONS_TEMPLATES . '/' . $this->template;

        $template_page_include = $template_path . '/pages/default/index.php';

        $this->parameters = array(
            'template' => $template_name,
            'template_path' => $template_path,
            'page' => $template_page_include,
            'parameters' => $template_parameters

        );

        /** Before Event */
        MolajoFactory::getApplication()->triggerEvent('onBeforeRender');

        /** Media */

        /** Application-specific CSS and JS in => media/[application]/css[js]/XYZ.css[js] */
        $filePath = MOLAJO_SITE_FOLDER_PATH_MEDIA . '/' . MOLAJO_APPLICATION;
        $urlPath = JURI::root() . 'sites/' . MOLAJO_SITE . '/media/' . MOLAJO_APPLICATION;
        self::_loadMediaCSS($filePath, $urlPath);
        self::_loadMediaJS($filePath, $urlPath);

        /** Template-specific CSS and JS in => template/[template-name]/css[js]/XYZ.css[js] */
        $filePath = MOLAJO_EXTENSIONS_TEMPLATES . '/' . $template_name;
        $urlPath = JURI::root() . 'extensions/templates/' . $template_name;
        self::_loadMediaCSS($filePath, $urlPath);
        self::_loadMediaJS($filePath, $urlPath);

        /** Language */
        $lang = MolajoFactory::getLanguage();
        $lang->load($template_name, MOLAJO_EXTENSIONS_TEMPLATES . '/' . $template_name, $lang->getDefault(), false, false);

        ob_start();
        require $template_include;
        $this->_template = ob_get_contents();
        ob_end_clean();

        $this->_parseTemplate();

        $body = $this->_renderTemplate();

        MolajoFactory::getApplication()->setBody($body);

        /** After Event */
        MolajoFactory::getApplication()->triggerEvent('onAfterRender');

        return;
    }

    /**
     * Parse a document template
     *
     * @return  The parsed contents of the template
     */
    protected function _parseTemplate()
    {
        $matches = array();

        if (preg_match_all('#<include:(.*)\/>#iU', $this->_template, $matches)) {
            foreach ($matches[1] as $includeString) {
                $positionArray = array();
                $positionArray = explode(' ', $includeString);
                $renderType = '';
                $attributes = array();
                foreach ($positionArray as $value) {
                    if ($renderType == '') {
                        $renderType = $value;
                    } else {
                        $test = str_replace('"', '', $value);
                        if (trim($test) == '') {
                        } else {
                            $attributes[] = str_replace('"', '', $value);
                        }
                    }
                }
                echo '<pre>';echo 'Type ';echo $renderType;echo ' Attributes';var_dump($attributes); echo '</pre>';
            }

            die;
            $template_tags_first = array();
            $template_tags_last = array();

            for ($i = count($matches[0]) - 1; $i >= 0; $i--) {

                $type = $matches[1][$i];
                $attributes = empty($matches[2][$i]) ? array() : MolajoUtility::parseAttributes($matches[2][$i]);
                $name = isset($attributes['name']) ? $attributes['name'] : null;

                // Separate buffers to be executed first and last
                if ($type == 'module' || $type == 'modules') {
                    $template_tags_first[$matches[0][$i]] = array('type' => $type, 'name' => $name, 'attributes' => $attributes);
                } else {
                    $template_tags_last[$matches[0][$i]] = array('type' => $type, 'name' => $name, 'attributes' => $attributes);
                }
            }
            // Reverse the last array so the docs are in forward order.
            $template_tags_last = array_reverse($template_tags_last);

            $this->_template_tags = $template_tags_first + $template_tags_last;
            echo '<pre>';var_dump($this->_template_tags);'</pre>';
            die;
        }
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

        foreach ($this->_template_tags AS $doc => $args) {
            echo '<pre>';echo $doc;var_dump($args);'</pre>';
            $replace[] = $doc;
            $with[] = $this->_getBuffer($args['type'], $args['name'], $args['attributes']);
        }
        return str_replace($replace, $with, $this->_template);
    }

    /**
     * _getBuffer
     *
     * Get the contents of a document include
     *
     * @param   string  $type        The type of renderer
     * @param   string  $name        The name of the element to render
     * @param   array   $attributes  Associative array of remaining attributes.
     *
     * @return  The output of the renderer
     */
    protected function _getBuffer($type = null, $name = null, $attributes = array())
    {
        if (isset($this->_buffer[$type][$name])) {
            return $this->_buffer[$type][$name];
        }

        // put head, message component, modules, module

        // todo: amy put back module caching
        
        $class = 'Molajo'.ucfirst($type);
        $extension = new $class ($name, $attributes, $this->config);
        $results = $extension->render();

        $this->_setBuffer($results, $type, $name);
        return $this->_buffer[$type][$name];
    }

    /**
     * _setBuffer
     *
     * Set the contents a document includes
     *
     * @param   string  $content    The content to be set in the buffer.
     * @param   array   $options    Array of optional elements.
     */
    protected function _setBuffer($content, $options = array())
    {
        if (func_num_args() > 1 && !is_array($options)) {
            $args = func_get_args();
            $options = array();
            $options['type'] = $args[1];
            $options['name'] = (isset($args[2])) ? $args[2] : null;
        }

        $this->_buffer[$options['type']][$options['name']] = $content;
    }

    /**
     * _loadMediaCSS
     *
     * Loads the CS located within the folder, as specified by the filepath
     *
     * @param $filePath
     * @param $urlPath
     * @return void
     */
    protected function _loadMediaCSS($filePath, $urlPath)
    {
        if (JFolder::exists($filePath . '/css')) {
        } else {
            return;
        }

        $files = JFolder::files($filePath . '/css', '\.css$', false, false);

        if (count($files) > 0) {
            foreach ($files as $file) {
                if (substr($file, 0, 4) == 'rtl_') {
//                    if (MolajoFactory::getApplication()->direction == 'rtl') {
//                        MolajoFactory::getApplication()->addStyleSheet($urlPath . '/css/' . $file);
//                    }
                } else {
                    MolajoFactory::getApplication()->addStyleSheet($urlPath . '/css/' . $file);
                }
            }
        }
    }

    /**
     * _loadMediaJS
     *
     * Loads the JS located within the folder, as specified by the filepath
     *
     * @param $filePath
     * @param $urlPath
     * @return void
     */
    protected function _loadMediaJS($filePath, $urlPath)
    {
        if (JFolder::exists($filePath . '/js')) {
        } else {
            return;
        }
        //todo: differentiate between script and scripts
        $files = JFolder::files($filePath . '/js', '\.js$', false, false);

        if (count($files) > 0) {
            foreach ($files as $file) {
                MolajoFactory::getApplication()->addScript($urlPath . '/js/' . $file);
            }
        }
    }

    /**
     * Load a template file
     *
     * @param string    $template    The name of the template
     * @param string    $filename    The actual filename
     * @return string The contents of the template
     */
    protected function _loadFavicon()
    {
        $contents = '';

        $path = MOLAJO_EXTENSIONS_TEMPLATES . '/' . $this->template . '/images/';

        if (file_exists($path . 'favicon.ico')) {
            $urlPath = JURI::root() . 'extensions/templates/' . $this->template . '/images/favicon.ico';
            MolajoFactory::getApplication()->addFavicon($urlPath);
            return;
        }

        return false;
    }
}