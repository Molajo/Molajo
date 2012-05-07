<?php
/**
 * @package   Molajo
 * @copyright 2012 Amy Stephen. All rights reserved.
 * @license   GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
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
	 * setRenderCriteria
	 *
	 * Initialize the request object for MVC values
	 *
	 * @return mixed
	 */
	protected function setRenderCriteria()
	{
		if ($this->type == 'request') {
		} else {
			parent::setRenderCriteria();
			return;
		}

		$this->parameters = Application::Request()->parameters;

		$this->task_request = Services::Registry()->initialise();

		/** extension */
		Services::Registry()->set('Parameters', 'extension_instance_id',
			(int)Services::Registry()->get('Request', 'extension_instance_id'));
		Services::Registry()->set('Parameters', 'extension_instance_name',
			Services::Registry()->get('Request', 'extension_instance_name'));
		Services::Registry()->set('Parameters', 'extension_catalog_type_id',
			(int)Services::Registry()->get('Request', 'extension_catalog_type_id'));
		Services::Registry()->set('Parameters', 'extension_catalog_id',
			(int)Services::Registry()->get('Request', 'extension_catalog_id'));
		Services::Registry()->set('Parameters', 'extension_view_group_id',
			(int)Services::Registry()->get('Request', 'extension_view_group_id'));
		Services::Registry()->set('Parameters', 'extension_custom_fields',
			Services::Registry()->get('Request', 'extension_custom_fields'));
		Services::Registry()->set('Parameters', 'extension_metadata',
			Services::Registry()->get('Request', 'extension_metadata'));
		Services::Registry()->set('Parameters', 'extension_parameters',
			Services::Registry()->get('Request', 'extension_parameters'));
		Services::Registry()->set('Parameters', 'extension_path',
			Services::Registry()->get('Request', 'extension_path'));
		Services::Registry()->set('Parameters', 'extension_type',
			Services::Registry()->get('Request', 'extension_type'));
		Services::Registry()->set('Parameters', 'source_catalog_type_id',
			Services::Registry()->get('Request', 'source_catalog_type_id'));

		Services::Registry()->set('Parameters', 'extension_primary', true);

		Services::Registry()->set('Parameters', 'extension_event_type',
			Services::Registry()->get('Request', 'extension_event_type'));

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
	 * import
	 *
	 * imports component folders and files
	 *
	 * @return  true
	 * @since   1.0
	 */
	protected function importClasses()
	{
		$load = new LoadHelper();

		$name = ucfirst(Services::Registry()->get('Parameters', 'extension_instance_name'));
		$name = str_replace(array('-', '_'), '', $name);
		$name = 'Molajo' . $name;

		/** Controllers */
		if (file_exists(Services::Registry()->get('Parameters', 'extension_path') . '/controller.php')) {
			$load->requireClassFile(
				Services::Registry()->get('Parameters', 'extension_path') . '/controller.php',
				$name . 'Controller'
			);
		}
		$files = Services::Filesystem()->folderFiles(Services::Registry()->get('Parameters', 'extension_path') . '/controllers', '\.php$', false, false);
		if ($files) {
			foreach ($files as $file) {
				$load->requireClassFile(
					Services::Registry()->get('Parameters', 'extension_path') . '/controllers/' . $file,
					$name . 'Controller' . ucfirst(substr($file, 0, strpos($file, '.')))
				);
			}
		}

		/** Helpers */
		$files = Services::Filesystem()->folderFiles(Services::Registry()->get('Parameters', 'extension_path') . '/helpers', '\.php$', false, false);
		if ($files) {
			foreach ($files as $file) {
				$load->requireClassFile(Services::Registry()->get('Parameters', 'extension_path') . '/helpers/' . $file,
					$name . ucfirst(substr($file, 0, strpos($file, '.')))
				);
			}
		}

		/** Models */
		$files = Services::Filesystem()->folderFiles(Services::Registry()->get('Parameters', 'extension_path') . '/models', '\.php$', false, false);
		if ($files) {
			foreach ($files as $file) {
				$load->requireClassFile(Services::Registry()->get('Parameters', 'extension_path') . '/models/' . $file,
					$name . 'Model' . ucfirst(substr($file, 0, strpos($file, '.')))
				);
			}
		}

		/** Tables */
		$files = Services::Filesystem()->folderFiles(Services::Registry()->get('Parameters', 'extension_path') . '/tables', '\.php$', false, false);
		if ($files) {
			foreach ($files as $file) {
				$load->requireClassFile(Services::Registry()->get('Parameters', 'extension_path') . '/tables/' . $file,
					$name . 'Table' . ucfirst(substr($file, 0, strpos($file, '.')))
				);
			}
		}

		/** Views */
		$folders = Services::Filesystem()->folderFolders(Services::Registry()->get('Parameters', 'extension_path') . '/views', false, false);
		if ($folders) {
			foreach ($folders as $folder) {
				$files = Services::Filesystem()->folderFiles(Services::Registry()->get('Parameters', 'extension_path') . '/View/' . $folder, false, false);
				if ($files) {
					foreach ($files as $file) {
						$load->requireClassFile(Services::Registry()->get('Parameters', 'extension_path') . '/View/' . $folder . '/' . $file,
							$name . 'View' . ucfirst($folder));
					}
				}
			}
		}
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
