<?php
/**
 * @package    Molajo
 * @copyright  2012 Amy Stephen. All rights reserved.
 * @license    GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
namespace Molajo\Extension\Trigger\Admingrid;

use Molajo\Extension\Trigger\Content\ContentTrigger;
use Molajo\Service\Services;

defined('MOLAJO') or die;

/**
 * @package     Molajo
 * @subpackage  Trigger
 * @since       1.0
 */
class AdmingridTrigger extends ContentTrigger
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
			self::$instance = new AdmingridTrigger();
		}

		return self::$instance;
	}

	/**
	 * Before-read processing
	 *
	 * Prepares the filter selections for the Grid Query
	 *
	 * @return boolean
	 * @since   1.0
	 */
	public function onBeforeRead()
	{

		/** Is this an Administrative Grid Request?  */

		/** Initialize Filter Registry */
		Services::Registry()->createRegistry('Admingrid');

		/** Retrieve Filters from Parameters for Component */
		$parameters = $this->get('parameters');
		$createAdmingrid = explode(',', $parameters['grid_lists']);
		$lists = array();

		if (is_array($createAdmingrid) && count($createAdmingrid) > 0) {

			/** Build each list and store in registry along with current selection */
			foreach ($createAdmingrid as $list) {

				$fieldValue = Services::Text()->getList($list, $this->parameters);

				if ($fieldValue == false) {
				} else {

					ksort($fieldValue);

					Services::Registry()->set('Admingrid', 'list_' . $list, $fieldValue);

					/** todo: Retrieves the user selected field from the session */
					$selectedValue = null;

					if (strtolower($list) == 'created_by') {
						$selectedValue = 100;

					} elseif (strtolower($list) == 'catalog_type_id') {
						$selectedValue = 10000;

					} elseif (strtolower($list) == 'featured') {
						$selectedValue = 1;

					} elseif (strtolower($list) == 'protected') {
						$selectedValue = 1;
					}

					Services::Registry()->set('Admingrid', 'list_' . $list . '_selected', $selectedValue);

					$lists[] = strtolower($list);
				}
			}
		}

		Services::Registry()->set('Admingrid', 'list', $lists);

		return true;
	}
}
