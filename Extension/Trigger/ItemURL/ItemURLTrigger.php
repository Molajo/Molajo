<?php
/**
 * @package    Molajo
 * @copyright  2012 Amy Stephen. All rights reserved.
 * @license    GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
namespace Molajo\Extension\Trigger\ItemURL;

use Molajo\Extension\Trigger\Content\ContentTrigger;

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
	 * @return  boolean
	 * @since   1.0
	 */
	public function onAfterRead()
	{
		if (isset($this->query_results->url)
			&& $this->query_results->url !== null
			&& $this->query_results->url !== ''
		) {
			return;
		}

		if (isset($this->query_results->catalog_id)
			&& (int)$this->query_results->catalog_id > 0
		) {

		} else if (isset($this->query_results->catalog_type_id) && (int)$this->query_results->catalog_type_id > 0
			&& isset($this->query_results->id) && (int)$this->query_results->id > 0
		) {
			$this->query_results->catalog_id = Helpers::Catalog()->getID($this->query_results->catalog_type_id, $this->query_results->id);
		}

		if (isset($this->query_results->catalog_id)
			&& (int)$this->query_results->catalog_id > 0
		) {
			$this->query_results->url = Helpers::Catalog()->getURL($this->query_results->catalog_id);
			return;
		}

		return;
	}
}
