<?php
/**
 * @package    Molajo
 * @copyright  2012 Amy Stephen. All rights reserved.
 * @license    GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
namespace Molajo\Extension\Includer;

use Molajo\Extension\Helpers;
use Molajo\Service\Services;
use Molajo\Extension\Includer;

defined('MOLAJO') or die;

/**
 * Component
 *
 * @package     Molajo
 * @subpackage  Includer
 * @since       1.0
 */
class ComponentIncluder extends Includer
{
    /**
     * __construct
     *
     * Class constructor.
     *
     * @param string $name
     * @param string $type
     *
     * @return null
     * @since   1.0
     */
    public function __construct($name = null, $type = null)
    {
        Services::Registry()->set('Parameters', 'extension_catalog_type_id', CATALOG_TYPE_EXTENSION_COMPONENT);

        return parent::__construct($name, $type);
    }

    /**
     * getAttributes
     *
     * Use the view and/or wrap criteria ife specified on the <include statement
     *
     * @return null
     * @since   1.0
     */
    protected function getAttributes()
    {
        /** Include and Parameter Registries are already loaded for Primary Component */
        if (Services::Registry()->get('Parameters', 'extension_primary') == true) {
            return;
        } else {
            return parent::getAttribute();
        }
    }

    /**
     * getExtension - Used for non-primary Component to set Parameter Values
     *
     * @return void
     * @since  1.0
     */
    protected function getExtension()
    {
        /** Include and Parameter Registries are already loaded for Primary Component */
        if (Services::Registry()->get('Parameters', 'extension_primary') == true) {
            return;
        }

        Services::Registry()->set('Parameters', 'extension_instance_id',
            Helpers::Extension()->getInstanceID(
                Services::Registry()->get('Parameters', 'extension_catalog_type_id'),
                Services::Registry()->get('Parameters', 'extension_title')
            )
        );

        $response = Helpers::Extension()->getExtension(
            Services::Registry()->get('Parameters', 'extension_instance_id'),
            'ExtensionInstances',
            'Table'
        );
        if ($response === false) {
            Services::Error()->set(500, 'Extension not found');
        }

        return parent::__construct();
    }

    /**
     * setRenderCriteria
     *
     * Use the view and/or wrap criteria ife specified on the <include statement
     * Retrieve View and wrap criteria and path information
     *
     * @return bool
     * @since   1.0
     */
    public function setRenderCriteria()
    {
        /** Include and Parameter Registries are already loaded for Primary Component */
        if (Services::Registry()->get('Parameters', 'extension_primary') == true) {
            return;
        }

        Services::Registry()->merge('Configuration', 'Parameters', true);

        Helpers::Extension()->setTemplateWrapModel();

        Services::Registry()->delete('Parameters', 'item*');
        Services::Registry()->delete('Parameters', 'list*');
        Services::Registry()->delete('Parameters', 'form*');

        Services::Registry()->sort('Parameters');

        return;
    }

        /**
     * loadMedia
     *
     * Loads Media Files for Site, Application, User, and Theme
     *
     * @return bool
     * @since   1.0
     */
    protected function loadMedia()
    {
        /** Primary Category */
        $this->loadMediaPlus('/category' . Services::Registry()->get('Parameters', 'catalog_category_id'),
            Services::Registry()->get('Parameters', 'asset_priority_category', 700));

        /** Menu Item */
        $this->loadMediaPlus('/menuitem' . Services::Registry()->get('Parameters', 'menu_item_id'),
            Services::Registry()->get('Parameters', 'asset_priority_menu_item', 800));

        /** Source */
        $this->loadMediaPlus('/source/'  . Services::Registry()->get('Parameters', 'extension_title')
                . Services::Registry()->get('Parameters', 'content_id'),
            Services::Registry()->get('Parameters', 'asset_priority_source', 900));

        /** Component */
        $this->loadMediaPlus('/component/' . Services::Registry()->get('Parameters', 'extension_title'),
            Services::Registry()->get('Parameters', 'asset_priority_extension', 900));

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
        $file_path = Services::Registry()->get('Parameters', 'theme_path');
        $url_path = Services::Registry()->get('Parameters', 'theme_path_url');
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
