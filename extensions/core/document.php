<?php
/**
 * @package     Molajo
 * @subpackage  Document
 * @copyright   Copyright (C) 2012 Amy Stephen. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */
defined('MOLAJO') or die;

/**
 * Document
 *
 * @package     Molajo
 * @subpackage  Document
 * @since       1.0
 */
class MolajoDocument
{
    /**
     * Rendering Sequence
     *
     * @var array
     * @since 1.0
     */
    protected $sequence = array();

    /**
     * Request
     *
     * @var object
     * @since 1.0
     */
    protected $request = null;

    /**
     * Template Parameters
     *
     * @var string
     * @since 1.0
     */
    protected $parameters = null;

    /**
     * Template
     *
     * @var string
     * @since 1.0
     */
    protected $_template = array();

    /**
     * Holds renderer set defined within the template and associated attributes
     *
     * @var string
     * @since 1.0
     */
    protected $_renderers = array();

    /**
     * __construct
     *
     * Class constructor.
     *
     * @param   null    $requestArray from MolajoRequests
     *
     * @return boolean
     *
     * @since  1.0
     */
    public function __construct($request = array())
    {
        /** Set Class Properties */
        $this->request = $request;

        $formatXML = MOLAJO_EXTENSIONS_CORE . '/core/formats/' . $this->request->get('format') . '.xml';
        if (JFile::exists($formatXML)) {
        } else {
            //error
            return false;
        }
        $sequence = simplexml_load_file($formatXML, 'SimpleXMLElement');
        foreach ($sequence->renderer as $next) {
            $this->sequence[] = (string)$next;
        }
        /** Request */
        $this->_render();
    }

    /**
     * Render the Template
     *
     * @return  object
     * @since  1.0
     */
    protected function _render()
    {
        /** Initialize */
        $this->request->set('template_include', '');

        if (file_exists(MOLAJO_EXTENSIONS_TEMPLATES . '/' . $this->request->get('template_name') . '/' . 'index.php')) {
            $this->request->set('template_include', MOLAJO_EXTENSIONS_TEMPLATES . '/' . $this->request->get('template_name') . '/' . 'index.php');
        } else {
            $this->request->set('template_name', 'system');
            $this->request->set('template_include', MOLAJO_EXTENSIONS_TEMPLATES . '/system/index.php');
        }

        $this->request->set('template_path', MOLAJO_EXTENSIONS_TEMPLATES . '/' . $this->request->get('template_name'));
        $this->request->set('page_include', $this->request->get('page_path') . '/index.php');

        $parameters = array(
            'template' => $this->request->get('template_name'),
            'template_path' => $this->request->get('template_path'),
            'page' => $this->request->get('page_include'),
            'parameters' => $this->request->get('template_parameters')
        );

        //        $this->parameters = array();
        //        $this->parameters = $this->request->get;
        //        $this->parameters = json_encode($this->request->get);
        //echo 'Parameters'.'<pre>';var_dump(json_encode($this->parameters));echo '</pre>';
        // die;
        //      die;
        /** Template Parameters */
        $this->parameters = new JRegistry;
        $this->parameters->loadArray($parameters);

        /** Before Event */
        //        MolajoController::getApplication()->triggerEvent('onBeforeRender');

        /** Media */

        /** Application-specific CSS and JS in => media/[application]/css[js]/XYZ.css[js] */
        $filePath = MOLAJO_SITE_FOLDER_PATH_MEDIA . '/' . MOLAJO_APPLICATION;
        $urlPath = MOLAJO_SITE_FOLDER_PATH_MEDIA_URL . '/' . MOLAJO_APPLICATION;
        MolajoController::getApplication()->loadMediaCSS($filePath, $urlPath);
        MolajoController::getApplication()->loadMediaJS($filePath, $urlPath);

        /** Load Language Files */
        $this->_loadLanguageTemplate();

        /** Favicon */
        $this->_loadFavicon();

        /** process template include, and then all rendered output, for <include statements */
        $body = $this->_renderLoop($this->request->get('template_include'));

        /** set the respond body */
        MolajoController::getApplication()->setBody($body);

        /** After Rendering */
        MolajoController::getApplication()->triggerEvent('onAfterRender');

        return;
    }

    /**
     * _loadLanguageTemplate
     *
     * Loads Language Files
     *
     * @return  boolean  True, if the file has successfully loaded.
     * @since   1.0
     */
    protected function _loadLanguageTemplate()
    {
        MolajoController::getApplication()->getLanguage()->load($this->request->get('template_name'),
            MOLAJO_EXTENSIONS_TEMPLATES . '/' . $this->request->get('template_name'),
            MolajoController::getApplication()->getLanguage()->getDefault(), false, false);
    }

