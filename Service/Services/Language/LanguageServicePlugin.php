<?php
/**
 * Language Service Plugin
 *
 * @package      Niambie
 * @license      MIT
 * @copyright    2013 Amy Stephen. All rights reserved.
 */
namespace Molajo\Service\Services\Language;

use Molajo\Service\Services;
use Molajo\Service\ServicesPlugin;

defined('NIAMBIE') or die;

/**
 * Language Service Plugin
 *
 * @author       Amy Stephen
 * @license      MIT
 * @copyright    2013 Amy Stephen. All rights reserved.
 * @since        1.0
 */
Class LanguageServicePlugin extends ServicesPlugin
{
    /**
     * on Before Startup Event
     *
     * Follows instantiation of the service class and before the method identified as the "start" method
     *
     * @return  void
     * @since   1.0
     */
    public function onBeforeServiceInitialise()
    {
        $this->getInstalledLanguages();
    }

    /**
     * On After Startup Event
     *
     * Follows the completion of the start method defined in the configuration
     *
     * @return  void
     * @since   1.0
     */
    public function onAfterServiceInitialise()
    {
        $language = $this->service_class_instance->get('language');

        if ($language == '') {
            $this->setGetSetLanguage();
        }

die;
        $this->service_class_instance->set('default_language', Services::Application()->get('language'));

        $this->service_class_instance->set('profile_missing_strings',
            Services::Application()->get('profiler_collect_missing_language_strings'));

        if (Services::User()->get('administrator') === 1) {
              $this->service_class_instance->set('insert_missing_strings', 1);
        }




        $this->getInstalledLanguages();

        $this->getCurrentLanguage();

        return;
    }

    /**
     * Sets language based on specific order of checking values
     *
     * @return  void
     * @since   1.0
     */
    protected function setGetSetLanguage()
    {


        $language = '';

        $this->service_class_instance->set('user_language', Services::User()->get('language'));

        if ($this->get('instantiated_language', '') == '') {
        } else {
            $language = $this->get('instantiated_language');
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
     * Retrieve installed languages for this application
     *
     * @return  null
     * @since   1.0
     * @throws  \Exception
     */
    protected function getInstalledLanguages()
    {
        $extension_class    = $this->frontcontroller_instance->get_class_array('ExtensionHelper');
        $extension_instance = new $extension_class();

        $results            = $extension_instance->get(0, 'Language', 'Datasource', 'Languageservice');

        var_dump($results);
        die;
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
        var_dump($tagArray);
        die;
        $this->service_class_instance->set('installed_languages', $tagArray);
        $this->service_class_instance->set('installed_language_registries', $languageList);

        return;
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

        /** @todo Retrieve from Session, if installed */

        if (in_array($this->service_class_instance->get('user_language'), $this->service_class_instance->get('installed_languages'))) {
            $this->current_language = $this->service_class_instance->get('user_language');

            return;
        }

        if (isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {

            $browserLanguages = explode(',', $_SERVER['HTTP_ACCEPT_LANGUAGE']);

            if (count($browserLanguages) > 0) {

                foreach ($browserLanguages as $language) {
                    if (in_array($language, $this->service_class_instance->get('installed_languages'))) {
                        $this->current_language = $language;

                        return;
                    }
                }
            }
        }

        if (in_array($this->service_class_instance->get('default_language'), $this->service_class_instance->get('installed_languages'))) {
            $this->current_language = $this->service_class_instance->get('default_language');

            return;
        }

        if (in_array('en-GB', $this->service_class_instance->get('installed_languages'))) {
            $this->current_language = 'en-GB';

            return;
        }

        throw new \Exception('Language: getCurrentLanguage cannot identify an available language.');
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
        $language = $this->service_class_instance->get('current_language', 'en-GB');

        if (in_array($language, $this->service_class_instance->get('loaded_languages', array()))) {
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

    /**
     * On After Read All Event
     *
     * Follows the getData Method and all special field activities
     *
     * @return  void
     * @since   1.0
     */
    public function onAfterReadAll()
    {
        if ($this->model_registry_name == 'LanguagestringsSystem') {
        } else {
            return;
        }

        $this->service_class_instance->set('parameters', Services::Registry()->getArray('ApplicationDatasourceParameters'));
        $this->service_class_instance->set('metadata', Services::Registry()->getArray('ApplicationDatasourceMetadata'));

        return;
    }
}
