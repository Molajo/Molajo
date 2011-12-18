<?php
/**
 * @package     Molajo
 * @subpackage  Helper
 * @copyright   Copyright (C) 2012 Amy Stephen. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */
defined('MOLAJO') or die;

/**
 * Text Helper
 *
 * @package     Molajo
 * @subpackage  Text Helper
 * @since       1.0
 */
class MolajoTextHelper
{
    /**
     * javascript strings
     */
    protected static $strings = array();

    /**
     * Translates a string into the current language.
     *
     * Examples:
     * <script>alert(Joomla.MolajoTextHelper._('<?php echo MolajoTextHelper::_("JDEFAULT", array("script"=>true));?>'));</script> will generate an alert message containing 'Default'
     * <?php echo MolajoTextHelper::_("JDEFAULT");?> it will generate a 'Default' string
     *
     * @param   string         The string to translate.
     * @param   boolean|array  boolean: Make the result javascript safe. array an array of option as described in the MolajoTextHelper::sprintf function
     * @param   boolean        To interpret backslashes (\\=\, \n=carriage return, \t=tabulation)
     * @param   boolean        To indicate that the string will be push in the javascript language store
     *
     * @return  string  The translated string or the key is $script is true
     *
     * @since   11.1
     *
     */
    public static function _($string, $jsSafe = false, $interpretBackSlashes = true, $script = false)
    {
        $lang = MolajoFactory::getLanguage();
        if (is_array($jsSafe)) {
            if (array_key_exists('interpretBackSlashes', $jsSafe)) {
                $interpretBackSlashes = (boolean)$jsSafe['interpretBackSlashes'];
            }
            if (array_key_exists('script', $jsSafe)) {
                $script = (boolean)$jsSafe['script'];
            }
            if (array_key_exists('jsSafe', $jsSafe)) {
                $jsSafe = (boolean)$jsSafe['jsSafe'];
            }
            else {
                $jsSafe = false;
            }
        }
        if ($script) {
            self::$strings[$string] = $lang->_($string, $jsSafe, $interpretBackSlashes);
            return $string;
        }
        else {
            return $lang->_($string, $jsSafe, $interpretBackSlashes);
        }
    }

    /**
     * Translates a string into the current language.
     *
     * Examples:
     * <?php echo MolajoTextHelper::alt("JALL","language");?> it will generate a 'All' string in English but a "Toutes" string in French
     * <?php echo MolajoTextHelper::alt("JALL","module");?> it will generate a 'All' string in English but a "Tous" string in French
     *
     * @param   string         The string to translate.
     * @param   string         The alternate option for global string
     * @param   boolean|array  boolean: Make the result javascript safe. array an array of option as described in the MolajoTextHelper::sprintf function
     * @param   boolean        To interpret backslashes (\\=\, \n=carriage return, \t=tabulation)
     * @param   boolean        To indicate that the string will be pushed in the javascript language store
     *
     * @return  string  The translated string or the key if $script is true
     *
     * @since   11.1
     *
     */
    public static function alt($string, $alt, $jsSafe = false, $interpretBackSlashes = true, $script = false)
    {
        $lang = MolajoFactory::getLanguage();
        if ($lang->hasKey($string . '_' . $alt)) {
            return self::_($string . '_' . $alt, $jsSafe, $interpretBackSlashes);
        }
        else {
            return self::_($string, $jsSafe, $interpretBackSlashes);
        }
    }

    /**
     * Like MolajoTextHelper::sprintf but tries to pluralise the string.
     *
     * Examples:
     * <script>alert(Joomla.MolajoTextHelper._('<?php echo MolajoTextHelper::plural("PLUGINS_N_ITEMS_UNPUBLISHED", 1, array("script"=>true));?>'));</script> will generate an alert message containing '1 plugin successfully disabled'
     * <?php echo MolajoTextHelper::plural("PLUGINS_N_ITEMS_UNPUBLISHED", 1);?> it will generate a '1 plugin successfully disabled' string
     *
     * @param   string   The format string.
     * @param   integer  The number of items
     * @param   mixed    Mixed number of arguments for the sprintf function. The first should be an integer.
     * @param   array    optional Array of option array('jsSafe'=>boolean, 'interpretBackSlashes'=>boolean, 'script'=>boolean) where
     *                    -jsSafe is a boolean to generate a javascript safe string
     *                    -interpretBackSlashes is a boolean to interpret backslashes \\->\, \n->new line, \t->tabulation
     *                    -script is a boolean to indicate that the string will be push in the javascript language store
     *
     * @return  string  The translated strings or the key if 'script' is true in the array of options
     *
     * @since   11.1
     */

    public static function plural($string, $n)
    {
        $lang = MolajoFactory::getLanguage();
        $args = func_get_args();
        $count = count($args);

        if ($count > 1) {
            // Try the key from the language plural potential suffixes
            $found = false;
            $suffixes = $lang->getPluralSuffixes((int)$n);
            foreach ($suffixes as $suffix) {
                $key = $string . '_' . $suffix;
                if ($lang->hasKey($key)) {
                    $found = true;
                    break;
                }
            }
            if (!$found) {
                // Not found so revert to the original.
                $key = $string;
            }
            if (is_array($args[$count - 1])) {
                $args[0] = $lang->_($key, array_key_exists('jsSafe', $args[$count - 1]) ? $args[$count - 1]['jsSafe']
                                                : false, array_key_exists('interpretBackSlashes', $args[$count - 1])
                                                ? $args[$count - 1]['interpretBackSlashes'] : true);
                if (array_key_exists('script', $args[$count - 1]) && $args[$count - 1]['script']) {
                    self::$strings[$key] = call_user_func_array('sprintf', $args);
                    return $key;
                }
            }
            else {
                $args[0] = $lang->_($key);
            }
            return call_user_func_array('sprintf', $args);
        }
        elseif ($count > 0) {

            // Default to the normal sprintf handling.
            $args[0] = $lang->_($string);
            return call_user_func_array('sprintf', $args);
        }

        return '';
    }

