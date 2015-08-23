<?php
/**
 * Smilies Plugin
 *
 * @package    Molajo
 * @copyright  2014-2015 Amy Stephen. All rights reserved.
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 */
namespace Molajo\Plugins\Smilies;

use CommonApi\Event\ReadEventInterface;
use Molajo\Plugins\ReadEvent;

/**
 * Smilies Plugin
 *
 * @package     Molajo
 * @license     http://www.opensource.org/licenses/mit-license.html MIT License
 * @since       1.0
 */
final class SmiliesPlugin extends ReadEvent implements ReadEventInterface
{
    /**
     * Replaces text with emotion images
     *
     * @return  $this
     * @since   1.0.0
     */
    public function onAfterReadRow()
    {
        if ($this->existFields('html') === false) {
            return $this;
        }

        $this->processFieldsByType('processSmilies', $this->hold_fields);

        return $this;
    }

    /**
     * Process Smilies
     *
     * @param   array $field
     *
     * @return  array
     * @since   1.0.0
     */
    protected function processSmilies(array $field = array())
    {
        $value       = $this->getFieldValue($field);
        $after_value = $this->smilies($value);

        if ($value === $after_value) {
            return $field;
        }

        $field['value'] = $after_value;

        return $field;
    }

    /**
     * Smilies - change text smiley values into icons
     *
     * @param  string $text
     *
     * @return string
     * @since  1.0.0
     */
    protected function smilies($text)
    {
        $smile = array(
            ':mrgreen:' => 'icon_mrgreen.gif',
            ':neutral:' => 'icon_neutral.gif',
            ':twisted:' => 'icon_twisted.gif',
            ':arrow:'   => 'icon_arrow.gif',
            ':shock:'   => 'icon_eek.gif',
            ':smile:'   => 'icon_smile.gif',
            ':???:'     => 'icon_confused.gif',
            ':cool:'    => 'icon_cool.gif',
            ':evil:'    => 'icon_evil.gif',
            ':grin:'    => 'icon_biggrin.gif',
            ':idea:'    => 'icon_idea.gif',
            ':oops:'    => 'icon_redface.gif',
            ':razz:'    => 'icon_razz.gif',
            ':roll:'    => 'icon_rolleyes.gif',
            ':wink:'    => 'icon_wink.gif',
            ':cry:'     => 'icon_cry.gif',
            ':eek:'     => 'icon_surprised.gif',
            ':lol:'     => 'icon_lol.gif',
            ':mad:'     => 'icon_mad.gif',
            ':sad:'     => 'icon_sad.gif',
            '8-)'       => 'icon_cool.gif',
            '8-O'       => 'icon_eek.gif',
            ':-('       => 'icon_sad.gif',
            ':-)'       => 'icon_smile.gif',
            ':-?'       => 'icon_confused.gif',
            ':-D'       => 'icon_biggrin.gif',
            ':-P'       => 'icon_razz.gif',
            ':-o'       => 'icon_surprised.gif',
            ':-x'       => 'icon_mad.gif',
            ':-|'       => 'icon_neutral.gif',
            ';-)'       => 'icon_wink.gif',
            '8)'        => 'icon_cool.gif',
            '8O'        => 'icon_eek.gif',
            ':('        => 'icon_sad.gif',
            ':)'        => 'icon_smile.gif',
            ':?'        => 'icon_confused.gif',
            ':D'        => 'icon_biggrin.gif',
            ':P'        => 'icon_razz.gif',
            ':o'        => 'icon_surprised.gif',
            ':x'        => 'icon_mad.gif',
            ':|'        => 'icon_neutral.gif',
            ';)'        => 'icon_wink.gif',
            ':!:'       => 'icon_exclaim.gif',
            ':?:'       => 'icon_question.gif',
        );

        if (count($smile) > 0) {

            foreach ($smile as $replace_this => $value) {
                $path = '/Media/smilies/';
                $path .= $value;
                $with_this = '<span><img src="' . $path . '" alt="' . $value . '" class="smiley-class" /></span>';
                $text      = str_ireplace($replace_this, $with_this, $text);
            }
        }

        return $text;
    }
}
