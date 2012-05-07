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
 * Head
 *
 * @package     Molajo
 * @subpackage  Includer
 * @since       1.0
 */
Class HeadIncluder extends Includer
{
	/**
	 * __construct
	 *
	 * Class constructor.
	 *
	 * @param  string $name
	 * @param  string $type
	 * @param  array  $items (used for event processing renderers, only)
	 *
	 * @return  null
	 * @since   1.0
	 */
	public function __construct($name = null, $type = null, $items = null)
	{
		$this->extension_required = false;
		parent::__construct($name, $type, $items);
		Services::Registry()->set('Parameters', 'html_display_filter', false);
	}

	/**
	 *  getApplicationDefaults
	 *
	 *  Retrieve default values, if not provided by extension
	 *
	 * @return  bool
	 * @since   1.0
	 */
	protected function getApplicationDefaults()
	{
		Services::Registry()->set('Parameters', 'model', 'HeadModel');
		Services::Registry()->set('Parameters', 'task', 'display');

		if ((int)Services::Registry()->get('Parameters', 'template_view_id', 0) == 0) {
			Services::Registry()->set('Parameters', 'template_view_id',
				Services::Registry()->get('Configuration', 'head_template_view_id', 'DocumentHead'));
		}

		if ((int)Services::Registry()->get('Parameters', 'wrap_view_id', 0) == 0) {
			Services::Registry()->set('Parameters', 'wrap_view_id',
				Services::Registry()->get('Configuration', 'head_wrap_view_id', 'none'));
		}

		if ($this->type == 'defer') {
			Services::Registry()->set('Parameters', 'defer', 1);
		} else {
			Services::Registry()->set('Parameters', 'defer', 0);
		}

		return true;
	}
}
