<?php
/**
 * @package    Molajo
 * @copyright  2012 Amy Stephen. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */
namespace Molajo\Extension\Helper;

use Molajo\Service\Services;
use Molajo\Extension\Helpers;

defined('MOLAJO') or die;

/**
 * Template View Helper
 *
 * @package       Molajo
 * @subpackage    Helper
 * @since         1.0
 */
Class TemplateViewHelper
{
	/**
	 * Static instance
	 *
	 * @var    object
	 * @since  1.0
	 */
	protected static $instance;

	/**
	 * getInstance
	 *
	 * @static
	 * @return bool|object
	 * @since  1.0
	 */
	public static function getInstance()
	{
		if (empty(self::$instance)) {
			self::$instance = new TemplateViewHelper();
		}

		return self::$instance;
	}

	/**
	 * get
	 *
	 * Get requested theme data
	 *
	 * @return  array
	 * @since   1.0
	 */
	public function get($template_view_id = 0)
	{
		if ($template_view_id == 0) {
			$template_view_id = $this->DefaultTemplateView();
		}

		$row = Helpers::Extension()->get($template_view_id);

		/** 500: Template not found */
		if (count($row) == 0) {
			/** Try Default Template */
			$template_view_id = Helpers::Extension()->getInstanceID(CATALOG_TYPE_EXTENSION_TEMPLATE, 'Default');
			$row = Helpers::Extension()->get($template_view_id);
			if (count($row) == 0) {
				Services::Error()->set(500, 'Template not found');
				return false;
			}
		}

		Services::Registry()->set('TemplateView', 'id', (int)$row->id);
		Services::Registry()->set('TemplateView', 'title', $row->title);
		Services::Registry()->set('TemplateView', 'translation_of_id', $row->translation_of_id);
		Services::Registry()->set('TemplateView', 'language', $row->language);
		Services::Registry()->set('TemplateView', 'view_group_id', $row->view_group_id);
		Services::Registry()->set('TemplateView', 'catalog_id', $row->catalog_id);
		Services::Registry()->set('TemplateView', 'catalog_type_id', (int)$row->catalog_type_id);
		Services::Registry()->set('TemplateView', 'catalog_type_title', $row->catalog_type_title);
		Services::Registry()->set('TemplateView', 'path', $this->getPath($row->title));
		Services::Registry()->set('TemplateView', 'path_url', $this->getPathURL($row->title));

		/** Load special fields for specific extension */
		$xml = Services::Configuration()->loadFile('Manifest', Services::Registry()->get('TemplateView', 'path'));
		$row = Services::Configuration()->addSpecialFields($xml->config, $row, 1);

		return;
	}

	/**
	 *  Determine the default theme value, given system default sequence
	 *
	 * @return  string
	 * @since   1.0
	 */
	public function DefaultTemplateView()
	{
		$template_view_id = Services::Registry()->get('ContentParameters', 'template_view_id', 0);
		if ((int)$template_view_id == 0) {
		} else {
			return $template_view_id;
		}

		$template_view_id = Services::Registry()->get('MenuItemParameters', 'template_view_id', 0);
		if ((int)$template_view_id == 0) {
		} else {
			return $template_view_id;
		}

		$template_view_id = Services::Registry()->get('CategoryParameters', 'template_view_id', 0);
		if ((int)$template_view_id == 0) {
		} else {
			return $template_view_id;
		}

		$template_view_id = Services::Registry()->get('ExtensionParameters', 'template_view_id', 0);
		if ((int)$template_view_id == 0) {
		} else {
			return $template_view_id;
		}

		$template_view_id = Services::Registry()->get('UserParameters', 'template_view_id', 0);
		if ((int)$template_view_id == 0) {
		} else {
			return $template_view_id;
		}

		$template_view_id = Services::Registry()->get('ApplicationParameters', 'template_view_id', 0);
		if ((int)$template_view_id == 0) {
		} else {
			return $template_view_id;
		}

		$template_view_id = Services::Registry()->get('SiteParameters', 'template_view_id', 0);
		if ((int)$template_view_id == 0) {
		} else {
			return $template_view_id;
		}

		return Helpers::Extension()->getInstanceID(CATALOG_TYPE_EXTENSION_TEMPLATE_VIEW, 'Default'); //55
	}

	/**
	 * getPath
	 *
	 * Return path for selected Template View
	 *
	 * Expects known path for Theme and Extension
	 *
	 * @param $template_view_name
	 * @return bool|string
	 */
	public function getPath($template_view_name)
	{
		$plus = '/View/Template/' . ucfirst(strtolower($template_view_name));

		/** 1. Theme */
		if (file_exists(Services::Registry()->get('Theme', 'path') . $plus . '/Manifest.xml')) {
			return Services::Registry()->get('Theme', 'path') . $plus;
		}

		/** 2. Extension */
		if (file_exists(Services::Registry()->get('Extension', 'path') . $plus . '/Manifest.xml')) {
			return Services::Registry()->get('Extension', 'path') . $plus;
		}

		/** 3. View */
		if (file_exists(EXTENSIONS_VIEWS . '/Template/' . $template_view_name . '/Manifest.xml')) {
			return EXTENSIONS_VIEWS . '/Template/' . $template_view_name;
		}

		/** 4. MVC */
		if (file_exists(MVC . $plus . '/Manifest.xml')) {
			return MVC . $plus;
		}

		return false ;
	}

	/**
	 * getURLPath
	 *
	 * Return URL path for selected Template View
	 *
	 * @return bool|string
	 * @since 1.0
	 */
	public function getPathURL($template_view_name)
	{
		$plus = '/View/Template/' . ucfirst(strtolower($template_view_name));

		/** 1. Theme */
		if (file_exists(Services::Registry()->get('Theme', 'path') . $plus . '/Manifest.xml')) {
			return Services::Registry()->get('Theme', 'path_url') . $plus;
		}

		/** 2. Extension */
		if (file_exists(Services::Registry()->get('Extension', 'path') . $plus . '/Manifest.xml')) {
			return Services::Registry()->get('Extension', 'path_url') . $plus;
		}

		/** 3. View */
		if (file_exists(EXTENSIONS_VIEWS . '/Template/' . $template_view_name . '/Manifest.xml')) {
			return EXTENSIONS_VIEWS_URL . '/Template/' . $template_view_name;
		}

		/** 4. MVC */
		if (file_exists(MVC . $plus . '/Manifest.xml')) {
			return MVC_URL . $plus;
		}

		return false;
	}
}
