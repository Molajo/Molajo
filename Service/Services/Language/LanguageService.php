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
        $language = $this->setCurrentLanguage($language);
		$this->setLanguageRegistry($language);
		return $this->loadLanguageStrings($language);
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
		$string = trim($string);

        if ($language == null) {
            $language = Services::Registry()->get('Languages', 'Current');
        }

        $result = Services::Registry()->get($language, $string);
		if ($result === null) {
		} else {
			return $result;
		}

		if ($language == Services::Registry()->get('Languages', 'Default')) {
		} else {
			$language = Services::Registry()->get('Languages', 'Default');
			$result = Services::Registry()->get($language, $string);
			if ($result === null) {
			} else {
				return $result;
			}
		}

		if ($language == Services::Registry()->get('Languages', 'en-GB')) {
		} else {
			$language = 'en-GB';
			$result = Services::Registry()->get($language, $string);
			if ($result === null) {
			} else {
				return $result;
			}
		}

		if ($string == 'Application configured default:')  {

		} else {
			Services::Language()->logUntranslatedString($string);
		}

		return $string;
    }

    /**
     * get language property
     *
     * @param string $property
     * @param string $default
     * @param string $language
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
	 * Loads language strings into registry
	 *
	 * @param null $language
	 *
	 * @return 	bool
	 * @since   1.0
	 */
	public function loadLanguageStrings($language = null)
    {
        if ($language === null) {
            $language = Services::Registry()->get('Languages', 'Current');
        }

		/** Don't reload */
		if (Services::Registry()->exists($language) == false) {
			 $this->setLanguageRegistry($language);
		}

		$results = $this->getLanguageStrings($language);

		if ($results === false || count($results) == 0) {


			if ($language == Services::Registry()->get('Languages', 'Default')) {
			} else {
				$language == Services::Registry()->get('Languages', 'Default');
				$results = $this->getLanguageStrings($language);
			}
		}

		if ($results === false || count($results) == 0) {

			if ($language == 'en-GB') {
			} else {
				$language == Services::Registry()->get('Languages', 'en-GB');
				$results = $this->getLanguageStrings($language);
			}
		}

		if ($results === false || count($results) == 0) {
			return false;
		}

		if (count($results) == 0 || $results === false) {
		} else {
			foreach ($results as $item) {

				if (trim($item->content_text) == '' || $item->content_text === null) {
					Services::Registry()->set($language, trim($item->title), trim($item->title));
				} else {
					Services::Registry()->set($language, trim($item->title), $item->content_text);
				}
			}
		}

		return true;
    }

	/**
	 * Determine language to be used as current
	 *
	 * @return string
	 * @since  1.0
	 */
	protected function setCurrentLanguage($language = null)
	{
		$installed = $this->getInstalledLanguages();

		if (count($installed) == 1) {
			return $installed[0];
		}

		if (in_array($language, $installed)) {
			return $language;
		}

		/** todo: Retrieve from Session, if installed */

		$language = Services::Registry()->get('User', 'Language', '');
		if (in_array($language, $installed)) {
			return $language;
		}

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

		$language = Services::Registry()->get('Configuration', 'Language');
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
		if (Services::Registry()->exists($language)) {
			return $language;
		}

		$languagesInstalled = Services::Registry()->get('Languages', 'installed');
		foreach ($languagesInstalled as $installed) {
			if ($installed->tag == trim($language)) {
				$id = $installed->id;
				break;
			}
		}

		Services::Registry()->createRegistry($language);

		Services::Registry()->set($language, 'id', $id);
		Services::Registry()->set($language, 'title', $installed->title);
		Services::Registry()->set($language, 'tag', $installed->tag);
		Services::Registry()->set($language, 'rtl', $installed->rtl);
		Services::Registry()->set($language, 'direction', $installed->direction);
		Services::Registry()->set($language, 'first_day', $installed->first_day);

		Services::Registry()->set('Languages', 'Current', $language);

		Services::Registry()->sort($language);

		return $language;
	}

	/**
	 * Get language strings from database
	 *
	 * @return bool
	 * @since  1.0
	 */
	protected function getLanguageStrings($language)
	{
		$controllerClass = 'Molajo\\MVC\\Controller\\Controller';
		$connect = new $controllerClass();

		$results = $connect->connect('System', 'Languagestrings');
		if ($results === false) {
			return false;
		}

		$primary_prefix = $connect->get('primary_prefix', 'a');

		$connect->model->query->select(
			$connect->model->db->qn($primary_prefix)
			. '.'
			. $connect->model->db->qn('title'));

		$connect->model->query->select(
			$connect->model->db->qn($primary_prefix)
				. '.'
				. $connect->model->db->qn('content_text'));

		$connect->model->query->where(
			$connect->model->db->qn($primary_prefix)
				. '.' . $connect->model->db->qn('language')
				. ' = '
				. $connect->model->db->q($language)
		);

		$connect->model->query->order(
			$connect->model->db->qn($primary_prefix)
				. '.'
				. $connect->model->db->qn('title')
		);

		$connect->set('model_offset', 0);
		$connect->set('model_count', 99999);

		return $connect->getData('List');
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

		/* Value preserves case */
		Services::Registry()->set('TranslatedStringsMissing', $string, $string);

		return;

	}

	/**
	 * Log missing strings
	 *
	 * @return bool
	 */
	public function logUntranslatedStrings()
	{
		Services::Registry()->sort('TranslatedStringsMissing');

		$body = '';
		$translated = Services::Registry()->getArray('TranslatedStringsMissing');

		if (count($translated) === 0) {
			return true;
		}

		$controllerClass = 'Molajo\\MVC\\Controller\\Controller';
		$connect = new $controllerClass();

		$results = $connect->connect('System', 'Languagestrings');
		if ($results === false) {
			return false;
		}

		$connect->model->insertLanguageString($translated);

		return true;
	}

    /**
     * Retrieve installed languages for this application
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

			$row = new \stdClass();

			$row->id = $language->extension_id;
			$row->title = $language->subtitle;
			$row->tag = $language->parameters_tag;
			$tagArray[] = $language->parameters_tag;
			$row->locale = $language->parameters_locale;

			if ($language->parameters_rtl == 1) {
				$row->rtl = $language->parameters_rtl;
				$row->direction = 'rtl';
			} else {
				$row->rtl = $language->parameters_rtl;
				$row->direction = '';
			}
			$row->first_day = $language->parameters_first_day;

			$languageList[] = $row;
        }

        Services::Registry()->createRegistry('Languages');
        Services::Registry()->set('Languages', 'installed', $languageList);

        return $tagArray;
    }
}
