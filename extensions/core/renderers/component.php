<?php
/**
 * @package     Molajo
 * @subpackage  Renderers
 * @copyright   Copyright (C) 2012 Amy Stephen. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */
defined('MOLAJO') or die;

/**
 * Component
 *
 * @package     Molajo
 * @subpackage  Renderers
 * @since       1.0
 */
class MolajoComponentRenderer
{
    /**
     * Name
     *
     * @var    string
     * @since  1.0
     */
    protected $name = null;

    /**
     * Request
     *
     * @var    object
     * @since  1.0
     */
    protected $request;

    /**
     * Attributes
     * Extracted in Document Class from Template/Page
     * <include:component statement attr1=x attr2=y attrN="and-so-on" />
     *
     * @var    array
     * @since  1.0
     */
    protected $attributes = array();

    /**
     * __construct
     *
     * Class constructor.
     *
     * @param null $name
     * @param array $request
     * @since 1.0
     */
    public function __construct($name = null, $request = array())
    {
        $this->name = $name;
        $this->request = $request;
    }

    /**
     * render
     *
     * Render the component.
     *
     * @param $attributes
     * @return mixed
     */
    public function render($attributes)
    {
        /** @var $attributes  */
        $this->attributes = $attributes;

        /** non-request executions of component from template */
        if ($this->request->get('primary_request') === false) {
            $this->_getRequest();
        }

        /** import files and classes */
        $this->import();

        /** Load Language Files */
        $this->_loadLanguage();

        /** Instantiate Controller */
        $controllerClass = ucfirst($this->request->get('option')) . 'Controller';
        if (ucfirst($this->request->get('controller')) == 'Display') {
        } else {
            $controllerClass .= $this->request->get('controller');
        }
        $controller = new $controllerClass ($this->request);

        /** Execute Task  */
        $task = (string)$this->request->get('task');
        return $controller->$task();
    }

    /**
     * _getRequest
     *
     *  Retrieve request information needed to execute component
     *  Note: this is only used for <include:component attr=1 attr=2 etc />
     *      Not for the <include:request /> Primary Component defined in MolajoRequest
     */
    protected function _getRequest()
    {
        foreach ($this->attributes as $name => $value) {
            if ($name == 'name' || $name == 'title' || $name == 'option') {
                $this->request->set('extension_title', $value);
                $this->request->set('option', $value);

            } else if ($name == 'wrap') {
                $this->request->set('wrap', $value);

            } else if ($name == 'view') {
                $this->request->set('view', $value);

            } else if ($name == 'id' || $name == 'wrap_id') {
                $this->request->set('wrap_id', $value);

            } else if ($name == 'class' || $name == 'wrap_class') {
                $this->request->set('wrap_class', $value);
            }
            // $this->request->set('other_parameters') = $other_parameters;
        }

        $this->request = MolajoExtensionHelper::getExtensionOptions($this->request);
        if ($this->request->get('results') === false) {
            echo 'failed getExtensionOptions';
        }

        /** View Path */
        $this->request->set('view_type', 'extensions');
        $viewHelper = new MolajoViewHelper($this->request->get('view'),
            $this->request->get('view_type'),
            $this->request->get('option'),
            $this->request->get('extension_type'),
            ' ',
            $this->request->get('template_name')
        );
        $this->request->set('view_path', $viewHelper->view_path);
        $this->request->set('view_path_url', $viewHelper->view_path_url);

        /** Wrap Path */
        $wrapHelper = new MolajoViewHelper($this->request->get('wrap'),
            'wraps',
            $this->request->get('option'),
            $this->request->get('extension_type'),
            ' ',
            $this->request->get('template_name')
        );
        $this->request->set('wrap_path', $wrapHelper->view_path);
        $this->request->set('wrap_path_url', $wrapHelper->view_path_url);
    }

    /**
     * import
     *
     * imports component folders and files
     * @since 1.0
     */
    public function import()
    {
        $fileHelper = new MolajoFileHelper();

        /** Controllers */
        if (file_exists($this->request->get('extension_path') . '/controller.php')) {
            $fileHelper->requireClassFile($this->request->get('extension_path') . '/controller.php', ucfirst($this->request->get('option')) . 'Controller');
        }
        $files = JFolder::files($this->request->get('extension_path') . '/controllers', '\.php$', false, false);
        if ($files) {
            foreach ($files as $file) {
                $fileHelper->requireClassFile($this->request->get('extension_path') . '/controllers/' . $file, ucfirst($this->request->get('option')) . 'Controller' . ucfirst(substr($file, 0, strpos($file, '.'))));
            }
        }
        /** Helpers */
        $files = JFolder::files($this->request->get('extension_path') . '/helpers', '\.php$', false, false);
        if ($files) {
            foreach ($files as $file) {
                $fileHelper->requireClassFile($this->request->get('extension_path') . '/helpers/' . $file, ucfirst($this->request->get('option')) . ucfirst(substr($file, 0, strpos($file, '.'))));
            }
        }

        /** Models */
        $files = JFolder::files($this->request->get('extension_path') . '/models', '\.php$', false, false);
        if ($files) {
            foreach ($files as $file) {
                $fileHelper->requireClassFile($this->request->get('extension_path') . '/models/' . $file, ucfirst($this->request->get('option')) . 'Model' . ucfirst(substr($file, 0, strpos($file, '.'))));
            }
        }

        /** Tables */
        $files = JFolder::files($this->request->get('extension_path') . '/tables', '\.php$', false, false);
        if ($files) {
            foreach ($files as $file) {
                $fileHelper->requireClassFile($this->request->get('extension_path') . '/tables/' . $file, ucfirst($this->request->get('option')) . 'Table' . ucfirst(substr($file, 0, strpos($file, '.'))));
            }
        }

        /** Views */
        $folders = JFolder::folders($this->request->get('extension_path') . '/views', false, false);
        if ($folders) {
            foreach ($folders as $folder) {
                $files = JFolder::files($this->request->get('extension_path') . '/views/' . $folder, false, false);
                if ($files) {
                    foreach ($files as $file) {
                        $fileHelper->requireClassFile($this->request->get('extension_path') . '/views/' . $folder . '/' . $file, ucfirst($this->request->get('option')) . 'View' . ucfirst($folder));
                    }
                }
            }
        }
    }

