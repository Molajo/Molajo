<?php
/**
 * @package     Molajo
 * @subpackage  Extension
 * @copyright   Copyright (C) 2012 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die;

/**
 * Extension Class
 *
 * Base class
 */
class MolajoExtension
{
    /**
     *  Redirect to ID
     *
     * @var integer
     * @since 1.0
     */
    public $redirect_to_id = null;

    /**
     *  Found
     *
     * @var boolean
     * @since 1.0
     */
    public $found = null;

    /**
     *  Request Array
     *
     * @var boolean
     * @since 1.0
     */
    public $requestArray = array();

    /**
     * __construct
     *
     * Class constructor.
     *
     * @param   null    $request    An optional argument to provide dependency injection for the asset
     * @param   null    $asset_id   An optional argument to provide dependency injection for the asset
     *
     * @return boolean
     *
     * @since  1.0
     */
    public function __construct($request = null, $asset_id = null)
    {
        /** MVC Request Variables */
        $this->requestArray = array();
        
        /** Specific URL path (less host, path and application) */
        if ($request == null) {
        } else {
            $this->requestArray['query_request'] = $request;
        }

        /** Specific asset */
        if ((int)$asset_id == 0) {
            $this->requestArray['asset_id'] = 0;
        } else {
            $this->requestArray['asset_id'] = $asset_id;
        }
    }

    /**
     * load
     *
     * Using the MOLAJO_PAGE_REQUEST value,
     *  retrieve the asset record,
     *  set the variables needed to render output
     *
     * @return bool|null
     */
    public function load()
    {
        /** Request */
        $this->_getSEFOptions();

        /** home: duplicate content - redirect */
        if ($this->requestArray['query_request'] == 'index.php'
            || $this->requestArray['query_request'] == 'index.php/'
            || $this->requestArray['query_request'] == 'index.php?'
            || $this->requestArray['query_request'] == '/index.php/'
        ) {
            MolajoController::getApplication()->redirect(MolajoController::getApplication()->get('home_asset_id'), 301);
            return;
        }

        /** Home */
        if ($this->requestArray['query_request'] == ''
            && (int)$this->requestArray['asset_id'] == 0
        ) {
            $this->requestArray['asset_id'] = MolajoController::getApplication()->get('home_asset_id', 0);
            $this->requestArray['home'] = true;
        }

        /** Site offline */
        if (MolajoController::getApplication()->get('offline', 0) == 1) {
            MolajoController::getApplication()->setHeader('Status', '503 Service Temporarily Unavailable', 'true');
            $this->requestArray['asset_id'] = MolajoController::getApplication()->get('asset_id', 0);
            $this->requestArray['template_name'] = MolajoController::getApplication()->get('offline_template', 'system');
            $this->requestArray['page'] = MolajoController::getApplication()->get('offline_page', 'full');
            $this->requestArray['view'] = MolajoController::getApplication()->get('offline_view', 'offline');
            $this->requestArray['wrap'] = MolajoController::getApplication()->get('offline_wrap', 'div');
            $this->requestArray['format'] = MolajoController::getApplication()->get('offline_format', 'static');
            $this->requestArray['message'] = MolajoController::getApplication()->get('offline_message', 'This site is not available.<br /> Please check back again soon.');
        }

        /** Get Asset Information */
        $this->_getAsset();

        /** Logged on Requirement */
        if (MolajoController::getApplication()->get('logon_requirement', 0) > 0
            && MolajoController::getUser()->get('guest', true) === true
            && $this->requestArray['asset_id'] <> MolajoController::getApplication()->get('logon_requirement', 0)
        ) {
            MolajoController::getApplication()->redirect(MolajoController::getApplication()->get('logon_requirement', 0), 303);
            return;
        }

        /** Route */
        //        $this->_route($this);

        /** 404 Not Found */
        if ($this->found === false) {
            MolajoController::getApplication()->setHeader('Status', '404 Not Found', 'true');
            $this->requestArray['template_name'] = MolajoController::getApplication()->get('error_template', 'system');
            $this->requestArray['page'] = MolajoController::getApplication()->get('error_page', 'print');
            $this->requestArray['view'] = MolajoController::getApplication()->get('error_view', 'error');
            $this->requestArray['wrap'] = MolajoController::getApplication()->get('error_wrap', 'none');
        }

        /** act on redirect_to_id */
        if ($this->redirect_to_id == 0) {
        } else {
            MolajoController::getApplication()->redirect($this->redirect_to_id, 301);
            return;
        }

        /** acl check */
        $this->_authorise();

        /** 403 Not Found */
        if ($this->found === false) {
            MolajoController::getApplication()->setHeader('Status', '403 Not Authorised', 'true');
            $this->requestArray['template_name'] = MolajoController::getApplication()->get('error_template', 'system');
            $this->requestArray['page'] = MolajoController::getApplication()->get('error_page', 'print');
            $this->requestArray['view'] = MolajoController::getApplication()->get('error_view', 'error');
            $this->requestArray['wrap'] = MolajoController::getApplication()->get('error_wrap', 'none');
        }

        /** build meta in default order */
        $this->_getMetaData();
        $this->_setMetaData();

        /** for MVC */
        $this->requestArray['format'] = 'html';
        $this->buildRequestArray();
        
        /** render */
        $this->requestArray['format'] = 'html';
        $this->_renderDocumentType();

        /** return to application */
        return;
    }
    //echo '<pre>';var_dump($this);echo '</pre>';
    /**
     * _getSEFOptions
     *
     * Request is stripped of Host, Folder, and Application
     *  Path ex. index.php?option=login or access/groups
     *
     * @param null $request
     * @return mixed
     */
    protected function _getSEFOptions($request = null)
    {
        /** Application SEF Options */
        $sef = MolajoController::getApplication()->get('sef', 1);
        $sef_rewrite = MolajoController::getApplication()->get('sef_rewrite', 0);
        $sef_suffix = MolajoController::getApplication()->get('sef_suffix', 0);
        $unicodeslugs = MolajoController::getApplication()->get('unicodeslugs', 0);
        $force_ssl = MolajoController::getApplication()->get('force_ssl', 0);

        /** Path ex. index.php?option=login or access/groups */
        if ($request == null) {
            $path = MOLAJO_PAGE_REQUEST;
        } else {
            $path = $request;
        }

        /** duplicate content: URLs without the .html */
        $sef_suffix = 1;
        if ($sef_suffix == 1 && substr($path, -11) == '/index.html') {
            $path = substr($path, 0, (strlen($path) - 11));
        }
        if ($sef_suffix == 1 && substr($path, -5) == '.html') {
            $path = substr($path, 0, (strlen($path) - 5));
        }

        /** populate value used in query  */
        $this->requestArray['query_request'] = $path;

        return;
    }

