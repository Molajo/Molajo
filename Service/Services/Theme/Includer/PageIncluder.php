<?php
/**
 * @package    Molajo
 * @copyright  2012 Amy Stephen. All rights reserved.
 * @license    GNU GPL v 2, or later and MIT, see License folder
 */
namespace Molajo\Service\Services\Page\Includer;

use Molajo\Helpers;
use Molajo\Service\Services;
use Molajo\Service\Services\Page\Includer;
use Molajo\MVC\Controller\DisplayController;

defined('MOLAJO') or die;

/**
 * Page
 *
 * @package     Molajo
 * @subpackage  Includer
 * @since       1.0
 */
class PageIncluder extends Includer
{
    /**
     * @return  null
     * @since   1.0
     */
    public function __construct($include_name = null, $include_type = null)
    {
        $this->name = $name;
        $this->type = $type;

        Services::Registry()->createRegistry('Include');

        Services::Registry()->set('include', 'includer_name', $this->name);
        Services::Registry()->set('include', 'includer_type', $this->type);

        return $this;
    }

    /**
     * For Item, List, or Menu Item, retrieve Parameter data needed to generate page.
     *
     * Once parameters are available, page cache is returned, if avaiable.
     *
     * @return   mixed | false or string (page cache)
     * @since    1.0
     * @throws   /Exception
     */
    public function setPrimaryData()
    {
        $catalog_id = Services::Registry()->get('include', 'catalog_id');
        $id = Services::Registry()->get('include', 'catalog_source_id');
        $catalog_extension_instance_id = Services::Registry()->get('include', 'catalog_extension_instance_id');
        $catalog_page_type = Services::Registry()->get('include', 'catalog_page_type');
        $model_type = ucfirst(strtolower(Services::Registry()->get('include', 'catalog_model_type')));
        $model_name = ucfirst(strtolower(Services::Registry()->get('include', 'catalog_model_name')));

        if (strtolower(trim($catalog_page_type)) == QUERY_OBJECT_LIST) {
            $response = $this->contentHelper->getRouteList($id, $model_type, $model_name);

        } elseif (strtolower(trim($catalog_page_type)) == QUERY_OBJECT_ITEM) {
            $response = $this->contentHelper->getRouteItem($id, $model_type, $model_name);

        } else {
            $response = $this->contentHelper->getRouteMenuitem();
        }

        if ($response === false) {
            throw new \Exception('Page Parameter Data for Catalog ID ' . $catalog_id);
        }

        Services::Registry()->set('include', 'extension_catalog_type_id', CATALOG_TYPE_RESOURCE);

        $this->getPageCache();

        return $this->rendered_output;
    }

    /**
     * See if page exists in Page Cache
     *
     * @return  mixed | false or string
     * @since   1.0
     */
    protected function getPageCache()
    {
        if (file_exists(Services::Registry()->get('include', 'Page_path_include'))) {
        } else {
            Services::Error()->set(500, 'Page Not found');
            throw new \Exception('Page not found '
                . Services::Registry()->get('include', 'Page_path_include'));
        }

        $parameters = Services::Registry()->getArray('include');

        $this->rendered_output = Services::Cache()->get(PAGE_LITERAL, implode('', $parameters));

        return;
    }

