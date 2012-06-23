<?php
/**
 * @package    Molajo
 * @copyright  2012 Amy Stephen. All rights reserved.
 * @license    GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
namespace Molajo\Extension\Trigger\Admindashboard;

use Molajo\Extension\Trigger\Content\ContentTrigger;
use Molajo\Service\Services;

defined('MOLAJO') or die;

/**
 * @package     Molajo
 * @subpackage  Trigger
 * @since       1.0
 */
class AdmindashboardTrigger extends ContentTrigger
{

	/**
	 * Before-read processing
	 *
	 * Prepares data for the Administrator Grid  - position AdmindashboardTrigger last
	 *
	 * @return  void
	 * @since   1.0
	 */
	public function onAfterRoute()
	{
		/** Is this an Administrative Grid Request?  */
		if (strtolower($this->get('template_view_path_node')) == 'admindashboard') {
		} else {
			return true;
		}

		return true;
	}

	/**
	 * Create Grid Query and save results in Trigger registry
	 *
	 * @param   $connect
	 * @param   $primary_prefix
	 * @param   $table_name
	 *
	 * @return  bool
	 * @since   1.0
	 */
	protected function setGrid($connect, $primary_prefix, $table_name)
	{

	}
}
