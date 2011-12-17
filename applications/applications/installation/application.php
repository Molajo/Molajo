<?php
/**
 * @package     Molajo
 * @subpackage  Installation
 * @copyright   Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @copyright   Copyright (C) 2012 Babs Gösgens. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die;

/**
 * Application class for Installation
 *
 * @package        Molajo
 * @subpackage  Installation
 * @since       1.0
 *
 */
class MolajoInstallationApplication extends MolajoApplicationHelper
{
    /**
     * The url of the site
     *
     * @var string
     */
    protected $_siteURL = null;

    /**
     * Initialise application.
     *
     * @param    array    $options
     *
     * @return    void
     */
    public function initialise($options = array())
    {
        /** Get localisation info in the localise.xml file. */
        $forced = $this->getLocalise();

        /** Language */

        /** 1. Request */
        if (empty($options['language'])) {
            $requestLang = JRequest::getCmd('language', null);
            if (!is_null($requestLang)) {
                $options['language'] = $requestLang;
            }
        }

        /** 2. Session */
        if (empty($options['language'])) {
            $sessionLang = MolajoFactory::getSession()->get('setup.language');
            if (!is_null($sessionLang)) {
                $options['language'] = $sessionLang;
            }
        }

        /** 3. Retrieve Language List */
        if (empty($options['language'])) {
            if (empty($forced['language'])) {
                $options['language'] = MolajoLanguageHelper::detectLanguage();
                if (empty($options['language'])) {
                    $options['language'] = 'en-GB';
                }
            } else {
                $options['language'] = $forced['language'];
            }
        }

        /** 4. Default to English */
        if (empty($options['language'])) {
            $options['language'] = 'en-GB';
        }

        /** 5. Set Language in Config */
        $conf = MolajoFactory::getConfig();
        $conf->set('language', $options['language']);
        $conf->set('debug_language', $forced['debug']);
        $conf->set('sampledata', $forced['sampledata']);
    }

    /**
     * route
     *
     * Route the application.
     *
     * @return void
     *
     * @since 1.0
     */
    public function route()
    {
    }

    /**
     * dispatch
     *
     * Execute the Component and render the results
     *
     * @param null $component
     *
     * @return void
     *
     * @since 1.0
     */
    public function dispatch($component = null)
    {
        try
        {
            // Get the component if not set.
            if ($component == null) {
                $component = JRequest::getCmd('option', 'installer');
            }

            JRequest::setVar('option', $component);
            $request = $this->componentRequest();

            $document = MolajoFactory::getDocument();
            $user = MolajoFactory::getUser();

            switch ($document->getType())
            {
                case 'html' :
                    $document->setTitle(MolajoTextHelper::_($request['title']));
                    break;
                default :
                    break;
            }

            /** render the component */
            $contents = MolajoComponent::renderComponent($request, $parameters = array());
            $document->setBuffer($contents, 'component');
        }

            // Mop up any uncaught exceptions.
        catch (Exception $e)
        {
            $code = $e->getCode();
            JError::raiseError($code ? $code : 500, $e->getMessage());
        }
    }

