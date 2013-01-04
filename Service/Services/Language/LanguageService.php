<?php
/**
 * Language Service
 *
 * @package      Niambie
 * @license      MIT
 * @copyright    2013 Amy Stephen. All rights reserved.
 */
namespace Molajo\Service\Services\Language;

use Molajo\Service\Services;
use Molajo\Service\Services\Theme\Helper\ExtensionHelper;

defined('NIAMBIE') or die;

/**
 * Language Services supporting translations for the User Interface
 *
 * Get specific information about languages:
 *
 *      Services::Language()->get('installed');
 *      Services::Language()->get('default');
 *      Services::Language()->get('current');
 *      Services::Language()->get('name-of-attribute');
 *          Note: id, name, rtl, local, first_day or '*' (all)
 *
 * Translate a language string:
 *      Services::Language()->translate($string);
 *
 * Insert strings found in code but not in database - initiated by the Applications cLass after render
 *      Services::Language()->logUntranslatedStrings();
 *
 * @author       Amy Stephen
 * @license      MIT
 * @dependency   RegistryService
 * @copyright    2013 Amy Stephen. All rights reserved.
 * @since        1.0
 */
Class LanguageService
{
    /**
     * Default Language
     *
     * @var    string
     * @since  1.0
     */
    protected $default_language;

    /**
     * Current Language
     *
     * @var    string
     * @since  1.0
     */
    protected $current_language;

    /**
     * Language Registry (by language)
     *
     * @var    array
     * @since  1.0
     */
    protected $language_registry = array();

    /**
     * Installed Languages (contains tag values, like en-GB)
     *
     * @var    array
     * @since  1.0
     */
    protected $installed_languages = array();

    /**
     * Language Strings (by language)
     *
     * @var    array
     * @since  1.0
     */
    protected $language_strings = array();

    /**
     * Loaded Languages
     *
     * @var    array
     * @since  1.0
     */
    protected $loaded_languages = array();

    /**
     * User Language
     *
     * @var    string
     * @since  1.0
     */
    protected $user_language;

    /**
     * Display missing strings in profiler
     *
     * @var    bool
     * @since  1.0
     */
    protected $profile_missing_strings;

    /**
     * List of Properties
     *
     * @var    object
     * @since  1.0
     */
    protected $property_array = array(
        'registry',
        'default',
        'current',
        'installed',
        'language',
        'list',
        'strings',
        'user',
        'profile_missing_strings',
        'id',
        'title',
        'tag',
        'locale',
        'rtl',
        'translation',
        'direction'
    );

    /**
     * Load language file for specific language
     *
     * @return  void
     * @since   1.0
     */
    public function initialise()
    {
        $this->profile_missing_strings = 0;
        $this->current_language        = null;
        $this->default_language        = null;
        $this->installed_languages     = array();
        $this->loaded_languages        = array();
        $this->language_strings        = array();

        return;
    }

    /**
     * Get language property
     *
     * @param   string  $key
     * @param   string  $default
     * @param   string  $language
     *
     * @return  array|mixed|string
     * @throws  \OutOfRangeException
     * @since   1.0
     */
    public function get($key, $default = '', $language = null)
    {
        $key = strtolower($key);

        if ($key == 'profile_missing_strings') {
            return $this->profile_missing_strings;
        }

        $this->setGetSetLanguage($language);
        $language = $this->current_language;

        if ($key == 'language') {
            return $language;
        }

        if (in_array($key, $this->property_array)) {
        } else {
            throw new \OutOfRangeException
            ('Language Service: attempting to get value for unknown property: ' . $key);
        }

        if ($key == 'translation') {
            return $this->translate($default, 0);
        }


        if ($key == 'list') {
            return $this->translate($default, 1);
        }


        if ($key == 'installed') {
            return $this->installed_languages;
        }

        if ($key == 'default') {
            return $this->default_language;
        }

        if ($key == 'current') {
            return $this->current_language;
        }

        if ($key == 'registry') {
            if ($this->language_registry == null) {
                $this->language_registry = $default;
            }

            return $this->language_registry;
        }

        if ($key == 'strings') {
            if ($this->language_registry == null) {
                $this->language_registry = $default;
            }

            return $this->language_registry;
        }

        if ($key == 'user') {
            if ($this->language_registry == null) {
                $this->language_registry = $default;
            }

            return $this->language_registry;
        }

        if (isset($this->language_registry[$language]->$key)) {
            return $this->language_registry[$language]->$key;
        }

        return $default;
    }

    /**
     * Set the value of the specified key
     *
     * @param   string  $key
     * @param   mixed   $value
     * @param   mixed   $language
     *
     * @return  void
     * @since   1.0
     * @throws  \OutOfRangeException
     */
    public function set($key, $value = null, $language = null)
    {
        $key = strtolower($key);

        if ($key == 'profile_missing_strings') {
            if ((int)$value == 1) {
                $this->profile_missing_strings = 1;

                return;
            }
        }

        $this->setGetSetLanguage($language);

        $language = $this->current_language;

        if ($key == 'language') {
            return;
        }

        if (in_array($key, $this->property_array)) {
        } else {
            throw new \OutOfRangeException
            ('Language Service: attempting to set value for unknown key: ' . $key);
        }

        $registry = array('id', 'title', 'tag', 'locale', 'rtl', 'direction');
        if (in_array($key, $registry)) {
            $this->language_registry[$language]->$key = $value;

            return;
        }

        $this->$key = $value;

        return;
    }

    /**
     * First time used, this method retrieves installed languages,
     * sets default language (if not set by configuration), sets current language,
     * and retrieves the language strings for the current language.
     *
     * Subsequent times, sets the current language, if not provided and
     * retrieves the language strings for that language, if not loaded.
     *
     * @param   string  $language
     *
     * @return  void
     * @since   1.0
     */
    protected function setGetSetLanguage($language)
    {
        if (count($this->get('installed_languages', array())) == 0) {
            $this->getInstalledLanguages();
        }

        if (in_array($language, $this->get('installed_languages'))) {
        } else {
            $language = null;
        }

        if ($this->default_language == null) {
            $this->default_language = $language;
        }

        if ($this->default_language == null) {
            $this->default_language = 'en-GB';
        }

        $load_language = false;
        if ($language == null) {
        } else {
            $this->current_language = $language;
            $load_language          = true;
        }

        if ($this->current_language == null) {
            $this->current_language = $this->default_language;
            $load_language          = true;
        }

        if ($this->current_language === null) {
            $load_language = true;
        }

        if ($load_language === true) {
            $this->getCurrentLanguage();
            $this->setLanguageStrings();
        }

        return;
    }

    /**
     * Translate String in Current Language, or fall back to defaults
     *
     * @param   string  $string
     * @param   bool    $list
     *
     * @return  mixed
     * @since   1.0
     */
    protected function translate($string, $list = 0)
    {
        $string = strtolower(trim($string));

        $language = $this->get('current_language');

        if ((int)$list == 1) {
            $found = array();

            $keys = array_keys($this->language_strings[$language]);

            foreach ($keys as $key) {
                if (strpos($string, strtolower($key))) {
                    $found[$key] = $this->language_strings[$key];
                }
            }

            return $found;

        } else {

            if (isset($this->language_strings[$language]->$string)) {
                return $this->language_strings[$language]->$string;
            }

            if ($language == $this->get('default_language', 'en-GB')) {
            } else {
                if (isset($this->language_strings[$language]->$string)) {
                    return $this->language_strings[$language]->$string;
                }
            }

            if ($language == 'en-GB') {
            } else {
                $language = 'en-GB';
                if (isset($this->language_strings[$language]->$string)) {
                    return $this->language_strings[$language]->$string;
                }
            }
        }

        $this->logUntranslatedString($string);

        return $string;
    }

    /**
     * Language strings found within the code but not identified to the database are captured and
     *      inserted into the language strings table when admin is logged on
     *
     * @param   $string
     *
     * @return  bool
     * @since   1.0
     */
    protected function logUntranslatedString($string)
    {
        $controllerClass = CONTROLLER_CLASS_NAMESPACE;
        $controller      = new $controllerClass();
        $controller->getModelRegistry('System', 'Languagestrings', 1);

        $controller->set('check_view_level_access', 0, 'model_registry');
        $controller->model->insertLanguageString($string);

        return true;
    }

    /**
     * Determine language to be used as current
     *
     * @return  void
     * @since   1.0
     * @throws  \Exception
     */
    protected function getCurrentLanguage()
    {
        if ($this->current_language == null) {
        } else {
            return;
        }

        if (count($this->get('installed_languages')) == 0) {
            $this->getInstalledLanguages();
        }

        if (count($this->get('installed_languages')) == 1) {
            $installed_language     = $this->get('installed_languages');
            $this->current_language = $installed_language[0];

            return;
        }

        /** @todo Retrieve from Session, if installed */

        if (in_array($this->get('user_language'), $this->get('installed_languages'))) {
            $this->current_language = $this->get('user_language');

            return;
        }

        if (isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {
            $browserLanguages = explode(',', $_SERVER['HTTP_ACCEPT_LANGUAGE']);
            if (count($browserLanguages) > 0) {
                foreach ($browserLanguages as $language) {
                    if (in_array($language, $this->get('installed_languages'))) {
                        $this->current_language = $language;

                        return;
                    }
                }
            }
        }

        if (in_array($this->get('default_language'), $this->get('installed_languages'))) {
            $this->current_language = $this->get('default_language');

            return;
        }

        if (in_array('en-GB', $this->get('installed_languages'))) {
            $this->current_language = 'en-GB';

            return;
        }

        throw new \Exception('Language: getCurrentLanguage cannot identify an available language.');
    }

    /**
     * Retrieve installed languages for this application
     *
     * @return  null
     * @throws  \Exception
     */
    protected function getInstalledLanguages()
    {
        $extensionHelper = new ExtensionHelper();
        $results         = $extensionHelper->get(0, 'Language', 'datasource', 'Languageservice');

        if (is_array($results)) {
        } else {
            $results = array();
        }

        if (count($results) == 0) {
            throw new \Exception('Languages: No languages installed');
        }

        $languageList = array();
        $tagArray     = array();

        foreach ($results as $language) {

            $temp_row = new \stdClass();

            $temp_row->id     = $language->extension_id;
            $temp_row->title  = $language->subtitle;
            $temp_row->tag    = $language->tag;
            $temp_row->locale = $language->locale;

            if ($language->rtl == 1) {
                $temp_row->rtl       = $language->rtl;
                $temp_row->direction = 'rtl';
            } else {
                $temp_row->rtl       = $language->rtl;
                $temp_row->direction = '';
            }
            $temp_row->first_day = $language->first_day;

            $languageList[] = $temp_row;
            $tagArray[]     = $language->tag;
        }

        $this->set('installed_languages', $tagArray);
        $this->set('installed_language_registries', $languageList);

        return;
    }

    /**
     * Set Language Strings for specified language by retrieving from the Database and
     *  storing in property array
     *
     * @return  void
     * @since   1.0
     * @throws  \Exception
     */
    protected function setLanguageStrings()
    {
        $language = $this->get('current_language', 'en-GB');

        if (in_array($language, $this->get('loaded_languages', array()))) {
            return;
        }

        $this->loaded_languages[] = $language;

        $controllerClass = CONTROLLER_CLASS_NAMESPACE;
        $controller      = new $controllerClass();
        $controller->getModelRegistry('System', 'Languagestrings', 1);

        $controller->set('check_view_level_access', 0, 'model_registry');
        $primary_prefix = $controller->get('primary_prefix', 'a', 'model_registry');

        $controller->model->query->select(
            $controller->model->db->qn($primary_prefix)
                . '.'
                . $controller->model->db->qn('title')
        );

        $controller->model->query->select(
            $controller->model->db->qn($primary_prefix)
                . '.'
                . $controller->model->db->qn('content_text')
        );

        $controller->model->query->select(
            $controller->model->db->qn('catalog')
                . '.'
                . $controller->model->db->qn('sef_request')
        );

        $controller->model->query->from(
            $controller->model->db->qn('#__language_strings')
                . ' as '
                . $controller->model->db->qn($primary_prefix)
        );

        $controller->model->query->from(
            $controller->model->db->qn('#__catalog')
                . ' as '
                . $controller->model->db->qn('catalog')
        );

        $controller->model->query->where(
            $controller->model->db->qn($primary_prefix)
                . '.' . $controller->model->db->qn('language')
                . ' = '
                . $controller->model->db->q($language)
        );

        $controller->model->query->where(
            $controller->model->db->qn('catalog')
                . '.' . $controller->model->db->qn('application_id')
                . ' = '
                . (int)APPLICATION_ID
        );

        $controller->model->query->where(
            $controller->model->db->qn($primary_prefix)
                . '.' . $controller->model->db->qn('id')
                . ' = '
                . $controller->model->db->qn('catalog')
                . '.' . $controller->model->db->qn('source_id')
        );

        $controller->model->query->where(
            $controller->model->db->qn($primary_prefix)
                . '.' . $controller->model->db->qn('catalog_type_id')
                . ' = '
                . $controller->model->db->qn('catalog')
                . '.' . $controller->model->db->qn('catalog_type_id')
        );

        $controller->model->query->order(
            $controller->model->db->qn($primary_prefix)
                . '.'
                . $controller->model->db->qn('title')
        );

        $controller->set('model_offset', 0, 'model_registry');
        $controller->set('model_count', 999999, 'model_registry');

        $results = $controller->getData(QUERY_OBJECT_LIST);

        if (is_array($results)) {
        } else {
            $results = array();
        }

        if (count($results) === 0) {
            throw new \Exception
            ('Language Services: No Language string results for Language: ' . $language);
        }

        foreach ($results as $item) {

            $item->title = trim($item->title);

            if (trim($item->content_text) == '' || $item->content_text === null) {
                $this->language_strings[$language][$item->title] = $item->title;
            } else {
                $this->language_strings[$language][$item->title] = $item->content_text;
            }
        }

        return;
    }
}
