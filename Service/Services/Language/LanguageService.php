<?php
/**
 * @package   Molajo
 * @copyright 2012 Amy Stephen. All rights reserved.
 * @license   GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
namespace Molajo\Service\Services\Language;

use Molajo\Service\Services;

defined('MOLAJO') or die;
/**
 * Language
 *
 * @package   Molajo
 * @subpackage  Language
 * @since       1.0
 */
Class LanguageService
{
    /**
     * Instance of each specific language
     *
     * $languages
     *
     * @var array
     * @since 1.0
     */
    protected static $languages = array();

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
     * direction
     *
     * @var    string
     * @since  1.0
     */
    protected $direction;

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
     * :paOverrides loaded
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
     * @param string $language
     *
     * @return object
     * @since   1.0
     */
    public static function getInstance($language = null)
    {
        if (isset(self::$languages[$language])) {
        } else {
            self::$languages[$language] = new LanguageService($language);
        }

        return self::$languages[$language];
     }

    /**
     * __construct
     *
     * @param string $language
     *
     * @return null
     * @since   1.0
     */
    protected function __construct($language = null)
    {
        if ($language == null || $language == '') {
            $language = $this->getDefault();
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
        $metadata = Services::Configuration()
            ->getFile(SITE_LANGUAGES . '/' . $this->language, 'Language');

        if (isset($metadata['name'])) {
            $this->name = $metadata['name'];
        }
        if (isset($metadata['tag'])) {
            $this->tag = $metadata['tag'];
        }
        if (isset($metadata['rtl'])) {
            $this->rtl = $metadata['rtl'];
            if ((int) $this->rtl == 0) {
                $this->direction = 'ltr';
            } else {
                $this->direction = 'rtl';
            }
        }
        if (isset($metadata['locale'])) {
            $locale = str_replace(' ', '', $metadata['locale']);
            $this->locale = explode(',', $metadata['locale']);
        }
        if (isset($metadata['first_day'])) {
            $this->first_day = $metadata['first_day'];
        }

        /** load language strings */
        $path = SITE_LANGUAGES . '/' . $this->language;
        $this->load($path);

        return;
    }

    /**
     * load
     *
     * Loads the requested language file. If not successful, loads the default language.
     *
     * @param string $path
     * @param string $language
     *
     * @return boolean
     * @since   1.0
     */
    public function load($path)
    {
        $loaded = $this->loadLanguage($path, $this->language . '.ini');
        if ($loaded == false) {
//			Services::Debug()->set('LanguageServices: cannot load file: '
                echo 'cannot load language file ' . $path . '/' . $this->language . '.ini';
        } else {
            return true;
        }

        $default = $this->getDefault();
        if ($this->language == $default) {
            return false;
        }

        $loaded = $this->loadLanguage($path, $default . '.ini');
        if ($loaded === false) {
            Services::Debug()->set('LanguageServices 2: cannot load default language file: '
                . $path . '/' . $default . '.ini');

            return false;
        }

        return $loaded;
    }

    /**
     * get
     *
     * @param string $key
     * @param string $default
     *
     * @return mixed
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
     * @param string $key
     * @param mixed  $value
     *
     * @return mixed
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
    public function translate($key)
    {
        if (isset($this->loaded_strings[$key])) {
            return $this->loaded_strings[$key];

        } else {
            Services::Debug()->set('MolajoLanguage: Missing language key: ' . $key);

            return $key;
        }
    }

    /**
     * loadLanguage
     *
     * Parses standard and override language files and merges strings
     *
     * @param string $filename
     *
     * @return boolean
     * @since   1.0
     */
    protected function loadLanguage($path, $file)
    {
        $filename = $path . '/' . $file;

        /** standard file */
        if (isset($this->loaded_files[$filename])) {
            return true;
        }

        if (file_exists($filename)) {
            $strings = $this->parse($filename);
            $this->loaded_files[$filename] = true;
        } else {
            $strings = array();
            $this->loaded_files[$filename] = false;
        }

        /** overrides */
        $filename = $path . '/' . $this->language . '.override.ini';

        if (file_exists($filename)) {
            $override_strings = $this->parse($filename);
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

        return true;
    }

    /**
     * parse
     *
     * Parses a language file.
     *
     * @param string $filename The name of the file.
     *
     * @return array The array of parsed strings.
     * @since   1.0
     */
    protected function parse($filename)
    {
        /** capture php errors during parsing */
        $track_errors = ini_get('track_errors');
        if ($track_errors === false) {
            ini_set('track_errors', true);
        }

        $contents = file_get_contents($filename);

        if ($contents) {
            $contents = str_replace('"', '', $contents);
            $contents = str_replace(LANGUAGE_QUOTE_REPLACEMENT, '"\""', $contents);
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

    /**
     * getDefault
     *
     * Tries to detect the language.
     *
     * @return string locale or null if not found
     * @since   1.0
     */
    public function getDefault()
    {
        /** Installed Languages */
        $languages = $this->getLanguages(SITE_LANGUAGES);

        $installed = array();
        foreach ($languages as $language) {
            $installed[] = $language->subtitle;
        }

        $language = false;

        /** 1. if there is just one, take it */
        if (count($installed) == 1) {
            return $installed[0];
        }

        /** 2. user  */
        $language = Services::Registry()->get('User', 'language');
        if ($language === false) {
        } elseif (in_array($language, $installed)) {
            return $language;
        }

        /** 3. language of browser */
        if (isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {
            $browserLanguages = explode(',', $_SERVER['HTTP_ACCEPT_LANGUAGE']);
        } else {
            return null;
        }
        foreach ($browserLanguages as $language) {
            if (in_array(strtolower($language), $installed)) {
                return $language;
            }
        }

        /** 4. Application configuration */
        $language = $this->get('tag', 'en-GB');
        if (in_array($language, $installed)) {
            return $language;
        }

        /** 5. default */

        return 'en-GB';
    }

    /**
     * createLanguageList
     *
     * Builds a list of the languages installed for core or an extension
     *
     * @return array
     * @since   1.0
     */
    public function createLanguageList($path = null)
    {
        if (APPLICATION_ID == 0) {
            $path = EXTENSIONS_COMPONENTS . '/' . 'installer';

        } else {
            if ($path == null) {
                $path = SITE_LANGUAGES;
            }
        }

        /** for selected item determination */
        $currentLanguage = $this->get('tag');
        if ($currentLanguage === false || $currentLanguage == null) {
            $currentLanguage = 'en-GB';
        }

        /** retrieve language list */
        $languages = $this->getLanguages($path);

        $list = array();
        foreach ($languages as $language) {
            $listItem = new \stdClass();

            $listItem->key = $language->title;
            $listItem->value = $language->subtitle;

            $list[] = $listItem;
        }

        return $list;
    }

    /**
     * getLanguages
     *
     * Returns languages for core or a specific extension
     *
     * @param string $path
     *
     * @return object
     * @since   1.0
     */
    public function getLanguages($path = SITE_LANGUAGES)
    {
        if ($path == SITE_LANGUAGES) {
            return $this->getLanguagesCore();
        }

        $languages = array();

        $files = Services::Filesystem()->folderFiles($path . '/language', '\.ini', false, false);
        if (count($files) == 0) {
            return false;
        }

        foreach ($files as $file) {
            $language = new \stdClass();

            $language->value = substr($file, 0, strlen($file) - 4);
            $language->key = substr($file, 0, strlen($file) - 4);

            $languages[] = $language;
        }

        return $languages;
    }

    /**
     * getLanguagesCore
     *
     * During Service Initiation, the language service is started before
     * the Date Service. This routine is used at that time in lieu of
     * ability to query where date comparisons are needed.
     *
     * @return array
     * @since  1.0
     */
    public function getLanguagesCore()
    {
        $subfolders = Services::Filesystem()->folderFolders(SITE_LANGUAGES);
        $languages = array();

        foreach ($subfolders as $path) {
            $language = new \stdClass();

            $language->title = $path;
            $language->subtitle = $path;

            $languages[] = $language;
        }

        return $languages;
    }

    /**
     * get_metadata
     *
     * Read Language Manifest XML file for metadata
     *
     * @param string $path
     *
     * @return array array
     * @since   1.0
     */
    public function get_metadata($file)
    {
        $xml = Services::Configuration()->getFile($file, 'Language');
        if ($xml) {
        } else {
            return true;
        }

        $metadata = array();
        foreach ($xml->metadata->children() as $child) {
            $metadata[$child->getName()] = (string) $child;
        }

        return $metadata;
    }
}
