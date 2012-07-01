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
 * Wrap
 *
 * @package     Molajo
 * @subpackage  Includer
 * @since       1.0
 */
Class WrapIncluder extends Includer
{
	/**
	 * @param string $name
	 * @param string $type
	 *
	 * @return null
	 * @since   1.0
	 */
	public function __construct($name = null, $type = null)
	{
		Services::Registry()->set('Parameters', 'extension_catalog_type_id', 0);
		parent::__construct($name, $type);
		Services::Registry()->set('Parameters', 'criteria_html_display_filter', false);

		return $this;
	}

	/**
	 * setRenderCriteria
	 *
	 * Retrieve default values, if not provided by extension
	 *
	 * @return bool
	 * @since   1.0
	 */
	protected function setRenderCriteria()
	{
		/** For wrap type - extension name is was set to the name of the wrap in the getAttributes method */
		$wrap_title = Services::Registry()->get('Parameters', 'extension_title');

		$wrap_id = Helpers::Extension()
			->getInstanceID(CATALOG_TYPE_EXTENSION_WRAP_VIEW, $wrap_title);

		if ((int)$wrap_id == 0) {
		} else {
			Services::Registry()->set('Parameters', 'wrap_view_id', $wrap_id);
		}

		/** Standard parameters (overwrite extension title with Wrap */
		Services::Registry()->set('Parameters', 'extension_title', 'Wrap');

		Services::Registry()->set('Parameters', 'criteria_display_view_on_no_results', 1);

		/** Set parameters and merge in configuration values */
		Helpers::View()->get(Services::Registry()->get('Parameters', 'wrap_view_id'), 'Wrap');

		Services::Registry()->merge('Configuration', 'Parameters', true);

		/* Set other model parameters: model_parameter is set in Attributes */
		$value = Services::Registry()->get('Parameters', 'model_parameter', '');

		Services::Registry()->set('Parameters', 'model_name', 'Triggerdata');
		Services::Registry()->set('Parameters', 'model_type', 'dbo');
		Services::Registry()->set('Parameters', 'model_query_object', 'getTriggerdata');

		/** Cleanup */
		Services::Registry()->delete('Parameters', 'item*');
		Services::Registry()->delete('Parameters', 'list*');
		Services::Registry()->delete('Parameters', 'form*');

		/** Sort */
		Services::Registry()->sort('Parameters');

		return true;
	}

	/**
	 * Loads Media CSS and JS files for Template and Wrap Views
	 *
	 * @return null
	 * @since   1.0
	 */
	protected function loadViewMedia()
	{
		$priority = Services::Registry()->get('Parameters', 'criteria_media_priority_other_extension', 400);

		$file_path = Services::Registry()->get('Parameters', 'wrap_view_path');
		$url_path = Services::Registry()->get('Parameters', 'wrap_view_path_url');

		Services::Asset()->addCssFolder($file_path, $url_path, $priority);
		Services::Asset()->addJsFolder($file_path, $url_path, $priority, 0);
		Services::Asset()->addJsFolder($file_path, $url_path, $priority, 1);

		return $this;
	}
}
