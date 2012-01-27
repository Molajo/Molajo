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

        $output = array;
        foreach($attributes as $name=>$value) {
            if ($name == 'name' || $name == 'title') {
                $this->request->set('extension_title', $value);

            } else if ($name == 'view') {
                $this->request->set('view_name', $value);

            } else if ($name == 'tag') {
                $this->_position = $value;

            } else if ($name == 'view') {
                $this->request->set('view_name', $value);

            } else if ($name == 'view_css_id' || $name == 'view_id') {
                $this->request->set('view_css_id', $value);

            } else if ($name == 'view_css_class' || $name == 'view_class') {
                $this->request->set('view_css_class', $value);
            }
        }

        $holdWrap =  $this->request->get('wrap_name');
        $holdWrapID =  $this->request->get('view_css_id');
        $holdWrapClass =  $this->request->get('view_css_class');

        $this->request->set('wrap_name', 'none');
        $this->request->set('view_css_id', '');
        $this->request->set('view_css_class', '');

        /** all modules for a specific tag */
        $modules = MolajoExtensionHelper::get(MOLAJO_ASSET_TYPE_EXTENSION_POSITION, $this->_tag, null);
        if (count($modules) > 0) {
        } else {
            return false;
        }

        foreach ($modules as $module) {
            $rendererClass = new MolajoRendererModule ('module', $this->request, '');
            return $rendererClass->render($attributes);
        }
    }
}
