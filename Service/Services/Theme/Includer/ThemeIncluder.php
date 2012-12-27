<?php
/**
 * Theme Service Theme Includer
 *
 * @package      Niambie
 * @license      MIT
 * @copyright    2013 Amy Stephen. All rights reserved.
 */
namespace Molajo\Service\Services\Theme\Includer;

use Molajo\Service\Services\Theme\Includer;

defined('NIAMBIE') or die;

/**
 * The Theme Includer sets parameter values needed to render the Theme Index.php file, the results
 * of which are feed into the parsing rendered output for <include:type/> statements process.
 *
 * In addition, the Theme Includer loads media and Plugins for the Theme.
 *
 * @author       Amy Stephen
 * @license      MIT
 * @copyright    2013 Amy Stephen. All rights reserved.
 * @since        1.0
 */
class ThemeIncluder extends Includer
{
    /**
     * The Theme Includer establishes values needed to render the Theme Index.php file and
     *  Plugins overriding core and extension plugins are loaded, along with Theme Assets.
     *
     * @param   array $attributes
     *
     * @return  mixed|null|string
     *
     * @return  mixed
     * @since   1.0
     */
    public function process($attributes = array())
    {
        $this->loadPlugins();

        $this->renderOutput();

        return $this->rendered_output;
    }

    /**
     * Set Item, List, or Menu Item Parameter data needed to generate page.
     *
     * @return   void
     * @since    1.0
     * @throws   /Exception
     */
    public function setThemeParameters()
    {
        $catalog_id        = $this->get('catalog_id');
        $catalog_page_type = $this->get('catalog_page_type');

        $class         = $this->class_array['ContentHelper'];
        $contentHelper = new $class();
        $contentHelper->initialise($this->parameters);

        if (strtolower(trim($catalog_page_type)) == strtolower(QUERY_OBJECT_LIST)) {
            $response = $contentHelper->getRouteList();

        } elseif (strtolower(trim($catalog_page_type)) == strtolower(QUERY_OBJECT_ITEM)) {
            $response = $contentHelper->getRouteItem();

        } else {
            $response = $contentHelper->getRouteMenuitem();
        }

        if ($response === false) {
            throw new \Exception('Theme Service: Could not identify Primary Data for Catalog ID ' . $catalog_id);
        }

        $this->parameters     = $response[0];
        $this->property_array = $response[1];

        return;
    }

    /**
     * Load Plugins Overrides from the Theme and/or Page View folders
     *
     * @return  void
     * @since   1.0
     */
    protected function loadPlugins()
    {
        Services::Event()->registerPlugins(
            Services::Registry()->get('include', 'theme_path'),
            Services::Registry()->get('include', 'theme_namespace')
        );

        Services::Event()->registerPlugins(
            Services::Registry()->get('include', 'page_view_path'),
            Services::Registry()->get('include', 'page_view_namespace')
        );

        Services::Event()->registerPlugins(
            Services::Registry()->get('include', 'extension_path'),
            Services::Registry()->get('include', 'extension_namespace')
        );

        return;
    }

    /**
     * The Theme Includer renders the Theme include file and feeds in the Page Name Value
     *  The rendered output from that process provides the initial data to be parsed for Include statements
     */
    protected function renderOutput()
    {
        if (file_exists($this->get('theme_path_include'))) {
        } else {
            Services::Error()->set(500, 'Theme Not found');
            throw new \Exception('Theme not found ' . $this->get('theme_path_include'));
        }

        $controller = new DisplayController();
        $controller->set('include', Services::Registry()->getArray('include'));
        $this->set(
            $this->get('extension_catalog_type_id', '', 'parameters'),
            CATALOG_TYPE_RESOURCE,
            'parameters'
        );

        $this->rendered_output = $controller->execute();
        echo $this->rendered_output;
        $this->loadMedia();

        $this->loadViewMedia();

        return;
    }