    /**
     * Render and return output
     *
     * @param   $attributes
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
     * Load Plugins Overrides from the Page and/or Page View folders
     *
     * @return  void
     * @since   1.0
     */
    protected function loadPlugins()
    {
        Services::Event()->registerPlugins(
            Services::Registry()->get('include', 'Page_path'),
            Services::Registry()->get('include', 'Page_namespace')
        );
        return;
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
     * The Page Includer renders the Page include file and feeds in the Page Name Value
     *  The rendered output from that process provides the initial data to be parsed for Include statements
     */
    protected function renderOutput()
    {
        $controller = new DisplayController();
        $controller->set('include', Services::Registry()->getArray('include'));

        $this->rendered_output = $controller->execute();

        Services::Registry()->delete('include');
        Services::Registry()->createRegistry('include');
        Services::Registry()->loadArray('include', $controller->get('include'));
        Services::Registry()->sort('include');

        $this->loadMedia();

        $this->loadViewMedia();

        return;
    }

    /**
     * loadMedia
     *
     * Loads Media Files for Site, Application, User, and Page
     *
     * @return  bool
     * @since   1.0
     */
    protected function loadMedia()
    {
        $this->loadMediaPlus('',
            Services::Registry()->get('include', 'asset_priority_site', 100));

        $this->loadMediaPlus('/application' . APPLICATION,
            Services::Registry()->get('include', 'asset_priority_application', 200));

        $this->loadMediaPlus('/user' . Services::Registry()->get(USER_LITERAL, 'id'),
            Services::Registry()->get('include', 'asset_priority_user', 300));

        $this->loadMediaPlus('/category' . Services::Registry()->get('include', 'catalog_category_id'),
            Services::Registry()->get('include', 'asset_priority_primary_category', 700));

        $this->loadMediaPlus('/menuitem' . Services::Registry()->get('include', 'menu_item_id'),
            Services::Registry()->get('include', 'asset_priority_menuitem', 800));

        $this->loadMediaPlus('/source/' . Services::Registry()->get('include', 'extension_title')
                . Services::Registry()->get('include', 'criteria_source_id'),
            Services::Registry()->get('include', 'asset_priority_item', 900));

        $this->loadMediaPlus('/resource/' . Services::Registry()->get('include', 'extension_title'),
            Services::Registry()->get('include', 'asset_priority_extension', 900));

        $priority = Services::Registry()->get('include', 'asset_priority_Page', 600);
        $file_path = Services::Registry()->get('include', 'Page_path');
        $url_path = Services::Registry()->get('include', 'Page_path_url');

        Services::Asset()->addCssFolder($file_path, $url_path, $priority);
        Services::Asset()->addJsFolder($file_path, $url_path, $priority, 0);
        Services::Asset()->addJsFolder($file_path, $url_path, $priority, 1);

        $priority = Services::Registry()->get('include', 'asset_priority_Page', 600);
        $file_path = Services::Registry()->get('include', 'page_view_path');
        $url_path = Services::Registry()->get('include', 'page_view_path_url');

        Services::Asset()->addCssFolder($file_path, $url_path, $priority);
        Services::Asset()->addJsFolder($file_path, $url_path, $priority, 0);
        Services::Asset()->addJsFolder($file_path, $url_path, $priority, 1);

        Services::Asset()->addLink(
            $url = Services::Registry()->get('include', 'Page_favicon'),
            $relation = 'shortcut icon',
            $relation_type = 'image/x-icon',
            $attributes = array()
        );

        $this->loadMediaPlus('', Services::Registry()->get('include', 'asset_priority_site', 100));

        return true;
    }

    /**
     * loadMediaPlus
     *
     * Loads Media Files for Site, Application, User, and Page
     *
     * @return bool
     * @since   1.0
     */
    protected function loadMediaPlus($plus = '', $priority = 500)
    {
        /** Page */
        $file_path = Services::Registry()->get('include', 'Page_path');
        $url_path = Services::Registry()->get('include', 'Page_path_url');
        $css = Services::Asset()->addCssFolder($file_path, $url_path, $priority);
        $js = Services::Asset()->addJsFolder($file_path, $url_path, $priority, 0);
        $defer = Services::Asset()->addJsFolder($file_path, $url_path, $priority, 1);
        if ($css === true || $js === true || $defer === true) {
            return true;
        }

        /** Site Specific: Application */
        $file_path = SITE_MEDIA_FOLDER . '/' . APPLICATION . $plus;
        $url_path = SITE_MEDIA_URL . '/' . APPLICATION . $plus;
        $css = Services::Asset()->addCssFolder($file_path, $url_path, $priority);
        $js = Services::Asset()->addJsFolder($file_path, $url_path, $priority, 0);
        $defer = Services::Asset()->addJsFolder($file_path, $url_path, $priority, 1);
        if ($css === true || $js === true || $defer === true) {
            return true;
        }

        /** Site Specific: Site-wide */
        $file_path = SITE_MEDIA_FOLDER . $plus;
        $url_path = SITE_MEDIA_URL . $plus;
        $css = Services::Asset()->addCssFolder($file_path, $url_path, $priority);
        $js = Services::Asset()->addJsFolder($file_path, $url_path, $priority, false);
        $defer = Services::Asset()->addJsFolder($file_path, $url_path, $priority, 1);
        if ($css === true || $js === true || $defer === true) {
            return true;
        }

        /** All Sites: Application */
        $file_path = SITES_MEDIA_FOLDER . '/' . APPLICATION . $plus;
        $url_path = SITES_MEDIA_URL . '/' . APPLICATION . $plus;
        $css = Services::Asset()->addCssFolder($file_path, $url_path, $priority);
        $js = Services::Asset()->addJsFolder($file_path, $url_path, $priority, 0);
        $defer = Services::Asset()->addJsFolder($file_path, $url_path, $priority, 1);
        if ($css === true || $js === true || $defer === true) {
            return true;
        }

        /** All Sites: Site Wide */
        $file_path = SITES_MEDIA_FOLDER . $plus;
        $url_path = SITES_MEDIA_URL . $plus;
        $css = Services::Asset()->addCssFolder($file_path, $url_path, $priority);
        $js = Services::Asset()->addJsFolder($file_path, $url_path, $priority, 0);
        $defer = Services::Asset()->addJsFolder($file_path, $url_path, $priority, 1);
        if ($css === true || $js === true || $defer === true) {
            return true;
        }

        /** nothing was loaded */

        return true;
    }
}
