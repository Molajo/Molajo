<?php
/**
 * @package    Molajo
 * @copyright  2012 Amy Stephen. All rights reserved.
 * @license    GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
namespace Molajo\Extension\Trigger\Content;

use Molajo\Extension\Trigger\Trigger\Trigger;

defined('MOLAJO') or die;

/**
 * Item Author
 *
 * @package     Molajo
 * @subpackage  Trigger
 * @since       1.0
 */
class ContentTrigger extends Trigger
{
	/**
	 * Static instance
	 *
	 * @var    object
	 * @since  1.0
	 */
	protected static $instance;

	/**
	 * Table Registry Name - can be used to retrieve table parameters
	 *
	 * @var    object
	 * @since  1.0
	 */
	protected $table_registry_name;

	/**
	 * Parameters set by the Includer and used in the MVC to generate include output
	 *
	 * @var    object
	 * @since  1.0
	 */
	protected $parameters;

	/**
	 * Model object
	 *
	 * @var    object
	 * @since  1.0
	 */
	protected $model;

	/**
	 * Query Results
	 *
	 * @var    object
	 * @since  1.0
	 */
	protected $query_results;

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
			self::$instance = new ContentTrigger();
		}
		return self::$instance;
	}

	/**
	 * get the class property following the trigger method execution
	 *
	 * @return mixed
	 * @since  1.0
	 */
	public function get($key, $value)
	{
		return $this->$key;
	}

	/**
	 * set the class property before the trigger method executes
	 *
	 * @return mixed
	 * @since  1.0
	 */
	public function set($key, $value)
	{
		$this->$key = $value;

		return $this;
	}

	/**
	 * Pre-create processing
	 *
	 * @param   $data
	 * @param   $model
	 *
	 * @return  boolean
	 * @since   1.0
	 */
	public function onBeforeCreate()
	{
		return false;
	}

	/**
	 * Post-create processing
	 *
	 * @param $data, $model
	 *
	 * @return  boolean
	 * @since   1.0
	 */
	public function onAfterCreate()
	{
		return false;
	}

	/**
	 * Pre-read processing
	 *
	 * @param   $data
	 * @param   $model
	 *
	 * @return  boolean
	 * @since   1.0
	 */
	public function onBeforeRead()
	{
		return false;
	}

	/**
	 * Post-read processing
	 *
	 * @param   $data
	 * @param   $model
	 *
	 * @return  boolean
	 * @since   1.0
	 */
	public function onAfterRead()
	{
		return false;
	}

	/**
	 * Pre-update processing
	 *
	 * @param   $data
	 * @param   $model
	 *
	 * @return  boolean
	 * @since   1.0
	 */
	public function onBeforeUpdate()
	{
		return false;
	}

	/**
	 * Post-update processing
	 *
	 * @param   $data
	 * @param   $model
	 *
	 * @return  boolean
	 * @since   1.0
	 */
	public function onAfterUpdate()
	{
		return false;
	}

	/**
	 * Pre-delete processing
	 *
	 * @param   $data
	 * @param   $model
	 *
	 * @return  boolean
	 * @since   1.0
	 */
	public function onBeforeDelete()
	{
		return false;
	}

	/**
	 * Post-read processing
	 *
	 * @param   $data
	 * @param   $model
	 *
	 * @return  boolean
	 * @since   1.0
	 */
	public function onAfterDelete()
	{
		return false;
	}
}
