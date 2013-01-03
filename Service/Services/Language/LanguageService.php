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
     * Helpers
     *
     * @var    object
     * @since  1.0
     */
    protected $extensionHelper;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->extensionHelper = new ExtensionHelper();
    }

    /**
     * Load language file for specific language
     *
     * @param   string  $language
     *
     * @return  null
     * @since   1.0
     */
    public function initialise($language = null)
    {

        $language = $this->setCurrentLanguage($language);
        $this->setLanguageRegistry($language);

        return $this->loadLanguageStrings($language);
    }

    /**
     * Get language property
     *
     * @param   string  $property
     * @param   string  $default
     * @param   string  $language
     *
     * @return  mixed
     * @since   1.0
     */
    public function get($property, $default = '', $language = null)
    {
        if ($language == null) {
            $language = Services::Registry()->get('Languages', 'Current');
        }

        if ($property == 'installed') {
            return Services::Registry()->get('Languages', 'Installed');

        } elseif ($property == 'default') {
            return Services::Registry()->get('Languages', 'Default');

        } elseif ($property == 'current') {
            return Services::Registry()->get('Languages', 'Current');
        }

        return Services::Registry()->get('Languages' . $language, $property, $default);
    }

    /**
     * Translate String
     *
     * @param   string  $string
     * @param   string  $language
     *
     * @return  mixed
     * @since   1.0
     */
    public function translate($string, $language = null)
    {
        $string = trim($string);

        if ($language == null) {
            $language = Services::Registry()->get('Languages', 'Current');
        }

        $result = Services::Registry()->get('Languages' . $language, $string, '');
        if ($result == '') {
        } else {
            return $result;
        }

        if ($language == Services::Registry()->get('Languages', 'Default')) {
        } else {
            $language = Services::Registry()->get('Languages', 'Default');
            $result   = Services::Registry()->get('Languages' . $language, $string, '');
            if ($result == '') {
            } else {
                return $result;
            }
        }

        if ($language == Services::Registry()->get('Languages', 'en-GB')) {
        } else {
            $language = 'en-GB';
            $result   = Services::Registry()->get('Languages' . $language, $string, '');
            if ($result == '') {
            } else {
                return $result;
            }
        }

        if ($string == 'Application configured default:') {

        } else {
            Services::Language()->logUntranslatedString($string);
        }

        return $string;
    }

    /**
     * Language strings found within the code but not identified to the database are captured and
     *      inserted into the language strings table
     *
     * @return  bool
     * @since   1.0
     */
    public function logUntranslatedStrings()
    {
        if (Services::Registry()->get('User', 'username') == 'admin') {
        } else {
            return true;
        }

        if (Services::Registry()->get('Configuration', 'profiler_collect_missing_language_strings') == '1') {
        } else {
            return true;
        }

        Services::Registry()->sort('Languages' . 'TranslatedStringsMissing');

        $body       = '';
        $translated = Services::Registry()->getArray('Languages' . 'TranslatedStringsMissing');

        if (count($translated) === 0) {
            return true;
        }

        $controllerClass = CONTROLLER_CLASS_NAMESPACE;
        $controller      = new $controllerClass();
        $controller->getModelRegistry('System', 'Languagestrings', 1);

        $controller->set('check_view_level_access', 0, 'model_registry');
        $controller->model->insertLanguageString($translated);

        return true;
    }

    /**
     * Log requests for translations that could not be processed
     *
     * @param   string  $string
     *
     * @return  void
     * @since   1.0
     */
    protected function logUntranslatedString($string)
    {
        if (Services::Registry()->exists('Languages' . 'TranslatedStringsMissing')) {
        } else {
            Services::Registry()->createRegistry('Languages' . 'TranslatedStringsMissing');
        }

        Services::Registry()->set('Languages' . 'TranslatedStringsMissing', $string, $string);

        return;
    }

    /**
     * Loads language strings into registry
     *
     * @param   null  $language
     *
     * @return  bool
     * @since   1.0
     */
    protected function loadLanguageStrings($language = null)
    {
        if ($language === null) {
            $language = Services::Registry()->get('Languages', 'Current');
        }

        if (Services::Registry()->exists('Languages' . $language) == false) {
            $this->setLanguageRegistry('Languages' . $language);
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
                    Services::Registry()->set('Languages' . $language, trim($item->title), trim($item->title));
                } else {
                    Services::Registry()->set('Languages' . $language, trim($item->title), $item->content_text);
                }
            }
        }

        return true;
    }

    /**
     * Determine language to be used as current
     *
     * @param   null|string  $language
     *
     * @return  null/string
     * @since   1.0
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

        /** @todo Retrieve from Session, if installed */

        $language = Services::Registry()->get('User', CATALOG_TYPE_LANGUAGE_LITERAL, '');
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

        $language = Services::Registry()->get('Configuration', CATALOG_TYPE_LANGUAGE_LITERAL);

        Services::Registry()->set('Languages', 'Default', $language);

        if (in_array($language, $installed)) {
            return $language;
        }

        if (in_array('en-GB', $installed)) {
            return 'en-GB';
        }

        throw new \Exception('Language: Logic error');
    }

    /**
     * setLanguageRegistry - Loads the Core Language for specified language
     *
     * @param   string  $language
     *
     * @return  string
     */
    protected function setLanguageRegistry($language)
    {
        if (Services::Registry()->exists('Languages' . $language)) {
            return $language;
        }

        $languagesInstalled = Services::Registry()->get('Languages', 'installed');

        if ($languagesInstalled === false || count($languagesInstalled) == 0) {
            throw new Exception('Language: ' . $language . ' Query returned no data');
        }

        foreach ($languagesInstalled as $installed) {
            if ($installed->tag == trim($language)) {
                $id = $installed->id;
                break;
            }
        }

        Services::Registry()->createRegistry('Languages' . $language);

        Services::Registry()->set('Languages' . $language, 'id', $id);
        Services::Registry()->set('Languages' . $language, 'title', $installed->title);
        Services::Registry()->set('Languages' . $language, 'tag', $installed->tag);
        Services::Registry()->set('Languages' . $language, 'rtl', $installed->rtl);
        Services::Registry()->set('Languages' . $language, 'direction', $installed->direction);
        Services::Registry()->set('Languages' . $language, 'first_day', $installed->first_day);

        Services::Registry()->set('Languages', 'Current', $language);

        Services::Registry()->sort('Languages' . $language);

        return $language;
    }

    /**
     * Get language strings from database
     *
     * @param   $language
     *
     * @return  bool
     * @since   1.0
     */
    protected function getLanguageStrings($language)
    {
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

        return $controller->getData(QUERY_OBJECT_LIST);
    }

    /**
     * Retrieve installed languages for this application
     *
     * @return   bool
     * @since    1.0
     */
    protected function getInstalledLanguages()
    {
        $installed = $this->extensionHelper->get(0, CATALOG_TYPE_LANGUAGE, 'datasource', 'Languageservice');

        if ($installed === false || count($installed) == 0) {
            throw new \Exception('Languages: No languages installed');
        }

        $languageList = array();
        $tagArray     = array();

        foreach ($installed as $language) {

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

        Services::Registry()->createRegistry('Languages');
        Services::Registry()->set('Languages', 'installed', $languageList);

        return $tagArray;
    }
}
