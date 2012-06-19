<?php
/**
 * @package    Molajo
 * @copyright  2012 Amy Stephen. All rights reserved.
 * @license    GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
namespace Molajo\Service\Services\Language;

use Molajo\Service\Services;
use Molajo\Extension\Helper\ExtensionHelper;

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
		$this->setInstalledLanguageRegistry();

		$language = $this->getLanguage($language);

		return $this;
	}

	/**
	 * Translate a specified string to the requested, or current, language
	 *
	 * @param string Value to be translated
	 *
	 * @return null
	 * @since   1.0
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
	 * @param string
	 *
	 * @return null
	 * @since   1.0
	 */
	public function get($property, $default = '', $language = null)
	{
		if ($language == null) {
			$language = Services::Registry()->get('Languages', 'Current');
		}
		return Services::Registry()->get(
			$language,
			$property,
			$default
		);
	}


	/**
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
		/** Determine if the files have already been loaded */
		$currentLanguage = Services::Registry()->get('Languages', 'Current');

		/** Finish path depending on whether it's a request for core or extension files */
		if ($path == EXTENSIONS_LANGUAGES) {
			$path = $path . '/' . $currentLanguage;
		} else {
			$path = $path . '/Language';
		}

		if (Services::Filesystem()->folderExists($path)) {
		} else {
			return false;
		}

		/** Load files for current language */
		$loaded = $this->loadPath($path, $currentLanguage);
		if ($loaded == false) {
		} else {
			return true;
		}

		/** If the requested language is not available, load the default */
		$defaultLanguage = Services::Registry()->get('Languages', 'Default');
		if ($currentLanguage == $defaultLanguage) {
			return true;
		}

		$loaded = $this->loadPath($path, $defaultLanguage);

		return $loaded;
	}

	/**
	 * Retrieves the language requested, or the default language
	 *
	 * @return string locale or null if not found
	 * @since   1.0
	 */
	protected function getLanguage($language = null)
	{
		/** Load the requested language( or use the default) if not loaded */
		if ($language == null) {
			$language = $this->getDefaultLanguage();
			Services::Registry()->set('Languages', 'Default', $language);
		}

		/** Has the language already been loaded? */
		if (Services::Registry()->exists($language) == true) {
			return true;
		}

		$language = $this->setLanguageRegistry($language);
		if ($language == false) {
			return false;
		}

		/** load language strings */
		$path = EXTENSIONS_LANGUAGES;
		return $this->load($path);

	}

	/**
	 * Get Default Language when no specific language is requested
	 *
	 * @return bool|null|string
	 * @since  1.0
	 */
	protected function getDefaultLanguage()
	{

		$language = 0;

		/** 1. if there is just one, take it */
		$results = Services::Registry()->get('Languages', 'installed', array());
		if (count($results) == 1) {
			foreach ($results as $language) {
				return $language->tag;
			}
		}

		/** Retrieve valid list */
		$installed = array();
		foreach ($results as $language) {
			$installed[] = $language->tag;
		}

		/** 2. session */
		//

		/** 3. user  */
		$language = Services::Registry()->get('User', 'language', false);
		if ($language === false) {
		} elseif (in_array($language, $installed)) {
			return $language;
		}

		/** 4. language of browser */
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

		/** 5. Application configuration */
		$language = Services::Registry()->get('Application', 'DefaultLanguage', '');
		if (in_array($language, $installed)) {
			return $language;
		}

		/** 6. default */
		return 'en-GB';
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
		/** Retrieve paths already loaded */
		$loadedPaths = Services::Registry()->get($language, 'LoadedPaths', array());

		/** Do not reload */
		if (in_array($path, $loadedPaths)) {
			return true;
		} else {
			$loadedPaths[] = $path;
		}

		/** Retrieve the list of files already loaded for this language */
		$loadedFiles = Services::Registry()->get($language, 'LoadedFiles', array());

		/** Retrieve the list of strings already loaded for this language */
		$loadedStrings = Services::Registry()->get($language, 'LoadedStrings', array());

		/** Retrieve the files that should now be loaded for this language */
		$loadFiles = Services::Filesystem()->folderFiles($path, '.ini');
		if ($loadFiles == false) {
			return true;
		}

		$loadedStrings = Services::Registry()->getArray($language . 'translate');

		/** Process each file, loading the language strings and override strings */
		foreach ($loadFiles as $file) {

			$filename = $path . '/' . $file;

			/** standard file */
			if (in_array($filename, $loadedFiles)) {
			} else {

				/** Load primary file */
				$strings = $this->parse($filename);
				$loadedFiles[] = $filename;

				/** load the corresponding override */
				$filename = $path . '/' . $language . '.override.ini';
				if (Services::Filesystem()->fileExists($filename)) {
					$override_strings = $this->parse($filename);
					$loadedFiles[] = $filename;
				} else {
					$override_strings = array();
				}

				/** Save strings in an array */
				$new = array_merge($strings, $override_strings);
			}
		}

		$loadedStrings = array_merge($loadedStrings, $new);

		/** Update registry for work accomplished */
		Services::Registry()->set($language, 'LoadedPaths', $loadedPaths);
		Services::Registry()->set($language, 'LoadedFiles', $loadedFiles);
		Services::Registry()->set($language, 'LoadedStrings', $loadedStrings);

		foreach($loadedStrings as $key => $value) {
			Services::Registry()->set($language.'translate', $key, $value);
		}
		Services::Registry()->sort($language.'translate');

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
	 * Retrieve a full list of installed languages for the application
	 *
	 * @return bool
	 * @since  1.0
	 */
	protected function setInstalledLanguageRegistry()
	{
		/** During System Initialization Helper is not loaded yet, instantiate here */
		$helper = new ExtensionHelper();
		$installed = $helper->get(0, 'Table', 'Languages', 'list', 1100);
		if ($installed == false || count($installed) < 1) {
			return false;
		}

		$languageList = array();
		foreach ($installed as $language) {

			$row = new \stdClass();

			$row->id = $language->extension_id;
			$row->title = $language->title;
			$row->tag = $language->alias;

			$languageList[] = $row;
		}

		Services::Registry()->createRegistry('Languages');
		Services::Registry()->set('Languages', 'installed', $languageList);

		return;
	}

	/**
	 * setLanguageRegistry for requested language
	 *
	 * @param $language
	 * @return bool
	 */
	protected function setLanguageRegistry($language)
	{
		/** Retrieve requested language */
		if (Services::Registry()->exists($language)) {
			return $language;
		}

		/** Determine if language requested is actually installed */
		$languagesInstalled = Services::Registry()->get('Languages', 'installed');

		$id = 0;
		foreach ($languagesInstalled as $installed) {
			if ($installed->tag == $language) {
				$id = $installed->id;
				break;
			}
		}
		if ($id == 0) {
			return false;
		}

		/** During System Initialization Helper is not loaded yet, instantiate here */
		$helper = new ExtensionHelper();
		$item = $helper->get($id, 'Table', 'Languages', 'item');
		if ($item == false) {
			return false;
		}

		Services::Registry()->createRegistry($language);
		Services::Registry()->set($language, 'id', $id);

		$parameters = Services::Registry()->get('LanguagesTableParameters');
		foreach ($parameters as $key => $value) {
			Services::Registry()->set($language, $key, $value);
		}

		$ltr = Services::Registry()->get($language, 'ltr');
		if ($ltr == 'ltr') {
			$direction = 'ltr';
		} else {
			$direction = '';
		}
		Services::Registry()->set($language, 'direction', $direction);

		/** Set current language */
		Services::Registry()->set('Languages', 'Current', $language);

		return $language;
	}
}