    /**
     * _getAsset
     *
     * Function to retrieve asset information for the Request or Asset ID
     *
     * @return  boolean
     * @since   1.0
     */
    protected function _getAsset()
    {
        $db = MolajoController::getDbo();
        $query = $db->getQuery(true);

        $query->select('a.' . $db->nameQuote('id') . ' as asset_id');
        $query->select('a.' . $db->nameQuote('asset_type_id'));
        $query->select('a.' . $db->nameQuote('source_id'));
        $query->select('a.' . $db->nameQuote('sef_request'));
        $query->select('a.' . $db->nameQuote('request'));
        $query->select('a.' . $db->nameQuote('primary_category_id'));
        $query->select('a.' . $db->nameQuote('template_id'));
        $query->select('a.' . $db->nameQuote('template_page'));
        $query->select('a.' . $db->nameQuote('language'));
        $query->select('a.' . $db->nameQuote('translation_of_id'));
        $query->select('a.' . $db->nameQuote('redirect_to_id'));
        $query->select('a.' . $db->nameQuote('view_group_id'));

        $query->select('b.' . $db->nameQuote('component_option') . ' as ' . $db->nameQuote('option'));
        $query->select('b.' . $db->nameQuote('source_table'));

        $query->from($db->nameQuote('#__assets') . ' as a');
        $query->from($db->nameQuote('#__asset_types') . ' as b');

        $query->where('a.' . $db->nameQuote('asset_type_id') . ' = b.' . $db->nameQuote('id'));

        if ((int)$this->requestArray['asset_id'] == 0) {
            if (MolajoController::getApplication()->get('sef', 1) == 1) {
                $query->where('a.' . $db->nameQuote('sef_request') . ' = ' . $db->Quote($this->requestArray['query_request']));
            } else {
                $query->where('a.' . $db->nameQuote('request') . ' = ' . $db->Quote($this->requestArray['query_request']));
            }
        } else {
            $query->where('a.' . $db->nameQuote('id') . ' = ' . (int)$this->requestArray['asset_id']);
        }

        $db->setQuery($query->__toString());
        $results = $db->loadObjectList();

        if ($db->getErrorNum() == 0) {
        } else {
            MolajoController::getApplication()->setMessage($db->getErrorMsg(), MOLAJO_MESSAGE_TYPE_ERROR);
            return false;
        }

        if (count($results) == 0) {
            $this->found = false;

        } else {
            $this->found = true;
            foreach ($results as $result) {

                if ($this->requestArray['asset_id'] == MolajoController::getApplication()->get('home_asset_id')) {
                    $this->requestArray['home'] = true;
                } else {
                    $this->requestArray['home'] = false;
                }
                $this->requestArray['option'] = $result->option;
                $this->requestArray['template_id'] = $result->template_id;
                if ((int)$this->requestArray['template_id'] > 0) {
                    $this->_getTemplate();
                }
                $this->requestArray['page'] = $result->template_page;
                $this->requestArray['asset_id'] = $result->asset_id;
                $this->requestArray['asset_type_id'] = $result->asset_type_id;
                $this->requestArray['source_table'] = $result->source_table;
                $this->requestArray['source_id'] = $result->source_id;
                $this->requestArray['source_language'] = $result->language;
                $this->requestArray['translation_of_id'] = $result->translation_of_id;
                $this->requestArray['view_group_id'] = $result->view_group_id;
                $this->requestArray['category'] = $result->primary_category_id;
                $this->redirect_to_id = $result->redirect_to_id;

                $this->requestArray['request'] = $result->request;
                $this->requestArray['sef_request'] = $result->sef_request;

                $parameterArray = array();
                $temp = substr($this->requestArray['request'], 10, (strlen($this->requestArray['request']) - 10));
                $parameterArray = explode('&', $temp);

                foreach ($parameterArray as $parameter) {

                    $pair = explode('=', $parameter);

                    if ($pair[0] == 'task') {
                        $this->requestArray['task'] = $pair[1];

                    } elseif ($pair[0] == 'format') {
                        $this->requestArray['format'] = $pair[1];

                    } elseif ($pair[0] == 'option') {
                        $this->requestArray['option'] = $pair[1];

                    } elseif ($pair[0] == 'view') {
                        $this->requestArray['view'] = $pair[1];

                    } elseif ($pair[0] == 'wrap') {
                        $this->requestArray['wrap'] = $pair[1];

                    } elseif ($pair[0] == 'template') {
                        $this->requestArray['template'] = $pair[1];

                    } elseif ($pair[0] == 'page') {
                        $this->requestArray['page'] = $pair[1];

                    } elseif ($pair[0] == 'static') {
                        $this->requestArray['wrap'] = $pair[1];

                    } elseif ($pair[0] == 'ids') {
                        $this->requestArray['ids'] = $pair[1];

                    } elseif ($pair[0] == 'id') {
                        $this->requestArray['id'] = $pair[1];
                    }
                }
            }
        }
    }

