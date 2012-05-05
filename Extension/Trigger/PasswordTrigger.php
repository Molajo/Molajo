<?php
/**
 * @package    Molajo
 * @copyright  2012 Amy Stephen. All rights reserved.
 * @license    GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
namespace Molajo\Extension\Trigger;

defined('MOLAJO') or die;

/**
 * Password
 *
 * @package     Molajo
 * @subpackage  Trigger
 * @since       1.0
 */
class PasswordTrigger extends ContentTrigger
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
			self::$instance = new PasswordTrigger();
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
        return $data;
    }

    /**
     * Post-read processing
     *
     * @param   $data
     * @param   $model
     *
     * @return  $data
     * @since   1.0
     */
    public function onAfterRead($data, $model)
    {
        return $data;
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
        return $data;
    }

    /**
     * Pre-delete processing
     *
     * @param   $data
     * @param   $model
     *
     * @return  $data
     * @since   1.0
     */
    public function onBeforeDelete($data, $model)
    {
        return $data;
    }

    /**
     * Post-read processing
     *
     * @param   $data
     * @param   $model
     *
     * @return  $data
     * @since   1.0
     */
    public function onAfterDelete($data, $model)
    {
        return $data;
    }
}
