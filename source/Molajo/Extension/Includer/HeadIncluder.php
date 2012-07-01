<?php
/**
 * @package   Molajo
 * @copyright 2012 Amy Stephen. All rights reserved.
 * @license   GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
namespace Molajo\Extension\Includer;

use Molajo\Service\Services;
use Molajo\Extension\Includer;

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
	 *  setRenderCriteria
	 *
	 *  Retrieve default values, if not provided by extension
	 *
	 * @return  bool
	 * @since   1.0
	 */
	protected function setRenderCriteria()
	{
		Services::Registry()->set('Parameters', 'criteria_display_view_on_no_results', 1);

		if ($this->type == 'defer') {

			if ((int)Services::Registry()->get('Parameters', 'template_view_id', 0) == 0) {
				Services::Registry()->set('Parameters', 'template_view_id',
					Services::Registry()->get('Configuration', 'defer_template_view_id'));
			}

			if ((int)Services::Registry()->get('Parameters', 'wrap_view_id', 0) == 0) {
				Services::Registry()->set('Parameters', 'wrap_view_id',
					Services::Registry()->get('Configuration', 'defer_wrap_view_id'));
			}

			Services::Registry()->set('Parameters', 'model_name', 'Metadata');
			Services::Registry()->set('Parameters', 'model_type', 'dbo');
			Services::Registry()->set('Parameters', 'model_query_object', 'getMetadata');
			Services::Registry()->set('Parameters', 'model_parameter', 'defer');

		} else {
			if ((int)Services::Registry()->get('Parameters', 'template_view_id', 0) == 0) {
				Services::Registry()->set('Parameters', 'template_view_id',
					Services::Registry()->get('Configuration', 'head_template_view_id'));
			}
			if ((int)Services::Registry()->get('Parameters', 'wrap_view_id', 0) == 0) {
				Services::Registry()->set('Parameters', 'wrap_view_id',
					Services::Registry()->get('Configuration', 'head_wrap_view_id'));
			}
			Services::Registry()->set('Parameters', 'model_name', 'Metadata');
			Services::Registry()->set('Parameters', 'model_type', 'dbo');
			Services::Registry()->set('Parameters', 'model_query_object', 'getMetadata');
			Services::Registry()->set('Parameters', 'model_parameter', 'head');
		}

		parent::setRenderCriteria();

		return true;
	}
}
