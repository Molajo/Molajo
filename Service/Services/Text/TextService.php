<?php
/**
 * @package   Molajo
 * @copyright 2012 Amy Stephen. All rights reserved.
 * @license   GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
namespace Molajo\Service\Services\Text;

use LoremIpsumGenerator\LoremIpsumGenerator;
use Molajo\Service\Services;

defined('MOLAJO') or die;

/**
 * Text
 *
 * @package     Molajo
 * @subpackage  Service
 * @since       1.0
 */
Class TextService
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
            self::$instance = new TextService();
        }

        return self::$instance;
    }

    /**
     * addLineBreaks
     *
     * changes line breaks to br tags
     *
     * @param  string $text
     * @return string
     * @since  1.0
     */
    public function addLineBreaks($text)
    {
        return nl2br($text);
    }

    /**
     * replaceBuffer
	 *
	 * todo: add event after rendering and change this approach
     *
     * Change a value in the buffer
     *
     * @param  string $text
     * @return string
     * @since  1.0
     */
    public function replaceBuffer($change_from, $change_to)
    {
        $buffer = preg_replace(
            $change_from,
            $change_to,
            Services::Response()->getBody()
        );
        Services::Response()->setContent($buffer);
    }

    /**
     * splitReadMoreText - search for the system-readmore break and split the text at that point into two text fields
     *
     * @param  $text
     *
     * @return array
     * @since   1.0
     */
    public function splitReadMoreText ($text)
    {
		$pattern = '#{readmore}#';

        $tagPos = preg_match($pattern, $text);

        $introductory_text = '';
        $fulltext = '';

        if ($tagPos == 0) {
            $introductory_text = $text;
        } else {
            list($introductory_text, $fulltext) = preg_split($pattern, $text, 2);
        }

        return (array($introductory_text, $fulltext));
    }

	/**
	 * pullquotes - searches for and returns pullquotes
	 *
	 * @param  $text
	 *
	 * @return array
	 * @since   1.0
	 */
	public function pullquotes ($text)
	{
		$pattern = '/{pullquote}(.*){\/pullquote}/';

		preg_match_all($pattern, $text, $matches);

		$pullquote = array();
		if (count($matches) == 0) {
		} else {

			/** add wrap for each */
			foreach ($matches[1] as $match) {
				$temp = strip_tags($match);
				if (trim($temp) == '') {
				} else {
					$pullquote[] = $temp;
				}
			}
		}

		$text = str_replace($matches[0], $matches[1], $text);

		return array($pullquote, $text);
	}

	/**
	 * snippet - strip HTML and return a short value of text field
	 *
	 * @param  $text
	 *
	 * @return array
	 * @since   1.0
	 */
	public function snippet ($text)
	{
		return substr(strip_tags($text), 0, Services::Registry()->get('Parameters', 'criteria_snippet_length', 200));
	}

    /**
     * smilies - change text smiley values into icons
     *
     * @param  string $text
     * @return string
     * @since  1.0
     */
    public function smilies($text)
    {
        $smile = array(
            ':mrgreen:' => 'icon_mrgreen.gif',
            ':neutral:' => 'icon_neutral.gif',
            ':twisted:' => 'icon_twisted.gif',
            ':arrow:' => 'icon_arrow.gif',
            ':shock:' => 'icon_eek.gif',
            ':smile:' => 'icon_smile.gif',
            ':???:' => 'icon_confused.gif',
            ':cool:' => 'icon_cool.gif',
            ':evil:' => 'icon_evil.gif',
            ':grin:' => 'icon_biggrin.gif',
            ':idea:' => 'icon_idea.gif',
            ':oops:' => 'icon_redface.gif',
            ':razz:' => 'icon_razz.gif',
            ':roll:' => 'icon_rolleyes.gif',
            ':wink:' => 'icon_wink.gif',
            ':cry:' => 'icon_cry.gif',
            ':eek:' => 'icon_surprised.gif',
            ':lol:' => 'icon_lol.gif',
            ':mad:' => 'icon_mad.gif',
            ':sad:' => 'icon_sad.gif',
            '8-)' => 'icon_cool.gif',
            '8-O' => 'icon_eek.gif',
            ':-(' => 'icon_sad.gif',
            ':-)' => 'icon_smile.gif',
            ':-?' => 'icon_confused.gif',
            ':-D' => 'icon_biggrin.gif',
            ':-P' => 'icon_razz.gif',
            ':-o' => 'icon_surprised.gif',
            ':-x' => 'icon_mad.gif',
            ':-|' => 'icon_neutral.gif',
            ';-)' => 'icon_wink.gif',
            '8)' => 'icon_cool.gif',
            '8O' => 'icon_eek.gif',
            ':(' => 'icon_sad.gif',
            ':)' => 'icon_smile.gif',
            ':?' => 'icon_confused.gif',
            ':D' => 'icon_biggrin.gif',
            ':P' => 'icon_razz.gif',
            ':o' => 'icon_surprised.gif',
            ':x' => 'icon_mad.gif',
            ':|' => 'icon_neutral.gif',
            ';)' => 'icon_wink.gif',
            ':!:' => 'icon_exclaim.gif',
            ':?:' => 'icon_question.gif',
        );

        if (count($smile) > 0) {
            foreach ($smile as $key => $val) {
                $text = str_ireplace($key,
                    '<span><img src="' . SITES_MEDIA_URL . '/images/smilies/'
                        . $val
                        . '" alt="smiley" class="smiley-class" /></span>',
                    $text);
            }
        }

        return $text;
    }

    /**
     * getPlaceHolderText
     *
     * @param         $count
     * @param  array  $options
     * @return string
     * @since   1.0
     */
    public function getPlaceHolderText($count, $options = array())
    {
        $options = array_merge(
            array(
                'html' => false,
                'lorem' => true
            ),
            $options
        );

        $generator = new LoremIpsumGenerator;

        $html_format = $options['html'] ? 'plain' : 'html';
        $start_with_lorem_ipsum = $options['lorem'];

        return ucfirst($generator->getContent($count, $html_format, $start_with_lorem_ipsum));
    }
}
