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
class MolajoPositionRenderer
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
     * Render the position.
     *
     * @return  object
     * @since  1.0
     */
    public function render($attributes)
    {

        $this->attributes = $attributes;
        $lessName = array();

        /** extract position name from attributes */
        $position = '';
        foreach ($this->attributes as $name => $value) {
            if ($name == 'name') {
                $position = $value;
            } else {
                $lessName[$name] = $value;
            }
        }

        /** position not defined */
        if ($position == '') {
            return false;
        }

        /** process modules */
        $modules = MolajoExtensionHelper::get(MOLAJO_ASSET_TYPE_EXTENSION_POSITION, $position, null);

        $buffer = '';
        $moduleAttributes = $lessName;

        if (count($modules) > 0) {

            foreach ($modules as $module) {
                $moduleAttributes['name'] = $module->title;
                echo 'Module Title.'.$module->title;
                echo '<pre>';var_dump($module);echo '</pre>';
                $renderer = new MolajoModuleRenderer ('module', $this->requestArray);
                $buffer .= $renderer->render($moduleAttributes);
            }
        }
        return $buffer;
    }
}
