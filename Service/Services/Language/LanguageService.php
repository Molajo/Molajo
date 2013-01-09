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

defined('NIAMBIE') or die;

/**
 * Language Services supporting translations for the User Interface
 *
 * Language strings automatically loaded in startup for language determined in this order (and installed):
 *  1. Instantiated value 2. Session 3. User 4. Browser 5. Application Configuration 6. en-GB
 *
 * To load a different language:
 *  $instantiate_the_class = new LanguageService($language);
 *  Then, all interactions should be with $instantiate_the_class instance.
 *
 * To work with the automatically loaded language, use Services::Language(), as shown below.
 *
 * To retrieve the key value (ex. 'en-GB') for the language which is loaded:
 *      Services::Language()->get('language');
 *
 * To translate the string $xyz:
 *      Services::Language()->get('translation', $xyz);
 *
 * To retrieve a list of language strings and translations matching a wildcard value:
 *      Services::Language()->get('translation', $xyz, 1);
 *
 * To retrieve all language strings and translations for the loaded language:
 *      Services::Language()->get('strings');
 *
 * To retrieve a list of all languages installed in this application:
 *      Services::Language()->get('installed');
 *
 * To retrieve a registry attribute value (id, name, rtl, local, first_day) for the loaded language:
 *      Services::Language()->get('name-of-attribute');
 *
 * To retrieve all registry attribute values as an array for the loaded language:
 *      Services::Language()->get('registry');
 *
 * To insert strings found in code but are not already in database
 *      If an administrator is logged on, the primary language services automatically insert untranslated strings
 *      To avoid doing so, override the LanguageServicePlugin and set insert_missing_strings to 0
 *      For instances you define, set the insert_missing_strings, as needed.
 *
 * To log strings found in code but are not already in database
 *      Set the Application configuration option profile_missing_strings to 1 and turn on profiling
 *
 * @author       Amy Stephen
 * @license      MIT
 * @copyright    2013 Amy Stephen. All rights reserved.
 * @since        1.0
 */
Class LanguageService
{
    /**
     * Language used for this instance
     *
     * @var    string
     * @since  1.0
     */
    protected $language;

    /**
     * Default Language Instance if loaded language is missing translation and is not the default language
     *
     * @var    string
     * @since  1.0
     */
    protected $default_language_instance;

    /**
     * en-GB Language Instance if loaded language is missing translation and is not the en-GB language
     *
     * @var    string
     * @since  1.0
     */
    protected $en_gb_instance;

    /**
     * Language Registry for the language loaded in this instance
     *
     * @var    array
     * @since  1.0
     */
    protected $registry = array();

    /**
     * Installed Languages includes tags, like en-GB, for all installed languages
     *
     * @var    array
     * @since  1.0
     */
    protected $installed = array();

    /**
     * Language Strings for the language loaded in this instance
     *
     * @var    array
     * @since  1.0
     */
    protected $strings = array();

    /**
     * Indicator of whether or not missing language strings should be profiled
     *
     * @var    bool
     * @since  1.0
     */
    protected $profile_missing_strings;

    /**
     * Indicator of whether or not missing language strings should be inserted into the database
     *
     * @var    bool
     * @since  1.0
     */
    protected $insert_missing_strings;

    /**
     * List of Properties
     *
     * @var    object
     * @since  1.0
     */
    protected $property_array = array(
        'registry',
        'installed',
        'language',
        'strings',
        'profile_missing_strings',
        'insert_missing_strings',
        'list',
        'translate'
    );

    /**
     * Class constructor
     *
     * @since   1.0
     */
    public function __construct($language = '')
    {
        $this->language = $language;

        return;
    }

    /**
     * Get language property
     *
     * @param   string  $key
     * @param   string  $default
     *
     * @return  array|mixed|string
     * @throws  \OutOfRangeException
     * @since   1.0
     */
    public function get($key, $default = '')
    {
        $key = strtolower($key);

        if ($key == 'translate') {
            return $this->translate($default, 0);
        }

        if ($key == 'list') {
            return $this->translate($default, 1);
        }

        if (in_array($key, $this->property_array)) {

            if (isset($this->$key)) {
            } else {
                $this->$key = $default;
            }

            return $this->$key;
        }

        if (isset($this->registry->$key)) {
            return $this->registry->$key;
        }

        throw new \OutOfRangeException
        ('Language Service: attempting to get value for unknown property: ' . $key);
    }

    /**
     * Set the value of the specified key
     *
     * @param   string  $key
     * @param   mixed   $value
     *
     * @return  mixed
     * @since   1.0
     * @throws  \OutOfRangeException
     */
    public function set($key, $value = null)
    {
        $key = strtolower($key);

        if ($key == 'translate') {
            return $this->translate($value, 0);
        }

        if ($key == 'list') {
            return $this->translate($value, 1);
        }

        if (in_array($key, $this->property_array)) {

            $this->$key = $value;

            return $this->$key;
        }

        if (isset($this->registry->$key)) {
            $this->registry->$key = $value;

            return $this->registry->$key;
        }

        throw new \OutOfRangeException
        ('Language Service: attempting to get value for unknown property: ' . $key);
    }

    /**
     * Translate String in loaded language or create new instance to translate using fall back language
     *
     * @param   string  $string
     * @param   int     $list
     *
     * @return  array|string
     * @since   1.0
     */
    protected function translate($string, $list = 0)
    {
        $string = strtolower(trim($string));

        if ((int)$list == 1) {
            $found = array();

            $keys = array_keys($this->strings);

            foreach ($keys as $key) {

                if (strpos($string, strtolower($key))) {
                    $found[$key] = $this->strings[$key];
                }
            }

            return $found;
        }

        if (isset($this->strings->$string)) {
            return $this->strings->$string;
        }

        $this->logUntranslatedString($string);

        if ($this->language == $this->get('default_language')) {
            if ($this->language == 'en-GB') {
                return $string;

            } else {
                $this->en_gb_instance = new LanguageService($this->get('en-GB'));
                $translated_string    = $this->en_gb_instance->translate($string);
            }

        } else {
            $this->default_language_instance = new LanguageService($this->get('default_language'));
            $translated_string               = $this->default_language_instance->translate($string);
        }

        if ($translated_string == false) {
        } else {
            return $translated_string;
        }

        return $string;
    }

    /**
     * Language strings found within the code but not translated can be saved to the database
     *
     * @param   $string
     *
     * @return  void
     * @since   1.0
     */
    protected function insertUntranslatedString($string)
    {
        if ((int)$this->get('insert_missing_strings', 0) === 0) {
            return;
        }

        $controllerClass = CONTROLLER_CLASS_NAMESPACE;
        $controller      = new $controllerClass();
        $controller->getModelRegistry('System', 'Languagestrings', 1);

        $controller->set('check_view_level_access', 0, 'model_registry');
        $controller->model->insertLanguageString($string);

        return;
    }

    /**
     * Language strings found within the code but not translated can be logged
     *
     * @param   $string
     *
     * @return  void
     * @since   1.0
     */
    protected function logUntranslatedString($string)
    {
        if ((int)$this->get('profile_missing_strings', 0) === 0) {
            return;
        }

        if (defined('PROFILER_ON') && PROFILER_ON === true) {
            Services::Profiler()->set(
                'Language Services: ' . $this->get('current_language', 'en-GB')
                    . ' Language is missing translation for string: ' . $string,
                'Application'
            );
        }

        return;
    }
}
