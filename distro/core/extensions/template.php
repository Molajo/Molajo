<?php
/**
 * @package     Molajo
 * @subpackage  Extensions
 * @copyright   Copyright (C) 2012 Amy Stephen. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */
defined('MOLAJO') or die;

/**
 * Template
 *
 * @package     Molajo
 * @subpackage  Application
 * @since       1.0
 */
abstract class MolajoTemplate
{
    /**
     * getTemplate
     *
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
        $menu = MolajoMenu::getInstance(MOLAJO_APPLICATION, array());

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
            $id = $menuItem->template_id;
        }

        /** Override if Template ID sent in */
        if (JRequest::getVar('template', 0) > 0) {
            $id = (int)JRequest::getVar('template', 0);
        }

        /** Configuration default */
        if ((int)$id == 0) {
            $id = strtolower(MolajoFactory::getConfig()->get('default_template_extension'));
        }

        /** Retrieve Template from the DB */
        $templates = MolajoExtension::getExtensions(MOLAJO_ASSET_TYPE_EXTENSION_TEMPLATE, $id);

        foreach ($templates as $template) {
            $registry = new JRegistry;
            $registry->loadJSON($template->parameters);
            $template->parameters = $registry;

            if (file_exists(MOLAJO_DISTRO_TEMPLATES . '/' . $template->title . '/' . 'index.php')) {
            } else {
                $template->title = 'molajito';
            }
        }

        return $templates;
    }

    /**
     * renderTemplate
     *
     * Render the Template - extract and process doc statements
     *
     * @return  object
     * @since  1.0
     */
    public static function renderTemplate()
    {
        /** Scope */
        $scope = MolajoFactory::getApplication()->scope;
        MolajoFactory::getApplication()->scope = 'Render Template';

        /** Template */
        $template = self::getTemplate();

        $parameters = array(
            'template' => $template[0]->title,
            'file' => 'index.php',
            'directory' => MOLAJO_DISTRO_TEMPLATES,
            'parameters' => $template[0]->parameters
        );

        /** Media */

        /** Application-specific CSS and JS in => media/[application]/css[js]/XYZ.css[js] */
        $filePath = MOLAJO_SITE_PATH_MEDIA . '/' . MOLAJO_APPLICATION;
        $urlPath = JURI::root() . 'sites/' . MOLAJO_SITE . '/media/' . MOLAJO_APPLICATION;
        self::loadMediaCSS($filePath, $urlPath);
        self::loadMediaJS($filePath, $urlPath);

        /** Template-specific CSS and JS in => template/[template-name]/css[js]/XYZ.css[js] */
        $filePath = MOLAJO_DISTRO_TEMPLATES . '/' . $template[0]->title;
        $urlPath = JURI::root() . 'cms/templates/' . $template[0]->title;
        self::loadMediaCSS($filePath, $urlPath);
        self::loadMediaJS($filePath, $urlPath);

        /** Language */
        $lang = MolajoFactory::getLanguage();
        $lang->load($template[0]->title, MOLAJO_DISTRO_TEMPLATES . '/' . $template[0]->title, $lang->getDefault(), false, false);

        /** Application  */
        $applicationClass = 'Molajo' . ucfirst(MOLAJO_APPLICATION) . 'Application';
        $app = new $applicationClass ();

        /** Parse */
        MolajoFactory::getDocument()->parse($parameters);

        /** Before Event */
        $app->triggerEvent('onBeforeRender');

        /** Render */
        $body = MolajoFactory::getDocument()->render(false, $parameters);

        JResponse::setBody($body);

        /** After Event */
        $app->triggerEvent('onAfterRender');

        /** Revert scope */
        MolajoFactory::getApplication()->scope = $scope;

        return;
    }

    /**
     * loadMediaCSS
     *
     * Loads the CS located within the folder, as specified by the filepath
     *
     * @param $filePath
     * @param $urlPath
     * @return void
     */
    public function loadMediaCSS($filePath, $urlPath)
    {
        if (JFolder::exists($filePath . '/css')) {
        } else {
            return;
        }

        $files = JFolder::files($filePath . '/css', '\.css$', false, false);

        if (count($files) > 0) {
            foreach ($files as $file) {
                if (substr($file, 0, 4) == 'rtl_') {
                    if (MolajoFactory::getDocument()->direction == 'rtl') {
                        MolajoFactory::getDocument()->addStyleSheet($urlPath . '/css/' . $file);
                    }
                } else {
                    MolajoFactory::getDocument()->addStyleSheet($urlPath . '/css/' . $file);
                }
            }
        }
    }

    /**
     * loadMediaJS
     *
     * Loads the JS located within the folder, as specified by the filepath
     *
     * @param $filePath
     * @param $urlPath
     * @return void
     */
    public function loadMediaJS($filePath, $urlPath)
    {
        if (JFolder::exists($filePath . '/js')) {
        } else {
            return;
        }
        //todo: differentiate between script and scripts
        $files = JFolder::files($filePath . '/js', '\.js$', false, false);

        if (count($files) > 0) {
            foreach ($files as $file) {
                MolajoFactory::getDocument()->addScript($urlPath . '/js/' . $file);
            }
        }
    }
}