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
 * Languages 
 *
 * @package     Molajo
 * @subpackage  Language
 * @since       1.0
 */
class MolajoLanguage
{
    /**
     * $languages
     * 
     * @var array
     * @since 1.0
     */
    protected static $languages = array();

    /**
     * The default language
     * 
     * @var    string
     * @since  1.0
     */
    protected $default = 'en-GB';

    /**
     * Language metadata
     * 
     * @var    array
     * @since  1.0
     */
    protected $metadata = null;

    /**
     * Language locale 
     * 
     * @var    array|boolean
     * @since  1.0
     */
    protected $locale = null;

    /**
     * Language to load
     *
     * @var    string
     * @since  1.0
     */
    protected $language = null;

    /**
     * Loaded Language files
     *
     * @var    array
     * @since  1.0
     */
    protected $paths = array();

    /**
     * Translations
     *
     * @var    array
     * @since  1.0
     */
    protected $strings = null;

    /**
     * Overrides loaded
     *
     * @var    array
     * @since  1.0
     */
    protected $override = array();

    /**
     * getInstance
     *
     * Returns a language object.
     *
     * @param   string   $language   The language to use.
     *
     * @return  MolajoLanguageHelper  The Language object.
     *
     * @since   1.0
     */
    public static function getInstance($language)
    {
        if (isset(self::$languages[$language])) {
        } else {
            self::$languages[$language] = new MolajoLanguage($language);
        }
        return self::$languages[$language];
    }

    /**
     * __construct
     *
     * Constructor activating default information of the language.
     *
     * @param   string   $language
     *
     * @return  MolajoLanguageHelper
     *
     * @since   1.0
     */
    public function __construct($language = null)
    {
        $this->strings = array();

        if ($language == null) {
            $language = $this->default;
        }

        $this->setLanguage($language);

        $filename = MOLAJO_EXTENSIONS_LANGUAGES . "/$language.override.ini";

        if (file_exists($filename)
            && $contents = $this->parse($filename)
        ) {

            if (is_array($contents)) {
                $this->override = $contents;
            }
            unset($contents);
        }

        /** Localise */
        $class = str_replace('-', '_', $language . 'Localise');
        if (class_exists($class)) {

        } else {
            $localise = MOLAJO_EXTENSIONS_LANGUAGES . "/$language.localise.php";
            if (file_exists($localise)) {
                require_once $localise;
            }
        }

        /** load language */
        $this->load();
    }

