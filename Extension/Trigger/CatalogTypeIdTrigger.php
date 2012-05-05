<?php
/**
 * @package    Molajo
 * @copyright  2012 Amy Stephen. All rights reserved.
 * @license    GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
namespace Molajo\Extension\Trigger;

defined('MOLAJO') or die;

/**
 * CatalogTypeID
 *
 * @package     Molajo
 * @subpackage  Trigger
 * @since       1.0
 */
class CatalogTypeIDTrigger extends ContentTrigger
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
     * @param   $data
     * @param   $model
     *
     * @return  $data
     * @since   1.0
     */
    public function onBeforeCreate($data, $model)
    {
		// catalog_type_id foreign key
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
		// save it
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
		// cannot change value
		// foreign key must exist
        return $data;
    }
}
