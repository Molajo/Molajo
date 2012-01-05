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
     * From Molajo Extension
     *
     * @var    string
     * @since  1.0
     */
    protected $name = null;

    /**
     * Request Array
     * From Molajo Extension
     *
     * @var    array
     * @since  1.0
     */
    protected $requestArray = array();

    /**
     * Attributes
     * Extracted in Format Class from Template/Page
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
     * @param array $requestArray
     * @since 1.0
     */
    public function __construct($name = null, $requestArray = array())
    {
        /**
        echo '<pre>';
        var_dump($requestArray);
        '</pre>';
         **/
        $this->name = $name;

        $this->requestArray = $requestArray;

        $this->import();
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
        if (file_exists($this->requestArray['extension_path'] . '/controller.php')) {
            $fileHelper->requireClassFile($this->requestArray['extension_path'] . '/controller.php', ucfirst($this->requestArray['option']) . 'Controller');
        }
        $files = JFolder::files($this->requestArray['extension_path'] . '/controllers', '\.php$', false, false);
        if ($files) {
            foreach ($files as $file) {
                $fileHelper->requireClassFile($this->requestArray['extension_path'] . '/controllers/' . $file, ucfirst($this->requestArray['option']) . 'Controller' . ucfirst(substr($file, 0, strpos($file, '.'))));
            }
        }
        /** Helpers */
        $files = JFolder::files($this->requestArray['extension_path'] . '/helpers', '\.php$', false, false);
        if ($files) {
            foreach ($files as $file) {
                $fileHelper->requireClassFile($this->requestArray['extension_path'] . '/helpers/' . $file, ucfirst($this->requestArray['option']) . ucfirst(substr($file, 0, strpos($file, '.'))));
            }
        }

        /** Models */
        $files = JFolder::files($this->requestArray['extension_path'] . '/models', '\.php$', false, false);
        if ($files) {
            foreach ($files as $file) {
                $fileHelper->requireClassFile($this->requestArray['extension_path'] . '/models/' . $file, ucfirst($this->requestArray['option']) . 'Model' . ucfirst(substr($file, 0, strpos($file, '.'))));
            }
        }

        /** Tables */
        $files = JFolder::files($this->requestArray['extension_path'] . '/tables', '\.php$', false, false);
        if ($files) {
            foreach ($files as $file) {
                $fileHelper->requireClassFile($this->requestArray['extension_path'] . '/tables/' . $file, ucfirst($this->requestArray['option']) . 'Table' . ucfirst(substr($file, 0, strpos($file, '.'))));
            }
        }

        /** Views */
        $folders = JFolder::folders($this->requestArray['extension_path'] . '/views', false, false);
        if ($folders) {
            foreach ($folders as $folder) {
                $files = JFolder::files($this->requestArray['extension_path'] . '/views/' . $folder, false, false);
                if ($files) {
                    foreach ($files as $file) {
                        $fileHelper->requireClassFile($this->requestArray['extension_path'] . '/views/' . $folder . '/' . $file, ucfirst($this->requestArray['option']) . 'View' . ucfirst($folder));
                    }
                }
            }
        }
    }

    /**
     * render
     *
     * Render the component.
     *
     * @return  object
     * @since  1.0
     */
    public function render($attributes)
    {
        /** renderer $attributes from template */
        $this->attributes = $attributes;
        $changeWrap = false;
        $changeView = false;

        foreach ($this->attributes as $name => $value) {
            if ($name == 'wrap') {
                $this->requestArray['wrap'] = $value;
                $changeWrap = true;

            } else if ($name == 'view') {
                $this->requestArray['view'] = $value;
                $changeView = true;

            } else if ($name == 'id' || $name == 'wrap_id') {
                $this->requestArray['wrap_id'] = $value;

            } else if ($name == 'class' || $name == 'wrap_class') {
                $this->requestArray['wrap_class'] = $value;
            }
            // $this->requestArray['other_parameters'] = $other_parameters;
        }

        if ($changeWrap === true || $changeView === true) {
            $this->requestArray = MolajoExtensionHelper::getExtensionOptions($this->requestArray);
            if ($this->requestArray['results'] === false) {
                echo 'failed getExtensionOptions';
            }

            /** View Path */
            $this->requestArray['view_type'] = 'extensions';
            $viewHelper = new MolajoViewHelper($this->requestArray['view'], $this->requestArray['view_type'], $this->requestArray['option'], $this->requestArray['extension_type'], ' ', $this->requestArray['template_name']);
            $this->requestArray['view_path'] = $viewHelper->view_path;
            $this->requestArray['view_path_url'] = $viewHelper->view_path_url;

            /** Wrap Path */
            $wrapHelper = new MolajoViewHelper($this->requestArray['wrap'], 'wraps', $this->requestArray['option'], $this->requestArray['extension_type'], ' ', $this->requestArray['template_name']);
            $this->requestArray['wrap_path'] = $wrapHelper->view_path;
            $this->requestArray['wrap_path_url'] = $wrapHelper->view_path_url;
        }

        /** Load Language Files */
        $this->_loadLanguageComponent();

        /** Instantiate Controller */
        $controllerClass = ucfirst($this->requestArray['option']) . 'Controller';
        if (ucfirst($this->requestArray['controller']) == 'Display') {
        } else {
            $controllerClass .= $this->requestArray['controller'];
        }
        $controller = new $controllerClass ($this->requestArray);

        /** Execute Task  */
        $task = $this->requestArray['task'];
        return $controller->$task();
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
        MolajoController::getLanguage()->load($this->requestArray['option'],
            $this->requestArray['extension_path'],
            MolajoController::getLanguage()->getDefault(), false, false);
    }
}