    /**
     * _getTemplate
     *
     * Get Template Name using the Template ID
     *
     * @param $template_id
     */
    protected function _getTemplate()
    {
        $db = MolajoController::getDbo();
        $query = $db->getQuery(true);

        $query->select('a.' . $db->nameQuote('title'));
        $query->from($db->nameQuote('#__extension_instances') . ' as a');
        $query->where('a.' . $db->nameQuote('id') . ' = ' . (int)$this->requestArray['template_id']);

        $db->setQuery($query->__toString());

        $this->requestArray['template_name'] = $db->loadResult();
    }

    /**
     *  _getMetaData
     *
     *  Retrieves metadata for page
     */
    protected function _getMetaData()
    {
        /** need to know if this is for edit or display - list or item - or is it a static page */

        /** Priority 1: Request Override */
        $this->_getSEFOptionsParameters();

        /** Priority 2: Asset */
        // already collected in getAsset

        /** Priority 3: Source Table ID */
        $this->_getSourceData();

        /** Priority 4: Menu Item */

        /** Priority 5: Primary List Category ID */
        if ((int)$this->requestArray['category'] == 0) {
        } else {
            $this->getPrimaryCategory();
        }

        /** Priority 6: Component */
        $this->_getComponent();

        /** Priority 7: Application (static, items, item, edit) */
        $this->application_template = MolajoController::getApplication()->get('default_template');
        $this->application_page = MolajoController::getApplication()->get('default_page');
        $this->default_view_items = MolajoController::getApplication()->get('default_view_items');
        $this->default_wrap_items = MolajoController::getApplication()->get('default_wrap_items');

        /** Priority 8: System-defined */
        if ($this->requestArray['template_name'] == null) {
            $this->requestArray['template_name'] = MolajoController::getApplication()->get('default_template');
        }
        if ($this->requestArray['page'] == null) {
            $this->requestArray['page'] = MolajoController::getApplication()->get('default_page');
        }
    }

