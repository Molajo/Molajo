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
 * Head
 *
 * @package     Molajo
 * @subpackage  Includer
 * @since       1.0
 */
Class HeadIncluder extends Includer
{
	/**
	 * @return null
	 * @since   1.0
	 */
	public function __construct($name = null, $type = null)
	{
		Services::Registry()->set('Parameters', 'extension_catalog_type_id', 0);
		parent::__construct($name, $type);
		Services::Registry()->set('Parameters', 'criteria_html_display_filter', false);

		return;
	}

	/**
	 *  Retrieve default values for Rendering, if not provided by extension
	 *
	 * @return bool
	 * @since   1.0
	 */
	protected function setRenderCriteria()
	{
		Services::Registry()->set('Parameters', 'criteria_display_view_on_no_results', 1);

		Services::Registry()->set('Parameters', 'model_type', 'dbo');

		if ($this->type == 'defer') {

			if ((int)Services::Registry()->get('Parameters', 'template_view_id', 0) == 0) {
				Services::Registry()->set('Parameters', 'template_view_id',
					Services::Registry()->get('Configuration', 'defer_template_view_id'));
			}

			if ((int)Services::Registry()->get('Parameters', 'wrap_view_id', 0) == 0) {
				Services::Registry()->set('Parameters', 'wrap_view_id',
					Services::Registry()->get('Configuration', 'defer_wrap_view_id'));
			}

		} else {
			if ((int)Services::Registry()->get('Parameters', 'template_view_id', 0) == 0) {
				Services::Registry()->set('Parameters', 'template_view_id',
					Services::Registry()->get('Configuration', 'head_template_view_id'));
			}
			if ((int)Services::Registry()->get('Parameters', 'wrap_view_id', 0) == 0) {
				Services::Registry()->set('Parameters', 'wrap_view_id',
					Services::Registry()->get('Configuration', 'head_wrap_view_id'));
			}
		}

		/** Save existing parameters */
		$savedParameters = array();
		$temp = Services::Registry()->getArray('Parameters');

		if (is_array($temp) && count($temp) > 0) {
			foreach ($temp as $key => $value) {
				if (is_array($value)) {
					$savedParameters[$key] = $value;

				} elseif ($value === 0 || trim($value) == '' || $value === null) {

				} else {
					$savedParameters[$key] = $value;
				}
			}
		}

		/** Template  */
		Helpers::View()->get(Services::Registry()->get('Parameters', 'template_view_id'), 'Template');

		/** Merge Parameters in (Pre-wrap) */
		if (is_array($savedParameters) && count($savedParameters) > 0) {
			foreach ($savedParameters as $key => $value) {
				Services::Registry()->set('Parameters', $key, $value);
			}
		}
		/** Default Wrap if needed */
		$wrap_view_id = Services::Registry()->get('Parameters', 'wrap_view_id');
		Services::Registry()->set('Parameters', 'wrap_view_path_node',
					Helpers::Extension()->getExtensionNode((int)$wrap_view_id));
		$wrap_view_title = Services::Registry()->get('Parameters', 'wrap_view_path_node');

		Services::Registry()->set('Parameters', 'wrap_view_title', $wrap_view_title);
		Services::Registry()->set('Parameters', 'wrap_view_path',
			Helpers::View()->getPath($wrap_view_title, 'Wrap'));
		Services::Registry()->set('Parameters', 'wrap_view_path_url',
			Helpers::View()->getPathURL($wrap_view_title, 'Wrap'));
		Services::Registry()->set('Parameters', 'wrap_view_namespace',
			Helpers::View()->getNamespace($wrap_view_title, 'Wrap'));

		if (Services::Registry()->exists('Parameters', 'wrap_view_role')) {
		} else {
			Services::Registry()->set('Parameters', 'wrap_view_role', '');
		}
		if (Services::Registry()->exists('Parameters', 'wrap_view_property')) {
		} else {
			Services::Registry()->set('Parameters', 'wrap_view_property', '');
		}
		if (Services::Registry()->exists('Parameters', 'wrap_view_header_level')) {
		} else {
			Services::Registry()->set('Parameters', 'wrap_view_header_level', '');
		}
		if (Services::Registry()->exists('Parameters', 'wrap_view_show_title')) {
		} else {
			Services::Registry()->set('Parameters', 'wrap_view_show_title', '');
		}
		if (Services::Registry()->exists('Parameters', 'wrap_view_show_subtitle')) {
		} else {
			Services::Registry()->set('Parameters', 'wrap_view_show_subtitle', '');
		}
		Services::Registry()->delete('Parameters', 'item*');
		Services::Registry()->delete('Parameters', 'list*');
		Services::Registry()->delete('Parameters', 'form*');
		Services::Registry()->delete('Parameters', 'menuitem');

		Services::Registry()->sort('Parameters');

		return true;
	}
}
