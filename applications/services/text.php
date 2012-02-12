<?php
/**
 * @package     Molajo
 * @subpackage  Service
 * @copyright   Copyright (C) 2012 Amy Stephen. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */
defined('MOLAJO') or die;
/**
 * Text
 *
 * @package     Molajo
 * @subpackage  Service
 * @since       1.0
 */
class MolajoTextService
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
            self::$instance = new MolajoTextService();
        }
        return self::$instance;
    }

    public function sprintf()
    {
        return Services::Language()->sprintf();
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
    function addLineBreaks($text)
    {
        return nl2br($text);
    }

    /**
     * replaceBuffer
     *
     * Change a value in the buffer
     *
     * @param  string $text
     * @return string
     * @since  1.0
     */
    function replaceBuffer($change_from, $change_to)
    {
        $buffer = preg_replace(
            $change_from,
            $change_to,
            Molajo::Responder()->getBody()
        );
        Molajo::Responder()->setBody($buffer);
    }

    /**
     * smilies
     *
     * change text smiley values into icons
     *
     * @param  string $text
     * @return string
     * @since  1.0
     */
    function smilies($text)
    {
        $smile = array(
            ':mrgreen:' => 'mrgreen.gif',
            ':neutral:' => 'neutral.gif',
            ':twisted:' => 'twisted.gif',
            ':arrow:' => 'arrow.gif',
            ':shock:' => 'eek.gif',
            ':smile:' => 'smile.gif',
            ':???:' => 'confused.gif',
            ':cool:' => 'cool.gif',
            ':evil:' => 'evil.gif',
            ':grin:' => 'biggrin.gif',
            ':idea:' => 'idea.gif',
            ':oops:' => 'redface.gif',
            ':razz:' => 'razz.gif',
            ':roll:' => 'rolleyes.gif',
            ':wink:' => 'wink.gif',
            ':cry:' => 'cry.gif',
            ':eek:' => 'surprised.gif',
            ':lol:' => 'lol.gif',
            ':mad:' => 'mad.gif',
            ':sad:' => 'sad.gif',
            '8-)' => 'cool.gif',
            '8-O' => 'eek.gif',
            ':-(' => 'sad.gif',
            ':-)' => 'smile.gif',
            ':-?' => 'confused.gif',
            ':-D' => 'biggrin.gif',
            ':-P' => 'razz.gif',
            ':-o' => 'surprised.gif',
            ':-x' => 'mad.gif',
            ':-|' => 'neutral.gif',
            ';-)' => 'wink.gif',
            '8)' => 'cool.gif',
            '8O' => 'eek.gif',
            ':(' => 'sad.gif',
            ':)' => 'smile.gif',
            ':?' => 'confused.gif',
            ':D' => 'biggrin.gif',
            ':P' => 'razz.gif',
            ':o' => 'surprised.gif',
            ':x' => 'mad.gif',
            ':|' => 'neutral.gif',
            ';)' => 'wink.gif',
            ':!:' => 'exclaim.gif',
            ':?:' => 'question.gif',
        );

        if (count($smile) > 0) {
            foreach ($smile as $key => $val) {
                $text = str_ireplace($key,
                    '<span><img src="' .
                        MOLAJO_SITE_MEDIA_URL .
                        'smiley/'
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
     * @param   $count
     * @param   array $options
     * @return  string
     * @since   1.0
     */
    function getPlaceHolderText($count, $options = array())
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
