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
	 * @param  string $name
	 * @param  string $type
	 *
	 * @return  null
	 * @since   1.0
	 */
	public function __construct($name = null, $type = null)
	{
		Services::Registry()->set('Include', 'extension_catalog_type_id', CATALOG_TYPE_EXTENSION_COMPONENT);
		return parent::__construct($name, $type);
	}

	/**
	 * getAttributes
	 *
	 * Use the view and/or wrap criteria ife specified on the <include statement
	 *
	 * @return  null
	 * @since   1.0
	 */
	protected function getAttributes()
	{
		/** Include and Parameter Registries are already loaded for Primary Component */
		if (Services::Registry()->get('Include', 'extension_primary') == true) {
			return;
		} else {
			return parent::getAttribute();
		}
	}

	/**
	 * getExtension - Used for non-primary Component to set Parameter Values
	 *
	 * @return null
	 * @since  1.0
	 */
	public function getExtension($extension_id = null)
	{
		/** Include and Parameter Registries are already loaded for Primary Component */
		if (Services::Registry()->get('Include', 'extension_primary') == true) {
			return;
		}

		/** Retrieve Component ID, then Extension, populate Include and Parameters */
		$extension_id = Helpers::Component()->get($this->name);
		return parent::getExtension($extension_id);
	}

	/**
	 * setRenderCriteria
	 *
	 * Use the view and/or wrap criteria ife specified on the <include statement
	 * Retrieve View and wrap criteria and path information
	 *
	 * @return  bool
	 * @since   1.0
	 */
	public function setRenderCriteria()
	{
		/** Include and Parameter Registries are already loaded for Primary Component */
		if (Services::Registry()->get('Include', 'extension_primary') == true) {
			return;
		}
		return parent::setRenderCriteria();
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
		$this->loadMediaPlus('/category' . Services::Registry()->get('Include', 'catalog_category_id'),
			Services::Registry()->get('Parameters', 'criteria_asset_priority_category', 700));

		/** Menu Item */
		$this->loadMediaPlus('/menuitem' . Services::Registry()->get('Include', 'menu_item_id'),
			Services::Registry()->get('Parameters', 'criteria_asset_priority_menu_item', 800));

		/** Source */
		$this->loadMediaPlus('/source/'  . Services::Registry()->get('Include', 'extension_title')
				. Services::Registry()->get('Include', 'content_id'),
			Services::Registry()->get('Parameters', 'criteria_asset_priority_source', 900));

		/** Component */
		$this->loadMediaPlus('/component/' . Services::Registry()->get('Include', 'extension_title'),
			Services::Registry()->get('Parameters', 'criteria_asset_priority_extension', 900));

		return true;
	}

	/**
	 * loadMediaPlus
	 *
	 * Loads Media Files for Site, Application, User, and Theme
	 *
	 * @return  bool
	 * @since   1.0
	 */
	protected function loadMediaPlus($plus = '', $priority = 500)
	{
		/** Theme */
		$file_path = EXTENSIONS_THEMES . '/' . Services::Registry()->get('Include', 'theme_title');
		$url_path = EXTENSIONS_THEMES_URL . '/' . Services::Registry()->get('Include', 'theme_title');
		$css = Services::Document()->add_css_folder($file_path, $url_path, $priority);
		$js = Services::Document()->add_js_folder($file_path, $url_path, $priority, 0);
		$defer = Services::Document()->add_js_folder($file_path, $url_path, $priority, 1);
		if ($css === true || $js === true || $defer === true) {
			return true;
		}

		/** Site Specific: Application */
		$file_path = SITE_MEDIA_FOLDER . '/' . APPLICATION . $plus;
		$url_path = SITE_MEDIA_URL . '/' . APPLICATION . $plus;
		$css = Services::Document()->add_css_folder($file_path, $url_path, $priority);
		$js = Services::Document()->add_js_folder($file_path, $url_path, $priority, 0);
		$defer = Services::Document()->add_js_folder($file_path, $url_path, $priority, 1);
		if ($css === true || $js === true || $defer === true) {
			return true;
		}

		/** Site Specific: Site-wide */
		$file_path = SITE_MEDIA_FOLDER . $plus;
		$url_path = SITE_MEDIA_URL . $plus;
		$css = Services::Document()->add_css_folder($file_path, $url_path, $priority);
		$js = Services::Document()->add_js_folder($file_path, $url_path, $priority, false);
		$defer = Services::Document()->add_js_folder($file_path, $url_path, $priority, 1);
		if ($css === true || $js === true || $defer === true) {
			return true;
		}

		/** All Sites: Application */
		$file_path = SITES_MEDIA_FOLDER . '/' . APPLICATION . $plus;
		$url_path = SITES_MEDIA_URL . '/' . APPLICATION . $plus;
		$css = Services::Document()->add_css_folder($file_path, $url_path, $priority);
		$js = Services::Document()->add_js_folder($file_path, $url_path, $priority, 0);
		$defer = Services::Document()->add_js_folder($file_path, $url_path, $priority, 1);
		if ($css === true || $js === true || $defer === true) {
			return true;
		}

		/** All Sites: Site Wide */
		$file_path = SITES_MEDIA_FOLDER . $plus;
		$url_path = SITES_MEDIA_URL . $plus;
		$css = Services::Document()->add_css_folder($file_path, $url_path, $priority);
		$js = Services::Document()->add_js_folder($file_path, $url_path, $priority, 0);
		$defer = Services::Document()->add_js_folder($file_path, $url_path, $priority, 1);
		if ($css === true || $js === true || $defer === true) {
			return true;
		}

		/** nothing was loaded */
		return true;
	}
}
