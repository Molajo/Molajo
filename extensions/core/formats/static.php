<?php
/**
 * @package     Molajo
 * @subpackage  Static
 * @copyright   Copyright (C) 2012 Amy Stephen. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */
defined('MOLAJO') or die;

/**
 * Static Page Renderer - useful for pages like Error and Offline
 *
 * @package     Molajo
 * @subpackage  Static
 * @since       1.0
 */
class MolajoStaticFormat
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
//        echo '<pre>';var_dump($config);'</pre>';

        /** set class properties */
        $this->config = $config;
        $this->message = $config->message;
        $this->template = $config->template;
        $this->page = $config->page;
        $this->layout = $config->layout;
        $this->wrap = $config->wrap;

        /** Request */
        $this->_renderTemplate();
    }

    /**
     * Render the Template
     *
     * @return  object
     * @since  1.0
     */
    protected function _renderTemplate()
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
$template_path = MOLAJO_EXTENSIONS_TEMPLATES . '/system';
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
        $body = ob_get_contents();
        ob_end_clean();

        MolajoFactory::getApplication()->setBody($body);

        /** After Event */
        MolajoFactory::getApplication()->triggerEvent('onAfterRender');

        return;
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
                    if (MolajoFactory::getApplication()->direction == 'rtl') {
                        MolajoFactory::getApplication()->addStyleSheet($urlPath . '/css/' . $file);
                    }
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

        /** Favicon */
        $path = $directory . '/';
        $dirs = array($path, $path . 'images/', MOLAJO_BASE_FOLDER . '/');
        foreach ($dirs as $dir) {
            $icon = $dir . 'favicon.ico';
            if (file_exists($icon)) {
                $path = str_replace(MOLAJO_BASE_FOLDER . '/', '', $dir);
                $path = str_replace('\\', '/', $path);
                $this->addFavicon(JURI::base(true) . '/' . $path . 'favicon.ico');
                break;
            }
        }

        return $contents;
    }
}