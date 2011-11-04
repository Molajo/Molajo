<?php
/**
 * @package     Molajo
 * @subpackage  Helper
 * @copyright   Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
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
    public function getTemplate($params = false)
    {
        if (is_object($this->template)) {
            if ($params) {
                return $this->template;
            }
            return $this->template->template;
        }

        // Get the id of the active menu item
        $menu = $this->getMenu();
        if ($menu == null) {
            $item = null;
        } else {
            $item = $menu->getActive();
            if (!$item) {
                $item = $menu->getItem(JRequest::getInt('Itemid'));
            }
        }

        $id = 0;
        if (is_object($item)) { // valid item retrieved
            $id = $item->template_style_id;
        }
        $condition = '';

        $tid = JRequest::getVar('template', 0);
        if (is_int($tid) && $tid > 0) {
            $id = (int) $tid;
        }

        $cache = MolajoFactory::getCache('com_templates', '');
        $defaultTemplate = MolajoFactory::getConfig()->get('default_template_extension_id');
        if ((int) $id == 0) {
            $id = $defaultTemplate;
        }

        if ($templates = $cache->get('templates0')) {
        } else {
            $templates = MolajoExtensionHelper::getExtensions(MOLAJO_EXTENSION_TYPE_TEMPLATES);
            foreach($templates as $template) {

                $registry = new JRegistry;
                $registry->loadJSON($template->parameters);
                $template->parameters = $registry;

                if ($template->id == $id) {
                    $selected = $template;
                    break;
                }
            }
            $cache->store($templates, 'templates0');
        }

        // Allows for overriding the active template from the request
        $selected->template = JRequest::getCmd('template', $selected->template);
        $selected->template = JFilterInput::getInstance()->clean($selected->template, 'cmd');

        // Fallback template
        if (file_exists(MOLAJO_EXTENSION_TEMPLATES.'/'.$selected->template.'/'.'index.php')) {
        } else {
            MolajoError::raiseWarning(0, MolajoText::_('MolajoError_ALERTNOTEMPLATE'));
            $selected->template = MolajoFactory::getConfig()->get('default_template_extension_id', 'molajito');
            if (file_exists(MOLAJO_EXTENSION_TEMPLATES.'/'.$selected->template.'/index.php')) {
            } else {
                $selected->template = '';
            }
        }
        echo 'safasdfasdf';
echo '<pre>'; var_dump($selected->template);'</pre>';
        die;
        // Cache the result
        $this->template = $selected->template;
        if ($params) {
            return $selected->template;
        }

        return $selected->template;
    }
}