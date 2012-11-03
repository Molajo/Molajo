<?php
/**
 * @package    Molajo
 * @copyright  2012 Individual Molajo Contributors. All rights reserved.
 * @license    GNU GPL v 2, or later and MIT, see License folder
 */
namespace Molajo\Service\Services\Language;

use Molajo\Service\Services;
use Molajo\Helper\ExtensionHelper;

defined('MOLAJO') or die;

/**
 * LanguageService API:
 *
 * To retrieve a list of all installed languages:
 * $list = Services::Registry()->get('Languages', 'installed');
 *
 * To retrieve the current language:
 * $current = Services::Registry()->get('Languages', 'Current');
 *
 * To retrieve the default language:
 * $default = Services::Registry()->get('Languages', 'Default');
 *
 * To retrieve all values for the current (or default) Language (en-GB as example):
 * Services::Registry()->get('en-GB', '*');
 *
 * or a specific value
 * echo Services::Registry()->get('en-GB', 'name');
 * or Services::Language()->get('direction');
 *
 * Other specific values include: id, name, rtl, local, first_day, loaded_folders, loaded_files, loaded_strings
 *
 * To load translations in a language folder
 * Services::Language()->load($path);
 *
 * To translate a string:
 * Services::Language()->translate($string);
 *
 * @package     Molajo
 * @subpackage  Language
 * @since       1.0
 */
