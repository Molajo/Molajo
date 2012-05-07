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
	 * setRenderCriteria - Used for Primary Component to set Parameter Values for the identified Route
	 *
	 * @return null
	 * @since  1.0
	 */
	protected function setRenderCriteria()
	{
		if ($this->type == 'request') {
		} else {
			parent::setRenderCriteria();
			return;
		}

		/** extension */
		Services::Registry()->set('Parameters', 'extension_instance_id',
			(int)Services::Registry()->get('Extension', 'id'));
		Services::Registry()->set('Parameters', 'extension_instance_title',
			Services::Registry()->get('Extension', 'title'));

		Services::Registry()->set('Parameters', 'extension_catalog_id',
			(int)Services::Registry()->get('Extension', 'catalog_id'));
		Services::Registry()->set('Parameters', 'extension_catalog_type_id',
			(int)Services::Registry()->get('Extension', 'catalog_type_id'));
		Services::Registry()->set('Parameters', 'extension_catalog_type_title',
			(int)Services::Registry()->get('Extension', 'catalog_type_title'));

		Services::Registry()->set('Parameters', 'extension_view_group_id',
			(int)Services::Registry()->get('Extension', 'view_group_id'));

		Services::Registry()->set('Parameters', 'extension_path',
			(int)Services::Registry()->get('Extension', 'path'));
		Services::Registry()->set('Parameters', 'extension_path_url',
			(int)Services::Registry()->get('Extension', 'path_url'));

		Services::Registry()->set('Parameters', 'extension_primary', true);

		parent::setRenderCriteria();


		/** view */
		Services::Registry()->set('Parameters', 'template_view_id',
			(int)Services::Registry()->get('Request', 'template_view_id'));
		Services::Registry()->set('Parameters', 'template_view_name',
			Services::Registry()->get('Request', 'template_view_name'));
		Services::Registry()->set('Parameters', 'template_view_css_id',
			Services::Registry()->get('Request', 'template_view_css_id'));
		Services::Registry()->set('Parameters', 'template_view_css_class',
			Services::Registry()->get('Request', 'template_view_css_class'));
		Services::Registry()->set('Parameters', 'template_view_catalog_type_id',
			Services::Registry()->get('Request', 'template_view_catalog_type_id'));
		Services::Registry()->set('Parameters', 'template_view_catalog_id',
			(int)Services::Registry()->get('Request', 'template_view_catalog_id'));
		Services::Registry()->set('Parameters', 'template_view_path',
			Services::Registry()->get('Request', 'template_view_path'));
		Services::Registry()->set('Parameters', 'template_view_path_url',
			Services::Registry()->get('Request', 'template_view_path_url'));

		/** wrap */
		Services::Registry()->set('Parameters', 'wrap_view_id',
			(int)Services::Registry()->get('Request', 'wrap_view_id'));
		Services::Registry()->set('Parameters', 'wrap_view_name',
			Services::Registry()->get('Request', 'wrap_view_name'));
		Services::Registry()->set('Parameters', 'wrap_view_css_id',
			Services::Registry()->get('Request', 'wrap_view_css_id'));
		Services::Registry()->set('Parameters', 'wrap_view_css_class',
			Services::Registry()->get('Request', 'wrap_view_css_class'));
		Services::Registry()->set('Parameters', 'wrap_view_catalog_type_id',
			Services::Registry()->get('Request', 'wrap_view_catalog_type_id'));
		Services::Registry()->set('Parameters', 'wrap_view_catalog_id',
			(int)Services::Registry()->get('Request', 'wrap_view_catalog_id'));
		Services::Registry()->set('Parameters', 'wrap_view_path',
			Services::Registry()->get('Request', 'wrap_view_path'));
		Services::Registry()->set('Parameters', 'wrap_view_path_url',
			Services::Registry()->get('Request', 'wrap_view_path_url'));

		/** mvc parameters */
		Services::Registry()->set('Parameters', 'controller',
			Services::Registry()->get('Request', 'mvc_controller'));
		Services::Registry()->set('Parameters', 'task',
			Services::Registry()->get('Request', 'action'));
		Services::Registry()->set('Parameters', 'model',
			Services::Registry()->get('Request', 'mvc_model'));
		Services::Registry()->set('Parameters', 'table',
			Services::Registry()->get('Request', 'source_table'));
		Services::Registry()->set('Parameters', 'id',
			(int)Services::Registry()->get('Request', 'mvc_id'));
		Services::Registry()->set('Parameters', 'category_id',
			(int)Services::Registry()->get('Request', 'mvc_category_id'));
		Services::Registry()->set('Parameters', 'suppress_no_results',
			(bool)Services::Registry()->get('Request', 'mvc_suppress_no_results'));

		return;
	}

	/**
	 * getExtension
	 *
	 * Retrieve extension information using either the ID or the name
	 *
	 * @return  bool
	 * @since   1.0
	 */
	protected function getExtension()
	{
		Services::Registry()->set('Parameters',
			'extension_catalog_type_id',
			CATALOG_TYPE_EXTENSION_COMPONENT
		);

		$results = parent::getExtension();
		if ($results === false) {
			return false;
		}

		Services::Registry()->set('Parameters',
			'extension_path',
			ComponentHelper::getPath(
				strtolower(Services::Registry()->get('Parameters', 'extension_instance_name')))
		);

		Services::Registry()->set('Parameters', 'extension_type', 'component');

		return true;
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
		/**  Primary Category */
		$this->loadMediaPlus('/category' . Services::Registry()->get('Parameters', 'category_id'),
			Services::Registry()->get('Configuration', 'media_priority_primary_category', 700));

		/** Menu Item */
		$this->loadMediaPlus('/menuitem' . Services::Registry()->get('Parameters', 'menu_item_id'),
			Services::Registry()->get('Configuration', 'media_priority_menu_item', 800));

		/** Source */
		$this->loadMediaPlus('/source' . Services::Registry()->get('Parameters', 'id'),
			Services::Registry()->get('Configuration', 'media_priority_source_data', 900));

		/** Component */
		$this->loadMediaPlus('/component' . Services::Registry()->get('Parameters', 'extension_instance_name'),
			Services::Registry()->get('Configuration', 'media_priority_source_data', 900));

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
		$file_path = EXTENSIONS_THEMES . '/' . Services::Registry()->get('Parameters', 'theme_name');
		$url_path = EXTENSIONS_THEMES_URL . '/' . Services::Registry()->get('Parameters', 'theme_name');
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
