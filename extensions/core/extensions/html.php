<?php
/**
 * @package     Molajo
 * @subpackage  HTML
 * @copyright   Copyright (C) 2012 Amy Stephen. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */
defined('MOLAJO') or die;

/**
 * HTML Renderer
 *
 * @package     Molajo
 * @subpackage  HTML
 * @since       1.0
 */
class MolajoHTML
{
    /**
     *  Template folder name
     *
     * @var string
     * @since 1.0
     */
    protected $template = null;

    /**
     *  Template Parameters
     *
     * @var string
     * @since 1.0
     */
    protected $parameters = null;

    /**
     *  Page include file
     *
     * @var string
     * @since 1.0
     */
    protected $page = null;

    /**
     *  Asset object
     *
     * @var string
     * @since 1.0
     */
    protected $asset = null;

    /**
     * __construct
     *
     * Class constructor.
     *
     * @param   null    $request    An optional argument to provide dependency injection for the asset
     * @param   null    $asset_id   An optional argument to provide dependency injection for the asset
     *
     * @return boolean
     *
     * @since  1.0
     */
    public function __construct($template, $page, $asset)
    {
        /** set class properties */
        $this->template = $template;
        $this->page = $page;
        $this->asset = $asset;

        /** Request */
        $this->renderTemplate();
    }

    /**
     * renderTemplate
     *
     * Render the Template - extract and process doc statements
     *
     * @return  object
     * @since  1.0
     */
    protected function _renderTemplate()
    {
        /** Template */
        $templates = MolajoExtension::getExtensions(MOLAJO_ASSET_TYPE_EXTENSION_TEMPLATE, $id);

        foreach ($templates as $template) {
            $registry = new JRegistry;
            $registry->loadJSON($template->parameters);
            $template->parameters = $registry;

            if (file_exists(MOLAJO_EXTENSIONS_TEMPLATES . '/' . $template->title . '/' . 'index.php')) {
            } else {
                $template->title = 'molajito';
            }
        }

        $this->parameters = array(
            'template' => $template[0]->title,
            'file' => 'index.php',
            'directory' => MOLAJO_EXTENSIONS_TEMPLATES,
            'parameters' => $template[0]->parameters
        );

        /** Media */

        /** Application-specific CSS and JS in => media/[application]/css[js]/XYZ.css[js] */
        $filePath = MOLAJO_SITE_FOLDER_PATH_MEDIA . '/' . MOLAJO_APPLICATION;
        $urlPath = JURI::root() . 'sites/' . MOLAJO_SITE . '/media/' . MOLAJO_APPLICATION;
        self::_loadMediaCSS($filePath, $urlPath);
        self::_loadMediaJS($filePath, $urlPath);

        /** Template-specific CSS and JS in => template/[template-name]/css[js]/XYZ.css[js] */
        $filePath = MOLAJO_EXTENSIONS_TEMPLATES . '/' . $template[0]->title;
        $urlPath = JURI::root() . 'cms/templates/' . $template[0]->title;
        self::_loadMediaCSS($filePath, $urlPath);
        self::_loadMediaJS($filePath, $urlPath);

        /** Language */
        $lang = MolajoFactory::getLanguage();
        $lang->load($template[0]->title, MOLAJO_EXTENSIONS_TEMPLATES . '/' . $template[0]->title, $lang->getDefault(), false, false);

        /** Fetch */
        $this->_fetchTemplate($this->parameters);

        /** Load */
        $this->_file = $directory . '/' . $filename;

        ob_start();
        require $directory . '/' . $filename;
        $this->_template = ob_get_contents();
        ob_end_clean();

        /** Parse */
        $this->_parseTemplate();

        /** Before Event */
        MolajoFactory::getApplication()->triggerEvent('onBeforeRender');

        /** Render */
        //$body = MolajoFactory::getDocument()->render(false, $this->parameters);

        MolajoFactory::getApplication()->setBody($body);

        /** After Event */
        MolajoFactory::getApplication()->triggerEvent('onAfterRender');

        return;
    }

    /**
     * _fetchTemplate --GET RID OF
     *
     * Fetch the template, and initialise the parameters
     *
     * @param   array  $this->parameters  parameters to determine the template
     */
    protected function _fetchTemplate()
    {
        if (isset($this->parameters['directory'])) {
            $directory = $this->parameters['directory'];
        } else {
            $directory = MOLAJO_EXTENSIONS_TEMPLATES;
        }

        $filter = JFilterInput::getInstance();
        $template = $filter->clean($this->parameters['template'], 'cmd');
        $file = $filter->clean($this->parameters['file'], 'cmd');

        if (file_exists($directory . '/' . $template . '/' . $file)) {
        } else {
            $template = 'system';
        }

        /** Language File */
        $lang = MolajoFactory::getLanguage();
        $lang->load('template_' . $template, MOLAJO_EXTENSIONS_TEMPLATES . '/' . $template, $lang->getDefault(), false, false);

        /** Variables */
        $this->template = $template;
        $this->baseurl = JURI::base(true);

        $this->parameters = isset($this->parameters['parameters']) ? $this->parameters['parameters'] : new JRegistry;

    }

    /**
     * Parse a document template
     *
     * @return  The parsed contents of the template
     */
    protected function _parseTemplate()
    {
        $matches = array();

        if (preg_match_all('#<doc:include\ type="([^"]+)" (.*)\/>#iU', $this->_template, $matches)) {
            $template_tags_first = array();
            $template_tags_last = array();
            // Step through the docs in reverse order.
            for ($i = count($matches[0]) - 1; $i >= 0; $i--) {
                $type = $matches[1][$i];
                $attribs = empty($matches[2][$i]) ? array() : MolajoUtility::parseAttributes($matches[2][$i]);
                $name = isset($attribs['name']) ? $attribs['name'] : null;

                // Separate buffers to be executed first and last
                if ($type == 'module' || $type == 'modules') {
                    $template_tags_first[$matches[0][$i]] = array('type' => $type, 'name' => $name, 'attribs' => $attribs);
                } else {
                    $template_tags_last[$matches[0][$i]] = array('type' => $type, 'name' => $name, 'attribs' => $attribs);
                }
            }
            // Reverse the last array so the docs are in forward order.
            $template_tags_last = array_reverse($template_tags_last);

            $this->_template_tags = $template_tags_first + $template_tags_last;
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
            $replace[] = $doc;
            $with[] = $this->_getBuffer($args['type'], $args['name'], $args['attribs']);
        }
        return str_replace($replace, $with, $this->_template);
    }

    /**
     * Get the contents of a document include
     *
     * @param   string  $type    The type of renderer
     * @param   string  $name    The name of the element to render
     * @param   array   $attribs Associative array of remaining attributes.
     *
     * @return  The output of the renderer
     */
    protected function _getBuffer($type = null, $name = null, $attribs = array())
    {
        if (isset($this->_buffer[$type][$name])) {
            return $this->_buffer[$type][$name];
        }

        // put head, message component, modules, module
        // $renderer = $this->loadRenderer($type);

        // todo: amy put back module caching
        //$results = $renderer->render($name, $attribs, false);

        $this->_setBuffer($results, $type, $name);
        return parent::$this->_buffer[$type][$name];
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
                    if (MolajoFactory::getDocument()->direction == 'rtl') {
                        MolajoFactory::getDocument()->addStyleSheet($urlPath . '/css/' . $file);
                    }
                } else {
                    MolajoFactory::getDocument()->addStyleSheet($urlPath . '/css/' . $file);
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
                MolajoFactory::getDocument()->addScript($urlPath . '/js/' . $file);
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