    /**
     * Translate function, mimics the php gettext (alias _) function.
     *
     * The function checks if $jsSafe is true, then if $interpretBackslashes is true.
     *
     * @param   string   $string                The string to translate
     *
     * @return  string  The translation of the string
     *
     * @since   1.0
     */
    public function _($string)
    {
        $key = strtoupper($string);
        $string = $this->strings[$key];
        return $string;
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
    public static function exists($language, $basePath = MOLAJO_EXTENSIONS_LANGUAGES)
    {
        static $paths = array();

        if ($language) {
        } else {
            return false;
        }

        $path = "$basePath/$language";

        if (isset($paths[$path])) {
            return $paths[$path];
        }

        $paths[$path] = JFolder::exists($path);
        return $paths[$path];
    }

    /**
     * Loads a single language file and appends the results to the existing strings
     *
     * @param   string   $extension  The extension for which a language file should be loaded.
     * @param   string   $basePath   The basepath to use.
     * @param   string   $language   The language to load, default null for the current language.
     * @param   boolean  $reload     Flag that will force a language to be reloaded if set to true.
     * @param   boolean  $default    Flag that force the default language to be loaded if the current does not exist.
     *
     * @return  boolean  True if the file has successfully loaded.
     *
     * @since   1.0
     */
    public function load($extension = 'molajo', $basePath = MOLAJO_EXTENSIONS_LANGUAGES, $language = null, $reload = false, $default = true)
    {
        if ($language) {
        } else {
            $language = $this->language;
        }

        $path = self::getLanguagePath($basePath, $language);

        $internal = false;
        if ($extension == 'molajo' || $extension == '') {
            $internal = true;
        }

        $filename = $internal ? $language : $language . '.' . $extension;
        $filename = "$path/$filename.ini";

        $result = false;

        if (isset($this->paths[$extension][$filename])
            && $reload === false
        ) {
            $result = true;

        } else {
            $result = $this->loadLanguage($filename, $extension);

            // Check whether there was a problem with loading the file
            if ($result === false
                && $default === true
            ) {

                // No strings, so either file doesn't exist or the file is invalid
                $oldFilename = $filename;

                // Check the standard file name
                $path = self::getLanguagePath($basePath, $this->default);
                $filename = $internal ? $this->default : $this->default . '.' . $extension;
                $filename = "$path/$filename.ini";

                // If the one we tried is different than the new name, try again
                if ($oldFilename != $filename) {
                    $result = $this->loadLanguage($filename, $extension, false);
                }
            }
        }

        return $result;
    }

    /**
     * Loads a language file.
     *
     * This method will not note the successful loading of a file - use load() instead.
     *
     * @param   string   $filename   The name of the file.
     * @param   string   $extension  The name of the extension.
     * @param   boolean  $overwrite  Not used??
     *
     * @return  boolean  True if new strings have been added to the language
     *
     * @see     MolajoLanguageHelper::load()
     * @since   1.0
     */
    protected function loadLanguage($filename, $extension = 'unknown', $overwrite = true)
    {
        $result = false;
        $strings = false;

        if (file_exists($filename)) {
            $strings = $this->parse($filename);
        }

        if ($strings) {
            if (is_array($strings)) {
                $this->strings = array_merge($this->strings, $strings);
            }

            if (is_array($strings) && count($strings)) {
                $this->strings = array_merge($this->strings, $this->override);
                $result = true;
            }
        }

        // Record the result of loading the extension's file.
        if (isset($this->paths[$extension])) {
        } else {
            $this->paths[$extension] = array();
        }

        $this->paths[$extension][$filename] = $result;

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
    protected function parse($filename)
    {
        $version = phpversion();

        // Capture hidden PHP errors from the parsing.
        $php_errormsg = null;
        $track_errors = ini_get('track_errors');
        ini_set('track_errors', true);

        if ($version >= '5.3.1') {
            $contents = file_get_contents($filename);
            $contents = str_replace('_QQ_', '"\""', $contents);
            $strings = @parse_ini_string($contents);
        } else {

            $strings = @parse_ini_file($filename);

            if ($version == '5.3.0' && is_array($strings)) {
                foreach ($strings as $key => $string) {
                    $strings[$key] = str_replace('_QQ_', '"', $string);
                }
            }
        }

        // Restore error tracking to what it was before.
        ini_set('track_errors', $track_errors);

        if (is_array($strings)) {
        } else {
            $strings = array();
        }

        return $strings;
    }

    /**
     * Get a metadata language property.
     *
     * @param   string  $property  The name of the property.
     * @param   mixed   $default   The default value.
     *
     * @return  mixed  The value of the property.
     *
     * @since   1.0
     */
    public function get($property, $default = null)
    {
        if (isset($this->metadata[$property])) {
            return $this->metadata[$property];
        }

        return $default;
    }

    /**
     * Getter for Name.
     *
     * @return  string  Official name element of the language.
     *
     * @since   1.0
     */
    public function getName()
    {
        return $this->metadata['name'];
    }

    /**
     * Get a list of language files that have been loaded.
     *
     * @param   string  $extension  An optional extension name.
     *
     * @return  array
     *
     * @since   1.0
     */
    public function getPaths($extension = null)
    {
        if (isset($extension)) {
            if (isset($this->paths[$extension])) {
                return $this->paths[$extension];
            }
            return null;
        } else {
            return $this->paths;
        }
    }

    /**
     * Getter for the language tag (as defined in RFC 3066)
     *
     * @return  string  The language tag.
     *
     * @since   1.0
     */
    public function getTag()
    {
        return $this->metadata['tag'];
    }

    /**
     * Get the RTL property.
     *
     * @return  boolean  True is it an RTL language.
     *
     * @since   1.0
     */
    public function isRtl()
    {
        return $this->metadata['rtl'];
    }

    /**
     * Get the default language code.
     *
     * @return  string  Language code.
     *
     * @since   1.0
     */
    public function getDefault()
    {
        return $this->default;
    }

    /**
     * Set the default language code.
     *
     * @param   string  $language  The language code.
     *
     * @return  string  Previous value.
     *
     * @since   1.0
     */
    public function setDefault($language)
    {
        $previous = $this->default;
        $this->default = $language;

        return $previous;
    }

    /**
     * hasKey
     *
     * Determines is a key exists.
     *
     * @param   string  $string  The key to check.
     *
     * @return  boolean  True, if the key exists.
     * @since   1.0
     */
    function hasKey($string)
    {
        $key = strtoupper($string);
        return isset($this->strings[$key]);
    }

    /**
     * Returns a associative array holding the metadata.
     *
     * @param   string  $language  The name of the language.
     *
     * @return  mixed  If $language exists return key/value pair with the language metadata, otherwise return NULL.
     *
     * @since   1.0
     */
    public static function getMetadata($language)
    {
        $path = self::getLanguagePath(MOLAJO_EXTENSIONS_LANGUAGES, $language);
        $file = "manifest.xml";

        $result = null;

        if (is_file("$path/$file")) {
            $result = self::parseXMLLanguageFile("$path/$file");
        }

        return $result;
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
    public static function getKnownLanguages($basePath = MOLAJO_EXTENSIONS_LANGUAGES)
    {
        $dir = self::getLanguagePath($basePath);
        $knownLanguages = self::parseLanguageFiles($dir);

        return $knownLanguages;
    }

    /**
     * getLanguagePath
     * 
     * Get the path to a language
     *
     * @param   string  $basePath  
     * @param   string  $language 
     *
     * @return  string  Path
     *
     * @since   1.0
     */
    public static function getLanguagePath($basePath = MOLAJO_EXTENSIONS_LANGUAGES, $language = null)
    {
        if ($basePath == MOLAJO_EXTENSIONS_LANGUAGES) {
            $dir = $basePath;
        } else {
            $dir = $basePath . '/language';
        }
        return $dir;
    }

    /**
     * setLanguage
     *
     * Set the language attributes to the given language.
     *
     * @param   string  $language
     *
     * @return  string  Previous value.
     * @since   1.0
     */
    public function setLanguage($language)
    {
        $previous = $this->language;
        $this->language = $language;
        $this->metadata = $this->getMetadata($this->language);

        return $previous;
    }

    /**
     * Get the language locale based on current language.
     *
     * @return  array  The locale according to the language.
     *
     * @since   1.0
     */
    public function getLocale()
    {
        if (!isset($this->locale)) {
            $locale = str_replace(' ', '', isset($this->metadata['locale']) ? $this->metadata['locale'] : '');

            if ($locale) {
                $this->locale = explode(',', $locale);
            }
            else
            {
                $this->locale = false;
            }
        }

        return $this->locale;
    }

    /**
     * Get the first day of the week for this language.
     *
     * @return  integer  The first day of the week according to the language
     *
     * @since   1.0
     */
    public function getFirstDay()
    {
        return (int)(isset($this->metadata['firstDay']) ? $this->metadata['firstDay'] : 0);
    }

    /**
     * Searches for language directories within a certain base dir.
     *
     * @param   string  $dir  directory of files.
     *
     * @return  array  Array holding the found languages as filename => real name pairs.
     *
     * @since   1.0
     */
    public static function parseLanguageFiles($dir = null)
    {
        $languages = array();

        $subdirs = JFolder::folders($dir);

        foreach ($subdirs as $path)
        {
            $xml = self::parseXMLLanguageFiles("$dir/$path");
            $languages = array_merge($languages, $xml);
        }

        return $languages;
    }

    /**
     * Parses XML files for language information
     *
     * @param   string  $dir  Directory of files.
     *
     * @return  array  Array holding the found languages as filename => metadata array.
     *
     * @since   1.0
     */
    public static function parseXMLLanguageFiles($dir = null)
    {
        if ($dir == null) {
            return null;
        }

        $languages = array();
        $files = JFolder::files($dir, '^([-_A-Za-z]*)\.xml$');

        foreach ($files as $file)
        {
            if ($content = file_get_contents("$dir/$file")) {
                if ($metadata = self::parseXMLLanguageFile("$dir/$file")) {
                    $lang = str_replace('.xml', '', $file);
                    $languages[$lang] = $metadata;
                }
            }
        }

        return $languages;
    }

    /**
     * parseXMLLanguageFile
     *
     * Parse XML file for language information.
     *
     * @param   string  $path  Path to the XML files.
     *
     * @return  array  Array holding the found metadata as a key => value pair.
     * @since   1.0
     */
    public static function parseXMLLanguageFile($path)
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

        foreach ($xml->metadata->children() as $child)
        {
            $metadata[$child->getName()] = (string)$child;
        }

        return $metadata;
    }
}
