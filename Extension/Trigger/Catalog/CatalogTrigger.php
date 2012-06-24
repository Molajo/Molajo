<?php
/**
 * @package    Molajo
 * @copyright  2012 Amy Stephen. All rights reserved.
 * @license    GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
namespace Molajo\Extension\Trigger\Catalog;

use Molajo\Extension\Trigger\Content\ContentTrigger;
use Molajo\Controller\CreateController;
use Molajo\Service\Services;

defined('MOLAJO') or die;

/**
 * Catalog
 *
 * @package     Molajo
 * @subpackage  Trigger
 * @since       1.0
 */
class CatalogTrigger extends ContentTrigger
{

	/**
	 * Post-create processing
	 *
	 * @return boolean
	 * @since   1.0
	 */
	public function onAfterCreate()
	{
		if ($this->query_results->catalog_type_id >= CATALOG_TYPE_EXTENSION_BEGIN
			AND $this->query_results->catalog_type_id <= CATALOG_TYPE_EXTENSION_END
		) {
		} else {
			return true;
		}

		/** Catalog ID */
		$id = $this->query_results->id;
		if ((int)$id == 0) {
			return false;
		}

		/** Catalog Activity */
		$controller = new CreateController();

		$data = new \stdClass();
		$data->catalog_id = $id;

		$controller->data = $data;

		$catalog_activity_id = $controller->create();

		if ($catalog_activity_id === false) {
			//install failed
			return false;
		}

		/** Permissions */
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
