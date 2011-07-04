<?php
/**
 * @package     Molajo
 * @subpackage  Text Helper
 * @copyright   Copyright (C) 2011 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die();

class MolajoTextHelper   {

    /**
     * addLineBreaks - changes line breaks to br tags
     * @param string $text
     * @return string
     */
    function addLineBreaks ($text) {
        return nl2br($text);
    }
    
    /**
     * replaceBuffer - change a value in the buffer
     * @param string $text
     * @return string
     */
    function replaceBuffer ($change_from, $change_to) {
        $buffer = JResponse::getBody();
        $buffer = preg_replace( $change_from, $change_to, $buffer );
        JResponse::setBody($buffer);
    }

    /**
     * smilies - change text smiley values into icons - smilie list from WordPress - Thank you, WordPress! :)
     * @param string $text
     * @return string
     */
    function smilies ($text) {
        
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

        if (count($smile) > 0 ) {
            foreach ( $smile as $key => $val )   {
                $text = JString::str_ireplace ($key, '<span><img src="'. JURI::base().'media/molajo/images/smiley/'.$val.'" alt="smiley" class="smiley-class" /></span>', $text);
            }
        }
        return $text;
    }
}