    /**
     *  _getSEFOptionsParameters
     *
     *  Retrieve Template and Template Page overrides from URL
     *  todo: amy add parameter to turn this off in the template manager
     */
    protected function _getSEFOptionsParameters()
    {
        $parameterArray = array();
        $temp = substr(MOLAJO_PAGE_REQUEST, 10, (strlen(MOLAJO_PAGE_REQUEST) - 10));
        $parameterArray = explode('&', $temp);

        foreach ($parameterArray as $parameter) {
            
            $pair = explode('=', $parameter);
            
            if ($pair[0] == 'view') {
                $this->requestArray['view'] = $pair[1];
                
            } elseif ($pair[0] == 'wrap') {
                $this->requestArray['wrap'] = $pair[1];
                
            } elseif ($pair[0] == 'template') {
                $this->requestArray['template_name'] = $pair[1];
                
            } elseif ($pair[0] == 'page') {
                $this->requestArray['page'] = $pair[1];
            }
        }
    }

    /**
     * _getSourceData
     *
     * Retrieve Parameters and MetaData for Source Detail Row
     *
     * @return  array
     * @since   1.0
     */
    protected function _getSourceData()
    {
        $db = MolajoController::getDbo();
        $query = $db->getQuery(true);

        $query->select('a.' . $db->nameQuote('extension_instance_id'));
        $query->select('a.' . $db->nameQuote('parameters'));
        $query->select('a.' . $db->nameQuote('metadata'));
        $query->from($db->nameQuote('#' . $this->requestArray['source_table']) . ' as a');
        $query->where('a.' . $db->nameQuote('id') . ' = ' . (int)$this->requestArray['source_id']);

        $db->setQuery($query->__toString());

        $results = $db->loadObjectList();
        if (count($results) > 0) {
            foreach ($results as $result) {
                $this->_setPageValues($result->parameters, $result->metadata);

                $parameters = new JRegistry;
                $parameters->loadString($result->parameters);
                $this->requestArray['source_parameters'] = $parameters;

                $this->requestArray['extension_instances_id'] = $result->extension_instance_id;
            }
        }
        //    echo '<pre>';var_dump($this->requestArray['source_parameters']);'</pre>';
        //    MolajoController::getApplication()->setMessage($db->getErrorMsg(), 'error');
        //    return false;
    }

    /**
     * getPrimaryCategory
     *
     * Retrieve the Menu Item Parameters and Meta Data
     *
     * @return  array
     * @since   1.0
     */
    protected function getPrimaryCategory()
    {
        $db = MolajoController::getDbo();
        $query = $db->getQuery(true);

        $query->select('a.' . $db->nameQuote('parameters'));
        $query->select('a.' . $db->nameQuote('metadata'));
        $query->from($db->nameQuote('#__content') . ' as a');
        $query->where('a.' . $db->nameQuote('id') . ' = ' . (int)$this->requestArray['category']);

        $db->setQuery($query->__toString());

        $results = $db->loadObjectList();
        if (count($results) > 0) {
            foreach ($results as $result) {
                $this->_setPageValues($result->parameters, $result->metadata);
                
                $parameters = new JRegistry;
                $parameters->loadString($result->parameters);
                $this->requestArray['category_parameters'] = $parameters;
            }
        }

        //    MolajoController::getApplication()->setMessage($db->getErrorMsg(), 'error');
        //    return false;
    }

