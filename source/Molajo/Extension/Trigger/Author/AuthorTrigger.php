<?php
/**
 * @package    Molajo
 * @copyright  2012 Amy Stephen. All rights reserved.
 * @license    GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
namespace Molajo\Extension\Trigger\Author;

use Molajo\Application;
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
	 * @return  boolean
	 * @since   1.0
	 */
	public function onAfterRead()
	{
		if (isset($this->query_results->created_by)
			&& (int)$this->query_results->created_by > 0
		) {
		} else {
			return false;
		}

		/** Get Author Profile Data */
		$m = Application::Controller()->connect('Users', 'Table');

		$m->model->set('id', $this->query_results->created_by);
		$m->model->set('get_customfields', 2);
		$m->model->set('get_item_children', 0);
		$m->model->set('check_view_level_access', 1);
		$m->model->set('check_published', 0);

		$results = $m->getData('load');

		if ($results == false) {
			return false;
		}

		/** Add new fields to query_results */
		$first_name = '';
		$last_name = '';

		while (list($name, $value) = each($results)) {

			if ($name == 'first_name') {
				$first_name = $value;
			}
			if ($name == 'last_name') {
				$last_name = $value;
			}

			if (substr($name, 0, strlen('item_')) == 'item_'
				|| substr($name, 0, strlen('form_')) == 'form_'
				|| substr($name, 0, strlen('list_')) == 'list_'
				|| substr($name, 0, strlen('password')) == 'password') {
			} else {
				$field = 'author_' . strtolower($name);
				$this->query_results->$field = $value;
			}
		}

		$this->query_results->author_full_name = $first_name . ' ' . $last_name;

		if (isset($this->query_results->author_email)
			&& $this->query_results->author_email !== ''
		) {
			$results = Services::Url()->obfuscateEmail($this->query_results->author_email);
			if ($results == false) {
			} else {
				$this->query_results->author_obfuscate_email = $results;
			}

			if (Services::Registry()->get('Parameters', 'criteria_use_gravatar', 0) == 1) {

				$size = Services::Registry()->get('Parameters', 'criteria_gravatar_size', 80);
				$type = Services::Registry()->get('Parameters', 'criteria_gravatar_type', 'mm');
				$rating = Services::Registry()->get('Parameters', 'criteria_gravatar_rating', 'pg');
				$image = Services::Registry()->get('Parameters', 'criteria_gravatar_image', 0);

				$this->query_results->author_gravatar = Services::Url()->getGravatar(
					$this->query_results->author_email,
					$size,
					$type,
					$rating,
					$image
				);
			}
		}

		return true;
	}
}