    /**
     * Passes a string thru a sprintf.
     *
     * @param   string  The format string.
     * @param   mixed   Mixed number of arguments for the sprintf function.
     * @param   array   optional Array of option array('jsSafe'=>boolean, 'interpretBackSlashes'=>boolean, 'script'=>boolean) where
     *                    -jsSafe is a boolean to generate a javascript safe strings
     *                    -interpretBackSlashes is a boolean to interpret backslashes \\->\, \n->new line, \t->tabulation
     *                    -script is a boolean to indicate that the string will be push in the javascript language store
     *
     * @return  string  The translated strings or the key if 'script' is true in the array of options
     *
     * @since   11.1
     */
    public static function sprintf($string)
    {
        $lang = MolajoFactory::getLanguage();
        $args = func_get_args();
        $count = count($args);
        if ($count > 0) {
            if (is_array($args[$count - 1])) {
                $args[0] = $lang->_($string, array_key_exists('jsSafe', $args[$count - 1]) ? $args[$count - 1]['jsSafe']
                                                   : false, array_key_exists('interpretBackSlashes', $args[$count - 1])
                                                   ? $args[$count - 1]['interpretBackSlashes'] : true);
                if (array_key_exists('script', $args[$count - 1]) && $args[$count - 1]['script']) {
                    self::$strings[$string] = call_user_func_array('sprintf', $args);
                    return $string;
                }
            }
            else {
                $args[0] = $lang->_($string);
            }
            return call_user_func_array('sprintf', $args);
        }
        return '';
    }

    /**
     * Passes a string thru an printf.
     *
     * @param   format The format string.
     * @param   mixed Mixed number of arguments for the sprintf function.
     *
     * @return  mixed
     *
     * @since   11.1
     */
    public static function printf($string)
    {
        $lang = MolajoFactory::getLanguage();
        $args = func_get_args();
        $count = count($args);
        if ($count > 0) {
            if (is_array($args[$count - 1])) {
                $args[0] = $lang->_($string, array_key_exists('jsSafe', $args[$count - 1]) ? $args[$count - 1]['jsSafe']
                                                   : false, array_key_exists('interpretBackSlashes', $args[$count - 1])
                                                   ? $args[$count - 1]['interpretBackSlashes'] : true);
            }
            else {
                $args[0] = $lang->_($string);
            }
            return call_user_func_array('printf', $args);
        }
        return '';
    }

    /**
     * Translate a string into the current language and stores it in the JavaScript language store.
     *
     * @param   string   The MolajoTextHelper key.
     *
     * @since   11.1
     */
    public static function script($string = null, $jsSafe = false, $interpretBackSlashes = true)
    {
        if (is_array($jsSafe)) {
            if (array_key_exists('interpretBackSlashes', $jsSafe)) {
                $interpretBackSlashes = (boolean)$jsSafe['interpretBackSlashes'];
            }
            if (array_key_exists('jsSafe', $jsSafe)) {
                $jsSafe = (boolean)$jsSafe['jsSafe'];
            }
            else {
                $jsSafe = false;
            }
        }

        // Add the string to the array if not null.
        if ($string !== null) {
            // Normalize the key and translate the string.
            self::$strings[strtoupper($string)] = MolajoFactory::getLanguage()->_($string, $jsSafe, $interpretBackSlashes);
        }

        return self::$strings;
    }

    /**
     * addLineBreaks - changes line breaks to br tags
     * @param string $text
     * @return string
     */
    function addLineBreaks($text)
    {
        return nl2br($text);
    }

    /**
     * replaceBuffer - change a value in the buffer
     * @param string $text
     * @return string
     */
    function replaceBuffer($change_from, $change_to)
    {
        $buffer = MolajoFactory::getApplication()->getBody();
        $buffer = preg_replace($change_from, $change_to, $buffer);
        MolajoFactory::getApplication()->setBody($buffer);
    }

    /**
     * smilies - change text smiley values into icons - smilie list from WordPress - Thank you, WordPress! :)
     * @param string $text
     * @return string
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
                $text = JString::str_ireplace($key, '<span><img src="' . JURI::base() . 'media/images/smiley/' . $val . '" alt="smiley" class="smiley-class" /></span>', $text);
            }
        }
        return $text;
    }


  function getPlaceHolderImage($width, $height, $options = array()) {

    $services_class = array(
      'placehold' => 'PlaceholdImage',
      'lorem_pixel' => 'LoremPixelImage'
    );

    $service = $options['service'];
    $service = isset($service) ? $service : 'placehold';

    $service_class = $services_class[$service];
    if (class_exists($service_class)) {
      $service = new $service_class($width, $height, $options);
      return $service->url();
    } else {
      render_error("No placeholder image service called #{$service} exists!");
    }
  }


  function getPlaceHolderText($count, $options = array()) {
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
