<?php
/**
 * @package    Molajo
 * @copyright  2012 Amy Stephen. All rights reserved.
 * @license    GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
namespace Molajo\Extension\Trigger\CatalogtypeId;

use Molajo\Extension\Trigger\Content\ContentTrigger;

defined('MOLAJO') or die;

/**
 * CatalogtypeId
 *
 * @package     Molajo
 * @subpackage  Trigger
 * @since       1.0
 */
class CatalogtypeIdTrigger extends ContentTrigger
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
			self::$instance = new CatalogTypeIDTrigger();
		}
		return self::$instance;
	}

	/**
	 * Pre-create processing
	 *
	 * @param   $this->query_results
	 * @param   $model
	 *
	 * @return  boolean
	 * @since   1.0
	 */
	public function onBeforeCreate()
	{
		// catalog_type_id foreign key
		return false;
	}

	/**
	 * Pre-update processing
	 *
	 * @param   $this->query_results
	 * @param   $model
	 *
	 * @return  boolean
	 * @since   1.0
	 */
	public function onBeforeUpdate()
	{
		return false;
		// save it
	}

	/**
	 * Post-update processing
	 *
	 * @param   $this->query_results
	 * @param   $model
	 *
	 * @return  boolean
	 * @since   1.0
	 */
	public function onAfterUpdate()
	{
		// cannot change value
		// foreign key must exist
		return false;
	}
}
