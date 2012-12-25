<?php
/**
 * @package    Niambie
 * @copyright  2013 Amy Stephen. All rights reserved.
 * @license    MIT
 */
namespace Molajo\Plugin\Smilies;

use Molajo\Plugin\Plugin\Plugin;
use Molajo\Service\Services;

defined('NIAMBIE') or die;

/**
 * @package     Niambie
 * @license     MIT
 * @since       1.0
 */
class SmiliesPlugin extends Plugin
{
    /**
     * Replaces text with emotion images
     *
     * @return boolean
     * @since   1.0
     */
    public function onAfterRead()
    {
        if (defined('ROUTE')) {
        } else {
            return true;
        }

        $fields = $this->retrieveFieldsByType('text');

        if (is_array($fields) && count($fields) > 0) {

            foreach ($fields as $field) {

                $name = $field['name'];

                $fieldValue = $this->getFieldValue($field);

                if ($fieldValue === false) {
                } else {

                    $value = $this->smilies($fieldValue);

                    if ($value === false) {
                    } else {

                        $this->saveField($field, $name, $value);
                    }
                }

            }
        }

        return true;
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

        if (Services::Registry()->get(CONFIGURATION_LITERAL, 'url_force_ssl', 0) > 0) {
            $protocol = 'https://';
        } else {
            $protocol = 'http://';
        }

        if (count($smile) > 0) {
            foreach ($smile as $key => $val) {
                $url = $protocol . SITE_MEDIA_URL . '/smilies/' . $val;
                $smiley = '<span><img src="' . $url . '" alt="smiley" class="smiley-class" /></span>';
                $text = str_ireplace($key, $smiley, $text);
            }
        }

        return $text;
    }
}
