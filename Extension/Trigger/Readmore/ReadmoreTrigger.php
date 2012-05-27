<?php
/**
 * @package    Molajo
 * @copyright  2012 Amy Stephen. All rights reserved.
 * @license    GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
namespace Molajo\Extension\Trigger\Readmore;

use Molajo\Extension\Trigger\Content\ContentTrigger;

use Molajo\Service\Services;

defined('MOLAJO') or die;

/**
 * Content Text
 *
 * @package     Molajo
 * @subpackage  Trigger
 * @since       1.0
 */
class ReadmoreTrigger extends ContentTrigger
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
			self::$instance = new ReadmoreTrigger();
		}
		return self::$instance;
	}

	/**
	 * After-read processing
	 *
	 * splits the content_text field into intro and full text on readmore
	 *
	 * @return  boolean
	 * @since   1.0
	 */
	public function onAfterRead()
	{
		$fields = $this->retrieveFieldsByType('text');
echo '<pre>';
var_dump($fields);
		die;
//todo deal with custom field names !

		if (is_array($fields) && count($fields) > 0) {

			foreach ($fields as $field) {

				$name = $field->name;

				$results = Services::Text()->splitReadMoreText($this->query_results->$name);

				if ($results == false) {
				} else {
					$newname = $name . '_' . 'introductory';
					$this->query_results->$newname = $results[0];
					$newname = $name . '_' . 'fulltext';
					$this->query_results->$newname = $results[1];
				}
				echo '<pre>';
				var_dump($this->query_results);
				die;
			}
		}

		return true;
	}
}
