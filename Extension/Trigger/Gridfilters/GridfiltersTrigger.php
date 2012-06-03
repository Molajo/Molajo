<?php
/**
 * @package    Molajo
 * @copyright  2012 Amy Stephen. All rights reserved.
 * @license    GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
namespace Molajo\Extension\Trigger\Gridfilters;

use Molajo\Extension\Trigger\Content\ContentTrigger;
use Molajo\Service\Services;

defined('MOLAJO') or die;

/**
 * @package     Molajo
 * @subpackage  Trigger
 * @since       1.0
 */
class GridfiltersTrigger extends ContentTrigger
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
			self::$instance = new GridfiltersTrigger();
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
		/** Initialize Filter Registry */
		Services::Registry()->createRegistry('Gridfilters');
		Services::Registry()->get('Parameters', '*');
		die;
		/** Retrieve Filters from Parameters for Component */
		$filters = explode(',', Services::Registry()->get('Parameters', 'criteria_view_filters'));
		var_dump($filters);
		if (is_array($filters) && count($filters) > 0) {

			/** @noinspection PhpWrongForeachArgumentTypeInspection */
			foreach ($filters as $filter) {

				$fieldValue = $this->getList($filter);

				if ($fieldValue == false) {
				} else {
					Services::Registry()->set('Gridfilters', $filter, $fieldValue);
				}

				/** Retrieves the user selected field from the session */
				$selectedValue = $this->getFieldValue($filter);

				Services::Registry()->set('Gridfilters', $filter.'Selected', $selectedValue);
			}
		}
		Services::Registry()->get('Gridfilters', '*');

		die;
		return true;
	}

	/**
	 * getList retrieves values used in the Grid List
	 *
	 * @return boolean
	 * @since   1.0
	 */
	protected function getList($filter)
	{
		$filter = 'Menuitems';

		$controllerClass = 'Molajo\\MVC\\Controller\\ModelController';
		$m = new $controllerClass();
		$m->connect($filter, 'Listbox');

		$primary_prefix = $m->get('primary_prefix');

		if ((int) $m->get('filter_catalog_type_id') > 0) {
			$m->model->query->where($m->model->db->qn($primary_prefix . '.' . 'catalog_type_id')
				. ' = ' . (int) $m->get('filter_catalog_type_id') );
		}

		$this->publishedStatus($m);

		return $m->getData('distinct');

	}

	/**
	 * publishedStatus
	 *
	 * @return boolean
	 * @since   1.0
	 */
	protected function publishedStatus($m)
	{
		$triggers = Services::Registry()->get($m->table_registry_name, 'triggers', array());
		if (in_array('Publishedstatus', $triggers)) {
		} else {
			return;
		}

		$primary_prefix = Services::Registry()->get($m->table_registry_name, 'primary_prefix', 'a');

		$m->model->query->where($m->model->db->qn($primary_prefix)
			. '.' . $m->model->db->qn('status')
			. ' > ' . STATUS_UNPUBLISHED);

		$m->model->query->where('(' . $m->model->db->qn($primary_prefix)
				. '.' . $m->model->db->qn('start_publishing_datetime')
				. ' = ' . $m->model->db->q($m->model->nullDate)
				. ' OR ' . $m->model->db->qn($primary_prefix) . '.' . $m->model->db->qn('start_publishing_datetime')
				. ' <= ' . $m->model->db->q($m->model->now) . ')'
		);

		$m->model->query->where('(' . $m->model->db->qn($primary_prefix)
				. '.' . $m->model->db->qn('stop_publishing_datetime')
				. ' = ' . $m->model->db->q($m->model->nullDate)
				. ' OR ' . $m->model->db->qn($primary_prefix) . '.' . $m->model->db->qn('stop_publishing_datetime')
				. ' >= ' . $m->model->db->q($m->model->now) . ')'
		);

		return $this;
	}
}
