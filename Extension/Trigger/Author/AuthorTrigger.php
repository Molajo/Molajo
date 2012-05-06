<?php
/**
 * @package    Molajo
 * @copyright  2012 Amy Stephen. All rights reserved.
 * @license    GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
namespace Molajo\Extension\Trigger\Author;

use Molajo\Extension\Trigger\Content\ContentTrigger;

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
	 * @param   $data
	 * @param   $model
	 *
	 * @return  $data
	 * @since   1.0
	 */
	public function onAfterRead($data, $model)
	{
		if (isset($data->created_by)
			&& (int)$data->created_by > 0
		) {
		} else {
			return;
		}

		$m = Services::Model()->connect('Users');

		$m->model->set('id', $data->created_by);

		$m->model->set('get_special_fields', 2);
		$m->model->set('use_special_joins', false);
		$m->model->set('add_acl_check', false);
		$m->model->set('get_item_children', false);

		$results = $m->execute('load');

		if ($results == false) {
			return;
		}

		$first_name = '';
		$last_name = '';

		while (list($name, $value) = each($results)) {
			if ($name == 'first_name') {
				$first_name = $value;
			}
			if ($name == 'last_name') {
				$last_name = $value;
			}
			$field = 'author_' . $name;
			$data->$field = $value;
		}

		$data->author_full_name = $first_name . ' ' . $last_name;

		if (isset($data->author_email)
			&& $data->author_email !== ''
		) {

			$size = Services::Registry()->get('ExtensionParameters', 'gravatar_size', 80);
			$type = Services::Registry()->get('ExtensionParameters', 'gravatar_type', 'mm');
			$rating = Services::Registry()->get('ExtensionParameters', 'gravatar_rating', 'pg');
			$image = Services::Registry()->get('ExtensionParameters', 'gravatar_image', 0);

			$data->author_gravatar = Services::URL()->getGravatar(
				$data->author_email,
				$size,
				$type,
				$rating,
				$image
			);
		}

		return $data;
	}
}
