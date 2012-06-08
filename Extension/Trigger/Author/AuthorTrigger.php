<?php
/**
 * @package    Molajo
 * @copyright  2012 Amy Stephen. All rights reserved.
 * @license    GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
namespace Molajo\Extension\Trigger\Author;

use Molajo\Extension\Trigger\Content\ContentTrigger;
use Molajo\Service\Services;

defined('MOLAJO') or die;

/**
 * Author
 *
 * @package     Molajo
 * @subpackage  Trigger
 * @since       1.0
 */
class AuthorTrigger extends ContentTrigger
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
			self::$instance = new AuthorTrigger();
		}

		return self::$instance;
	}

	/**
	 * After-read processing
	 *
	 * Retrieves Author Information for Item
	 *
	 * @return boolean
	 * @since   1.0
	 */
	public function onAfterRead()
	{
		/** Retrieve created_by field definition */
		$field = $this->getField('created_by');
		if ($field == false) {
			echo 'nope in <br />';
			return;
		}

		/** Retrieve the current value for created by field */
		$fieldValue = $this->getFieldValue($field);
		if ((int)$fieldValue == 0) {
			return;
		}

		/** Author information already available */
		if (Services::Registry()->exists('Trigger', 'Author' . $fieldValue)) {

			$item = Services::Registry()->get('Trigger', 'Author' . $fieldValue);

			foreach ($item[0] as $key => $value) {
				$new_field_name = $key;
				$this->saveField($field, $new_field_name, $value);
			}
			return true;
		}

		/** Using the created_by value, retrieve the Author Profile Data */
		$controllerClass = 'Molajo\\MVC\\Controller\\ModelController';
		$m = new $controllerClass();
		$results = $m->connect('Users');
		if ($results == false) {
			return false;
		}

		$m->set('id', (int)$fieldValue);
		$m->set('get_item_children', 0);

		$item = $m->getData('item');

		if ($item == false || count($item) == 0) {
			return false;
		}

		$authorArray = array();
		$row = new \stdClass();

		/** Save each field */
		foreach (get_object_vars($item) as $key => $value) {

			if (substr($key, 0, strlen('item_')) == 'item_'
				|| substr($key, 0, strlen('form_')) == 'form_'
				|| substr($key, 0, strlen('list_')) == 'list_'
				|| substr($key, 0, strlen('password')) == 'password') {

			} else {

				$new_field_name = 'author' . '_' . $key;
				$this->saveField($field, $new_field_name, $value);

				$row->$new_field_name = $value;
			}

			$authorArray[] = $row;
		}

		/** Save Trigger Data */
		Services::Registry()->set('Trigger', 'Author' . $fieldValue, $authorArray);
		Services::Registry()->set('Trigger', 'Author', $authorArray);

		return true;
	}
}
