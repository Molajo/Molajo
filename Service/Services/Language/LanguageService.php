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

		Services::Registry()->set('Languages', 'Default', $language);

		$language = $this->setLanguageRegistry($language);
		if ($language === false) {
			return false;
		}

		$this->load(EXTENSIONS_LANGUAGES, $language);

		return $this;
	}

	/**
	 * Translate a specified string to the requested, or current, language
	 *
	 * @param $string value to be translated
	 * @param null $language requested language (defaults to current or default)
	 *
	 * @return mixed
	 * @since  1.0
	 */
	public function translate($string, $language = null)
	{
		if ($language == null) {
			$language = Services::Registry()->get('Languages', 'Current');
		}

		return Services::Registry()->get($language . 'translate', $string, $string);
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
	 * @param string $path
	 *
	 * @return boolean
	 * @since   1.0
	 */
	public function load($path, $language = null)
	{
		if ($language === null) {
			$language = Services::Registry()->get('Languages', 'Current');
		}

		/** Core or extension files */
		if ($path == EXTENSIONS_LANGUAGES) {
			$path .= '/' . $language;
		} else {
			$path .= '/Language';
		}

		if (Services::Filesystem()->folderExists($path)) {
		} else {
			return false;
		}

		/** Load files for current language */
		$loaded = $this->loadPath($path, $language);
		if ($loaded === false) {
		} else {
			return true;
		}

		/** If the requested language is not available, load the default */
		$defaultLanguage = Services::Registry()->get('Languages', 'Default');
		if ($language == $defaultLanguage) {
			return true;
		}

		$loaded = $this->loadPath($path, $defaultLanguage);

		return $loaded;
	}

	/**
	 * load all language files located within the specified path
	 *
	 * @param $path
	 * @param $language
	 *
	 * @return bool
	 * @since  1.0
	 */
	protected function loadPath($path, $language)
	{
		/** Format language for use comparing to folders/files */
		//$language = strtolower(substr($language, 0, 2)) . strtoupper(substr($language, 2, strlen($language) - 2));

		/** Retrieve paths already loaded */
		$loadedPaths = Services::Registry()->get($language, 'LoadedPaths', array());

		/** Do not reload */
		if (in_array($path, $loadedPaths)) {
			return true;
		} else {
			$loadedPaths[] = $path;
		}
		Services::Registry()->set($language, 'LoadedPaths', $loadedPaths);

		/** Retrieve the files that should now be loaded for this language */
		$loadFiles = Services::Filesystem()->folderFiles($path, '.ini');
		if ($loadFiles === false) {
			return true;
		}

		/** Process each file, loading the language strings and override strings */
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
			$contents = str_replace(LANGUAGE_QUOTE_REPLACEMENT, '"\""', $contents);
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
	 * Get Default Language when no specific language is requested
	 *
	 * @return bool|null|string
	 * @since  1.0
	 */
	protected function getLanguage($language = null)
	{
		/** 1. Use language specified, if it is available */
		$installed = $this->getInstalledLanguages();
		if ($installed === false || count($installed) == 0) {
		} else {
			if (in_array($language, $installed)) {
				return $language;
			}
		}

		/** 2. if there is only one language installed, use it */
		if ($installed === false || count($installed) == 0) {
		} elseif (count($installed) == 1) {
			return $installed[0];
		}

		/** 3. session */
		//

		/** 4. User specified value, it is is installed  */
		$language = Services::Registry()->get('User', 'language', false);
		if ($installed === false || count($installed) == 0) {
		} else {
			if (in_array($language, $installed)) {
				return $language;
			}
		}

		/** 5. language of browser */
		if (isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {
			$browserLanguages = explode(',', $_SERVER['HTTP_ACCEPT_LANGUAGE']);
			if (count($browserLanguages) > 0) {
				foreach ($browserLanguages as $language) {
					if ($installed === false || count($installed) == 0) {
					} else {
						if (in_array($language, $installed)) {
							return $language;
						}
					}
				}
			}
		}

		/** 5. Application configuration */
		$language = Services::Registry()->get('Application', 'DefaultLanguage', '');
		if (trim($language) == '') {
		} elseif ($installed === false || count($installed) == 0) {
		} else {
			if (in_array($language, $installed)) {
				return $language;
			}
		}

		/** 6. default */
		return 'en-GB';
	}

	/**
	 * setLanguageRegistry for requested language
	 *
	 * @param $language
	 * @return bool
	 */
	protected function setLanguageRegistry($language)
	{
		/** If it's already loaded, move on */
		if (Services::Registry()->exists($language)) {
			return $language;
		}

		/** Determine if language requested is actually installed */
		$languagesInstalled = Services::Registry()->get('Languages', 'installed');
		if ($languagesInstalled === false
			|| count($languagesInstalled) == 0
		) {
			return false;
		}

		$id = 0;
		foreach ($languagesInstalled as $installed) {
			if ($installed->tag == trim($language)) {
				$id = $installed->id;
				break;
			}
		}
		if ($id == 0) {
			return false;
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
		$installed = $helper->get(0, 'Table', 'Languageservice', 'list', 1100);
		if ($installed === false || count($installed) < 1) {
			return false;
		}

		$languageList = array();
		$tagArray = array();
		foreach ($installed as $language) {

			$row = new \stdClass();

			$row->id = $language->extension_id;
			$row->title = $language->title;
			$row->tag = strtolower(substr($language->alias, 0, 2))
				. strtoupper(substr($language->alias, 2, strlen($language->alias) - 2));

			/** Format language for use comparing to folders/files */
			$tagArray[] = $row->tag;

			$languageList[] = $row;
		}

		Services::Registry()->createRegistry('Languages');
		Services::Registry()->set('Languages', 'installed', $languageList);

		return $tagArray;
	}
}
