<?php
/**
 * @package     Molajo
 * @subpackage  Helper
 * @copyright   Copyright (C) 2011 Amy Stephen. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */
defined('MOLAJO') or die;

/**
 * Template Helper
 *
 * @package     Molajo
 * @subpackage  Template Helper
 * @since       1.0
 */
abstract class MolajoTemplateHelper
{

    /**
     * Get the template
     *
     * @return string The template name
     * @since 1.0
     */
    static public function getTemplate()
    {
        /** initialize */
        $menuItem = null;
        $id = 0;
        $condition = '';

        /** Menu Item Template */
        $menu = MolajoMenu::getInstance(null, array());
        if ($menu == null) {
            $menuItem = null;
        } else {
            $menuItem = $menu->getActive();
            if ($menuItem) {
            } else {
                $menuItem = $menu->getItem(JRequest::getInt('Itemid'));
            }
        }

        if (is_object($menuItem)) {
            $id = $menuItem->template_style_id;
        }

        /** Override if Template ID sent in */
        if (JRequest::getVar('template', 0) > 0) {
            $id = (int)JRequest::getVar('template', 0);
        }

        /** Configuration default */
        if ((int)$id == 0) {
            $id = MolajoFactory::getConfig()->get('default_template_extension_id');
        }

        /** Retrieve Template from the DB */
        $templates = MolajoExtensionHelper::getExtensions(MOLAJO_EXTENSION_TYPE_TEMPLATES, $id);

        foreach ($templates as $template) {
            $registry = new JRegistry;
            $registry->loadJSON($template->parameters);
            $template->parameters = $registry;
        }

        if (file_exists(MOLAJO_EXTENSION_TEMPLATES . '/' . $template->name . '/' . 'index.php')) {
        } else {
            $template->name = 'molajito';
        }

        return $templates;
    }
}