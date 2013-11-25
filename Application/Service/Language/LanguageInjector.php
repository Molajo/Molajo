<?php
/**
 * Language Dependency Injector
 *
 * @package    Molajo
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2013 Amy Stephen. All rights reserved.
 */
namespace Molajo\Service\Language;

use Exception;
use Molajo\IoC\Handler\AbstractInjector;
use CommonApi\IoC\ServiceHandlerInterface;
use CommonApi\Exception\RuntimeException;

/**
 * Language Dependency Injector
 *
 * @author     Amy Stephen
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2013 Amy Stephen. All rights reserved.
 * @since      1.0
 */
class LanguageInjector extends AbstractInjector implements ServiceHandlerInterface
{
    /**
     * Language List
     *
     * @var     array
     * @since   1.0
     */
    protected $installed_languages = array();

    /**
     * Language List
     *
     * @var     array
     * @since   1.0
     */
    protected $tag_array = array();

    /**
     * Constructor
     *
     * @param  $options
     *
     * @since  1.0
     */
    public function __construct(array $options = array())
    {
        $options['service_name']             = basename(__DIR__);
        $options['store_instance_indicator'] = true;
        $options['service_namespace']        = 'Molajo\\Language\\Adapter';

        parent::__construct($options);
    }

    /**
     * Instantiate a new handler and inject it into the Adapter for the ServiceHandlerInterface
     * Retrieve a list of Interface dependencies and return the data ot the controller.
     *
     * @return  array
     * @since   1.0
     * @throws  \CommonApi\Exception\RuntimeException;
     */
    public function setDependencies(array $reflection = null)
    {
        $this->reflection = array();

        $options                            = array();
        $this->dependencies                 = array();
        $this->dependencies['Runtimedata']  = $options;
        $this->dependencies['Resources']    = $options;
        $this->dependencies['Database']     = $options;
        $this->dependencies['Fieldhandler'] = $options;

        return $this->dependencies;
    }

    /**
     * Instantiate Class
     *
     * @return  object
     * @since   1.0
     * @throws  \CommonApi\Exception\RuntimeException;
     */
    public function instantiateService()
    {
        $handler = $this->instantiateDatabaseHandler(
            $this->instantiateDatabaseModel(),
            $this->setLanguage()
        );

        try {
            $this->service_instance = new $this->service_namespace (
                $handler,
                $this->setLanguage()
            );
        } catch (Exception $e) {

            throw new RuntimeException
            ('IoC: Injector Instance Failed for ' . $this->service_namespace
            . ' failed.' . $e->getMessage());
        }

        return $this;
    }

    /**
     * Instantiate Database Handler for Language
     *
     * @param   string $model
     * @param   string $language
     *
     * @return  object
     * @since   1.0
     * @throws  \CommonApi\Exception\RuntimeException;
     */
    public function instantiateDatabaseHandler($model, $language)
    {
        $default_language      = null;
        $en_gb_instance        = null;
        $extension_id          = $this->installed_languages['en-GB']->extension_id;
        $extension_instance_id = $this->installed_languages['en-GB']->extension_instance_id;
        $title                 = $this->installed_languages['en-GB']->title;
        $tag                   = $this->installed_languages['en-GB']->tag;
        $locale                = $this->installed_languages['en-GB']->locale;
        $rtl                   = $this->installed_languages['en-GB']->rtl;
        $direction             = $this->installed_languages['en-GB']->rtl;
        $first_day             = $this->installed_languages['en-GB']->first_day;
        $language_utc_offset   = $this->installed_languages['en-GB']->language_utc_offset;

        try {
            $class = 'Molajo\\Language\\Handler\\Database';

            return new $class(
                $language,
                $extension_id,
                $extension_instance_id,
                $title,
                $tag,
                $locale,
                $rtl,
                $direction,
                $first_day,
                $language_utc_offset,
                $model,
                $default_language,
                $en_gb_instance
            );
        } catch (Exception $e) {

            throw new RuntimeException
            ('IoC: Injector Instance Failed for ' . $this->service_namespace
            . ' failed.' . $e->getMessage());
        }
    }

    /**
     * Instantiate Language Model for capturing missing translations
     *
     * @param   $handler
     *
     * @return  object
     * @since   1.0
     * @throws  \CommonApi\Exception\RuntimeException;
     */
    protected function instantiateDatabaseModel()
    {
        $application_id = $this->dependencies['Runtimedata']->application->id;
        $database       = $this->dependencies['Database'];
        $query          = $this->dependencies['Database']->getQueryObject();
        $null_date      = $this->dependencies['Database']->getNullDate();
        $current_date   = $this->dependencies['Database']->getDate();
        $fieldhandler   = $this->dependencies['Fieldhandler'];
        $model_registry = $this->dependencies['Resources']->get(
            'xml:///Molajo//Datasource//Languageservice.xml'
        );

        $class = 'Molajo\\Language\\Handler\\DatabaseModel';

        try {
            $databasemodel = new $class(
                $application_id,
                $database,
                $query,
                $null_date,
                $current_date,
                $fieldhandler,
                $model_registry
            );
        } catch (Exception $e) {

            throw new RuntimeException
            ('IoC: Injector Instance Failed for ' . $class . ' in LanguageInjector ' . $e->getMessage());
        }

        $this->installed_languages = $databasemodel->get('installed_languages');
        $this->tag_array           = $databasemodel->get('tag_array');

        return $databasemodel;
    }

    /**
     * Sets language based on specific order of checking values
     *
     * @return  string
     * @since   1.0
     * @throws  \CommonApi\Exception\RuntimeException;
     */
    protected function setLanguage()
    {
        if (count($this->tag_array) == 1) {
            $languages = $this->tag_array;
            $language  = $languages[0];
            return $language;
        }

        $language = $this->dependencies['User']->getUserData('language');
        if (in_array($language, $this->tag_array)) {
            return $language;
        }

        $language = $this->dependencies['Runtimedata']->application->parameters->language;
        if (in_array($language, $this->tag_array)) {
            return $language;
        }

        //todo: needs work (and likely not worth it)
        if (isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {
            $browserLanguages = explode(',', $_SERVER['HTTP_ACCEPT_LANGUAGE']);
            if (count($browserLanguages) > 0) {
                foreach ($browserLanguages as $language) {
                    if (in_array($language, $this->tag_array)) {
                        return $language;
                    }
                }
            }
        }

        $language = 'en-GB';
        if (in_array($language, $this->tag_array)) {
            return $language;
        }

        throw new RuntimeException
        ('Language Injector: No Language Defined.');
    }

    /**
     * Schedule the Next Service
     *
     * @return  $this
     * @since   1.0
     */
    public function scheduleNextService()
    {
        $options = array();

        $css_options['language_direction']
            = $this->dependencies['Runtimedata']->application->parameters->language_direction;
        $css_options['html5']
            = $this->dependencies['Runtimedata']->application->parameters->application_html5;
        $css_options['line_end']
            = $this->dependencies['Runtimedata']->application->parameters->application_line_end;
//$css_options['mimetype']

        $resources                             = array();
        $resources['Resourcescss']             = $css_options;
        $resources['Resourcescssdeclarations'] = $css_options;
        $resources['Resourcesjs']              = $options;
        $resources['Resourcesjsdeclarations']  = $options;

        $options['resources_array'] = $resources;

        $this->schedule_service['Resourcesquery'] = $options;
        $this->schedule_service['Date']           = array();
        $this->schedule_service['Url']            = array();
        $this->schedule_service['Image']          = array();
        $this->schedule_service['Text']           = array();

        return $this->schedule_service;
    }
}