    /**
     * _loadLanguage
     *
     * Loads Language Files
     *
     * @return  boolean  True, if the file has successfully loaded.
     * @since   1.0
     */
    protected function _loadLanguage()
    {
        MolajoController::getApplication()->getLanguage()->load
        ($this->request->get('extension_path'),
            MolajoController::getApplication()->getLanguage()->getDefault(), false, false);
    }

    /**
     * _loadMedia
     *
     * Loads Media Files for Site, Application, User, and Template
     *
     * @return  boolean  True, if the file has successfully loaded.
     * @since   1.0
     */
    protected function _loadMedia()
    {
        /**  Primary Category */
        $this->_loadMediaPlus('/category' . $this->request->get('primary_category'),
            MolajoController::getApplication()->get('media_priority_primary_category', 700));

        /** Menu Item */
        $this->_loadMediaPlus('/menuitem' . $this->request->get('menu_item'),
            MolajoController::getApplication()->get('media_priority_menu_item', 800));

        /** Source */
        $this->_loadMediaPlus('/source' . $this->request->get('source_id'),
            MolajoController::getApplication()->get('media_priority_source_data', 900));

        /** Component */
        $this->_loadMediaPlus('/component' . $this->request->get('option'),
            MolajoController::getApplication()->get('media_priority_source_data', 900));
    }

    /**
     * _loadMediaPlus
     *
     * Loads Media Files for Site, Application, User, and Template
     *
     * @return  boolean  True, if the file has successfully loaded.
     * @since   1.0
     */
    protected function _loadMediaPlus($plus = '', $priority = 500)
    {
        /** Template */
        $filePath = MOLAJO_EXTENSIONS_TEMPLATES . '/' . $this->request->get('template_name');
        $urlPath = MOLAJO_EXTENSIONS_TEMPLATES_URL . '/' . $this->request->get('template_name');
        $css = MolajoController::getApplication()->addStyleLinksFolder($filePath, $urlPath, $priority);
        $js = MolajoController::getApplication()->addScriptLinksFolder($filePath, $urlPath, $priority);
        $defer = MolajoController::getApplication()->addScriptLinksFolder($filePath, $urlPath, $priority, true);
        if ($css === true || $js === true || $defer === true) {
            return true;
        }

        /** Site Specific: Application */
        $filePath = MOLAJO_SITE_FOLDER_PATH_MEDIA . '/' . MOLAJO_APPLICATION . $plus;
        $urlPath = MOLAJO_SITE_FOLDER_PATH_MEDIA_URL . '/' . MOLAJO_APPLICATION . $plus;
        $css = MolajoController::getApplication()->addStyleLinksFolder($filePath, $urlPath, $priority);
        $js = MolajoController::getApplication()->addScriptLinksFolder($filePath, $urlPath, $priority);
        $defer = MolajoController::getApplication()->addScriptLinksFolder($filePath, $urlPath, $priority, true);
        if ($css === true || $js === true || $defer === true) {
            return true;
        }

        /** Site Specific: Site-wide */
        $filePath = MOLAJO_SITE_FOLDER_PATH_MEDIA . $plus;
        $urlPath = MOLAJO_SITE_FOLDER_PATH_MEDIA_URL . $plus;
        $css = MolajoController::getApplication()->addStyleLinksFolder($filePath, $urlPath, $priority);
        $js = MolajoController::getApplication()->addScriptLinksFolder($filePath, $urlPath, $priority, false);
        $defer = MolajoController::getApplication()->addScriptLinksFolder($filePath, $urlPath, $priority, true);
        if ($css === true || $js === true || $defer === true) {
            return true;
        }

        /** All Sites: Application */
        $filePath = MOLAJO_SHARED_MEDIA . '/' . MOLAJO_APPLICATION . $plus;
        $urlPath = MOLAJO_SHARED_MEDIA_URL . '/' . MOLAJO_APPLICATION . $plus;
        $css = MolajoController::getApplication()->addStyleLinksFolder($filePath, $urlPath, $priority);
        $js = MolajoController::getApplication()->addScriptLinksFolder($filePath, $urlPath, $priority);
        $defer = MolajoController::getApplication()->addScriptLinksFolder($filePath, $urlPath, $priority, true);
        if ($css === true || $js === true || $defer === true) {
            return true;
        }

        /** All Sites: Site Wide */
        $filePath = MOLAJO_SHARED_MEDIA . $plus;
        $urlPath = MOLAJO_SHARED_MEDIA_URL . $plus;
        $css = MolajoController::getApplication()->addStyleLinksFolder($filePath, $urlPath, $priority);
        $js = MolajoController::getApplication()->addScriptLinksFolder($filePath, $urlPath, $priority);
        $defer = MolajoController::getApplication()->addScriptLinksFolder($filePath, $urlPath, $priority, true);
        if ($css === true || $js === true || $defer === true) {
            return true;
        }

        /** nothing was loaded */
        return false;
    }
}
