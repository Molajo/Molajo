<?php
/**
 * @package    Molajo
 * @copyright  2012 Individual Molajo Contributors. All rights reserved.
 * @license    GNU GPL v 2, or later and MIT, see License folder
 */
namespace Molajo\Extension\Includer;

use Molajo\Helpers;
use Molajo\Service\Services;
use Molajo\Extension\Includer;

defined('MOLAJO') or die;

/**
 * Profiler
 *
 * @package     Molajo
 * @subpackage  Includer
 * @since       1.0
 */
Class ProfilerIncluder extends Includer
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
		Services::Registry()->set('Parameters', 'template_view_id',
			Services::Registry()->get('Configuration', 'profiler_console_template_view_id'));

		Services::Registry()->set('Parameters', 'wrap_view_id',
			Services::Registry()->get('Configuration', 'profiler_console_wrap_view_id'));

		Services::Registry()->set('Parameters', 'criteria_display_view_on_no_results', 1);

		/** Template  */
		Helpers::View()->get(Services::Registry()->get('Parameters', 'template_view_id'), 'Template');

		/** Wrap  */
		Helpers::View()->get(Services::Registry()->get('Parameters', 'wrap_view_id'), 'Wrap');

		/** Merge Configuration in */
		Services::Registry()->merge('Configuration', 'Parameters', true);

		/** DBO  */
		Services::Registry()->set('Parameters', 'model_name', 'Profiler');
		Services::Registry()->set('Parameters', 'model_type', 'dbo');
		Services::Registry()->set('Parameters', 'model_query_object', 'getProfiler');

		/** Cleanup */
		Services::Registry()->delete('Parameters', 'item*');
		Services::Registry()->delete('Parameters', 'list*');
		Services::Registry()->delete('Parameters', 'form*');

		/** Sort */
		Services::Registry()->sort('Parameters');

		return true;
	}
}
