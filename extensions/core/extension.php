<?php
/**
 * @package     Molajo
 * @subpackage  Extension
 * @copyright   Copyright (C) 2012 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die;

// todo: amy add error checking
/**
 * Extension
 *
 * Base class
 */
class MolajoExtension
{
    /**
     *  Override Request
     *
     * @var boolean
     * @since 1.0
     */
    private $_override_request = null;

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
     * @param   null    $request
     * @param   null    $asset_id
     *
     * @return boolean
     *
     * @since  1.0
     */
    public function __construct($request = null, $asset_id = null)
    {
        /** MVC Request Variables */
        $this->requestArray = MolajoExtensionHelper::createRequestArray();

        /** Specific URL path (host, path and application removed) */
        if ($request == null) {
        } else {
            $this->_override_request = $request;
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
        $this->_getQueryRequest();

        /** home: duplicate content - redirect */
        if ($this->requestArray['query_request'] == 'index.php'
            || $this->requestArray['query_request'] == 'index.php/'
            || $this->requestArray['query_request'] == 'index.php?'
            || $this->requestArray['query_request'] == '/index.php/'
        ) {
            MolajoController::getApplication()->redirect('', 301);
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
        $this->requestArray = MolajoExtensionHelper::getAsset($this->requestArray);

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
        if ($this->requestArray['found'] === false) {
            MolajoController::getApplication()->setHeader('Status', '404 Not Found', 'true');
            $this->requestArray['template_name'] = MolajoController::getApplication()->get('error_template', 'system');
            $this->requestArray['page'] = MolajoController::getApplication()->get('error_page', 'print');
            $this->requestArray['view'] = MolajoController::getApplication()->get('error_view', 'error');
            $this->requestArray['wrap'] = MolajoController::getApplication()->get('error_wrap', 'none');
            $this->requestArray['format'] = 'error';
        }

        /** act on redirect_to_id */
        if ($this->requestArray['redirect_to_id'] == 0) {
        } else {
            MolajoController::getApplication()->redirect($this->requestArray['redirect_to_id'], 301);
            return;
        }

        /** acl check */
        $this->_authorise();

        /** 403 Not Found */
        if ($this->requestArray['found'] === false) {
            MolajoController::getApplication()->setHeader('Status', '403 Not Authorised', 'true');
            $this->requestArray['template_name'] = MolajoController::getApplication()->get('error_template', 'system');
            $this->requestArray['page'] = MolajoController::getApplication()->get('error_page', 'print');
            $this->requestArray['view'] = MolajoController::getApplication()->get('error_view', 'error');
            $this->requestArray['wrap'] = MolajoController::getApplication()->get('error_wrap', 'none');
            $this->requestArray['format'] = 'error';
        }

        /**
         *  Determine rendering parameters and page metadata in priority order
         *
         *  1. Query Parameters
         *  2. Asset (already set in array)
         *  3. Source Data
         *  4. Menu Item
         *  5. Primary Category
         *  6. Component
         *  7. Application
         */

        /** need to know if this is for edit or display - list or item - or is it a static page */

        /** 1: Request Override */
        $this->_getQueryParameters();

        /** 2: Asset (already set in array) */

        /** 3: Source Table ID */
        $results = $this->_getSourceData();
        if ($results === false) {
            //error
        } else {
            $this->_setPageValues($this->requestArray['source_parameters'], $this->requestArray['source_metadata']);
        }

        /** 4: Menu Item */

        /** 5: Primary Category */
        if ((int)$this->requestArray['category'] == 0) {
        } else {
            $results = $this->_getPrimaryCategory();
            if ($results === false) {
                //error
            }
            $this->_setPageValues($this->requestArray['category_parameters'], $this->requestArray['category_metadata']);
        }

        /** 6: Component */
        $results = $this->_getComponent();
        if ($results === false) {
            //error
        } else {
            $this->_setPageValues($this->requestArray['extension_parameters'], $this->requestArray['extension_metadata']);
        }

        /** 7: Application (static, items, item, edit) */
        $results = $this->_getApplicationDefaults();

        /**
         *  Set Application Meta Data Values
         */
        $this->_setMetaData();

        /** todo: Amy fix and remove */
        $this->requestArray['format'] = 'html';

        /** Template values */
        $results = $this->_getTemplate();
        if ($results === false) {
            // error
        }

        /** todo: amy fix and remove */
        $this->requestArray['format'] = 'html';
        $this->requestArray['view'] = 'dashboard';
        $this->requestArray['page'] = 'default';

        $this->requestArray = MolajoExtensionHelper::getExtensionOptions($this->requestArray);
        if ($this->requestArray['results'] === false) {
            echo 'failed getExtensionOptions';
        }

        /** todo: amy fix and remove */
        $this->requestArray['format'] = 'html';
        $this->requestArray['view'] = 'dashboard';
        $this->requestArray['page'] = 'default';

        /** View Path */
        $this->requestArray['view_type'] = 'extensions';
        $viewHelper = new MolajoViewHelper($this->requestArray['view'], $this->requestArray['view_type'], $this->requestArray['option'], $this->requestArray['extension_type'], ' ', $this->requestArray['template_name']);
        $this->requestArray['view_path'] = $viewHelper->view_path;
        $this->requestArray['view_path_url'] = $viewHelper->view_path_url;

        /** Wrap Path */
        $wrapHelper = new MolajoViewHelper($this->requestArray['wrap'], 'wraps', $this->requestArray['option'], $this->requestArray['extension_type'], ' ', $this->requestArray['template_name']);
        $this->requestArray['wrap_path'] = $wrapHelper->view_path;
        $this->requestArray['wrap_path_url'] = $wrapHelper->view_path_url;

        /** Page Path */
        $pageHelper = new MolajoViewHelper($this->requestArray['page'], 'pages', $this->requestArray['option'], $this->requestArray['extension_type'], ' ', $this->requestArray['template_name']);
        $this->requestArray['page_path'] = $pageHelper->view_path;
        $this->requestArray['page_path_url'] = $pageHelper->view_path_url;
        /**
        echo '<pre>';
        var_dump($this->requestArray);
        echo '</pre>';
         */
        /** Render by Format */
        $this->_renderFormat();

        /** return to application */
        return;
    }

    /**
     * getQueryRequest
     *
     * Request is stripped of Host, Folder, and Application
     *  Path ex. index.php?option=login or access/groups
     *
     * @return null
     */
    protected function _getQueryRequest()
    {
        $this->requestArray['sef'] = MolajoController::getApplication()->get('sef', 1);
        $this->requestArray['sef_rewrite'] = MolajoController::getApplication()->get('sef_rewrite', 0);
        $this->requestArray['sef_suffix'] = MolajoController::getApplication()->get('sef_suffix', 0);
        $this->requestArray['unicodeslugs'] = MolajoController::getApplication()->get('unicodeslugs', 0);
        $this->requestArray['force_ssl'] = MolajoController::getApplication()->get('force_ssl', 0);

        if ($this->_override_request == null) {
            $path = MOLAJO_PAGE_REQUEST;
        } else {
            $path = $this->_override_request;
        }

        /** duplicate content: URLs without the .html */
        $this->requestArray['sef_suffix'] = 1;
        if ($this->requestArray['sef_suffix'] == 1
            && substr($path, -11) == '/index.html'
        ) {
            $path = substr($path, 0, (strlen($path) - 11));
        }
        if ($this->requestArray['sef_suffix'] == 1
            && substr($path, -5) == '.html'
        ) {
            $path = substr($path, 0, (strlen($path) - 5));
        }

        /** populate value used in query  */
        $this->requestArray['query_request'] = $path;

        return;
    }

    /**
     * _getTemplate
     *
     * Get Template Name using either the Template ID or the Template Name
     *
     * @return bool
     * @since 1.0
     */
    protected function _getTemplate()
    {
        if ((int)$this->requestArray['template_id'] == 0) {
            $template = $this->requestArray['template_name'];
        } else {
            $template = $this->requestArray['template_id'];
        }

        $results = MolajoExtensionHelper::get(MOLAJO_ASSET_TYPE_EXTENSION_TEMPLATE, $template, null);

        if (count($results) > 0) {
            foreach ($results as $result) {
                $parameters = new JRegistry;
                $parameters->loadString($result->parameters);
                $this->requestArray['template_parameters'] = $parameters;
                $this->requestArray['template_id'] = $result->extension_id;
                $this->requestArray['template_name'] = $result->title;
            }
            return true;
        } else {
            return false;
        }
    }

    /**
     * _getQueryParameters
     *
     * Retrieve Parameter overrides from URL
     *
     * @return bool
     * @since 1.0
     */
    protected function _getQueryParameters()
    {
        //  todo: amy add parameter to turn this off in the template manager
        //  todo: amy filter input
        $parameterArray = array();
        $temp = substr(MOLAJO_PAGE_REQUEST, 10, (strlen(MOLAJO_PAGE_REQUEST) - 10));
        $parameterArray = explode('&', $temp);

        foreach ($parameterArray as $parameter) {

            $pair = explode('=', $parameter);

            if ($pair[0] == 'view') {
                $this->requestArray['view'] = (string)$pair[1];

            } elseif ($pair[0] == 'wrap') {
                $this->requestArray['wrap'] = (string)$pair[1];

            } elseif ($pair[0] == 'template') {
                $this->requestArray['template_name'] = (string)$pair[1];

            } elseif ($pair[0] == 'page') {
                $this->requestArray['page'] = (string)$pair[1];
            }
        }
        return true;
    }

    /**
     * _getSourceData
     *
     * Retrieve Parameters and MetaData for Source Detail
     *
     * @return  bool
     * @since   1.0
     */
    protected function _getSourceData()
    {
        $db = MolajoController::getDbo();
        $query = $db->getQuery(true);

        $query->select('a.' . $db->nameQuote('extension_instance_id'));
        $query->select('a.' . $db->nameQuote('title'));
        $query->select('a.' . $db->nameQuote('parameters'));
        $query->select('a.' . $db->nameQuote('metadata'));
        $query->from($db->nameQuote('#' . $this->requestArray['source_table']) . ' as a');
        $query->where('a.' . $db->nameQuote('id') . ' = ' . (int)$this->requestArray['source_id']);

        $db->setQuery($query->__toString());

        $results = $db->loadObjectList();
        if (count($results) > 0) {
            foreach ($results as $result) {
                $this->requestArray['extension_instance_id'] = $result->extension_instance_id;
                $this->requestArray['source_title'] = $result->title;
                $this->requestArray['source_parameters'] = $result->parameters;
                $this->requestArray['source_metadata'] = $result->metadata;
            }
            return true;
        } else {
            return false;
        }
    }

    /**
     * _getPrimaryCategory
     *
     * Retrieve the Menu Item Parameters and Meta Data
     *
     * @return  boolean
     * @since   1.0
     */
    protected function _getPrimaryCategory()
    {
        $db = MolajoController::getDbo();
        $query = $db->getQuery(true);

        $query->select('a.' . $db->nameQuote('title'));
        $query->select('a.' . $db->nameQuote('parameters'));
        $query->select('a.' . $db->nameQuote('metadata'));
        $query->from($db->nameQuote('#__content') . ' as a');
        $query->where('a.' . $db->nameQuote('id') . ' = ' . (int)$this->requestArray['category']);

        $db->setQuery($query->__toString());

        $results = $db->loadObjectList();
        if (count($results) > 0) {
            foreach ($results as $result) {
                $this->requestArray['category_title'] = $result->title;
                $this->requestArray['category_parameters'] = $result->parameters;
                $this->requestArray['category_metadata'] = $result->metadata;
            }
            return true;
        } else {
            return false;
        }
    }

    /**
     * _getComponent
     *
     * Retrieve Component information using either the ID
     *
     * @return bool
     * @since 1.0
     */
    protected function _getComponent()
    {
        // todo: amy fix and remove
        $this->requestArray['extension_instance_id'] = 9;
        $results = MolajoExtensionHelper::get(MOLAJO_ASSET_TYPE_EXTENSION_COMPONENT, (int)$this->requestArray['extension_instance_id'], null);

        if (count($results) > 0) {
            foreach ($results as $result) {
                $this->requestArray['extension_title'] = $result->title;

                $parameters = new JRegistry;
                $parameters->loadString($result->parameters);
                $this->requestArray['extension_parameters'] = $parameters;
                $this->requestArray['extension_metadata'] = $result->metadata;

                if (isset($this->requestArray['extension_parameters']->static)
                    && $this->requestArray['extension_parameters']->static === true
                ) {
                    $this->requestArray['static'] = true;
                } else {
                    $this->requestArray['static'] = false;
                }
                $this->requestArray['extension_path'] = MOLAJO_EXTENSIONS_COMPONENTS . '/' . $this->requestArray['option'];
                $this->requestArray['extension_type'] = 'component';
                $this->requestArray['extension_folder'] = '';
            }
            return true;
        } else {
            return false;
        }
    }

    /**
     *  _getApplicationDefaults
     *
     *  Retrieve Template and Page from the final level of default values, if needed
     *
     * @return bool
     * @since 1.0
     */
    protected function _getApplicationDefaults()
    {
        if ($this->requestArray['format'] == '') {
            $this->requestArray['format'] = MolajoController::getApplication()->get('default_format');
        }
        if ($this->requestArray['template_name'] == '') {
            $this->requestArray['template_name'] = MolajoController::getApplication()->get('default_template');
        }
        if ($this->requestArray['page'] == '') {
            $this->requestArray['page'] = MolajoController::getApplication()->get('default_page');
        }
        if ($this->requestArray['view'] == '') {

            if ($this->requestArray['static'] === true) {
                $this->requestArray['view'] = MolajoController::getApplication()->get('default_view_static');

            } else if ($this->requestArray['task'] == 'add'
                || $this->requestArray['task'] == 'edit'
            ) {
                $this->requestArray['task'] = MolajoController::getApplication()->get('default_view_edit');

            } else if ((int)$this->requestArray['id'] == 0) {
                $this->requestArray['view'] = MolajoController::getApplication()->get('default_view_items');

            } else {
                $this->requestArray['view'] = MolajoController::getApplication()->get('default_view_item');
            }
        }
        if ($this->requestArray['wrap'] == '') {

            if ($this->requestArray['static'] === true) {
                $this->requestArray['wrap'] = MolajoController::getApplication()->get('default_wrap_static');

            } elseif ($this->requestArray['task'] == 'add'
                || $this->requestArray['task'] == 'edit'
            ) {
                $this->requestArray['task'] = MolajoController::getApplication()->get('default_wrap_edit');

            } else if ((int)$this->requestArray['id'] == 0) {
                $this->requestArray['view'] = MolajoController::getApplication()->get('default_wrap_items');

            } else {
                $this->requestArray['view'] = MolajoController::getApplication()->get('default_wrap_item');
            }
        }
    }

    /**
     * _setPageValues
     *
     * Set the values needed to generate the page (template, page, view, wrap, and various metadata)
     *
     * @param null $sourceParameters
     * @param null $sourceMetadata
     *
     * @return bool
     * @since 1.0
     */
    protected function _setPageValues($parameters = null, $metadata = null)
    {
        /** rendering parameters */
        $params = new JRegistry;
        $params->loadString($parameters);

        if ($this->requestArray['template_name'] == '') {
        } else {
            $this->requestArray['template_name'] = $params->def('template', '');
        }
        if ((int)$this->requestArray['template_id'] == 0) {
        } else {
            $this->requestArray['template_id'] = $params->def('template_id', 0);
        }
        if ($this->requestArray['page'] == '') {
        } else {
            $this->requestArray['page'] = $params->def('page', '');
        }
        if ($this->requestArray['view'] == '') {
        } else {
            $this->requestArray['view'] = $params->def('view', '');
        }
        if ($this->requestArray['wrap'] == '') {
        } else {
            $this->requestArray['wrap'] = $params->def('wrap', '');
        }

        /** merge meta data */
        $meta = new JRegistry;
        $meta->loadString($metadata);

        if ($this->requestArray['metadata_title'] == '') {
            $this->requestArray['metadata_title'] = $meta->def('metadata_title', '');
        }

        if ($this->requestArray['metadata_description'] == '') {
            $this->requestArray['metadata_description'] = $meta->def('metadata_description', '');
        }

        if ($this->requestArray['metadata_keywords'] == '') {
            $this->requestArray['metadata_keywords'] = $meta->def('metadata_keywords', '');
        }

        if ($this->requestArray['metadata_author'] == '') {
            $this->requestArray['metadata_author'] = $meta->def('metadata_author', '');
        }

        if ($this->requestArray['metadata_rights'] == '') {
            $this->requestArray['metadata_rights'] = $meta->def('metadata_rights', '');
        }

        if ($this->requestArray['metadata_robots'] == '') {
            $this->requestArray['metadata_robots'] = $meta->def('metadata_robots', '');
        }
        //todo: amy figure out how to retrieve and keep all other meta
    }

    /**
     * getRedirectURL
     *
     * Function to retrieve asset information for the Request or Asset ID
     *
     * @return  string url
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
    }

    /**
     *  _setMetaData
     *
     * Establish the meta data for this web page
     *
     * @return bool
     * @since 1.0
     */
    protected function _setMetaData()
    {
        MolajoController::getApplication()->setTitle($this->requestArray['metadata_title']);
        MolajoController::getApplication()->setDescription($this->requestArray['metadata_description']);
        MolajoController::getApplication()->setMetaData('metadata_keywords', $this->requestArray['metadata_keywords']);
        MolajoController::getApplication()->setMetaData('metadata_author', $this->requestArray['metadata_author']);
        MolajoController::getApplication()->setMetaData('metadata_rights', $this->requestArray['metadata_rights']);
        MolajoController::getApplication()->setMetaData('metadata_robots', $this->requestArray['metadata_robots']);
        //todo: set extra values
        return true;
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
        //        MolajoPluginHelper::importPlugin('system');
        //        MolajoController::getApplication()->triggerEvent('onAfterRoute');
    }

    /**
     * _authorise
     *
     * Test user is authorised to view page
     *
     * @return  boolean
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
     * _renderFormat
     *
     * Render output by Format Class
     *
     * @return  object
     *
     * @since   1.0
     */
    protected function _renderFormat()
    {
        $documentTypeClass = 'Molajo' . ucfirst($this->requestArray['format']) . 'Format';
        new $documentTypeClass ($this->requestArray);
    }
}
