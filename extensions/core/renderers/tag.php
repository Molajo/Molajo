<?php
/**
 * @package     Molajo
 * @subpackage  Renderer
 * @copyright   Copyright (C) 2012 Amy Stephen. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */
defined('MOLAJO') or die;

/**
 * Tag
 *
 * @package     Molajo
 * @subpackage  Renderer
 * @since       1.0
 */
class MolajoRendererTag extends MolajoRenderer
{
    /**
     * render
     *
     * Render the component.
     *
     * @param   $attributes <include:renderer attr1=x attr2=y attr3=z ... />
     *
     * @return  mixed
     * @since   1.0
     */
    public function render($attributes)
    {
        /** attributes come from <include:renderer statement */
        $this->_attributes = $attributes;
        $holdAttributes = $this->_attributes;

        $tag = '';
        $output = array();
        foreach ($attributes as $name => $value) {
            if ($name == 'name' || $name == 'title') {
                $tag = $value;

            } else if ($name == 'view') {
                $output['view'] = $value;

            } else if ($name == 'view_css_id' || $name == 'view_id') {
                $output['view_css_id'] = $value;

            } else if ($name == 'view_css_class' || $name == 'view_class') {
                $output['view_css_class'] = $value;
            }
        }

        $output['wrap'] = 'none';
        $output['wrap_css_id'] = '';
        $output['wrap_css_class'] = '';
        $this->_attributes = $output;

        $holdWrap = $this->mvc->get('wrap_name');
        $holdWrapID = $this->mvc->get('view_css_id');
        $holdWrapClass = $this->mvc->get('view_css_class');

        $this->mvc->set('wrap_name', 'none');
        $this->mvc->set('view_css_id', '');
        $this->mvc->set('view_css_class', '');

        /** all modules for a specific tag */
        if (trim($tag) == '') {
            return;
        }
        $modules = MolajoExtensionHelper::get(MOLAJO_ASSET_TYPE_EXTENSION_POSITION, $tag, null);
        if (count($modules) > 0) {
        } else {
            return false;
        }

        $renderedOutput = '';
        foreach ($modules as $module) {

            $this->_name = $module->title;

            /** reset view and other MVC values for extension */
            $this->_setRequest();

            $this->mvc->set('extension_instance_id', $module->extension_instance_id);
            $this->mvc->set('extension_instance_name', $module->title);
            $this->mvc->set('extension_asset_type_id', MOLAJO_ASSET_TYPE_EXTENSION_MODULE);
            $this->mvc->set('extension_asset_id', $module->extension_instance_asset_id);
            $this->mvc->set('extension_view_group_id', $module->extension_instance_view_group_id);
            $this->mvc->set('extension_custom_fields', $module->custom_fields);
            $this->mvc->set('extension_metadata', $module->metadata);
            $this->mvc->set('extension_parameters', $module->parameters);
            $this->mvc->set('extension_path', $module->title);
            $this->mvc->set('extension_type', 'module');
            $this->mvc->set('extension_folder', '');
            $this->mvc->set('extension_event_type', 'content');

            /** get extension values */
            $this->_getAsset();

            $this->_getExtension();

            /** establish values needed for MVC */
            $this->_setParameters();

            /** retrieves MVC defaults for application */
            $this->_getApplicationDefaults();

            /** lazy load paths for view files */
            $this->_setPaths();

            $this->_initialize = false;
            $renderedOutput .= $this->render($attributes);
        }

        return $renderedOutput;
    }
}
