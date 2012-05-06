<?php
/**
 * @package    Molajo
 * @copyright  2012 Amy Stephen. All rights reserved.
 * @license    GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
namespace Molajo\Extension\Trigger\ContentText;

use Molajo\Extension\Trigger\Content\ContentTrigger;

defined('MOLAJO') or die;

/**
 * Content Text
 *
 * @package     Molajo
 * @subpackage  Trigger
 * @since       1.0
 */
class ContentTextTrigger extends ContentTrigger
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
			self::$instance = new ContentTextTrigger();
		}
		return self::$instance;
	}

    /**
     * Pre-read processing
	 *
	 * splits the content_text field into intro and full text on readmore
     *
     * @param   $data
     * @param   $model
     *
     * @return  $data
     * @since   1.0
     */
    public function onBeforeRead($data, $model)
    {
		if (isset($data->content_text)) {
		} else {
			$data->introtext = '';
			$data->fulltext = '';
			return $data;
		}

		$pattern = '#<hr\s+id=("|\')system-readmore("|\')\s*\/*>#i';

		$tagPos = preg_match($pattern, $data->content_text);

		if ($tagPos == 0) {
			$introtext = $data->content_text;
			$fulltext = '';
		} else {
			list($introtext, $fulltext) = preg_split($pattern, $data->content_text, 2);
		}

		$data->introtext = $introtext;
		$data->fulltext = $fulltext;

		return $data;
    }
}
