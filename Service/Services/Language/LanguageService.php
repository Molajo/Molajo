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
 * List installed languages:
 * $list = Services::Registry()->get('Languages', 'installed');
 *
 * Current language:
 * $current = Services::Registry()->get('Languages', 'Current');
 *
 * Default language:
 * $default = Services::Registry()->get('Languages', 'Default');
 *
 * All values for a specific language:
 * Services::Registry()->get('en-GB', '*');
 *
 * Specific value for specific language
 * Services::Registry()->get('en-GB', 'direction');
 *
 * Other language key values:
 * 	id, name, rtl, local, first_day
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
        $language = $this->setLanguageOption($language);

        $this->setLanguageRegistry($language);

        $this->load($language);

        return $this;
    }

    /**
     * Translate string
     *
     * @param string $string
     * @param string $language
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
		} else {
			return $result;
		}

		if ($language == Services::Registry()->get('Languages', 'Default')) {
		} else {
			$language = Services::Registry()->get('Languages', 'Default');
			$result = Services::Registry()->get($language . 'translate', $string, $string);
			if ($result == $string) {
			} else {
				return $result;
			}
		}

		if ($language == Services::Registry()->get('Languages', 'en-GB')) {
		} else {
			$language = 'en-GB';
			$result = Services::Registry()->get($language . 'translate', $string, $string);
			if ($result == $string) {
			} else {
				return $result;
			}
		}

		return $result;
    }

	/**
	 * Log requests for translations that could not be processed
	 *
	 * @param $string
	 */
	protected function logUntranslatedString ($string)
	{
		if (Services::Registry()->exists('TranslatedStringsMissing')) {
		} else {
			Services::Registry()->createRegistry('TranslatedStringsMissing');
		}

		Services::Registry()->set('TranslatedStringsMissing', $string);

		return;

	}

	/**
	 * Add missing strings to log file - or database
	 *
	 * @return bool
	 */
	public function logUntranslatedStrings()
	{

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
	 * Loads the requested language
	 * If not successful, loads default language.
	 *
	 * @param null $language
	 *
	 * @return bool
	 * @since   1.0
	 */
	public function load($language = null)
    {
        if ($language === null) {
            $language = Services::Registry()->get('Languages', 'Current');
        }

		$language = strtolower(substr($language, 0, 2)) . strtoupper(substr($language, 2, strlen($language) - 2));

		//retrieve content
        if ($results === false || count($results) == 0) {
			$current = Services::Registry()->get('Languages', 'Default');
			//read
        }


		//retrieve content
		if ($results === false || count($results) == 0) {
			$current = Services::Registry()->get('Languages', 'en-GB');
			//read
		}

		if ($found === true) {
        	return $this->loadPath($path, $language);
		} else {
			return false;
		}

            if (count($strings) > 0) {
                foreach ($strings as $key => $value) {
                    Services::Registry()->set($language . 'translate', $key, $value);
                }
            }

        Services::Registry()->sort($language . 'translate');

        return true;
    }


	/**
	 * Determine which Language in priority order
	 *
	 * @return string
	 * @since  1.0
	 */
	protected function setLanguageOption($language = null)
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