    /**
     * componentRequest
     *
     * populate the request object for the MVC
     *
     * @return array
     *
     * @since 1.0
     */
    private function componentRequest()
    {
        $request = array();

        $request['application_id'] = MOLAJO_APPLICATION_ID;
        $request['controller'] = 'display';
        $request['extension_type'] = 'component';

        $request['option'] = JRequest::getCmd('option', 'installer');
        $request['view'] = JRequest::getCmd('view', 'display');
        $request['layout'] = JRequest::getCmd('layout', 'step1');
        $request['model'] = JRequest::getCmd('model', 'display');
        $request['task'] = JRequest::getCmd('task', 'display');
        $request['format'] = JRequest::getCmd('format', 'html');

        $request['wrap'] = JRequest::getCmd('wrap', 'none');
        $request['wrap_id'] = JRequest::getCmd('wrap_id', '');
        $request['wrap_class'] = JRequest::getCmd('wrap_class', '');
        $request['wrap_title'] = '';
        $request['wrap_subtitle'] = '';
        $request['wrap_date'] = '';
        $request['wrap_author'] = '';
        $request['wrap_more_array'] = array();

        $request['plugin_type'] = JRequest::getCmd('plugin_type', '');

        $request['id'] = 0;
        $request['cid'] = 0;
        $request['catid'] = 0;
        $request['parameters'] = array();
        $request['extension'] = 'component';
        $request['component_specific'] = '';

        $request['current_url'] = JURI::base() . '/installation';
        $request['component_path'] = MOLAJO_CMS_COMPONENTS . '/' . $request['option'];
        $request['base_url'] = MOLAJO_BASE_FOLDER . '/installation';
        $request['item_id'] = null;

        $request['acl_implementation'] = 'core';
        $request['component_table'] = '__dummy';
        $request['filter_name'] = '';
        $request['select_name'] = '';

        $request['title'] = 'Molajo Installer: Step ' . substr($request['layout'], -1);
        $request['subtitle'] = '';
        $request['metakey'] = '';
        $request['metadesc'] = '';
        $request['metadata'] = '';
        $request['position'] = '';

        JRequest::setVar('option', $request['option']);
        JRequest::setVar('view', $request['view']);
        JRequest::setVar('layout', $request['layout']);
        JRequest::setVar('model', $request['model']);
        JRequest::setVar('task', $request['task']);
        JRequest::setVar('format', $request['format']);
        JRequest::setVar('wrap', $request['wrap']);
        JRequest::setVar('wrap_id', $request['wrap_id']);
        JRequest::setVar('wrap_class', $request['wrap_class']);
        JRequest::setVar('plugin_type', $request['plugin_type']);

        return $request;
    }

    /**
     * render
     *
     * Parse the Template and generate the JDoc statements
     *
     * @return void
     *
     * @since 1.0
     */
    public function render()
    {
        $document = MolajoFactory::getDocument();
        $user = MolajoFactory::getUser();

        // get the format to render
        $format = $document->getType();

        switch ($format)
        {
            case 'html':
            default:
                $template = $this->getTemplate(true);
                $file = JRequest::getCmd('layout', 'index');
                $parameters = array(
                    'template' => $template->template,
                    'file' => $file . '.php',
                    'directory' => MOLAJO_CMS_TEMPLATES,
                    'parameters' => $template->parameters
                );
                break;
        }

        // Parse the document.
        $document = MolajoFactory::getDocument();
        $document->parse($parameters);

        $caching = false;

        // Render the document.
        JResponse::setBody($document->render($caching, $parameters));
    }

    /**
     * debugLanguage
     *
     * @return    void
     */
    public static function debugLanguage()
    {
        ob_start();
        $lang = MolajoFactory::getLanguage();
        echo '<h4>Parsing errors in language files</h4>';
        $errorfiles = $lang->getErrorFiles();

        if (count($errorfiles)) {
            echo '<ul>';

            foreach ($errorfiles as $file => $error)
            {
                echo "<li>$error</li>";
            }
            echo '</ul>';
        }
        else {
            echo '<pre>None</pre>';
        }

        echo '<h4>Untranslated Strings</h4>';
        echo '<pre>';
        $orphans = $lang->getOrphans();

        if (count($orphans)) {
            ksort($orphans, SORT_STRING);

            foreach ($orphans as $key => $occurance)
            {
                $guess = str_replace('_', ' ', $key);

                $parts = explode(' ', $guess);
                if (count($parts) > 1) {
                    array_shift($parts);
                    $guess = implode(' ', $parts);
                }

                $guess = trim($guess);


                $key = trim(strtoupper($key));
                $key = preg_replace('#\s+#', '_', $key);
                $key = preg_replace('#\W#', '', $key);

                // Prepare the text
                $guesses[] = $key . '="' . $guess . '"';
            }

            echo "\n\n# " . $file . "\n\n";
            echo implode("\n", $guesses);
        }
        else {
            echo 'None';
        }
        echo '</pre>';
        $debug = ob_get_clean();
        JResponse::appendBody($debug);
    }

