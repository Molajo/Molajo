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
 * Theme
 *
 * @package     Molajo
 * @subpackage  Includer
 * @since       1.0
 */
Class ThemeIncluder extends Includer
{

	/**
	 * __construct
	 *
	 * Class constructor.
	 *
	 * @param  string $name
	 * @param  string $type
	 * @param  array  $items (used for event processing includes, only)
	 *
	 * @return  null
	 * @since   1.0
	 */
	public function __construct($name = null, $type = null, $items = null)
	{
		Services::Registry()->set('Parameters', 'extension_catalog_type_id', CATALOG_TYPE_EXTENSION_THEME);
		$this->name = $name;
		$this->type = $type;

		return $this;
	}

	/**
	 * render
	 *
	 * Establishes language files and media for theme
	 *
	 * @param   $attributes <include:type attr1=x attr2=y attr3=z ... />
	 *
	 * @return  mixed
	 * @since   1.0
	 */
	public function process($attributes = array())
	{

		$this->loadMetadata();

		$this->loadLanguage();

		$this->loadMedia();

		return;
	}

	/**
	 * loadMetadata
	 *
	 * Loads Metadata values into Services::Document Metadata array
	 *
	 * @return  null
	 * @since   1.0
	 */
	protected function loadMetadata()
	{
		// todo: trigger for metadata

		Services::Document()->set_metadata('title', '');
		Services::Document()->set_metadata('description', '');
		Services::Document()->set_metadata('keywords', '');
		Services::Document()->set_metadata('robots', '');
		Services::Document()->set_metadata('author', '');
		Services::Document()->set_metadata('content_rights', '');

		if (Services::Registry()->get('Request', 'status_error') == true) {
			Services::Document()->set_metadata('title',
				Services::Language()->translate('ERROR_FOUND'));
			return;
		}

		/** Last Modified */
		if (Services::Registry()->get('Content', 'source_last_modified', '') == '') {
			Services::Document()->set_last_modified(
				Services::Registry()->get('Menuitem', 'source_last_modified'));
		} else {
			Services::Document()->set_last_modified(
				Services::Registry()->get('Content', 'source_last_modified'));
		}

		/** Metadata */
		if (Services::Registry()->get('ContentMetadata', 'metadata_title', '') == '') {
		} else {
			return $this->setMetadata('ContentMetadata');
		}

		if (Services::Registry()->get('MenuItemMetadata', 'metadata_title', '') == '') {
		} else {
			return $this->setMetadata('MenuItemMetadata');
		}

		if (Services::Registry()->get('CategoryMetadata', 'metadata_title', '') == '') {
		} else {
			return $this->setMetadata('CategoryMetadata');
		}

		if (Services::Registry()->get('ExtensionMetadata', 'metadata_title', '') == '') {
		} else {
			return $this->setMetadata('ExtensionMetadata');
		}

		if (Services::Registry()->get('ApplicationMetadata', 'metadata_title', '') == '') {
		} else {
			return $this->setMetadata('ApplicationMetadata');
		}

		return $this->setMetadata('SiteMetadata');
	}

	/**
	 * setMetadata for specific Namespace
	 *
	 * @return  null
	 * @since   1.0
	 */
	protected function setMetadata($namespace)
	{
		$metadata = Services::Registry()->get($namespace);

		if (count($metadata) > 0) {
			foreach ($metadata as $key => $value) {
				Services::Document()->set_metadata(substr($key, 10, strlen($key) - 10), $value);
			}
		}
	}

	/**
	 * loadLanguage
	 *
	 * Loads Language Files for extension
	 *
	 * @return  null
	 * @since   1.0
	 */
	protected function loadLanguage()
	{
		/** Theme */
		Helpers::Extension()->loadLanguage(Services::Registry()->get('Theme', 'path'));

		/** Page View */
		Helpers::Extension()->loadLanguage(Services::Registry()->get('PageView', 'path'));
	}

	/**
	 * loadMedia
	 *
	 * Loads Media Files for Site, Application, User, and Theme
	 *
	 * @return  boolean  True, if the file has successfully loaded.
	 * @since   1.0
	 */
	protected function loadMedia()
	{
		/**  Site */
		$this->loadMediaPlus('',
			Services::Registry()->get('Configuration', 'media_priority_site', 100));

		/** Application */
		$this->loadMediaPlus('/application' . APPLICATION,
			Services::Registry()->get('Configuration', 'media_priority_application', 200));

		/** User */
		$this->loadMediaPlus('/user' . Services::Registry()->get('User', 'id'),
			Services::Registry()->get('Configuration', 'media_priority_user', 300));

		/** Load custom Theme Helper Media, if exists */
		$helperClass = 'Molajo\\Extension\\Theme\\' . 'Theme'
			. ucfirst(Services::Registry()->get('Theme', 'title'))
			. 'Helper';

		if (\class_exists($helperClass)) {
			$load = new $helperClass();
			if (\method_exists($load, 'loadMedia')) {
				$load->loadMedia();
			}
		}

		/** Theme */
		$priority = Services::Registry()->get('Configuration', 'media_priority_theme', 600);
		$file_path = Services::Registry()->get('Theme', 'path');
		$url_path = Services::Registry()->get('Theme', 'path_url');

		$css = Services::Document()->add_css_folder($file_path, $url_path, $priority);
		$js = Services::Document()->add_js_folder($file_path, $url_path, $priority, 0);
		$js = Services::Document()->add_js_folder($file_path, $url_path, $priority, 1);

		/** Page */
		$priority = Services::Registry()->get('Configuration', 'media_priority_theme', 600);
		$file_path = Services::Registry()->get('PageView', 'path');
		$url_path = Services::Registry()->get('PageView', 'path_url');

		$css = Services::Document()->add_css_folder($file_path, $url_path, $priority);
		$js = Services::Document()->add_js_folder($file_path, $url_path, $priority, 0);
		$js = Services::Document()->add_js_folder($file_path, $url_path, $priority, 1);

		/** Catalog ID specific */
		$this->loadMediaPlus('', Services::Registry()->get('Configuration', 'media_priority_site', 100));

		return;
	}

	/**
	 * loadMediaPlus
	 *
	 * Loads Media Files for Site, Application, User, and Theme
	 *
	 * @return  boolean  True, if the file has successfully loaded.
	 * @since   1.0
	 */
	protected function loadMediaPlus($plus = '', $priority = 500)
	{
		/** Site Specific: Application */
		$file_path = SITE_MEDIA_FOLDER . '/' . $plus;
		$url_path = SITE_MEDIA_URL . '/' . $plus;

		$css = Services::Document()->add_css_folder($file_path, $url_path, $priority);
		$js = Services::Document()->add_js_folder($file_path, $url_path, $priority, 0);
		$defer = Services::Document()->add_js_folder($file_path, $url_path, $priority, 1);

		/** Site Specific: Site-wide */
		$file_path = SITE_MEDIA_FOLDER . $plus;
		$url_path = SITE_MEDIA_URL . $plus;

		$css = Services::Document()->add_css_folder($file_path, $url_path, $priority);
		$js = Services::Document()->add_js_folder($file_path, $url_path, $priority, false);
		$defer = Services::Document()->add_js_folder($file_path, $url_path, $priority, 1);

		/** All Sites: Application */
		$file_path = SITES_MEDIA_FOLDER . '/' . APPLICATION . $plus;
		$url_path = SITES_MEDIA_URL . '/' . APPLICATION . $plus;

		$css = Services::Document()->add_css_folder($file_path, $url_path, $priority);
		$js = Services::Document()->add_js_folder($file_path, $url_path, $priority, 0);
		$defer = Services::Document()->add_js_folder($file_path, $url_path, $priority, 1);

		/** All Sites: Site Wide */
		$file_path = SITES_MEDIA_FOLDER . $plus;
		$url_path = SITES_MEDIA_URL . $plus;

		$css = Services::Document()->add_css_folder($file_path, $url_path, $priority);
		$js = Services::Document()->add_js_folder($file_path, $url_path, $priority, 0);
		$defer = Services::Document()->add_js_folder($file_path, $url_path, $priority, 1);

		return;
	}
}
