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
     * javascript strings
     */
    protected static $strings = array();

    /**
     * @var array
     */
    protected $_language = array();

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

    /**
     * __construct
     *
     * Class constructor.
     *
     * @return boolean
     * @since  1.0
     */
    public function __construct()
    {
    }

    public function connect()
    {
        $this->_language = Molajo::Services()->connect('Language');
    }

    /**
     * Translates a string into the current language.
     *
     * Examples:
     * <script>alert(Joomla.TextService._('<?php echo TextService::_("JDEFAULT", array("script"=>true));?>'));</script> will generate an alert message containing 'Default'
     * <?php echo TextService::_("JDEFAULT");?> it will generate a 'Default' string
     *
     * @param   string         The string to translate.
     * @param   boolean|array  boolean: Make the result javascript safe. array an array of option as described in the TextService::sprintf function
     * @param   boolean        To interpret backslashes (\\=\, \n=carriage return, \t=tabulation)
     * @param   boolean        To indicate that the string will be push in the javascript language store
     *
     * @return  string  The translated string or the key is $script is true
     *
     * @since   1.0
     *
     */
    public function _($string, $jsSafe = false, $interpretBackSlashes = true, $script = false)
    {
        if ($this->_language == null) {
            return $string;
        }

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
            self::$strings[$string] = $this->_language->_($string, $jsSafe, $interpretBackSlashes);
            return $string;
        }
        else {
            return $this->_language->_($string, $jsSafe, $interpretBackSlashes);
        }
    }

    /**
     * alt
     *
     * Translates a string into the current language.
     *
     * Examples:
     * <?php echo TextService::alt("JALL","language");?> it will generate a 'All' string in English but a "Toutes" string in French
     * <?php echo TextService::alt("JALL","module");?> it will generate a 'All' string in English but a "Tous" string in French
     *
     * @param   string         The string to translate.
     * @param   string         The alternate option for global string
     * @param   boolean|array  boolean: Make the result javascript safe. array an array of option as described in the TextService::sprintf function
     * @param   boolean        To interpret backslashes (\\=\, \n=carriage return, \t=tabulation)
     * @param   boolean        To indicate that the string will be pushed in the javascript language store
     *
     * @return  string  The translated string or the key if $script is true
     *
     * @since   1.0
     *
     */
    public static function alt($string, $alt, $jsSafe = false, $interpretBackSlashes = true, $script = false)
    {
        if ($this->_language->hasKey($string . '_' . $alt)) {
            return self::_($string . '_' . $alt, $jsSafe, $interpretBackSlashes);
        }
        else {
            return self::_($string, $jsSafe, $interpretBackSlashes);
        }
    }

    /**
     * sprintf
     *
     * Passes a string thru a sprintf.
     *
     * @static
     * @param  $string
     * optional Array of option array
     * ('jsSafe'=>boolean, 'interpretBackSlashes'=>boolean, 'script'=>boolean) where
     *  -jsSafe is a boolean to generate a javascript safe strings
     *  -interpretBackSlashes is a boolean to interpret backslashes \\->\, \n->new line, \t->tabulation
     *  -script is a boolean to indicate that the string will be push in the javascript language store
     *
     * @return  string  The translated strings or the key if 'script' is true in the array of options
     *
     * @return mixed|string
     * @since  1.0
     */
    public static function sprintf($string)
    {
        $args = func_get_args();
        $count = count($args);
        if ($count > 0) {
            if (is_array($args[$count - 1])) {
                $args[0] = $this->_language->_($string, array_key_exists('jsSafe', $args[$count - 1]) ? $args[$count - 1]['jsSafe']
                    : false, array_key_exists('interpretBackSlashes', $args[$count - 1])
                    ? $args[$count - 1]['interpretBackSlashes'] : true);
                if (array_key_exists('script', $args[$count - 1]) && $args[$count - 1]['script']) {
                    self::$strings[$string] = call_user_func_array('sprintf', $args);
                    return $string;
                }
            }
            else {
                $args[0] = $this->_language->_($string);
            }
            return call_user_func_array('sprintf', $args);
        }
        return '';
    }

    /**
     * printf
     *
     * Passes a string thru an printf.
     *
     * @static
     * @param  $string
     * @return mixed|string
     * @since  1.0
     */
    public static function printf($string)
    {
        $args = func_get_args();
        $count = count($args);
        if ($count > 0) {
            if (is_array($args[$count - 1])) {
                $args[0] = $this->_language->_($string, array_key_exists('jsSafe', $args[$count - 1]) ? $args[$count - 1]['jsSafe']
                    : false, array_key_exists('interpretBackSlashes', $args[$count - 1])
                    ? $args[$count - 1]['interpretBackSlashes'] : true);
            }
            else {
                $args[0] = $this->_language->_($string);
            }
            return call_user_func_array('printf', $args);
        }
        return '';
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
