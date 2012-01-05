<?php
/**
 * @package     Molajo
 * @subpackage  Renderers
 * @copyright   Copyright (C) 2012 Amy Stephen. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */
defined('MOLAJO') or die;

/**
 * Module
 *
 * @package     Molajo
 * @subpackage  Renderers
 * @since       1.0
 */
class MolajoModuleRenderer
{
    /**
     * Name
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
     * <include:module statement attr1=x attr2=y attrN="and-so-on" />
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

    }

    /**
     * render
     *
     * Render the module.
     *
     * @return  object
     * @since  1.0
     */
    public function render($attributes)
    {
        /** renderer $attributes from template */
        $this->attributes = $attributes;

        $this->_getModule();

        $this->_import();

        $this->requestArray['task'] = 'display';

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

        /** View Path */
        $this->requestArray['view_type'] = 'extensions';
        $viewHelper = new MolajoViewHelper($this->requestArray['view'], $this->requestArray['view_type'], $this->requestArray['option'], $this->requestArray['extension_type'], ' ', $this->requestArray['template_name']);
        $this->requestArray['view_path'] = $viewHelper->view_path;
        $this->requestArray['view_path_url'] = $viewHelper->view_path_url;

        /** Wrap Path */
        $wrapHelper = new MolajoViewHelper($this->requestArray['wrap'], 'wraps', $this->requestArray['option'], $this->requestArray['extension_type'], ' ', $this->requestArray['template_name']);
        $this->requestArray['wrap_path'] = $wrapHelper->view_path;
        $this->requestArray['wrap_path_url'] = $wrapHelper->view_path_url;
/**
echo '<pre>';var_dump($this->requestArray);echo '</pre>';
*/
        /** Load Language Files */
        $this->_loadLanguageModule();

        /** Instantiate Controller */
        $controller = new $this->requestArray['controller'] ($this->requestArray);

        /** Execute Task  */
        $task = $this->requestArray['task'];
        return $controller->$task();
    }

    /**
     * _getModule
     *
     * Retrieve Module information using the Title
     *
     * @return bool
     * @since 1.0
     */
    protected function _getModule()
    {
        foreach ($this->attributes as $name => $value) {
            if ($name == 'name' || $name == 'title') {
                $this->requestArray['extension_title'] = $value;
                echo $value;
            }
        }

        $results = MolajoExtensionHelper::get(MOLAJO_ASSET_TYPE_EXTENSION_MODULE, $this->requestArray['extension_title'], null);

        if (count($results) > 0) {
            foreach ($results as $result) {
                $this->requestArray['extension_id'] = $result->extension_id;
                $this->requestArray['extension_name'] = $result->extension_name;
                $this->requestArray['extension_title'] = $result->title;

                $parameters = new JRegistry;
                $parameters->loadString($result->parameters);
                $this->requestArray['extension_parameters'] = $parameters;
                $this->requestArray['extension_metadata'] = $result->metadata;

                if (isset($this->requestArray['extension_parameters']->static)
                    && $this->requestArray['extension_parameters']->static === true
                ) {
                    $this->requestArray['static'] = true;
                } else {
                    $this->requestArray['static'] = false;
                }
                $this->requestArray['extension_path'] = MOLAJO_EXTENSIONS_MODULES . '/' . $this->requestArray['extension_name'];
                $this->requestArray['extension_type'] = 'module';
                $this->requestArray['extension_folder'] = '';

                $this->requestArray['controller'] = ucfirst($this->requestArray['extension_name']) . 'ControllerModule';
                $this->requestArray['model'] = ucfirst($this->requestArray['extension_name']) . 'ModelModule';
            }
            return true;
        } else {
            return false;
        }
    }

    /**
     * import
     *
     * imports module folders and files
     * @since 1.0
     */
    protected function _import()
    {
        $fileHelper = new MolajoFileHelper();

        /** Controller */
        if (file_exists($this->requestArray['extension_path'] . '/controller.php')) {
            $fileHelper->requireClassFile($this->requestArray['extension_path'] . '/controller.php', ucfirst($this->requestArray['extension_name']) . 'ControllerModule');
        }
        /** Model */
        if (file_exists($this->requestArray['extension_path'] . '/model.php')) {
            $fileHelper->requireClassFile($this->requestArray['extension_path'] . '/model.php', ucfirst($this->requestArray['extension_name']) . 'ModelModule');
        }
    }

    /**
     * _loadLanguageModule
     *
     * Loads Language Files
     *
     * @return  boolean  True, if the file has successfully loaded.
     * @since   1.0
     */
    protected function _loadLanguageModule()
    {
        MolajoController::getLanguage()->load($this->requestArray['option'],
            $this->requestArray['extension_path'],
            MolajoController::getLanguage()->getDefault(), false, false);
    }
}
