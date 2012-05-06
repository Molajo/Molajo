<?php
/**
 * @package    Molajo
 * @copyright  2012 Amy Stephen. All rights reserved.
 * @license    GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
namespace Molajo\Extension\Trigger\Snippet;

use Molajo\Extension\Trigger\Content\ContentTrigger;

defined('MOLAJO') or die;

/**
 * Snippet
 *
 * @package     Molajo
 * @subpackage  Trigger
 * @since       1.0
 */
class SnippetTrigger extends ContentTrigger
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
			self::$instance = new SnippetTrigger();
		}
		return self::$instance;
	}

	/**
	 * After-read processing
	 *
	 * Parses the Content Text into a snippet, stripped of HTML tags
	 *
	 * @param   $data
	 * @param   $model
	 *
	 * @return  $data
	 * @since   1.0
	 */
	public function onAfterRead($data, $model)
	{
		if (isset($data->content_text)) {
		} else {
			$data->snippet = '';
			return $data;
		}

		$data->snippet =
			substr(
				strip_tags($data->content_text),
				0,
				Services::getRegistry()->get('ExtensionParameters', 'snippet_length', 200)
			);

		return $data;
	}
}
