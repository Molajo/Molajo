<?php
/**
 * Smilies Plugin
 *
 * @package    Molajo
 * @copyright  2014 Amy Stephen. All rights reserved.
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 */
namespace Molajo\Plugins\Smilies;

use CommonApi\Event\ReadInterface;
use Molajo\Plugins\ReadEventPlugin;

/**
 * Smilies Plugin
 *
 * @package     Molajo
 * @license     http://www.opensource.org/licenses/mit-license.html MIT License
 * @since       1.0
 */
class SmiliesPlugin extends ReadEventPlugin implements ReadInterface
{
    /**
     * Replaces text with emotion images
     *
     * @return  $this
     * @since   1.0
     */
    public function onAfterRead()
    {
        if (isset($this->runtime_data->route)) {
        } else {
            return $this;
        }

        $fields = $this->getFieldsByType('text');

        if (is_array($fields) && count($fields) > 0) {

            foreach ($fields as $field) {

                $name = $field['name'];

                $fieldValue = $this->getFieldValue($field);

                if ($fieldValue === false) {
                } else {

                    $value = $this->smilies($fieldValue);

                    if ($value === false) {
                    } else {

                        $this->setField($field, $name, $value);
                    }
                }
            }
        }

        return $this;
    }

    /**
     * smilies - change text smiley values into icons
     *
     * @param  string $text
     *
     * @return string
     * @since  1.0
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

        if ($this->runtime_data->application->parameters->url_force_ssl > 0) {
            $protocol = 'https://';
        } else {
            $protocol = 'http://';
        }

        if (count($smile) > 0) {
            foreach ($smile as $key => $val) {
                $url    = $this->runtime_data->application->base_url . 'smilies/' . $val;
                $smiley = '<span><img src="' . $url . '" alt="smiley" class="smiley-class" /></span>';
                $text   = str_ireplace($key, $smiley, $text);
            }
        }

        return $text;
    }
}
