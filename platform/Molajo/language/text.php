<?php
/**
 * @package     Molajo
 * @subpackage  Application
 * @copyright   Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @copyright   Copyright (C) 2011 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die;

/**
 * Text  handling class.
 *
 * @package     Joomla.Platform
 * @subpackage  Language
 * @since       11.1
 */
class MolajoText
{
    /**
     * javascript strings
     */
    protected static $strings = array();

    /**
     * Translates a string into the current language.
     *
     * Examples:
     * <script>alert(Joomla.MolajoText._('<?php echo MolajoText::_("JDEFAULT", array("script"=>true));?>'));</script> will generate an alert message containing 'Default'
     * <?php echo MolajoText::_("JDEFAULT");?> it will generate a 'Default' string
     *
     * @param   string         The string to translate.
     * @param   boolean|array  boolean: Make the result javascript safe. array an array of option as described in the MolajoText::sprintf function
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
     * <?php echo MolajoText::alt("JALL","language");?> it will generate a 'All' string in English but a "Toutes" string in French
     * <?php echo MolajoText::alt("JALL","module");?> it will generate a 'All' string in English but a "Tous" string in French
     *
     * @param   string         The string to translate.
     * @param   string         The alternate option for global string
     * @param   boolean|array  boolean: Make the result javascript safe. array an array of option as described in the MolajoText::sprintf function
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
        if ($lang->hasKey($string.'_'.$alt)) {
            return self::_($string.'_'.$alt, $jsSafe, $interpretBackSlashes);
        }
        else {
            return self::_($string, $jsSafe, $interpretBackSlashes);
        }
    }

    /**
     * Like MolajoText::sprintf but tries to pluralise the string.
     *
     * Examples:
     * <script>alert(Joomla.MolajoText._('<?php echo MolajoText::plural("PLUGINS_N_ITEMS_UNPUBLISHED", 1, array("script"=>true));?>'));</script> will generate an alert message containing '1 plugin successfully disabled'
     * <?php echo MolajoText::plural("PLUGINS_N_ITEMS_UNPUBLISHED", 1);?> it will generate a '1 plugin successfully disabled' string
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
                $key = $string.'_'.$suffix;
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
     * @param   string   The MolajoText key.
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
}

class JText extends MolajoText {}