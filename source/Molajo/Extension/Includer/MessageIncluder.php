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
 * Message
 *
 * @package     Molajo
 * @subpackage  Includer
 * @since       1.0
 */
Class MessageIncluder extends Includer
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
		Services::Registry()->set('Parameters', 'extension_catalog_type_id', 0);
		$this->extension_required = false;
		parent::__construct($name, $type, $items);
		return Services::Registry()->set('Parameters', 'criteria_html_display_filter', false);
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
		Services::Registry()->set('Parameters', 'model', 'MessagesModel');
		Services::Registry()->set('Parameters', 'action', 'display');

		$this->parameters = Services::Registry()->initialise();
		Services::Registry()->set('Parameters', 'suppress_no_results', 1);

		if ((int)Services::Registry()->get('Parameters', 'template_view_id', 0) == 0) {
			Services::Registry()->set('Parameters', 'template_view_id',
				Services::Registry()->get('Configuration', 'message_template_view_id'));
		}
		if ((int)Services::Registry()->get('Parameters', 'wrap_view_id', 0) == 0) {
			Services::Registry()->set('Parameters', 'wrap_view_id',
				Services::Registry()->get('Configuration', 'message_wrap_view_id'));
		}

		return true;
	}
}

