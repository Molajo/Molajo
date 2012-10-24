<?php
/**
 * @package    Molajo
 * @copyright  2012 Individual Molajo Contributors. All rights reserved.
 * @license    GNU GPL v 2, or later and MIT, see License folder
 */
namespace Molajo\Includer;

use Molajo\Helpers;
use Molajo\Service\Services;
use Molajo\Includer;

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
     * @return null
     * @since   1.0
     */
    public function __construct($name = null, $type = null)
    {
        Services::Registry()->set('Parameters', 'extension_catalog_type_id', CATALOG_TYPE_RESOURCE);

		$this->name = $name;
		$this->type = $type;

		Services::Registry()->createRegistry('Include');

		Services::Registry()->set('Parameters', 'includer_name', $this->name);
		Services::Registry()->set('Parameters', 'includer_type', $this->type);

        return;
    }


	/**
	 * process
	 *
	 * - Loads Metadata (only Theme Includer)
	 * - Loads Language files for Extension
	 * - Loads Assets for Extension
	 * - Activates Controller for Task
	 * - Returns Rendered Output to Parse for <include:type /> replacement
	 *
	 * @param   $attributes <include:type attr1=x attr2=y attr3=z ... />
	 *
	 * @return mixed
	 * @since   1.0
	 */
	public function process($attributes = array())
	{
		/** attributes from <include:type */
		$this->attributes = $attributes;

		$this->getAttributes();

		$this->getExtension();

		$this->loadLanguage();

		$this->loadPlugins();

		$rendered_output = $this->invokeMVC();

		/** only load media if there was rendered output */
		if ($rendered_output == ''
			&& Services::Registry()->get('Parameters', 'criteria_display_view_on_no_results') == 0
		) {
		} else {
			$this->loadMedia();
			$this->loadViewMedia();
		}

		return $rendered_output;
	}

    /**
     * getExtension - Used for non-primary Resource to set Parameter Values
     *
     * @return void
     * @since  1.0
     */
    protected function getExtension()
    {
        /** Include and Parameter Registries are already loaded for Primary Resource */
        if (Services::Registry()->get('Parameters', 'extension_primary') === true) {
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
            'Table',
            'ExtensionInstances'
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
     * @return bool
     * @since   1.0
     */
    protected function loadMedia()
    {
        /** Primary Category */
        $this->loadMediaPlus('/category' . Services::Registry()->get('Parameters', 'catalog_category_id'),
            Services::Registry()->get('Parameters', 'asset_priority_primary_category', 700));

        /** Menu Item */
        $this->loadMediaPlus('/menuitem' . Services::Registry()->get('Parameters', 'menu_item_id'),
            Services::Registry()->get('Parameters', 'asset_priority_menuitem', 800));

        /** Source */
        $this->loadMediaPlus('/source/' . Services::Registry()->get('Parameters', 'extension_title')
                . Services::Registry()->get('Parameters', 'criteria_source_id'),
            Services::Registry()->get('Parameters', 'asset_priority_item', 900));

        /** Resource */
        $this->loadMediaPlus('/resource/' . Services::Registry()->get('Parameters', 'extension_title'),
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