    /**
     *  _renderLoop
     *
     */
    protected function _renderLoop()
    {
        /** include the template and page */
        ob_start();
        require $this->request->get('template_include');
        $this->_template = ob_get_contents();
        ob_end_clean();

        /** process all buffered input for include: statements  */
        $complete = false;
        $loop = 0;
        while ($complete === false) {

            /** count looping */
            $loop++;

            /** parse $this->template for include statements */
            $this->_parseTemplate();

            /** if no more include statements found, processing is complete */
            if (count($this->_renderers) == 0) {
                break;
            } else {
                /** invoke renderers for new include statements */
                $this->_template = $this->_renderTemplate();
            }
            if ($loop > MOLAJO_STOP_LOOP) {
                break;
            }
            /** look for new include statements in just rendered output */
            continue;
        }

        return $this->_template;
    }

    /**
     * _parseTemplate
     *
     * Parse the template and extract renderers and associated attributes
     *
     * @return  The parsed contents of the template
     */
    protected function _parseTemplate()
    {
        /** initialise */
        $matches = array();
        $this->_renderers = array();
        $i = 0;

        /** parse template for renderers */
        preg_match_all('#<include:(.*)\/>#iU', $this->_template, $matches);

        if (count($matches) == 0) {
            return;
        }

        /** store renderers in array */
        foreach ($matches[1] as $includeString) {

            /** initialise for each renderer */
            $includeArray = array();
            $includeArray = explode(' ', $includeString);
            $rendererType = '';

            foreach ($includeArray as $rendererCommand) {

                /** Type of Renderer */
                if ($rendererType == '') {
                    $rendererType = $rendererCommand;
                    $this->_renderers[$i]['name'] = $rendererType;
                    $this->_renderers[$i]['replace'] = $includeString;

                    /** Renderer Attributes */
                } else {
                    $rendererAttributes = str_replace('"', '', $rendererCommand);

                    if (trim($rendererAttributes) == '') {
                    } else {

                        /** Associative array of named pairs */
                        $splitAttribute = array();
                        $splitAttribute = explode('=', $rendererAttributes);
                        $this->_renderers[$i]['attributes'][$splitAttribute[0]] = $splitAttribute[1];
                    }
                }
            }
            $i++;
        }

        /** echo '<pre>';var_dump($this->_renderers);echo '</pre>'; */
    }

    /**
     * _renderTemplate
     *
     * Render pre-parsed template
     *
     * @return string rendered template
     */
    protected function _renderTemplate()
    {
        $replace = array();
        $with = array();

        /** 1. process every renderer in the format file in defined order */
        foreach ($this->sequence as $nextSequence) {

            /** 2. if necessary, split renderer name from include name (ex. request:component) */
            if (stripos($nextSequence, ':')) {
                $includeName = substr($nextSequence, 0, strpos($nextSequence, ':'));
                $rendererName = substr($nextSequence, strpos($nextSequence, ':') + 1, 999);
            } else {
                $includeName = $nextSequence;
                $rendererName = $nextSequence;
            }

            /** 3. primary component has requestArray loaded already <include:request attr1=x /> */
            if ($includeName == 'request') {
                $this->request->get('primary_request', true);
            } else {
                $this->request->get('primary_request', false);
            }

            /** 4. loop thru all extracted include values to find match */
            for ($i = 0; $i < count($this->_renderers); $i++) {

                $rendererArray = $this->_renderers[$i];

                if ($includeName == $rendererArray['name']) {

                    /** 5. place attribute pairs into variable */
                    if (isset($rendererArray['attributes'])) {
                        $attributes = $rendererArray['attributes'];
                    } else {
                        $attributes = array();
                    }

                    /** 6. store the "replace this" value */
                    $replace[] = "<include:" . $rendererArray['replace'] . "/>";

                    /** 7. load the renderer class and send in requestArray */
                    $class = 'Molajo' . ucfirst($rendererName) . 'Renderer';
                    if (class_exists($class)) {
                        $rendererClass = new $class ($rendererName, $this->request);
                    } else {
                        echo 'failed renderer = ' . $class . '<br />';
                        // ERROR
                    }
                    /** 8. render output and store results as "replace with" */
                    $with[] = $rendererClass->render($attributes);
                }
            }
        }
        /** 9. replace it */
        $this->_template = str_replace($replace, $with, $this->_template);

        /** 10. make certain all <include:xxx /> literals are removed */
        $replace = array();
        $with = array();
        for ($i = 0; $i < count($this->_renderers); $i++) {
            $replace[] = "<include:" . $this->_renderers[$i]['replace'] . "/>";
            $with[] = '';
        }

        return str_replace($replace, $with, $this->_template);
    }

    /**
     * Load a Favicon
     *
     * @return bool
     */
    protected function _loadFavicon()
    {
        $path = MOLAJO_EXTENSIONS_TEMPLATES . '/' . $this->request->get('template_name') . '/images/';

        if (file_exists($path . 'favicon.ico')) {
            $this->request->set('template_favicon', MOLAJO_EXTENSIONS_TEMPLATES_URL . '/' . $this->request->get('template_name') . '/images/favicon.ico');
            return true;
        }
        return false;
    }
}