    /**
     * loadMedia
     *
     * Loads Media Files for Site, Application, User, and Theme
     *
     * @return  bool
     * @since   1.0
     */
    protected function loadMedia()
    {
        $this->loadMediaPlus(
            '',
            Services::Registry()->get('include', 'asset_priority_site', 100)
        );

        $this->loadMediaPlus(
            '/application' . APPLICATION,
            Services::Registry()->get('include', 'asset_priority_application', 200)
        );

        $this->loadMediaPlus(
            '/user' . Services::Registry()->get(USER_LITERAL, 'id'),
            Services::Registry()->get('include', 'asset_priority_user', 300)
        );

        $this->loadMediaPlus(
            '/category' . Services::Registry()->get('include', 'catalog_category_id'),
            Services::Registry()->get('include', 'asset_priority_primary_category', 700)
        );

        $this->loadMediaPlus(
            '/menuitem' . Services::Registry()->get('include', 'menu_item_id'),
            Services::Registry()->get('include', 'asset_priority_menuitem', 800)
        );

        $this->loadMediaPlus(
            '/source/' . Services::Registry()->get('include', 'extension_title')
                . Services::Registry()->get('include', 'criteria_source_id'),
            Services::Registry()->get('include', 'asset_priority_item', 900)
        );

        $this->loadMediaPlus(
            '/resource/' . Services::Registry()->get('include', 'extension_title'),
            Services::Registry()->get('include', 'asset_priority_extension', 900)
        );

        $priority  = Services::Registry()->get('include', 'asset_priority_theme', 600);
        $file_path = Services::Registry()->get('include', 'theme_path');
        $url_path  = Services::Registry()->get('include', 'theme_path_url');

        $this->class_asset->addCssFolder($file_path, $url_path, $priority);
        $this->class_asset->addJsFolder($file_path, $url_path, $priority, 0);
        $this->class_asset->addJsFolder($file_path, $url_path, $priority, 1);

        $priority  = Services::Registry()->get('include', 'asset_priority_theme', 600);
        $file_path = Services::Registry()->get('include', 'page_view_path');
        $url_path  = Services::Registry()->get('include', 'page_view_path_url');

        $this->class_asset->addCssFolder($file_path, $url_path, $priority);
        $this->class_asset->addJsFolder($file_path, $url_path, $priority, 0);
        $this->class_asset->addJsFolder($file_path, $url_path, $priority, 1);

        $this->class_asset->addLink(
            $url = Services::Registry()->get('include', 'theme_favicon'),
            $relation = 'shortcut icon',
            $relation_type = 'image/x-icon',
            $attributes = array()
        );

        $this->loadMediaPlus('', Services::Registry()->get('include', 'asset_priority_site', 100));

        return true;
    }

    /**
     * Loads Media Files for Site, Application, User, and Theme
     *
     * @param   string  $plus
     * @param   int     $priority
     *
     * @return  bool
     * @since   1.0
     */
    protected function loadMediaPlus($plus = '', $priority = 500)
    {
        /** Theme */
        $file_path = Services::Registry()->get('include', 'theme_path');
        $url_path  = Services::Registry()->get('include', 'theme_path_url');
        $css       = $this->class_asset->addCssFolder($file_path, $url_path, $priority);
        $js        = $this->class_asset->addJsFolder($file_path, $url_path, $priority, 0);
        $defer     = $this->class_asset->addJsFolder($file_path, $url_path, $priority, 1);
        if ($css === true || $js === true || $defer === true) {
            return true;
        }

        /** Site Specific: Application */
        $file_path = SITE_MEDIA_FOLDER . '/' . APPLICATION . $plus;
        $url_path  = SITE_MEDIA_URL . '/' . APPLICATION . $plus;
        $css       = $this->class_asset->addCssFolder($file_path, $url_path, $priority);
        $js        = $this->class_asset->addJsFolder($file_path, $url_path, $priority, 0);
        $defer     = $this->class_asset->addJsFolder($file_path, $url_path, $priority, 1);
        if ($css === true || $js === true || $defer === true) {
            return true;
        }

        /** Site Specific: Site-wide */
        $file_path = SITE_MEDIA_FOLDER . $plus;
        $url_path  = SITE_MEDIA_URL . $plus;
        $css       = $this->class_asset->addCssFolder($file_path, $url_path, $priority);
        $js        = $this->class_asset->addJsFolder($file_path, $url_path, $priority, false);
        $defer     = $this->class_asset->addJsFolder($file_path, $url_path, $priority, 1);
        if ($css === true || $js === true || $defer === true) {
            return true;
        }

        /** All Sites: Application */
        $file_path = SITES_MEDIA_FOLDER . '/' . APPLICATION . $plus;
        $url_path  = SITES_MEDIA_URL . '/' . APPLICATION . $plus;
        $css       = $this->class_asset->addCssFolder($file_path, $url_path, $priority);
        $js        = $this->class_asset->addJsFolder($file_path, $url_path, $priority, 0);
        $defer     = $this->class_asset->addJsFolder($file_path, $url_path, $priority, 1);
        if ($css === true || $js === true || $defer === true) {
            return true;
        }

        /** All Sites: Site Wide */
        $file_path = SITES_MEDIA_FOLDER . $plus;
        $url_path  = SITES_MEDIA_URL . $plus;
        $css       = $this->class_asset->addCssFolder($file_path, $url_path, $priority);
        $js        = $this->class_asset->addJsFolder($file_path, $url_path, $priority, 0);
        $defer     = $this->class_asset->addJsFolder($file_path, $url_path, $priority, 1);
        if ($css === true || $js === true || $defer === true) {
            return true;
        }

        /** nothing was loaded */

        return true;
    }
}
