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
        $this->setInstalledLanguages();
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

            $this->setLanguage();

            $language = $this->service_class_instance->get('language');

            $registry = $this->service_class_instance->get('registry');

            foreach ($registry as $entry) {

                if ($entry->tag == $language) {
                    $this->service_class_instance->set('registry', $entry);
                    break;
                }
            }
        }

        $this->setLanguageStrings();

        $this->service_class_instance->set(
            'profile_missing_strings',
            Services::Application()->get('profiler_collect_missing_language_strings')
        );

        if (Services::User()->get('administrator') === 1) {
            $this->service_class_instance->set('insert_missing_strings', 1);
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
    protected function setInstalledLanguages()
    {
        $extension_class    = $this->frontcontroller_instance->get_class_array('ExtensionHelper');
        $extension_instance = new $extension_class();

        $results = $extension_instance->get(0, 'Language', 'Datasource', 'Languageservice');

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
            $temp_row->tag    = $language->parameters_tag;
            $temp_row->locale = $language->parameters_locale;

            if ($language->parameters_rtl == 1) {
                $temp_row->rtl       = $language->parameters_rtl;
                $temp_row->direction = 'rtl';
            } else {
                $temp_row->rtl       = $language->parameters_rtl;
                $temp_row->direction = '';
            }
            $temp_row->first_day = $language->parameters_first_day;

            $languageList[] = $temp_row;
            $tagArray[]     = $language->parameters_tag;
        }

        $this->service_class_instance->set('installed', $tagArray);
        $this->service_class_instance->set('registry', $languageList);

        return;
    }


    /**
     * Sets language based on specific order of checking values
     *
     * @return  void
     * @since   1.0
     * @throws  \Exception
     */
    protected function setLanguage()
    {
        $language = $this->service_class_instance->get('language');

        if ($language == '') {
        } else {
            if (in_array($language, $this->get('installed'))) {
                $this->service_class_instance->set('language', $language);

                return;
            }
        }

        if (count($this->service_class_instance->get('installed')) == 1) {
            $languages = $this->service_class_instance->get('installed');
            $this->service_class_instance->set('language', $languages[0]);

            return;
        }

        // session

        $language = Services::User()->get('language');
        if (in_array($language, $this->get('installed'))) {
            $this->service_class_instance->set('language', $language);

            return;
        }

        $language = Services::Application()->get('language');
        if (in_array($language, $this->get('installed'))) {
            $this->service_class_instance->set('language', $language);

            return;
        }

        if (isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {
            $browserLanguages = explode(',', $_SERVER['HTTP_ACCEPT_LANGUAGE']);
            if (count($browserLanguages) > 0) {
                foreach ($browserLanguages as $language) {
                    if (in_array($language, $this->service_class_instance->get('installed'))) {
                        $this->service_class_instance->set('language', $language);

                        return;
                    }
                }
            }
        }

        $language = 'en-GB';
        if (in_array($language, $this->get('installed'))) {
            $this->service_class_instance->set('language', $language);

            return;
        }

        throw new \Exception
        ('Language Services: No Language Define.');
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
        $language = $this->service_class_instance->get('language');

        $controllerClass = CONTROLLER_CLASS_NAMESPACE;
        $controller      = new $controllerClass();
        $controller->getModelRegistry('System', 'Languagestrings', 1);

        $controller->set('check_view_level_access', 1, 'model_registry');
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
                $strings[$item->title] = $item->title;
            } else {
                $strings[$item->title] = $item->content_text;
            }
        }

        $this->service_class_instance->set('strings', $strings);

        return;
    }
}