    /**
     * _getComponent
     *
     * Retrieve the Parameters and Meta Data for Component
     *
     * @return  array
     * @since   1.0
     */
    protected function _getComponent()
    {
        $db = MolajoController::getDbo();
        $query = $db->getQuery(true);

        $query->select('a.' . $db->nameQuote('parameters'));
        $query->select('a.' . $db->nameQuote('metadata'));
        $query->from($db->nameQuote('#__extension_instances') . ' as a');
        $query->where('a.' . $db->nameQuote('id') . ' = ' . (int)$this->requestArray['extension_instances_id']);

        $db->setQuery($query->__toString());

        $results = $db->loadObjectList();
        if (count($results) > 0) {
            foreach ($results as $result) {
                $this->_setPageValues($result->parameters, $result->metadata);
                $parameters = new JRegistry;
                $parameters->loadString($result->parameters);
                $this->requestArray['extension_parameters'] = $parameters;

                if (isset($this->requestArray['extension_parameters']->static)
                    && $this->requestArray['extension_parameters']->static === true) {
                    $this->requestArray['static'] = true;
                } else {
                    $this->requestArray['static'] = false;
                }
            }
        }
        //    MolajoController::getApplication()->setMessage($db->getErrorMsg(), 'error');
        //    return false;
    }

    /**
     * _setPageValues
     *
     * Set the values needed to generate the page (template, page, view, wrap, and various metadata)
     *
     * @param null $sourceParameters
     * @param null $sourceMetadata
     */
    protected function _setPageValues($sourceParameters = null, $sourceMetadata = null)
    {
        $parameters = new JRegistry;
        $parameters->loadString($sourceParameters);

        if (isset($this->requestArray['template_name']) && ($this->requestArray['template_name'] != '')) {
        } else {
            $this->requestArray['template_name'] = $parameters->get('template', '');
        }
        if (isset($this->requestArray['page']) && ($this->requestArray['page'] != '')) {
        } else {
            $this->requestArray['page'] = $parameters->get('page', '');
        }
        if (isset($this->requestArray['view']) && ($this->requestArray['view'] != '')) {
        } else {
            $this->requestArray['view'] = $parameters->get('view', '');
        }
        if (isset($this->requestArray['wrap']) && ($this->requestArray['wrap'] != '')) {
        } else {
            $this->requestArray['wrap'] = $parameters->get('wrap', '');
        }

        $metadata = new JRegistry;
        $metadata->loadString($sourceMetadata);

        if (isset($this->requestArray['meta_title']) && ($this->requestArray['meta_title'] != '')) {
        } else {
            $this->requestArray['meta_title'] = $metadata->get('meta_title', '');
        }
        if (isset($this->requestArray['meta_description']) && ($this->requestArray['meta_description'] != '')) {
        } else {
            $this->requestArray['meta_description'] = $metadata->get('meta_description', '');
        }
        if (isset($this->requestArray['meta_keywords']) && ($this->requestArray['meta_keywords'] != '')) {
        } else {
            $this->requestArray['meta_keywords'] = $metadata->get('meta_keywords', '');
        }
        if (isset($this->requestArray['meta_author']) && ($this->requestArray['meta_author'] != '')) {
        } else {
            $this->requestArray['meta_author'] = $metadata->get('meta_author', '');
        }
        if (isset($this->requestArray['meta_content_rights']) && ($this->requestArray['meta_content_rights'] != '')) {
        } else {
            $this->requestArray['meta_content_rights'] = $metadata->get('meta_content_rights', '');
        }
        if (isset($this->requestArray['meta_robots']) && ($this->requestArray['meta_robots'] != '')) {
        } else {
            $this->requestArray['meta_robots'] = $metadata->get('meta_robots', '');
        }
    }

    /**
     * getRedirectURL
     *
     * Function to retrieve asset information for the Request or Asset ID
     *
     * @return  boolean
     * @since   1.0
     */
    public static function getRedirectURL($asset_id)
    {
        $db = MolajoController::getDbo();
        $query = $db->getQuery(true);

        if ((int)$asset_id == MolajoController::getApplication()->get('home_asset_id', 0)) {
            return '';
        }

        if (MolajoController::getApplication()->get('sef', 1) == 0) {
            $query->select('a.' . $db->nameQuote('sef_request'));
        } else {
            $query->select('a.' . $db->nameQuote('request'));
        }

        $query->from($db->nameQuote('#__assets') . ' as a');
        $query->where('a.' . $db->nameQuote('id') . ' = ' . (int)$asset_id);

        $db->setQuery($query->__toString());

        return $db->loadResult();

        //    MolajoController::getApplication()->setMessage($db->getErrorMsg(), 'error');
        //    return false;
    }

