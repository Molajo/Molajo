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
	 * @return  $data
	 * @since   1.0
	 */
	public function onBeforeCreate($data, $model)
	{
		// if published or greater status
		// make certain published start date is today or later
		return $data;
	}

	/**
	 * Post-create processing
	 *
	 * @param $data, $model
	 *
	 * @return  $data
	 * @since   1.0
	 */
	public function onAfterCreate($data, $model)
	{
		// if it is published, notify
		return $data;
	}

	/**
	 * Pre-read processing
	 *
	 * @param   $data
	 * @param   $model
	 *
	 * @return  $data
	 * @since   1.0
	 */
	public function onBeforeRead($data, $model)
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
	 * @return  $data
	 * @since   1.0
	 */
	public function onBeforeUpdate($data, $model)
	{
		// hold status
		// if it is published (or greater) make certain published dates are ok
		return $data;
	}

	/**
	 * Post-update processing
	 *
	 * @param   $data
	 * @param   $model
	 *
	 * @return  $data
	 * @since   1.0
	 */
	public function onAfterUpdate($data, $model)
	{
		// if it wasn't published and now is

		// is email notification enabled? are people subscribed?
		// tweets
		// pings
		return $data;
	}

	public function notify($data, $model)
	{
		// is email notification enabled? are people subscribed?
		// tweets
		// pings
	}
}
