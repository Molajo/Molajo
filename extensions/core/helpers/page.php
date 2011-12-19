<?php
/**
 * @package     Molajo
 * @subpackage  Helper
 * @copyright   Copyright (C) 2012 Amy Stephen. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */
defined('MOLAJO') or die;

/**
 * Page Helper
 *
 * @package     Molajo
 * @subpackage  Page Helper
 * @since       1.0
 */
abstract class MolajoPageHelper
{
    /**
     * Get the template
     *
     * @return string The template name
     * @since 1.0
     */
    static public function getPage()
    {
        /** initialize */
        $menuItem = null;
        $id = 0;
        $condition = '';

        /** Menu Item Page */
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

        /** Override if Page ID sent in */
        if (JRequest::getVar('template', 0) > 0) {
            $id = (int)JRequest::getVar('template', 0);
        }

        /** Configuration default */
        if ((int)$id == 0) {
            $id = strtolower(MolajoFactory::getApplication()->getConfig->get('default_template_name'));
        }

        /** Retrieve Page from the DB */
        $pages = MolajoExtension::getExtensions(MOLAJO_ASSET_TYPE_EXTENSION_TEMPLATE, $id);

        foreach ($pages as $page) {
            $registry = new JRegistry;
            $registry->loadJSON($page->parameters);
            $page->parameters = $registry;

            if (file_exists(MOLAJO_EXTENSIONS_TEMPLATES . '/' . $page->title . '/' . 'index.php')) {
            } else {
                $page->title = 'default';
            }
        }

        return $pages;
    }

    /**
     * renderPage
     *
     * Render the Page - extract and process doc statements
     *
     * @return  object
     * @since  1.0
     */
    public static function renderPage()
    {
        /** Scope */
        $scope = MolajoFactory::getApplication()->scope;
        MolajoFactory::getApplication()->scope = 'Render Page';

        /** Page */
        $page = self::getPage();

        $parameters = array(
            'template' => $page[0]->title,
            'file' => 'index.php',
            'directory' => MOLAJO_EXTENSIONS_TEMPLATES,
            'parameters' => $page[0]->parameters
        );

        /** Media */

        /** Application-specific CSS and JS in => media/[application]/css[js]/XYZ.css[js] */
        $filePath = MOLAJO_SITE_FOLDER_PATH_MEDIA . '/' . MOLAJO_APPLICATION;
        $urlPath = JURI::root() . 'sites/' . MOLAJO_SITE . '/media/' . MOLAJO_APPLICATION;
        self::loadMediaCSS($filePath, $urlPath);
        self::loadMediaJS($filePath, $urlPath);

        /** Page-specific CSS and JS in => template/[template-name]/css[js]/XYZ.css[js] */
        $filePath = MOLAJO_EXTENSIONS_TEMPLATES . '/' . $page[0]->title;
        $urlPath = JURI::root() . 'extensions/templates/' . $page[0]->title;
        self::loadMediaCSS($filePath, $urlPath);
        self::loadMediaJS($filePath, $urlPath);

        /** Language */
        $lang = MolajoFactory::getLanguage();
        $lang->load($page[0]->title, MOLAJO_EXTENSIONS_TEMPLATES . '/' . $page[0]->title, $lang->getDefault(), false, false);

        /** Application  */
        $applicationClass = 'Molajo' . ucfirst(MOLAJO_APPLICATION) . 'Application';
        $app = new $applicationClass ();

        /** Parse */
        MolajoFactory::getDocument()->parse($parameters);

        /** Before Event */
        MolajoFactory::getApplication()->triggerEvent('onBeforeRender');

        /** Render */
        $body = MolajoFactory::getDocument()->render(false, $parameters);
        MolajoFactory::getApplication()->setBody($body);

        /** After Event */
        MolajoFactory::getApplication()->triggerEvent('onAfterRender');

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