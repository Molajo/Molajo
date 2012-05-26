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
	 * After-read processing
	 *
	 * splits the content_text field into intro and full text on readmore
	 *
	 * @return  boolean
	 * @since   1.0
	 */
	public function onAfterRead()
	{
		if (isset($this->query_results->content_text)) {
		} else {
			return false;
		}

		$pattern = '#<hr\s+id=("|\')system-readmore("|\')\s*\/*>#i';

		$tagPos = preg_match($pattern, $this->query_results->content_text);

		if ($tagPos == 0) {
			$introtext = $this->query_results->content_text;
			$fulltext = '';
		} else {
			list($introtext, $fulltext) = preg_split($pattern, $this->query_results->content_text, 2);
		}

		$this->query_results->introtext = $introtext;
		$this->query_results->fulltext = $fulltext;

		return true;
	}
}