Class LanguageService
{
    /**
     * Static instance
     *
     * @var    object
     * @since  1.0
     */
    protected static $instance;

    /**
     * @static
     * @return bool|object
     * @since  1.0
     */
    public static function getInstance()
    {
        if (empty(self::$instance)) {
            self::$instance = new LanguageService();
        }

        return self::$instance;
    }

    /**
     * @param string $language
     *
     * @return null
     * @since   1.0
     */
    protected function __construct($language = null)
    {
        $language = $this->getLanguage($language);

        $this->setLanguageRegistry($language);

        $this->load(CORE_LANGUAGES, $language);

        return $this;
    }

    /**
     * Translate a specified string for the language requested (or current language)
     *
     * @param $string Translate this
     * @param null Language (if overriding current)
     *
     * @return mixed
     * @since  1.0
     */
    public function translate($string, $language = null)
    {
        if ($language == null) {
            $language = Services::Registry()->get('Languages', 'Current');
        }

        $result = Services::Registry()->get($language . 'translate', $string, $string);
		if ($result == $string) {
			  $this->logUntranslatedString ($string, $language);
		}

		return $result;
    }

	protected function logUntranslatedString ($string, $language)
	{
		if (Services::Registry()->exists('TranslatedStringsMissing')) {
		} else {
			Services::Registry()->createRegistry('TranslatedStringsMissing');
		}

		Services::Registry()->set('TranslatedStringsMissing', $string);

		return;

	}

	public function logUntranslatedStrings()
	{
		if (Services::Filesystem()->fileExists(SITE_LOGS_FOLDER . '/' . 'language.php')) {
		} else {
			return false;
		}

		//$body = Services::Filesystem()->fileRead(SITE_LOGS_FOLDER . '/' . 'language.php');
		Services::Registry()->sort('TranslatedStringsMissing');

		$body = '';
		$translated = Services::Registry()->getArray('TranslatedStringsMissing');
		foreach ($translated as $key => $value) {
			$body .= $key . CHR(10);
		}

		Services::Filesystem()->fileWrite(SITE_LOGS_FOLDER . '/' . 'language.php', $body);

		return true;
	}

    /**
     * retrieve a specified property for the current language
     *
     * @param  $property
     * @param string $default
     * @param null   $language
     *
     * @return mixed
     * @since  1.0
     */
    public function get($property, $default = '', $language = null)
    {
        if ($language == null) {
            $language = Services::Registry()->get('Languages', 'Current');
        }

        return Services::Registry()->get($language, $property, $default);
    }

	/**
	 * Loads the requested language file (as specified by path).
	 * If not successful, loads default language.
	 *
	 * @param $path
	 * @param null $language
	 *
	 * @return bool
	 * @since   1.0
	 */
	public function load($path, $language = null)
    {
        if ($language === null) {
            $language = Services::Registry()->get('Languages', 'Current');
        }

		$language = strtolower(substr($language, 0, 2)) . strtoupper(substr($language, 2, strlen($language) - 2));

		$found = false;

        if ($path == CORE_LANGUAGES) {
            $path .= '/' . $language;
        } else {
            $path .= '/Language/' . $language;;
        }

        if (Services::Filesystem()->folderExists($path)) {
			$found = true;
		} else {
			$current = Services::Registry()->get('Languages', 'Current');
			$current = strtolower(substr($current, 0, 2)) . strtoupper(substr($current, 2, strlen($current) - 2));
			if ($language == $current) {
			} else {
				$language = $current;
				if ($path == CORE_LANGUAGES) {
					$path .= '/' . $language;
				} else {
					$path .= '/Language/' . $language;
				}
			}
        }

		if ($found === true
			|| Services::Filesystem()->folderExists($path)) {
			$found = true;
		} else {
			$default = Services::Registry()->get('Languages', 'Default');
			$default = strtolower(substr($default, 0, 2)) . strtoupper(substr($default, 2, strlen($default) - 2));
			if ($language == $default) {
			} else {
				$language = $default;
				if ($path == CORE_LANGUAGES) {
					$path .= '/' . $language;
				} else {
					$path .= '/Language/' . $language;
				}
			}
		}

		if ($found === true) {
        	return $this->loadPath($path, $language);
		} else {
			return false;
		}
    }

    /**
     * load all language files within specified path
     *
     * @param $path
     * @param $language
     *
     * @return bool
     * @since  1.0
     */
    protected function loadPath($path, $language)
    {
        /** Retrieve paths already loaded */
        $loadedPaths = Services::Registry()->get($language, 'LoadedPaths', array());
        if (in_array($path, $loadedPaths)) {
            return true;
        }

        $loadedPaths[] = $path;
        Services::Registry()->set($language, 'LoadedPaths', $loadedPaths);

        /** Retrieve the files that should now be loaded for this language */
        $loadFiles = Services::Filesystem()->folderFiles($path, '.ini');
        if ($loadFiles === false) {
            return false;
        }

        /** Process each file, loading the language strings followed by the override strings */
        foreach ($loadFiles as $file) {

            /** Load language file */
            $filename = $path . '/' . $file;

            $strings = false;
            if (Services::Filesystem()->fileExists($filename)) {
                $strings = $this->parse($filename);
            }

            if ($strings === false) {
                $strings = array();
            }

            if (count($strings) > 0) {
                foreach ($strings as $key => $value) {
                    Services::Registry()->set($language . 'translate', $key, $value);
                }
            }

            /** override language */
            $filename = $path . '/' . $language . '.override.ini';

            $strings = false;
            if (Services::Filesystem()->fileExists($filename)) {
                $strings = $this->parse($filename);
            }

            if ($strings === false) {
                $strings = array();
            }

            if (count($strings) > 0) {
                foreach ($strings as $key => $value) {
                    Services::Registry()->set($language . 'translate', $key, $value);
                }
            }
        }

        Services::Registry()->sort($language . 'translate');

        return true;
    }

    /**
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
            $strings = parse_ini_string($contents, false, INI_SCANNER_RAW);
        } else {
            return false;
        }

        /** restore previous error tracking */
        if ($track_errors === false) {
            ini_set('track_errors', false);
        }

        if (is_array($strings)) {
            return $strings;
        }

        return false;
    }

	/**
	 * Get Language in priority order
	 *
	 * @return string
	 * @since  1.0
	 */
	protected function getLanguage($language = null)
	{
		/** 1. List of Installed Languages */
		$installed = $this->getInstalledLanguages();

		/** 2. If only one language is installed, use it. */
		if (count($installed) == 1) {
			return $installed[0];
		}

		/** 3. Requested */
		if (in_array($language, $installed)) {
			return $language;
		}

		/** 4. Retrieve from Session, if installed */
		//

		/** 5. Retrieve from User Registry, if installed */
		$language = Services::Registry()->get('User', 'Language', '');
		if (in_array($language, $installed)) {
			return $language;
		}

		/** 6. Browser Language */
		if (isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {
			$browserLanguages = explode(',', $_SERVER['HTTP_ACCEPT_LANGUAGE']);
			if (count($browserLanguages) > 0) {
				foreach ($browserLanguages as $language) {
					if (in_array($language, $installed)) {
						return $language;
					}
				}
			}
		}

		/** 7. Application Configuration */
		$language = Services::Registry()->get('Application', 'Language');
		Services::Registry()->set('Languages', 'Default', $language);
		if (in_array($language, $installed)) {
			return $language;
		}

		/** 8. Shouldn't happen */
		if (in_array('en-GB', $installed)) {
			return 'en-GB';
		}

		//throw exception
		echo 'Language Error: no matching and installed language option.';
		die;
	}

	/**
	 * setLanguageRegistry - Loads the Core Language for specified language
	 *
	 * @param $language
	 * @return string
	 */
	protected function setLanguageRegistry($language)
	{
		/** If it's already loaded, move on */
		if (Services::Registry()->exists($language)) {
			return $language;
		}

		/** Determine if language requested is actually installed */
		$languagesInstalled = Services::Registry()->get('Languages', 'installed');

		$id = 0;
		foreach ($languagesInstalled as $installed) {
			if ($installed->tag == trim($language)) {
				$id = $installed->id;
				break;
			}
		}

		Services::Registry()->createRegistry($language);
		Services::Registry()->set($language, 'id', $id);

		$parameters = Services::Registry()->get('LanguagesSystemParameters');
		foreach ($parameters as $key => $value) {
			Services::Registry()->set($language, $key, $value);
		}

		$rtl = Services::Registry()->get($language, 'rtl');
		if ($rtl == 'rtl') {
			$direction = 'rtl';
		} else {
			$direction = '';
		}
		Services::Registry()->set($language, 'direction', $direction);

		/** Set current language */
		Services::Registry()->set('Languages', 'Current', $language);

		return $language;
	}

    /**
     * Retrieve a full list of installed languages for the application
     *
     * @return bool
     * @since  1.0
     */
    protected function getInstalledLanguages()
    {
        /** During System Initialization Helper is not loaded yet, instantiate here */
        $helper = new ExtensionHelper();
        $installed = $helper->get(0, 'Table', 'Languageservice', 'list', CATALOG_TYPE_LANGUAGE);
        if ($installed === false || count($installed) == 0) {
            //throw error
			echo 'No Language Installed';
			die;
        }

        $languageList = array();
        $tagArray = array();

        foreach ($installed as $language) {

            if ($language->page_type == 'Item') {
                $row = new \stdClass();

                $row->id = $language->extension_id;
                $row->title = $language->title;
                $row->tag = strtolower(substr($language->alias, 0, 2))
                    . strtoupper(substr($language->alias, 2, strlen($language->alias) - 2));

                /** Format language for use comparing to folders/files */
                $tagArray[] = $row->tag;

                $languageList[] = $row;
            }
        }

        Services::Registry()->createRegistry('Languages');
        Services::Registry()->set('Languages', 'installed', $languageList);

        return $tagArray;
    }
}
