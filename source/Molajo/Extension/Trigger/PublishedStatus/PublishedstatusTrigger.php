<?php
/**
 * @package    Molajo
 * @copyright  2012 Amy Stephen. All rights reserved.
 * @license    GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
namespace Molajo\Extension\Trigger\Publishedstatus;

use Molajo\Extension\Trigger\Content\ContentTrigger;

defined('MOLAJO') or die;

/**
 * Published Status
 *
 * @package     Molajo
 * @subpackage  Trigger
 * @since       1.0
 */
class PublishedstatusTrigger extends ContentTrigger
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
			self::$instance = new PublishedstatusTrigger();
		}
		return self::$instance;
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
		// if published or greater status
		// make certain published start date is today or later
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
		// if it is published, notify
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
		$model->query->where(
			$model->db->qn($model->primary_prefix)
				. '.'
				. $model->db->qn('status')
				. ' > '
				. (int)STATUS_UNPUBLISHED
		);

		$model->query->where('('
				. $model->db->qn($model->primary_prefix)
				. '.'
				. $model->db->qn('start_publishing_datetime')
				. ' = '
				. $model->db->q($model->nullDate)
				. ' OR '
				. $model->db->qn($model->primary_prefix)
				. '.'
				. $model->db->qn('start_publishing_datetime')
				. ' <= '
				. $model->db->q($model->now)
				. ')'
		);

		$model->query->where('('
				. $model->db->qn($model->primary_prefix)
				. '.'
				. $model->db->qn('stop_publishing_datetime')
				. ' = '
				. $model->db->q($model->nullDate)
				. ' OR '
				. $model->db->qn($model->primary_prefix)
				. '.'
				. $model->db->qn('stop_publishing_datetime')
				. ' >= '
				. $model->db->q($model->now)
				. ')'
		);

		return $this;
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
		// hold status
		// if it is published (or greater) make certain published dates are ok
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
		// if it wasn't published and now is

		// is email notification enabled? are people subscribed?
		// tweets
		// pings
		return false;
	}

	public function notify()
	{
		// is email notification enabled? are people subscribed?
		// tweets
		// pings
	}
}
