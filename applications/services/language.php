<?php
/**
 * @package     Molajo
 * @subpackage  Service
 * @copyright   Copyright (C) 2012 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die;

/**
 * Language
 *
 * @package     Molajo
 * @subpackage  Language
 * @since       1.0
 */
class MolajoLanguageService
{
    /**
     * Instance of each specific language
     *
     * $_languages
     *
     * @var array
     * @since 1.0
     */
    protected static $_languages = array();

    /**
     * Language
     *
     * @var    string
     * @since  1.0
     */
    protected $language;

    /**
     * Name
     *
     * @var    string
     * @since  1.0
     */
    protected $name;

    /**
     * Tag
     *
     * @var    string
     * @since  1.0
     */
    protected $tag;

    /**
     * rtl
     *
     * @var    string
     * @since  1.0
     */
    protected $rtl;

    /**
     * locale
     *
     * @var    string
     * @since  1.0
     */
    protected $locale;

    /**
     * first_day
     *
     * @var    string
     * @since  1.0
     */
    protected $first_day;

    /**
     * Loaded Language Files
     *
     * @var    array
     * @since  1.0
     */
    protected $loaded_files;

    /**
     * Loaded Translation Strings
     *
     * @var    array
     * @since  1.0
     */
    protected $loaded_strings;

    /**
     * Overrides loaded
     *
     * @var    array
     * @since  1.0
     */
    protected $loaded_override_strings;

    /**
     * getInstance
     *
     * Returns a language object
     *
     * @param   string   $language
     *
     * @return  object
     * @since   1.0
     */
    public static function getInstance($language = null)
    {
        if (isset(self::$_languages[$language])) {
        } else {
            self::$_languages[$language] = new MolajoLanguageService($language);
        }
        return self::$_languages[$language];
    }

    /**
     * __construct
     *
     * @param   string  $language
     *
     * @return  null
     * @since   1.0
     */
    protected function __construct($language = null)
    {
        if ($language == null || $language == '') {
            $language = LanguageHelper::getDefault();
        }
        $this->language = $language;
        $this->loaded_override_strings = array();
        $this->loaded_strings = array();
        $this->loaded_files = array();

        return $this->load_core_files();
    }

    /**
     * load_core_files
     *
     * Loads metadata from XML File for Language
     *
     * Loads core standard language strings
     */
    protected function load_core_files()
    {
        /** load metadata */
        $xmlFile = MOLAJO_EXTENSIONS_LANGUAGES . '/' . $this->language . '/' . 'manifest.xml';
        $metadata = LanguageHelper::getMetadata($xmlFile);

        if (isset($metadata['name'])) {
            $this->name = $metadata['name'];
        }
        if (isset($metadata['tag'])) {
            $this->tag = $metadata['tag'];
        }
        if (isset($metadata['rtl'])) {
            $this->rtl = $metadata['rtl'];
        }
        if (isset($metadata['locale'])) {
            $locale = str_replace(' ', '', $metadata['locale']);
            $this->locale = explode(',', $metadata['locale']);
        }
        if (isset($metadata['first_day'])) {
            $this->first_day = $metadata['first_day'];
        }

        /** load language strings */
        $path = MOLAJO_EXTENSIONS_LANGUAGES . '/' . $this->language;
        $this->load($path);

        return;
    }

    /**
     * load
     *
     * Loads the requested language file. If not successful, loads the default language.
     *
     * @param   string   $path
     * @param   string   $language
     *
     * @return  boolean
     * @since   1.0
     */
    public function load($path)
    {
        $loaded = $this->_loadLanguage($path, $this->language . '.ini');
        if ($loaded === false) {
            echo 'MolajoLanguageServices: cannot load file: ' . $path . '/' . $this->language . '.ini' . '<br />';
        } else {
            return true;
        }

        $default = LanguageHelper::getDefault();
        if ($this->language == $default) {
            return false;
        }

        $loaded = $this->_loadLanguage($path, $default . '.ini');
        if ($loaded === false) {
            echo 'MolajoLanguageServices 2: cannot load default language file: ' . $path . '/' . $default . '.ini' . '<br />';
            return false;
        }
        return $loaded;
    }

    /**
     * get
     *
     * @param  string  $key
     * @param  string  $default
     *
     * @return  mixed
     *
     * @since   1.0
     */
    public function get($key, $default = null)
    {
        if (isset($this->$key)) {
            return $this->$key;
        } else {
            return $default;
        }
    }

    /**
     * set
     *
     * @param  string  $key
     * @param  mixed   $value
     *
     * @return  mixed
     * @since   1.0
     */
    public function set($key, $value = null)
    {
        return $this->$key = $value;
    }

    /**
     * _
     *
     * Replaces Language Key with translation
     *
     * @param  $key
     *
     * @return mixed
     * @since  1.0
     */
    public function _($key)
    {
        if (isset($this->loaded_strings[$key])) {
            return $this->loaded_strings[$key];

        } else {
            //            echo 'Missing language key: '.$key.'<br />';
            return $key;
        }
    }

    public function sprintf()
    {
    }

    /**
     * _loadLanguage
     *
     * Parses standard and override language files and merges strings
     *
     * @param   string   $filename
     *
     * @return  boolean
     * @since   1.0
     */
    protected function _loadLanguage($path, $file)
    {
        $filename = $path . '/' . $file;

        /** standard file */
        if (isset($this->loaded_files[$filename])) {
            return true;
        }

        if (file_exists($filename)) {
            $strings = $this->_parse($filename);
            $this->loaded_files[$filename] = true;
        } else {
            $strings = array();
            $this->loaded_files[$filename] = false;
        }

        /** overrides */
        $filename = $path . '/' . $this->language . '.override.ini';

        if (file_exists($filename)) {
            $override_strings = $this->_parse($filename);
            $this->loaded_files[$filename] = true;
        } else {
            $override_strings = array();
            $this->loaded_files[$filename] = false;
        }

        /** merge */
        if (is_array($strings)
            && count($strings) > 0
        ) {

            $this->loaded_strings =
                array_merge(
                    $this->loaded_strings,
                    $strings
                );
        }

        if (is_array($override_strings)
            && count($override_strings) > 0
        ) {

            $this->loaded_override_strings =
                array_merge(
                    $this->loaded_override_strings,
                    $override_strings
                );

            $this->loaded_strings =
                array_merge(
                    $this->loaded_strings,
                    $override_strings
                );
        }
        /**
        echo '<pre>';
        var_dump($this->loaded_strings);
        echo '</pre>';
         */
    }

    /**
     * Parses a language file.
     *
     * @param   string  $filename  The name of the file.
     *
     * @return  array  The array of parsed strings.
     *
     * @since   1.0
     */
    private function _parse($filename)
    {
        /** capture php errors during parsing */
        $track_errors = ini_get('track_errors');
        if ($track_errors === false) {
            ini_set('track_errors', true);
        }

        $contents = file_get_contents($filename);

        if ($contents) {
            $contents = str_replace(MOLAJO_LANGUAGE_QUOTE_REPLACEMENT, '"\""', $contents);
            $strings = parse_ini_string($contents);
        } else {
            $strings = array();
        }

        /** restore previous error tracking */
        if ($track_errors === false) {
            ini_set('track_errors', false);
        }

        return $strings;
    }
}