    /**
     * getPathway
     *
     * Returns the application MolajoPathway object.
     *
     * @param   string  $name     The name of the application.
     * @param   array   $options  An optional associative array of configuration settings.
     *
     * @return  MolajoPathway  A MolajoPathway object
     *
     * @since  1.0
     */
    public function getPathway($name = null, $options = array())
    {
        return null;
    }

    /**
     * getMenu
     *
     * Returns the Menu object.
     *
     * @param   string  $name     The name of the application/application.
     * @param   array   $options  An optional associative array of configuration settings.
     *
     * @return  MolajoMenu  MolajoMenu object.
     *
     * @since  1.0
     */
    public function getMenu($name = null, $options = array())
    {
        return null;
    }

    /**
     * setConfig
     *
     * Set configuration values
     *
     * @param    array    $vars        Array of configuration values
     * @param    string    $namespace    The namespace
     *
     * @return    void
     */
    public function setConfig(array $vars = array(), $namespace = 'config')
    {
        $this->_registry->loadArray($vars, $namespace);
    }

    /**
     * _createConfiguration
     *
     * Create the configuration registry
     *
     * @return    void
     */
    public function _createConfiguration($file = null)
    {
        $this->_registry = new JRegistry('config');
    }

    /**
     * getTemplate
     *
     * Get the Template for the Application
     *
     * @param bool $parameters
     * @return stdClass|string
     */
    public function getTemplate($parameters = false)
    {
        if ((bool)$parameters) {
            $template = new stdClass();
            $template->template = 'install';
            $template->parameters = new JRegistry;
            return $template;
        }
        return 'install';
    }

    /**
     * _createSession
     *
     * Create the user session
     *
     * @param    string    $name    The sessions name
     *
     * @return    object    MolajoSession
     */
    public function & _createSession($name)
    {
        $options = array();
        $options['name'] = $name;

        $session = MolajoFactory::getSession($options);
        if (is_a($session->get('registry'), 'JRegistry')) {
        } else {
            $session->set('registry', new JRegistry('session'));
        }

        return $session;
    }

    /**
     * getLocalise
     *
     * Returns the language code and help url set in the localise.xml file.
     * Used for forcing a particular language in localised releases.
     *
     * @return    bool|array    False on failure, array on success.
     */
    public function getLocalise()
    {
        $xml = MolajoFactory::getXML(MOLAJO_APPLICATION_PATH . '/localise.xml');

        if ($xml) {
        } else {
            return false;
        }

        $ret = array();

        $ret['language'] = (string)$xml->forceLang;
        $ret['helpurl'] = (string)$xml->helpurl;
        $ret['debug'] = (string)$xml->debug;
        $ret['sampledata'] = (string)$xml->sampledata;

        /**
        <?xml version="1.0" encoding="utf-8"?>
        <localise version="1.6" client="installation" >
        <forceLang>da-DK</forceLang>
        <helpurl></helpurl>
        <debug>0</debug>
        <sampledata>sample_data_da.sql</sampledata>
        <parameters/>
        </localise>
         */
        return $ret;
    }

    /**
     * getLocaliseAdmin
     *
     * Returns the installed language files in the administrative and
     * front-end area.
     *
     * @param    boolean    $db
     *
     * @return array Array with installed language packs in admin and site area
     */
    public function getLocaliseAdmin($db = false)
    {
        $path = JLanguage::getLanguagePath(MOLAJO_APPLICATION_CORE);
        $langfiles[] = JFolder::folders($path);

        if ($db) {
            $langfiles_disk = $langfiles;
            $langfiles = Array();
            $langfiles[] = Array();

            $query = $db->getQuery(true);

            $query->select('element, application_id');
            $query->from('#__extensions');
            $query->where('type = ' . $db->quote('language'));
            $query->where('application = ' . (int)MOLAJO_APPLICATION_ID);

            $db->setQuery($query);

            $langs = $db->loadObjectList();

            foreach ($langs as $lang) {
                $langfiles_disk[] = $lang->element;
            }
        }
        return $langfiles;
    }
}