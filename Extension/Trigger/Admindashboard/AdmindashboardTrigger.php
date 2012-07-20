<?php
/**
 * @package    Molajo
 * @copyright  2012 Amy Stephen. All rights reserved.
 * @license    GNU GPL v 2, or later and MIT, see License folder
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
     * @return void
     * @since   1.0
     */
    public function onBeforeParse()
    {
		if (APPLICATION_ID == 2) {
		} else {
			return true;
		}

        if (strtolower($this->get('template_view_path_node')) == 'admindashboard') {
        } else {
            return true;
        }
		$query_results = array();

		$this->set('model_name', 'Triggerdata');
		$this->parameters['model_name'] = 'Triggerdata';
		$this->set('model_type', 'dbo');
		$this->parameters['model_type'] = 'dbo';
		$this->set('model_query_object', 'getTriggerdata');
		$this->set('model_parameter', 'PrimaryRequestQueryResults');

		$query_results = array();
		$row = new \stdClass();
		$row->title = $this->parameters['dashboard_section'];

		$query_results[] = $row;

 		Services::Registry()->set('Triggerdata', 'PrimaryRequestQueryResults', $query_results);

        return true;
    }
}
