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
 * LanguageService
 *
 * @package     Molajo
 * @subpackage  Language
 * @since       1.0
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
 * Insert strings found in code but not in database - initiated by the Applications cLass after render
 *      Services::Language()->logUntranslatedStrings();
 */
Class LanguageService
{
    /**
     * Load language file for specific language
     *
     * @param   string  $language
     *
     * @return  null
     * @since   1.0
     */
    public function load($language = null)
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
            $language = Services::Registry()->get(LANGUAGES_LITERAL, 'Current');
        }

        if ($property == 'installed') {
            return Services::Registry()->get(LANGUAGES_LITERAL, 'Installed');

        } elseif ($property == 'default') {
            return Services::Registry()->get(LANGUAGES_LITERAL, 'Default');

        } elseif ($property == 'current') {
            return Services::Registry()->get(LANGUAGES_LITERAL, 'Current');
        }

        return Services::Registry()->get(LANGUAGES_LITERAL . $language, $property, $default);
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
            $language = Services::Registry()->get(LANGUAGES_LITERAL, 'Current');
        }

        $result = Services::Registry()->get(LANGUAGES_LITERAL . $language, $string, '');
        if ($result == '') {
        } else {
            return $result;
        }

        if ($language == Services::Registry()->get(LANGUAGES_LITERAL, 'Default')) {
        } else {
            $language = Services::Registry()->get(LANGUAGES_LITERAL, 'Default');
            $result = Services::Registry()->get(LANGUAGES_LITERAL . $language, $string, '');
            if ($result == '') {
            } else {
                return $result;
            }
        }

        if ($language == Services::Registry()->get(LANGUAGES_LITERAL, 'en-GB')) {
        } else {
            $language = 'en-GB';
            $result = Services::Registry()->get(LANGUAGES_LITERAL . $language, $string, '');
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
        if (Services::Registry()->get(USER_LITERAL, 'username') == 'admin') {
        } else {
            return true;
        }

        if (Services::Registry()->get(CONFIGURATION_LITERAL, 'profiler_collect_missing_language_strings') == '1') {
        } else {
            return true;
        }

        Services::Registry()->sort(LANGUAGES_LITERAL . 'TranslatedStringsMissing');

        $body = '';
        $translated = Services::Registry()->getArray(LANGUAGES_LITERAL . 'TranslatedStringsMissing');

        if (count($translated) === 0) {
            return true;
        }

        $controllerClass = CONTROLLER_CLASS;
        $controller = new $controllerClass();
        $controller->getModelRegistry(SYSTEM_LITERAL, 'Languagestrings');
        $controller->setDataobject();

        $controller->set('check_view_level_access', 0);
        $controller->model->insertLanguageString($translated);

        return true;
    }

    /**
     * Log requests for translations that could not be processed
     *
     * @param   $string
     *
     * @return  void
     * @since   1.0
     */
    protected function logUntranslatedString($string)
    {
        if (Services::Registry()->exists(LANGUAGES_LITERAL . 'TranslatedStringsMissing')) {
        } else {
            Services::Registry()->createRegistry(LANGUAGES_LITERAL . 'TranslatedStringsMissing');
        }

        Services::Registry()->set(LANGUAGES_LITERAL . 'TranslatedStringsMissing', $string, $string);

        return;
    }

    /**
     * Loads language strings into registry
     *
     * @param   string $language, optional
     *
     * @return     bool
     * @since   1.0
     */
    protected function loadLanguageStrings($language = null)
    {
        if ($language === null) {
            $language = Services::Registry()->get(LANGUAGES_LITERAL, 'Current');
        }

        if (Services::Registry()->exists(LANGUAGES_LITERAL . $language) == false) {
            $this->setLanguageRegistry(LANGUAGES_LITERAL . $language);
        }

        $results = $this->getLanguageStrings($language);

        if ($results === false || count($results) == 0) {
            if ($language == Services::Registry()->get(LANGUAGES_LITERAL, 'Default')) {
            } else {
                $language == Services::Registry()->get(LANGUAGES_LITERAL, 'Default');
                $results = $this->getLanguageStrings($language);
            }
        }

        if ($results === false || count($results) == 0) {

            if ($language == 'en-GB') {
            } else {
                $language == Services::Registry()->get(LANGUAGES_LITERAL, 'en-GB');
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
                    Services::Registry()->set(LANGUAGES_LITERAL . $language, trim($item->title), trim($item->title));
                } else {
                    Services::Registry()->set(LANGUAGES_LITERAL . $language, trim($item->title), $item->content_text);
                }
            }
        }

        return true;
    }

    /**
     * Determine language to be used as current
     *
     * @param   string $language, optional
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

        /** todo: Retrieve from Session, if installed */

        $language = Services::Registry()->get(USER_LITERAL, CATALOG_TYPE_LANGUAGE_LITERAL, '');
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

        $language = Services::Registry()->get(CONFIGURATION_LITERAL, CATALOG_TYPE_LANGUAGE_LITERAL);

        Services::Registry()->set(LANGUAGES_LITERAL, 'Default', $language);

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
     * @return  string
     */
    protected function setLanguageRegistry($language)
    {
        if (Services::Registry()->exists(LANGUAGES_LITERAL . $language)) {
            return $language;
        }

        $languagesInstalled = Services::Registry()->get(LANGUAGES_LITERAL, 'installed');

        if ($languagesInstalled === false || count($languagesInstalled) == 0) {
            throw new Exception('Language: ' . $language . ' Query returned no data');
        }

        foreach ($languagesInstalled as $installed) {
            if ($installed->tag == trim($language)) {
                $id = $installed->id;
                break;
            }
        }

        Services::Registry()->createRegistry(LANGUAGES_LITERAL . $language);

        Services::Registry()->set(LANGUAGES_LITERAL . $language, 'id', $id);
        Services::Registry()->set(LANGUAGES_LITERAL . $language, 'title', $installed->title);
        Services::Registry()->set(LANGUAGES_LITERAL . $language, 'tag', $installed->tag);
        Services::Registry()->set(LANGUAGES_LITERAL . $language, 'rtl', $installed->rtl);
        Services::Registry()->set(LANGUAGES_LITERAL . $language, 'direction', $installed->direction);
        Services::Registry()->set(LANGUAGES_LITERAL . $language, 'first_day', $installed->first_day);

        Services::Registry()->set(LANGUAGES_LITERAL, 'Current', $language);

        Services::Registry()->sort(LANGUAGES_LITERAL . $language);

        return $language;
    }

    /**
     * Get language strings from database
     *
     * @return  bool
     * @since   1.0
     */
    protected function getLanguageStrings($language)
    {
        $controllerClass = CONTROLLER_CLASS;
        $controller = new $controllerClass();
        $controller->getModelRegistry(SYSTEM_LITERAL, 'Languagestrings');
        $controller->setDataobject();

        $controller->set('check_view_level_access', 0);
        $primary_prefix = $controller->get('primary_prefix', 'a');

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

        $controller->set('model_offset', 0);
        $controller->set('model_count', 99999);

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
        $helper = new ExtensionHelper();
        $installed = $helper->get(0, DATASOURCE_LITERAL, 'Languageservice', QUERY_OBJECT_LIST, CATALOG_TYPE_LANGUAGE);
        if ($installed === false || count($installed) == 0) {
            throw new Exception('Languages: No languages installed');
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

        Services::Registry()->createRegistry(LANGUAGES_LITERAL);
        Services::Registry()->set(LANGUAGES_LITERAL, 'installed', $languageList);

        return $tagArray;
    }
}
