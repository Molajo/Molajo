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
        $this->_loadLanguageComponent();

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
     * _loadLanguageComponent
     *
     * Loads Language Files
     *
     * @return  boolean  True, if the file has successfully loaded.
     * @since   1.0
     */
    protected function _loadLanguageComponent()
    {
        MolajoController::getApplication()->getLanguage()->load
            ($this->request->get('option'),
                $this->request->get('extension_path'),
                MolajoController::getApplication()->getLanguage()->getDefault(), false, false);
    }
}
