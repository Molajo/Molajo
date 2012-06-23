<?php
/**
 * @package    Molajo
 * @copyright  2012 Amy Stephen. All rights reserved.
 * @license    GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
namespace Molajo\Extension\Trigger\Extension;

use Molajo\Extension\Trigger\Content\ContentTrigger;
use Molajo\Controller\CreateController;
use Molajo\Service\Services;

defined('MOLAJO') or die;

/**
 * Extension
 *
 * @package     Molajo
 * @subpackage  Trigger
 * @since       1.0
 */
class ExtensionTrigger extends ContentTrigger
{

	/**
	 * Pre-create processing
	 *
	 * @return boolean
	 * @since   1.0
	 */
	public function onBeforeCreate()
	{

		if ($this->table_registry_name == 'ExtensionsTable') {
			return true;
		}

		if ($this->query_results->catalog_type_id >= CATALOG_TYPE_EXTENSION_BEGIN
			AND $this->query_results->catalog_type_id <= CATALOG_TYPE_EXTENSION_END
		) {
		} else {
			return true;
		}

		$field = $this->getField('extension_id');

		if ($field == false) {
			$fieldValue = false;
		} else {
			$fieldValue = $this->getFieldValue($field);
		}

		if ((int)$fieldValue > 0) {
			return true;
		}

		/** See if the Extension Root exists */
		$controllerClass = 'Molajo\\Controller\\ModelController';
		$m = new $controllerClass();
		$m->connect('Table', 'Extensions');

		$titleField = $this->getField('title');
		$titleValue = $this->getFieldValue($titleField);

		$m->model->query->select($m->model->db->qn('a.id'));
		$m->model->query->where($m->model->db->qn('a.name')
			. ' = ' . $m->model->db->q($titleValue));

		$id = $m->getData('result');

		if ((int)$id > 0) {
			$newFieldValue = (int)$id;
			$this->saveField($field, 'extension_id', $newFieldValue);
			return true;
		}

		/** Create Extension Row */
		$controller = new CreateController();

		$data = new \stdClass();
		$data->name = $this->query_results->title;
		$data->catalog_type_id = $this->query_results->catalog_type_id;
		$data->model_name = 'Extensions';

		$controller->data = $data;

		$id = $controller->create();

		if ($id === false) {
			//error
			return false;
		} else {
			$newFieldValue = (int)$id;
			$this->saveField($field, 'extension_id', $newFieldValue);
		}

		return true;
	}

	/**
	 * Post-create processing
	 *
	 * @return boolean
	 * @since   1.0
	 */
	public function onAfterCreate()
	{
		return true;
	}

	/**
	 * Pre-read processing
	 *
	 * @return boolean
	 * @since   1.0
	 */
	public function onBeforeRead()
	{
		return true;
	}

	/**
	 * Post-read processing
	 *
	 * @return boolean
	 * @since   1.0
	 */
	public function onAfterRead()
	{
		return true;
	}

	/**
	 * On after route
	 *
	 * @return boolean
	 * @since   1.0
	 */
	public function onAfterRoute()
	{
		return true;
	}

	/**
	 * Before the Query results are injected into the View
	 *
	 * @return boolean
	 * @since   1.0
	 */
	public function onBeforeViewRender()
	{
		return true;
	}

	/**
	 * Pre-update processing
	 *
	 * @param   $this->query_results
	 * @param   $model
	 *
	 * @return boolean
	 * @since   1.0
	 */
	public function onBeforeUpdate()
	{
		return true;
	}

	/**
	 * Post-update processing
	 *
	 * @param   $this->query_results
	 * @param   $model
	 *
	 * @return boolean
	 * @since   1.0
	 */
	public function onAfterUpdate()
	{
		return true;
	}

	/**
	 * Pre-delete processing
	 *
	 * @param   $this->query_results
	 * @param   $model
	 *
	 * @return boolean
	 * @since   1.0
	 */
	public function onBeforeDelete()
	{
		return true;
	}

	/**
	 * Post-delete processing
	 *
	 * @param   $this->query_results
	 * @param   $model
	 *
	 * @return boolean
	 * @since   1.0
	 */
	public function onAfterDelete()
	{
		return true;
	}
}
