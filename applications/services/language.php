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
    public $language = null;

    /**
     * Name
     *
     * @var    string
     * @since  1.0
     */
    public $name = null;

    /**
     * Tag
     *
     * @var    string
     * @since  1.0
     */
    public $tag = null;

    /**
     * rtl
     *
     * @var    string
     * @since  1.0
     */
    public $rtl = null;

    /**
     * locale
     *
     * @var    string
     * @since  1.0
     */
    public $locale = null;

    /**
     * first_day
     *
     * @var    string
     * @since  1.0
     */
    public $first_day = null;

    /**
     * Loaded Language Files
     *
     * @var    array
     * @since  1.0
     */
    protected $paths = array();

    /**
     * Loaded Translation Strings
     *
     * @var    array
     * @since  1.0
     */
    protected $strings = array();

    /**
     * Overrides loaded
     *
     * @var    array
     * @since  1.0
     */
    protected $_override_strings = array();

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
    public function __construct($language = null)
    {
        if ($language == null || $language == '') {
            $language = MolajoLanguageHelper::get();
        }
        $this->language = $language;

        /** load metadata */
        $metadata = $this->_getMetadata();

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

        /** load language overrides */
        $filename = MOLAJO_EXTENSIONS_LANGUAGES .
            "/$language/$language.override.ini";

        if (file_exists($filename)) {
            $contents = $this->_parse($filename);
            if (is_array($contents)) {
                $this->_override_strings = $contents;
            }
        }

        /** load the language files */
        $this->_strings = array();
        return $this->load(MOLAJO_EXTENSIONS_LANGUAGES, $language);
    }

    /**
     * load
     *
     * Loads language file
     *
     * @param   string   $path
     * @param   string   $language
     *
     * @return  boolean
     * @since   1.0
     */
    public function load($path = null, $language = null)
    {
        /** defaults */
        if ($language == null) {
            $this->language = MolajoLanguageHelper::get();
        }
        if ($path == null) {
            $path = MOLAJO_EXTENSIONS_LANGUAGES;
        }
        $path = LanguageHelper::getPath($path, $language);

        /** filename */
        $filename = $language;
        $filename = "$path/$filename.ini";

        /** already loaded */
        if (isset($this->paths[$filename])) {
            return true;
        }

        /** load */
        $result = $this->_loadLanguage($filename);
        if ($result === false) {
            echo 'MolajoLanguageServices: cannot load file: ' . $filename . '<br />';
        }

        /** try default */
        if ($result === false) {
            $default = MolajoLanguageHelper::get();
            if ($this->language == $default) {
                echo 'MolajoLanguageServices 2: cannot load file: ' . $filename . '<br />';
                return false;
            }
            $result = $this->_loadLanguage($filename);
            if ($result === false) {
                echo 'MolajoLanguageServices 3: cannot load file: ' . $filename . '<br />';
                return false;
            }
        }
        return $result;
    }

    /**
     * _loadLanguage
     *
     * Loads a language file.
     *
     * @param   string   $filename   The name of the file.
     *
     * @return  boolean  True if new strings have been added to the language
     * @since   1.0
     */
    private function _loadLanguage($filename)
    {
        $result = false;
        $strings = false;

        if (file_exists($filename)) {
            $strings = $this->_parse($filename);
        }

        if ($strings) {
            if (is_array($strings)) {
                $this->_strings = array_merge($this->_strings, $strings);
            }

            if (is_array($strings) && count($strings)) {
                $this->_strings = array_merge($this->_strings, $this->_override_strings);
                $result = true;
            }
        }

        // Record the result of loading the extension's file.
        if (isset($this->_paths[$filename])) {
        } else {
            $this->_paths[$filename] = array();
        }

        $this->_paths[$filename] = $result;

        return $result;
    }

    /**
     * _getMetadata
     *
     * Returns an associative array holding the metadata.
     *
     * @param   string  $language  The name of the language.
     *
     * @return  mixed  If $language exists return metadata key/value pair, otherwise NULL
     * @since   1.0
     */
    private function _getMetadata()
    {
        $path = LanguageHelper::getPath(
            MOLAJO_EXTENSIONS_LANGUAGES,
            $this->language
        );
        $file = "manifest.xml";

        $result = null;

        if (is_file("$path/$file")) {
            $result = $this->_parseMetadata("$path/$file");
        }

        return $result;
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
        $php_errormsg = null;
        $track_errors = ini_get('track_errors');
        if ($track_errors === false) {
            ini_set('track_errors', true);
        }

        $contents = file_get_contents($filename);
        //echo 'filename '.$filename.'<br />';
        if ($contents) {
            $contents = str_replace('_QQ_', '"\""', $contents);
            $strings = parse_ini_string($contents);
        } else {
            $strings = '';
        }

        /** restore previous error tracking */
        if ($track_errors === false) {
            ini_set('track_errors', $track_errors);
        }

        if (is_array($strings)) {
        } else {
            $strings = array();
        }

        return $strings;
    }

    /**
     * parseLanguageFiles
     *
     * Searches for language directories within a certain base dir.
     *
     * @param   string  $dir  directory of files.
     *
     * @return  array  languages discovered
     * @since   1.0
     */
    public function parseLanguageFiles($dir = null)
    {
        $_languages = array();

        if ($dir == MOLAJO_EXTENSIONS_LANGUAGES) {
            $subfolders = JFolder::folders($dir);
            foreach ($subfolders as $path) {
                $xml = $this->_parseXMLLanguageFiles("$dir/$path");
                $_languages = array_merge($_languages, $xml);
            }
        }

        return $_languages;
    }

    /**
     * _parseXMLLanguageFiles
     *
     * Parses XML files for language information
     *
     * @param   string  $dir  Directory of files.
     *
     * @return  array  Array holding the found languages as filename => metadata array.
     * @since   1.0
     */
    private function _parseXMLLanguageFiles($dir = null)
    {
        if ($dir == null) {
            return null;
        }

        $_languages = array();
        $files = JFolder::files($dir, '^([-_A-Za-z]*)\.xml$');

        foreach ($files as $file)
        {
            if ($content = file_get_contents("$dir/$file")) {
                if ($metadata = $this->_parseMetadata("$dir/$file")) {
                    $lang = str_replace('.xml', '', $file);
                    $_languages[$lang] = $metadata;
                }
            }
        }

        return $_languages;
    }

    /**
     * _parseMetadata
     *
     * Parse XML file for metadata
     *
     * @param   string  $path  Path to the XML files.
     *
     * @return  array  Array holding the found metadata as a key => value pair.
     * @since   1.0
     */
    private function _parseMetadata($path)
    {
        $xml = simplexml_load_file($path);

        if (!$xml) {
            return null;
        }
        if ((string)$xml->getName() == 'extension') {
        } else {
            return null;
        }

        $metadata = array();
        foreach ($xml->metadata->children() as $child) {
            $metadata[$child->getName()] = (string)$child;
        }

        return $metadata;
    }
}
