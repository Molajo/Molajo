<?php
/**
 * @package    Molajo
 * @copyright  2012 Amy Stephen. All rights reserved.
 * @license    GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
namespace Molajo\Extension\Trigger;

defined('MOLAJO') or die;

/**
 * Item URL
 *
 * @package     Molajo
 * @subpackage  Trigger
 * @since       1.0
 */
class ItemURLTrigger extends ContentTrigger
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
			self::$instance = new ItemURLTrigger();
		}
		return self::$instance;
	}

    /**
     * After-read processing
	 *
	 * Retrieves the URL for the Item
     *
     * @param   $data
     * @param   $model
     *
     * @return  $data
     * @since   1.0
     */
    public function onAfterRead($data, $model)
    {
		if (isset($data->url)
			&& $data->url !== null
			&& $data->url !== '') {
			return;
		}

		if (isset($data->catalog_id)
			&& (int) $data->catalog_id > 0) {

		} else if (isset($data->catalog_type_id) && (int)$data->catalog_type_id > 0
			&& isset($data->id) && (int)$data->id > 0) {
			$data->catalog_id = Helpers::Catalog()->getID($data->catalog_type_id, $data->id);
		}

		if (isset($data->catalog_id)
			&& (int) $data->catalog_id > 0) {
			$data->url = Helpers::Catalog()->getURL($data->catalog_id);
			return;
		}

		return;
    }
}