    /**
     *  _setMetaData
     *
     * Establish the meta data for this web page
     */
    protected function _setMetaData()
    {
        MolajoController::getApplication()->setTitle($this->requestArray['meta_title']);
        MolajoController::getApplication()->setDescription($this->requestArray['meta_description']);
        MolajoController::getApplication()->setMetaData('meta_keywords', $this->requestArray['meta_keywords']);
        MolajoController::getApplication()->setMetaData('meta_author', $this->requestArray['meta_author']);
        MolajoController::getApplication()->setMetaData('meta_content_rights', $this->requestArray['meta_content_rights']);
        MolajoController::getApplication()->setMetaData('meta_robots', $this->requestArray['meta_robots']);
    }

    /**
     * _route
     *
     * Route the application.
     *
     * Routing is the process of examining the request environment to determine which
     * component should receive the request. The component optional parameters
     * are then set in the request object to be processed when the application is being
     * dispatched.
     *
     * @return  void;
     * @since  1.0
     */
    private function _route()
    {
        MolajoPluginHelper::importPlugin('system');
        MolajoController::getApplication()->triggerEvent('onAfterRoute');
    }

    /**
     * Execute Extension
     *
     * @return  boolean
     *
     * @since   1.0
     */
    protected function _authorise()
    {
        if (in_array($this->requestArray['view_group_id'], MolajoController::getUser()->view_groups)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * buildRequestArray
     *
     * Construct the Request Array for the MVC
     *
     * @return bool
     */
    public function buildRequestArray()
    {
        $this->requestArray['component_path'] = '';
        $this->requestArray['controller'] = '';
        $this->requestArray['model'] = '';
        $this->requestArray['plugin_type'] = '';
        $this->requestArray['acl_implementation'] = '';
        $this->requestArray['component_table'] = '';

        /** configuration model */
        $configModel = new MolajoModelConfiguration ($this->requestArray['option']);

        /** 1. Component Path */
        $this->requestArray['component_path'] = MOLAJO_EXTENSIONS_COMPONENTS . '/' . $this->requestArray['option'];

        /** 2. Task */
        if (isset($this->requestArray['task']) && ($this->requestArray['task'] != '')) {
        } else {
            $this->requestArray['task'] = 'display';
        }

        /** 3. Retrieve Controller while validating Task */
        $this->requestArray['controller'] = $configModel->getOptionLiteralValue(MOLAJO_EXTENSION_OPTION_ID_TASKS_CONTROLLER, $this->requestArray['task']);
        if ($this->requestArray['controller'] === false) {
            MolajoError::raiseError(500, MolajoTextHelper::_('MOLAJO_INVALID_TASK_CONTROLLER') . ' ' . $this->requestArray['task']);
            return false;
        }

        /** 4. id, ids, category */
        if (isset($this->requestArray['id'])) {
        } else {
            $this->requestArray['id'] = 0;
        }
        if (isset($this->requestArray['ids'])) {
        } else {
            $this->requestArray['ids'] = array();
        }
        if (isset($this->requestArray['category'])) {
        } else {
            $this->requestArray['category'] = 0;
        }

        if ($this->requestArray['task'] == 'add') {

            if ((int)$this->requestArray['id'] == 0 && count($this->requestArray['ids']) == 0) {
            } else {
                MolajoError::raiseError(500, MolajoTextHelper::_('MOLAJO_ERROR_ADD_TASK_MUST_NOT_HAVE_ID'));
                return false;
            }

        } else if ($this->requestArray['task'] == 'edit' || $this->requestArray['task'] == 'restore') {

            if ($this->requestArray['id'] > 0 && count($this->requestArray['ids']) == 0) {

            } else if ((int)$this->requestArray['id'] == 0 && count($this->requestArray['ids']) == 1) {
                $this->requestArray['id'] = $this->requestArray['ids'][0];
                $this->requestArray['ids'] = array();

            } else if ((int)$this->requestArray['id'] == 0 && count($this->requestArray['ids']) == 0) {
                MolajoError::raiseError(500, MolajoTextHelper::_('MOLAJO_ERROR_EDIT_TASK_MUST_HAVE_ID'));
                return false;

            } else if (count($this->requestArray['ids']) > 1) {
                MolajoError::raiseError(500, MolajoTextHelper::_('MOLAJO_ERROR_TASK_MAY_NOT_HAVE_MULTIPLE_IDS'));
                return false;
            }
        }

        /** 5. model */
        if ($this->requestArray['controller'] == 'display') {
            if ($this->requestArray['static'] === true) {
                $this->requestArray['model'] = 'dummy';
            } else {
                $this->requestArray['model'] = 'display';
            }
        } else {
            $this->requestArray['model'] = 'edit';
        }

        if ($this->requestArray['controller'] == 'display') {

            /** 6. Format */
            if ($this->requestArray['format'] == null) {
                $results = false;
            } else {
                $results = $configModel->getOptionLiteralValue(MOLAJO_EXTENSION_OPTION_ID_FORMATS, $this->requestArray['format']);
            }

            /** get default format */
            if ($results === false) {
                $this->requestArray['format'] = $configModel->getOptionValue(MOLAJO_EXTENSION_OPTION_ID_FORMATS_DEFAULT);
                if ($this->requestArray['format'] === false) {
                    $this->requestArray['format'] = MolajoController::getApplication()->get('default_format', 'html');
                }
            }

            /** 7. View **/
            if ($this->requestArray['static'] === true) {
                $option = 3300;

            } else if ($this->requestArray['id'] > 0) {
                if ($this->requestArray['task'] == 'display') {
                    /** item */
                    $option = 3110;
                } else {
                    /** edit */
                    $option = 3310;
                }
            } else {
                /** items */
                $option = 3210;
            }

            if ($this->requestArray['view'] == null) {
                $results = false;
            } else {
                $results = $configModel->getOptionLiteralValue($option, $this->requestArray['view']);
            }

            $option = $option + 10;
            if ($results === false) {
                $this->requestArray['view'] = $configModel->getOptionValue($option);
                if ($this->requestArray['view'] === false) {
                    MolajoController::getApplication()->setMessage(MolajoTextHelper::_('MOLAJO_NO_VIEWS_DEFAULT_DEFINED'), 'error');
                    return false;
                }
            }

            /** 8. Wrap **/
            $option = $option + 10;
            if ($this->requestArray['wrap'] == null) {
                $results = false;
            } else {
                $results = $configModel->getOptionLiteralValue($option, $this->requestArray['wrap']);
            }

            $option = $option + 10;
            if ($results === false) {
                $this->requestArray['wrap'] = $configModel->getOptionValue($option);
                if ($this->requestArray['wrap'] === false) {
                    $this->requestArray['wrap'] = 'none';
                }
            }

            /** 9. Page **/
            $option = $option + 10;
            if ($this->requestArray['page'] == null) {
                $results = false;
            } else {
                $results = $configModel->getOptionLiteralValue($option, $this->requestArray['page']);
            }

            $option = $option + 10;
            if ($results === false) {
                $this->requestArray['page'] = $configModel->getOptionValue($option);
                if ($this->requestArray['page'] === false) {
                    $this->requestArray['page'] = 'full';
                }
            }
        }
        /** todo: amy: come back and get redirect */

        /** 11. acl implementation */
        $this->requestArray['acl_implementation'] = $configModel->getOptionValue(MOLAJO_EXTENSION_OPTION_ID_ACL_IMPLEMENTATION);
        if ($this->requestArray['acl_implementation'] === false) {
            $this->requestArray['acl_implementation'] = 'core';
        }

        /** 12. component table */
        $this->requestArray['component_table'] = $configModel->getOptionValue(MOLAJO_EXTENSION_OPTION_ID_TABLE);
        if ($this->requestArray['component_table'] === false) {
            $this->requestArray['component_table'] = '__content';
        }

        /** 13. plugin helper */
        $this->requestArray['plugin_type'] = $configModel->getOptionValue(MOLAJO_EXTENSION_OPTION_ID_PLUGIN_TYPE);
        if ($this->requestArray['plugin_type'] === false) {
            $this->requestArray['plugin_type'] = 'content';
        }

        return true;
    }

    /**
     * _renderDocumentType
     *
     * Get Header information for Page
     *
     * @return  void
     *
     * @since   1.0
     */
    protected function _renderDocumentType()
    {
        $documentTypeClass = 'Molajo' . ucfirst($this->requestArray['format']) . 'Format';
        $results = new $documentTypeClass ($this->requestArray);
    }
}
