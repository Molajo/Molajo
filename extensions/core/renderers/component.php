<?php
/**
 * @package     Molajo
 * @subpackage  Renderers
 * @copyright   Copyright (C) 2012 Amy Stephen. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */
defined('MOLAJO') or die;

/**
 * Component Renderer
 *
 * @package     Molajo
 * @subpackage  Renderers
 * @since       1.0
 */
class MolajoComponentRenderer
{
    /**
     * Name - from MolajoExtension
     *
     * @var    string
     * @since  1.0
     */
    protected $name = null;

    /**
     * Request Array - from MolajoExtension
     *
     * @var    array
     * @since  1.0
     */
    protected $requestArray = array();

    /**
     * Attributes - from Template/Page <include:component statement attr1=x attr2=y />
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
     * @param array $config
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

        /** wrap and wrap path */
        foreach ($this->attributes as $name => $value) {
            if ($name == 'wrap') {
                $this->requestArray['wrap'] = $value;
                break;
            }
        }

        if ($this->requestArray['wrap'] == '') {
            $this->requestArray['wrap'] = 'none';
        }
        $wrapHelper = new MolajoViewHelper($this->requestArray['wrap'], 'wraps', $this->requestArray['option'], $this->requestArray['extension_type'], ' ', $this->requestArray['template_name']);
        $this->requestArray['wrap_path'] = $wrapHelper->view_path;
        $this->requestArray['wrap_path_url'] = $wrapHelper->view_path_url;
        /*
        echo '<pre>';var_dump($this->requestArray);echo '</pre>';
        **/

        /** Before Rendering */
        //        MolajoController::getApplication()->registerEvent ('onBeforeRender', 'system');
        //        MolajoController::getApplication()->triggerEvent ('onBeforeRender', $this);

        /** path */
        $path = $this->requestArray['extension_path'] . '/' . $this->requestArray['option'] . '.php';

        /** installation */
        if (MOLAJO_APPLICATION_ID == 0
            && file_exists($path)
        ) {

            /** language */
        } elseif (file_exists($path)) {
            $this->_loadLanguage();

        } else {
            MolajoError::raiseError(404, MolajoTextHelper::_('MOLAJO_APPLICATION_ERROR_COMPONENT_NOT_FOUND'));
        }

        /** component => MVC */

        $controllerClass = 'MolajoController' . ucfirst($this->requestArray['controller']);
        $controller = new $controllerClass ($this->requestArray);
        $task = $this->requestArray['task'];
        $output = $controller->$task();

        /** After Rendering */
        MolajoController::getApplication()->registerEvent('onAfterRender', 'system');
        MolajoController::getApplication()->triggerEvent('onAfterRender', array($this, $output));

        /** Return output */
        return $output;
    }

    /**
     * _loadLanguage
     *
     * Loads the view language file
     *
     * @return  boolean  True, if the file has successfully loaded.
     * @since   1.0
     */
    protected function _loadLanguage()
    {
        MolajoController::getLanguage()->load(strtolower($this->requestArray['option']),
            $this->requestArray['extension_path'], null, false, false);
    }

    /**
     * import
     *
     * import component folders and files
     *
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
                echo $file . '<br />';
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
}
