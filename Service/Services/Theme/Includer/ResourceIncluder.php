<?php
/**
 * @package    Molajo
 * @copyright  2012 Amy Stephen. All rights reserved.
 * @license    GNU GPL v 2, or later and MIT, see License folder
 */
namespace Molajo\Service\Services\Theme\Includer;

use Molajo\Helpers;
use Molajo\Service\Services;
use Molajo\Service\Services\Theme\Includer;

defined('MOLAJO') or die;

/**
 * Resource
 *
 * @package     Molajo
 * @subpackage  Includer
 * @since       1.0
 */
class ResourceIncluder extends Includer
{
    /**
     * @return  null
     * @since   1.0
     */
    public function __construct($name = null, $type = null)
    {
        $this->name = $name;
        $this->type = $type;

        Services::Registry()->createRegistry('Include');

		Services::Registry()->set(PARAMETERS_LITERAL, 'includer_name', $this->name);
		Services::Registry()->set(PARAMETERS_LITERAL, 'includer_type', $this->type);

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
    public function getPrimaryData()
    {
        $catalog_id = Services::Registry()->get('parameters', 'catalog_id');
        $id = Services::Registry()->get('parameters', 'catalog_source_id');
        $catalog_extension_instance_id = Services::Registry()->get('parameters', 'catalog_extension_instance_id');
        $catalog_page_type = Services::Registry()->get('parameters', 'catalog_page_type');
        $model_type = ucfirst(strtolower(Services::Registry()->get('parameters', 'catalog_model_type')));
        $model_name = ucfirst(strtolower(Services::Registry()->get('parameters', 'catalog_model_name')));

        if (strtolower(trim($catalog_page_type)) == QUERY_OBJECT_LIST) {
            $response = Helpers::Content()->getRouteList($id, $model_type, $model_name);

        } elseif (strtolower(trim($catalog_page_type)) == QUERY_OBJECT_ITEM) {
            $response = Helpers::Content()->getRouteItem($id, $model_type, $model_name);

        } else {
            $response = Helpers::Content()->getRouteMenuitem();
        }

        if ($response === false) {
            throw new \Exception('Resource Parameter Data for Catalog ID ' . $catalog_id);
        }

        Services::Registry()->set(PARAMETERS_LITERAL, 'extension_catalog_type_id', CATALOG_TYPE_RESOURCE);

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
        if (file_exists(Services::Registry()->get('parameters', 'theme_path_include'))) {
        } else {
            Services::Error()->set(500, 'Theme Not found');
            throw new \Exception('Theme not found '
                . Services::Registry()->get('parameters', 'theme_path_include'));
        }

        $parameters = Services::Registry()->getArray('parameters');

        $results = Services::Cache()->get(PAGE_LITERAL, implode('', $parameters));
        if ($results === false) {
            return;
        }

        $this->rendered_output = $results;
        return;
    }

    /**
     * getExtension - Used for non-primary Resource to set Parameter Values
     *
     * @return  void
     * @since   1.0
     */
    protected function getExtension()
    {
        Services::Registry()->set(PARAMETERS_LITERAL, 'extension_instance_id',
            Helpers::Extension()->getInstanceID(
                Services::Registry()->get('parameters', 'extension_catalog_type_id'),
                Services::Registry()->get('parameters', 'extension_title')
            )
        );

        $response = Helpers::Extension()->getExtension(
            Services::Registry()->get('parameters', 'extension_instance_id'), DATA_SOURCE_LITERAL, 'ExtensionInstances'
        );

        if ($response === false) {
            Services::Error()->set(500, 'Extension not found');
        }

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
        /** Primary Category */
        $this->loadMediaPlus('/category' . Services::Registry()->get('parameters', 'catalog_category_id'),
            Services::Registry()->get('parameters', 'asset_priority_primary_category', 700));

        /** Menu Item */
        $this->loadMediaPlus('/menuitem' . Services::Registry()->get('parameters', 'menu_item_id'),
            Services::Registry()->get('parameters', 'asset_priority_menuitem', 800));

        /** Source */
        $this->loadMediaPlus('/source/' . Services::Registry()->get('parameters', 'extension_title')
                . Services::Registry()->get('parameters', 'criteria_source_id'),
            Services::Registry()->get('parameters', 'asset_priority_item', 900));

        /** Resource */
        $this->loadMediaPlus('/resource/' . Services::Registry()->get('parameters', 'extension_title'),
            Services::Registry()->get('parameters', 'asset_priority_extension', 900));

        return true;
    }

    /**
     * loadMediaPlus
     *
     * Loads Media Files for Site, Application, User, and Theme
     *
     * @return bool
     * @since   1.0
     */
    protected function loadMediaPlus($plus = '', $priority = 500)
    {

        /** Theme */
        $file_path = Services::Registry()->get('parameters', 'theme_path');
        $url_path = Services::Registry()->get('parameters', 'theme_path_url');
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
