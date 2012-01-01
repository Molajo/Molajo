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
     * Config - from MolajoExtension
     *
     * @var    array
     * @since  1.0
     */
    protected $config = array();

    /**
     * Option - extracted from config
     *
     * @var    string
     * @since  1.0
     */
    protected $option = null;

    /**
     *  Template folder name - extracted from config
     *
     * @var string
     * @since 1.0
     */
    protected $template = null;

    /**
     *  Page include file - extracted from config
     *
     * @var string
     * @since 1.0
     */
    protected $page = null;

    /**
     *  View include file - extracted from config
     *
     * @var string
     * @since 1.0
     */
    protected $view = null;

    /**
     *  Wrap for View - extracted from config
     *
     * @var string
     * @since 1.0
     */
    protected $wrap = null;

    /**
     *  Template Parameters - extracted from config
     *
     * @var string
     * @since 1.0
     */
    protected $parameters = null;

    /**
     * Attributes - from the Molajo Format Class <include:component statement>
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
    public function __construct($name = null, $config = array())
    {
        /**
        echo '<pre>';
        var_dump($config);
        '</pre>';
         **/
        /** set class properties */
        $this->name = $name;

        /** set class properties */
        $this->config = $config;
        if (isset($config['message'])) {
            $this->message = $config['message'];
        } else {
            $this->message ='';
        }
        $this->option = $config['option'];
        $this->template = $config['template_name'];
        $this->page = $config['page'];
        $this->view = $config['view'];
        $this->wrap = $config['wrap'];

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

        /** Before Rendering */
//        MolajoController::getApplication()->registerEvent ('onBeforeRender', 'system');
//        MolajoController::getApplication()->triggerEvent ('onBeforeRender', $this);

        /** path */
        $path = MOLAJO_EXTENSIONS_COMPONENTS . '/' . $this->option . '/' . $this->option . '.php';

        /** installation */
        if (MOLAJO_APPLICATION_ID == 0
            && file_exists($path)
        ) {

        /** language */
        } elseif (file_exists($path)) {
            MolajoController::getLanguage()->load($this->option, $path, MolajoController::getLanguage()->getDefault(), false, false);

        } else {
            MolajoError::raiseError(404, MolajoTextHelper::_('MOLAJO_APPLICATION_ERROR_COMPONENT_NOT_FOUND'));
        }

        /** component => MVC */
        $request = $this->config;
        ob_start();
        require_once $path;
        $output = ob_get_contents();
        ob_end_clean();

        /** After Rendering */
        MolajoController::getApplication()->registerEvent ('onAfterRender', 'system');
        MolajoController::getApplication()->triggerEvent ('onAfterRender', array($this, $output));

        /** Return output */
        return $output;
    }
    
    /**
     * import
     * 
     * import component folders and files
     * 
     */
    public function import ()
    {
        $fileHelper = new MolajoFileHelper();
        
        /** Controllers */
        if (file_exists($this->config['component_path'] . '/controller.php')) {
            $fileHelper->requireClassFile($this->config['component_path'] . '/controller.php', ucfirst($this->config['option']) . 'Controller');
        }
        $files = JFolder::files($this->config['component_path'] . '/controllers', '\.php$', false, false);
        if ($files) {
            foreach ($files as $file) {
                echo $file . '<br />';
                $fileHelper->requireClassFile($this->config['component_path'] . '/controllers/' . $file, ucfirst($this->config['option']) . 'Controller' . ucfirst(substr($file, 0, strpos($file, '.'))));
            }
        }
        /** Helpers */
        $files = JFolder::files($this->config['component_path'] . '/helpers', '\.php$', false, false);
        if ($files) {
            foreach ($files as $file) {
                $fileHelper->requireClassFile($this->config['component_path'] . '/helpers/' . $file, ucfirst($this->config['option']) . ucfirst(substr($file, 0, strpos($file, '.'))));
            }
        }
        
        /** Models */
        $files = JFolder::files($this->config['component_path'] . '/models', '\.php$', false, false);
        if ($files) {
            foreach ($files as $file) {
                $fileHelper->requireClassFile($this->config['component_path'] . '/models/' . $file, ucfirst($this->config['option']) . 'Model' . ucfirst(substr($file, 0, strpos($file, '.'))));
            }
        }
        
        /** Tables */
        $files = JFolder::files($this->config['component_path'] . '/tables', '\.php$', false, false);
        if ($files) {
            foreach ($files as $file) {
                $fileHelper->requireClassFile($this->config['component_path'] . '/tables/' . $file, ucfirst($this->config['option']) . 'Table' . ucfirst(substr($file, 0, strpos($file, '.'))));
            }
        }
        
        /** Views */
        $folders = JFolder::folders($this->config['component_path'] . '/views', false, false);
        if ($folders) {
            foreach ($folders as $folder) {
                $files = JFolder::files($this->config['component_path'] . '/views/' . $folder, false, false);
                if ($files) {
                    foreach ($files as $file) {
                        $fileHelper->requireClassFile($this->config['component_path'] . '/views/' . $folder . '/' . $file, ucfirst($this->config['option']) . 'View' . ucfirst($folder));
                    }
                }
            }
        }        
    }        
}
