<?php
/**
 * @package     Molajo
 * @subpackage  Language
 * @copyright   Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
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
class MolajoLanguage
{
    /**
     * $_languages
     *
     * @var array
     * @since 1.0
     */
    protected static $_languages = array();

    /**
     * The default language
     *
     * @var    string
     * @since  1.0
     */
    protected $_default = 'en-GB';

    /**
     * Language metadata
     *
     * @var    array
     * @since  1.0
     */
    protected $_metadata = null;

    /**
     * Language locale
     *
     * @var    array|boolean
     * @since  1.0
     */
    protected $_locale = null;

    /**
     * Language to load
     *
     * @var    string
     * @since  1.0
     */
    protected $_language = null;

    /**
     * Loaded Language files
     *
     * @var    array
     * @since  1.0
     */
    protected $_paths = array();

    /**
     * Translations
     *
     * @var    array
     * @since  1.0
     */
    protected $_strings = null;

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
    public static function getInstance($language)
    {
        if (isset(self::$_languages[$language])) {
        } else {
            self::$_languages[$language] = new MolajoLanguage($language);
        }
        return self::$_languages[$language];
    }

    /**
     * __construct
     *
     * Constructor activating default information of the language.
     *
     * @param   string  $language
     *
     * @return  null
     * @since   1.0
     */
    public function __construct($language = null)
    {
        $this->_strings = array();

        if ($language == null) {
            $language = $this->_default;
        }

        /** load metadata */
        $this->_setMetadata($language);

        /** load language overrides */
        $filename = MOLAJO_EXTENSIONS_LANGUAGES . "/$language/$language.override.ini";
        if (file_exists($filename)
            && $contents = $this->_parse($filename)
        ) {
            if (is_array($contents)) {
                $this->_override_strings = $contents;
            }
        }

        /** load language */
        $this->load();
    }

    /**
     * load
     *
     * Loads a single language file and appends the results to the existing strings
     *
     * @param   string   $basePath   The basepath to use.
     * @param   string   $language   The language to load, default null for the current language.
     * @param   boolean  $reload     Flag that will force a language to be reloaded if set to true.
     * @param   boolean  $default    Flag that force the default language to be loaded if the current does not exist.
     *
     * @return  boolean  True if the file has successfully loaded.
     *
     * @since   1.0
     */
    public function load($basePath = null, $language = null, $reload = false, $default = true)
    {
        /** language and default language request */
        if ($language == null) {
            $language = $this->_language;
        }
        if ($language == $this->_language) {
            $default = false;
        }

        /** path */
        if ($basePath == null) {
            $basePath = MOLAJO_EXTENSIONS_LANGUAGES;
        }
        $path = self::_getPath($basePath, $language);

        /** filename */
        $filename = $language;
        $filename = "$path/$filename.ini";

        /** is the file already loaded? */
        if (isset($this->_paths[$filename])
            && $reload === false
        ) {
            return true;
        }

        /** load it */
        $result = $this->_loadLanguage($filename);

        /** did language load fail? And, was load default requested? */
        if ($result === false
            && $default === true
        ) {
        } else {
            return $result;
        }

        /** try the default language */
        $filename = $this->_default;
        $filename = "$path/$filename.ini";

        /** is the file already loaded? */
        if (isset($this->_paths[$filename])
            && $reload === false
        ) {
            return true;
        }

        return $this->_loadLanguage($filename);
    }

    /**
     * _
     *
     * Translate function, mimics the php gettext (alias _) function.
     *
     * @param   string  $string
     *
     * @return  string  The translation of the string
     * @since   1.0
     */
    public function _($string)
    {
        $key = strtoupper($string);
        if (isset($this->_strings[$key])) {
            $string = $this->_strings[$key];
        } else {
            //todo: amy capture error
        }
        return $string;
    }

    /**
     * isRtl
     *
     * Get the RTL property.
     *
     * @return  boolean  True is it an RTL language.
     * @since   1.0
     */
    public function isRtl()
    {
        return $this->_metadata['rtl'];
    }

    /**
     * getDefault
     *
     * Get the default language code.
     *
     * @return  string  Language code.
     * @since   1.0
     */
    public function getDefault()
    {
        return $this->_default;
    }

    /**
     * setDefault
     *
     * Set the default language code.
     *
     * @param   string  $language  The language code.
     *
     * @return  string  Previous value.
     * @since   1.0
     */
    public function setDefault($language)
    {
        $previous = $this->_default;
        $this->_default = $language;

        return $previous;
    }

    /**
     * getKnownLanguages
     *
     * Returns a list of known languages for a specific directory
     *
     * @param   string  $basePath  The basepath to use
     *
     * @return  array  key/value pair with the language file and real name.
     * @since   1.0
     */
    public function getKnownLanguages($basePath = MOLAJO_EXTENSIONS_LANGUAGES)
    {
        $dir = self::_getPath($basePath);
        $knownLanguages = self::_parseLanguageFiles($dir);

        return $knownLanguages;
    }

    /**
     * Checks if a language exists.
     *
     * This is a simple, quick check for the directory
     *
     * @param   string  $language      Language to check.
     * @param   string  $basePath  Optional path to check.
     *
     * @return  boolean  True if the language exists.
     *
     * @since   1.0
     */
    public function exists($language, $basePath)
    {
        static $paths = array();

        /** language */
        if ($language == null) {
            $language = $this->_language;
        }
        /** path */
        if ($basePath == null) {
            $basePath = MOLAJO_EXTENSIONS_LANGUAGES;
        }
        $path = self::_getPath($basePath, $language);

        /** filename */
        $filename = $language;
        $filename = "$path/$filename.ini";

        /** loaded already? */
        if (isset($paths[$path])) {
            return $paths[$path];
        }

        /** return path */
        $paths[$path] = JFolder::exists($path);
        return $paths[$path];
    }

    /**
     * getName
     *
     * Getter for Name.
     *
     * @return  string  Official name element of the language.
     * @since   1.0
     */
    public function getName()
    {
        return $this->_metadata['name'];
    }

    /**
     * getPaths
     *
     * Get a list of language files that have been loaded.
     *
     * @param   string  $extension  An optional extension name.
     *
     * @return  array
     * @since   1.0
     */
    public function getPaths($path = null)
    {
        if (isset($path)) {
            if (isset($this->_paths[$path])) {
                return $this->_paths[$path];
            }
            return null;
        } else {
            return $this->_paths;
        }
    }

    /**
     * getTag
     *
     * Getter for the language tag (as defined in RFC 3066)
     *
     * @return  string  The language tag.
     * @since   1.0
     */
    public function getTag()
    {
        return $this->_metadata['tag'];
    }

    /**
     * getLocale
     *
     * Get the language locale based on current language.
     *
     * @return  array  The locale according to the language.
     * @since   1.0
     */
    public function getLocale()
    {
        if (isset($this->_locale)) {
        } else {

            if (isset($this->_metadata['locale'])) {
                $locale = str_replace(' ', '', $this->_metadata['locale']);
            } else {
                $locale = '';
            }
            if ($locale == '' || $locale == null) {
                $this->_locale = false;
            } else {
                $this->_locale = explode(',', $locale);
            }
        }

        return $this->_locale;
    }

    /**
     * getFirstDay
     *
     * Get the first day of the week for this language.
     *
     * @return  integer  The first day of the week according to the language
     * @since   1.0
     */
    public function getFirstDay()
    {
        return (int)(isset($this->_metadata['firstDay']) ? $this->_metadata['firstDay'] : 0);
    }

    /**
     * get
     *
     * Get a metadata language property.
     *
     * @param   string  $property  The name of the property.
     * @param   mixed   $default   The default value.
     *
     * @return  mixed  The value of the property.
     * @since   1.0
     */
    public function get($property, $default = null)
    {
        if (isset($this->_metadata[$property])) {
            return $this->_metadata[$property];
        }
        return $default;
    }

    /**
     * _setMetadata
     *
     * Set the language attributes to the given language.
     *
     * @param   string  $language
     *
     * @return  null
     * @since   1.0
     */
    private function _setMetadata($language)
    {
        $this->_language = $language;
        $this->_metadata = $this->_getMetadata($this->_language);
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
    private function _getMetadata($language)
    {
        $path = self::_getPath(MOLAJO_EXTENSIONS_LANGUAGES, $language);
        $file = "manifest.xml";

        $result = null;

        if (is_file("$path/$file")) {
            $result = self::_parseMetadata("$path/$file");
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
     * _getPath
     *
     * Get the path to a specific language
     *
     * @param   string  $basePath
     * @param   string  $language
     *
     * @return  string  Path
     *
     * @since   1.0
     */
    private function _getPath($path = MOLAJO_EXTENSIONS_LANGUAGES, $language = null)
    {
        if ($path == MOLAJO_EXTENSIONS_LANGUAGES) {
            $dir = $path . '/' . $language;
        } else {
            $dir = $path . '/language';
        }
        return $dir;
    }

    /**
     * _parseLanguageFiles
     *
     * Searches for language directories within a certain base dir.
     *
     * @param   string  $dir  directory of files.
     *
     * @return  array  Array holding the found languages as filename => real name pairs.
     * @since   1.0
     */
    private function _parseLanguageFiles($dir = null)
    {
        $_languages = array();

        $subfolders = JFolder::folders($dir);
        foreach ($subfolders as $path) {
            $xml = self::_parseXMLLanguageFiles("$dir/$path");
            $_languages = array_merge($_languages, $xml);
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
                if ($metadata = self::_parseMetadata("$dir/$file")) {
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
        if ($xml = MolajoController::getXML($path)) {
        } else {
            return null;
        }

        if ((string)$xml->getName() == 'manifest') {
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
