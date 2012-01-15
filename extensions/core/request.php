<?php
/**
 * @package     Molajo
 * @subpackage  Request
 * @copyright   Copyright (C) 2012 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die;

/**
 * Processes the Request
 *
 * Base class
 */
class MolajoRequest
{
    /**
     *  Override Request
     *
     * @var boolean
     * @since 1.0
     */
    private $_override_request = null;

    /**
     *  Request
     *
     * @var object
     * @since 1.0
     */
    public $request;

    /**
     * __construct
     *
     * Class constructor.
     *
     * @param   null    $override_url_request
     * @param   null    $asset_id
     *
     * @return boolean
     *
     * @since  1.0
     */
    public function __construct($override_url_request = null, $asset_id = null)
    {
        /** Request Variables: Passed to Document, Renderers and MVC */
        $this->_initializeRequest();

        /** Specific URL path (host, path and application removed) */
        if ($override_url_request == null) {
        } else {
            $this->_override_request = $override_url_request;
        }

        /** Specific asset */
        if ((int)$asset_id == 0) {
            $this->request->set('asset_id', 0);
        } else {
            $this->request->set('asset_id', $asset_id);
        }
    }

    /**
     * _initializeRequest
     *
     * Create and Initialize the request
     *
     * Request Object which is passed on to Document, Renderers and the MVC classes
     *
     * @static
     * @return array
     * @since 1.0
     */
    private function _initializeRequest()
    {
        /** @var $this->request */
        $this->request = new JObject();
        $this->request->set('type', 'base');

        $this->request->set('parameters', '');

        /** status */
        $this->request->set('error', false);
        $this->request->set('authorised', false);
        $this->request->set('found', false);

        /** request */
        $this->request->set('base', MOLAJO_BASE_URL);
        $this->request->set('query_request', '');
        $this->request->set('request', '');
        $this->request->set('sef_request', '');
        $this->request->set('redirect_to_id', 0);
        $this->request->set('home', 0);
        $this->request->set('sef', 0);
        $this->request->set('sef_rewrite', 0);
        $this->request->set('sef_suffix', 0);
        $this->request->set('unicode_slugs', 0);
        $this->request->set('force_ssl', 0);

        /** format */
        $this->request->set('format', '');

        /** template */
        $this->request->set('template_id', 0);
        $this->request->set('template_name', '');
        $this->request->set('template_parameters', array());
        $this->request->set('template_path', '');
        $this->request->set('template_include', '');
        $this->request->set('template_favicon', '');
        $this->request->set('template_asset_id', '');

        /** page */
        $this->request->set('page_name', '');
        $this->request->set('page_path', '');
        $this->request->set('page_path_url', '');
        $this->request->set('page_template_include_statement', '');

        /** head */
        $this->request->set('metadata_title', '');
        $this->request->set('metadata_description', '');
        $this->request->set('metadata_generator', MolajoController::getApplication()->get('generator', 'Molajo'));
        $this->request->set('metadata_keywords', '');
        $this->request->set('metadata_author', '');
        $this->request->set('metadata_content_rights', '');
        $this->request->set('metadata_robots', '');
        $this->request->set('metadata_additional_array', array());

        /** render parameters */
        $this->request->set('controller', '');
        $this->request->set('model', '');
        $this->request->set('static', '');
        $this->request->set('option', '');
        $this->request->set('task', '');
        $this->request->set('id', 0);
        $this->request->set('ids', array());

        /** view */
        $this->request->set('view', '');
        $this->request->set('view_type', 'extensions');
        $this->request->set('view_path', '');
        $this->request->set('view_path_url', '');

        /** wrap */
        $this->request->set('wrap', '');
        $this->request->set('wrap_path', '');
        $this->request->set('wrap_path_url', '');
        $this->request->set('wrap_id', '');
        $this->request->set('wrap_class', '');

        /** other */
        $this->request->set('plugin_type', '');
        $this->request->set('acl_implementation', '');
        $this->request->set('other_parameters', array());

        /** asset */
        $this->request->set('asset_id', 0);
        $this->request->set('asset_type_id', 0);
        $this->request->set('source_language', '');
        $this->request->set('translation_of_id', 0);
        $this->request->set('view_group_id', 0);

        /** extension */
        $this->request->set('extension_instance_id', 0);
        $this->request->set('extension_title', '');
        $this->request->set('extension_parameters', array());
        $this->request->set('extension_metadata', array());
        $this->request->set('extension_path', '');
        $this->request->set('extension_type', '');
        $this->request->set('extension_folder', '');

        /** primary category */
        $this->request->set('category', 0);
        $this->request->set('category_title', '');
        $this->request->set('category_parameters', array());
        $this->request->set('category_metadata', array());

        /** menu item data */
        $this->request->set('menu_item_id', 0);
        $this->request->set('menu_item_parameters', array());
        $this->request->set('menu_item_metadata', array());

        /** source data */
        $this->request->set('source_table', '');
        $this->request->set('source_id', 0);
        $this->request->set('source_last_modified', getDate());
        $this->request->set('source_parameters', array());
        $this->request->set('source_metadata', array());

        /** results */
        $this->request->set('suppress_no_results', false);
        $this->request->set('results', '');

        return $this->request;
    }

    /**
     * process
     *
     * Using the MOLAJO_PAGE_REQUEST value,
     *  retrieve the asset record,
     *  set the variables needed to render output,
     *  execute document renders and MVC
     *
     * @return bool|null
     */
    public function process()
    {
        /** Request */
        $this->_getQueryRequest();

        /** home: duplicate content - redirect */
        if ($this->request->get('query_request', '') == 'index.php'
            || $this->request->get('query_request', '') == 'index.php/'
            || $this->request->get('query_request', '') == 'index.php?'
            || $this->request->get('query_request', '') == '/index.php/'
        ) {
            MolajoController::getApplication()->redirect('', 301);
            return $this->request;
        }

        /** Home */
        if ($this->request->get('query_request', '') == ''
            && (int)$this->request->get('asset_id', 0) == 0
        ) {
            $this->request->set('asset_id', MolajoController::getApplication()->get('home_asset_id', 0));
            $this->request->set('home', true);
        }

        /** Site offline */
        if (MolajoController::getApplication()->get('offline', 0) == 1) {
            $this->request->set('error', true);
            MolajoController::getApplication()->setHeader('Status', '503 Service Temporarily Unavailable', 'true');
            $message = MolajoController::getApplication()->get('offline_message', 'This site is not available.<br /> Please check back again soon.');
            MolajoController::getApplication()->setMessage($message, MOLAJO_MESSAGE_TYPE_WARNING);
            $this->request->set('template_name', MolajoController::getApplication()->get('offline_template', 'system'));
            $this->request->set('page_name', MolajoController::getApplication()->get('offline_page', 'offline'));
            return $this->_render();
        }

        /** Get Asset Information */
        $this->request = MolajoExtensionHelper::getAsset($this->request);

        /** Must be Logged on Requirement */
        if (MolajoController::getApplication()->get('logon_requirement', 0) > 0
            && MolajoController::getUser()->get('guest', true) === true
            && $this->request->get('asset_id') <> MolajoController::getApplication()->get('logon_requirement', 0)
        ) {
            $this->request->set('error', true);
            MolajoController::getApplication()->redirect(MolajoController::getApplication()->get('logon_requirement', 0), 303);
            return $this->request;
        }

        /** Route */
        //        $this->_route($this);

        /** 404 Not Found */
        if ($this->request->get('found') === false) {
            $this->request->set('error', true);
            MolajoController::getApplication()->setHeader('Status', '404 Not Found', 'true');
            $message = MolajoController::getApplication()->get('error_404_message', 'Page not found.');
            MolajoController::getApplication()->setMessage($message, MOLAJO_MESSAGE_TYPE_ERROR, 404);
            $this->request->set('template_name', MolajoController::getApplication()->get('error_template', 'system'));
            $this->request->set('page_name', MolajoController::getApplication()->get('error_page', 'error'));
            $this->request->set('view', MolajoController::getApplication()->get('error_view', 'error'));
            $this->request->set('wrap', MolajoController::getApplication()->get('error_wrap', 'none'));
            return $this->_render();
        }

        /** redirect_to_id */
        if ($this->request->get('redirect_to_id', 0) == 0) {
        } else {
            MolajoController::getApplication()->redirect($this->request->set('redirect_to_id', 301));
            return $this->request;
        }

        /** acl */
        $this->_authorise();
        if ($this->request->get('authorised') === false) {
            $this->request->set('error', true);
            MolajoController::getApplication()->setHeader('Status', '403 Not Authorised', 'true');
            $this->request->set('template_name', MolajoController::getApplication()->get('error_template', 'system'));
            $this->request->set('page_name', MolajoController::getApplication()->get('error_page', 'error'));
            $this->request->set('format', MolajoController::getApplication()->get('error_format', 'error'));
            return $this->_render();
        }

        if ($this->request->get('task') == 'add'
            || $this->request->get('task') == 'edit'
            || $this->request->get('task') == 'display'
        ) {
            return $this->_render();
        } else {
            return $this->_task();
        }
    }

    /**
     *  _render
     *
     * @return
     * @since  1.0
     */
    protected function _render()
    {
        $this->_setRenderingParameters();

        $this->_setRenderingPaths();

        new MolajoDocument ($this->request);
        return $this->request;
    }

    /**
     *  _task
     *
     * @return
     * @since  1.0
     */
    protected function _task()
    {

    }

    /**
     *  _setRenderingParameters
     *
     *  Determine rendering parameters and page metadata in priority order
     *
     *  1. Query Parameters
     *  2. Asset (already set in array)
     *  3. Source Data
     *  4. Component
     *  5. Primary Category
     *  6. Menu Item
     *  7. User
     *  8. Application
     *  9. Hardcoded
     */
    protected function _setRenderingParameters()
    {
        /** 1: Request Override */
        $this->_getQueryParameters();

        /** 2: Asset (already set in array) */

        /** 3: Source Table ID */
        if ((int)$this->request->get('source_id') == 0) {
        } else {
            $results = $this->_getSourceData();
            if ($results === false) {
                //error
            } else {
                $this->_setPageValues($this->request->get('source_parameters',
                    $this->request->get('source_metadata')));
            }
        }

        /** 4: Component */
        if ((int)$this->request->get('extension_instance_id') == 0) {
        } else {
            $results = $this->_getComponent();
            if ($results === false) {
                //error
            } else {
                $this->_setPageValues($this->request->get('extension_parameters',
                    $this->request->get('extension_metadata')));
            }
        }

        /** 5: Primary Category */
        if ((int)$this->request->get('category', 0) == 0) {
        } else {
            $results = $this->_getPrimaryCategory();
            if ($results === false) {
                //error
            }
            $this->_setPageValues($this->request->get('category_parameters',
                $this->request->get('category_metadata')));
        }

        /** 6: Menu Item */

        /** 7: User */

        /** 8: Application (static, items, item, edit) */
        $results = $this->_getApplicationDefaults();

        /** 9. Template values */
        $results = $this->_getTemplate();
        if ($results === false) {
            // error
        }

        /** 10. Extension Request Verification and Defaults  */
        if ((int)$this->request->get('extension_instance_id') == 0) {
        } else {
            $this->request = MolajoExtensionHelper::getExtensionOptions($this->request);
            if ($this->request->set('results', false)) {
                echo 'failed getExtensionOptions';
            }
        }

        //$this->_mergeParameters();
        /**
        $temp = (array)$this->request;
        echo '<pre>';
        var_dump($temp);
        echo '</pre>';
        die;
         */
    }

    /**
     * _setRenderingPaths
     *
     * Set paths for Template, page, view, and wrap
     *
     * @return mixed
     */
    protected function _setRenderingPaths()
    {
        /** View Path */
        $this->request->set('view_type', 'extensions');
        $viewHelper = new MolajoViewHelper($this->request->get('view'),
            $this->request->get('view_type'),
            $this->request->get('option'),
            $this->request->get('extension_type'),
            ' ',
            $this->request->get('template_name'));
        $this->request->set('view_path', $viewHelper->view_path);
        $this->request->set('view_path_url', $viewHelper->view_path_url);

        /** Wrap Path */
        $wrapHelper = new MolajoViewHelper($this->request->get('wrap'),
            'wraps',
            $this->request->get('option'),
            $this->request->get('extension_type'),
            ' ',
            $this->request->get('template_name'));
        $this->request->set('wrap_path', $wrapHelper->view_path);
        $this->request->set('wrap_path_url', $wrapHelper->view_path_url);

        /** Page Path */
        $pageHelper = new MolajoViewHelper($this->request->get('page'),
            'pages',
            $this->request->get('option'),
            $this->request->get('extension_type'),
            ' ',
            $this->request->get('template_name'));
        $this->request->set('page_path', $pageHelper->view_path);
        $this->request->set('page_path_url', $pageHelper->view_path_url);

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
        $this->request->set('sef', MolajoController::getApplication()->get('sef', 1));
        $this->request->set('sef_rewrite', MolajoController::getApplication()->get('sef_rewrite', 0));
        $this->request->set('sef_suffix', MolajoController::getApplication()->get('sef_suffix', 0));
        $this->request->set('unicode_slugs', MolajoController::getApplication()->get('unicode_slugs', 0));
        $this->request->set('force_ssl', MolajoController::getApplication()->get('force_ssl', 0));

        if ($this->_override_request == null) {
            $path = MOLAJO_PAGE_REQUEST;
        } else {
            $path = $this->_override_request;
        }

        /** duplicate content: URLs without the .html */
        $this->request->set('sef_suffix', 1);
        if ($this->request->set('sef_suffix') == 1
            && substr($path, -11) == '/index.html'
        ) {
            $path = substr($path, 0, (strlen($path) - 11));
        }
        if ($this->request->set('sef_suffix') == 1
            && substr($path, -5) == '.html'
        ) {
            $path = substr($path, 0, (strlen($path) - 5));
        }

        /** populate value used in query  */
        $this->request->set('query_request', $path);

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
        if ((int)$this->request->set('template_id') == 0) {
            $template = $this->request->get('template_name');
        } else {
            $template = $this->request->get('template_id');
        }

        $results = MolajoExtensionHelper::get(MOLAJO_ASSET_TYPE_EXTENSION_TEMPLATE, $template, null);

        if (count($results) > 0) {
            foreach ($results as $result) {
                $parameters = new JRegistry;
                $parameters->loadString($result->parameters);
                $this->request->set('template_parameters', $parameters);
                $this->request->set('template_id', $result->extension_id);
                $this->request->set('template_name', $result->title);
                $this->request->set('template_asset_id', $result->extension_instance_asset_id);
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
                $this->request->set('view', (string)$pair[1]);

            } elseif ($pair[0] == 'wrap') {
                $this->request->set('wrap', (string)$pair[1]);

            } elseif ($pair[0] == 'template') {
                $this->request->set('template_name', (string)$pair[1]);

            } elseif ($pair[0] == 'page') {
                $this->request->set('page_name', (string)$pair[1]);
            }
        }
        return true;
    }

    /**
     * _getSourceData
     *
     * Retrieve Parameters and Metadata for Source Detail
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
        $query->from($db->nameQuote('#' . $this->request->get('source_table')) . ' as a');
        $query->where('a.' . $db->nameQuote('id') . ' = ' . (int)$this->request->get('source_id'));

        $db->setQuery($query->__toString());
        $results = $db->loadObjectList();

        if (count($results) > 0) {
            foreach ($results as $result) {
                $this->request->set('extension_instance_id', $result->extension_instance_id);
                $this->request->set('source_title', $result->title);
                $this->request->set('source_parameters', $result->parameters);
                $this->request->set('source_metadata', $result->metadata);
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
        $query->where('a.' . $db->nameQuote('id') . ' = ' . (int)$this->request->get('category'));

        $db->setQuery($query->__toString());

        $results = $db->loadObjectList();
        if (count($results) > 0) {
            foreach ($results as $result) {
                $this->request->set('category_title', $result->title);
                $this->request->set('category_parameters', $result->parameters);
                $this->request->set('category_metadata', $result->metadata);
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
        $this->request->set('extension_instance_id', 9);

        $results = MolajoExtensionHelper::get(MOLAJO_ASSET_TYPE_EXTENSION_COMPONENT, (int)$this->request->get('extension_instance_id'));

        if (count($results) > 0) {
            foreach ($results as $result) {
                $this->request->set('extension_name', $result->extension_name);
                $this->request->set('extension_title', $result->title);

                $parameters = new JRegistry;
                $parameters->loadString($result->parameters);
                $this->request->set('extension_parameters', $parameters);
                $this->request->set('extension_metadata', $result->metadata);

                if (isset($parameters->static)
                    && $parameters->static === true
                ) {
                    $this->request->set('static', true);
                } else {
                    $this->request->set('static', false);
                }
                $this->request->set('extension_path', MOLAJO_EXTENSIONS_COMPONENTS . '/' . $this->request->set('option'));
                $this->request->set('extension_type', 'component');
                $this->request->set('extension_folder', '');
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
        /** format */
        if ($this->request->get('format', '') == '') {
            $this->request->set('format', MolajoController::getApplication()->get('default_format', ''));
        }
        /** template/page */
        if ($this->request->get('template_name', '') == '') {
            $this->request->set('template_name', MolajoController::getApplication()->get('default_template', ''));
        }
        if ($this->request->get('page', '') == '') {
            $this->request->set('page_name', MolajoController::getApplication()->get('default_page', ''));
        }

        /** view */
        if ($this->request->get('view', '') == '') {

            if ($this->request->get('static', true)) {
                $this->request->set('view', MolajoController::getApplication()->get('default_static_view', ''));

            } else if ($this->request->get('task', '') == 'add'
                || $this->request->get('task', '') == 'edit'
            ) {
                $this->request->set('task', MolajoController::getApplication()->get('default_edit_view', ''));

            } else if ((int)$this->request->get('id', 0) == 0) {
                $this->request->set('view', MolajoController::getApplication()->get('default_items_view', ''));

            } else {
                $this->request->set('view', MolajoController::getApplication()->get('default_item_view', ''));
            }
        }

        /** wrap */
        if ($this->request->get('wrap', '') == '') {

            if ($this->request->get('static', false) === true) {
                $this->request->set('wrap', MolajoController::getApplication()->get('default_static_wrap', ''));

            } elseif ($this->request->get('task', '') == 'add'
                || $this->request->get('task', '') == 'edit'
            ) {
                $this->request->set('task', MolajoController::getApplication()->get('default_edit_wrap', ''));

            } else if ((int)$this->request->get('id', 0) == 0) {
                $this->request->set('wrap', MolajoController::getApplication()->get('default_items_wrap', ''));

            } else {
                $this->request->set('wrap', MolajoController::getApplication()->get('default_item_wrap', ''));
            }
        }

        /** metadata  */

        if ($this->request->get('metadata_title', '') == '') {
            $appname = MolajoController::getApplication()->get('application_name', '');
            $sitename = MolajoController::getApplication()->get('site_name', '');
            if (trim($appname) == trim($sitename)) {
                $temp = $appname;
            } else {
                $temp = $appname . ' - ' . $sitename;
            }
            $this->request->set('metadata_title', $temp);
        }

        if ($this->request->get('metadata_description', '') == '') {
            $this->request->set('metadata_description', MolajoController::getApplication()->get('metadata_description', ''));
        }

        if ($this->request->get('metadata_keywords', '') == '') {
            $this->request->set('metadata_keywords', MolajoController::getApplication()->get('metadata_keywords', ''));
        }

        if ($this->request->get('metadata_author', '') == '') {
            $this->request->set('metadata_author', MolajoController::getApplication()->get('metadata_author', ''));
        }

        if ($this->request->get('metadata_content_rights', '') == '') {
            $this->request->set('metadata_content_rights', MolajoController::getApplication()->get('metadata_content_rights', ''));
        }

        if ($this->request->get('metadata_robots', '') == '') {
            $this->request->set('metadata_robots', MolajoController::getApplication()->get('metadata_robots', ''));
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

        if ($this->request->get('template_name', '') == '') {
        } else {
            $this->request->set('template_name', $params->def('template', ''));
        }

        if ((int)$this->request->get('template_id', 0) == 0) {
        } else {
            $this->request->set('template_id', $params->def('template_id', 0));
        }

        if ($this->request->get('page', '') == '') {
        } else {
            $this->request->set('page_name', $params->def('page', ''));
        }

        if ($this->request->get('view', '') == '') {
        } else {
            $this->request->set('view', $params->def('view', ''));
        }

        if ($this->request->get('wrap', '') == '') {
        } else {
            $this->request->set('wrap', $params->def('wrap', ''));
        }

        /** merge meta data */
        $meta = new JRegistry;
        $meta->loadString($metadata);

        if ($this->request->get('metadata_title', '') == '') {
            $this->request->set('metadata_title', $meta->def('metadata_title', ''));
        }

        if ($this->request->get('metadata_description', '') == '') {
            $this->request->set('metadata_description', $meta->def('metadata_description', ''));
        }

        if ($this->request->get('metadata_keywords', '') == '') {
            $this->request->set('metadata_keywords', $meta->def('metadata_keywords', ''));
        }

        if ($this->request->get('metadata_author', '') == '') {
            $this->request->set('metadata_author', $meta->def('metadata_author', ''));
        }

        if ($this->request->get('metadata_content_rights', '') == '') {
            $this->request->set('metadata_content_rights', $meta->def('metadata_content_rights', ''));
        }

        if ($this->request->get('metadata_robots', '') == '') {
            $this->request->set('metadata_robots', $meta->def('metadata_robots', ''));
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
        if (in_array($this->request->get('view_group_id'), MolajoController::getUser()->view_groups)) {
            $this->request->set('authorised', true);
        } else {
            $this->request->set('authorised', false);
        }
    }

    /**
     *  _mergeParameters
     */
    protected function _mergeParameters()
    {
        /** initialize */
        $temp = array();
        $parameters = array();

        /** load request (without parameter fields) */
        //        $temp = $this->request;
        //        $parameters = $this->_merge($parameters, $temp);

        /** source parameters */
        $temp = array();
        $temp = json_decode($this->request->get('source_parameters'));
        $parameters = $this->_merge($parameters, $temp);

        /** category parameters */
        $temp = array();
        $temp = json_decode($this->request->get('category_parameters'));
        $parameters = $this->_merge($parameters, $temp);

        /** extension parameters */
        $temp = array();
        $temp = json_decode($this->request->get('extension_parameters'));

        $this->parameters = $this->_merge($parameters, $temp);

        echo '<pre>';
        var_dump($this->parameters);
        '</pre>';
        die();
    }

    /**
     *  _merge
     */
    protected function _merge($parameters, $temp)
    {
        if (count($temp) == 0) {
            return $parameters;
        }
        foreach ($temp as $name => $value) {
            if (strpos($name, 'parameter')) {
            } else {
                if (isset($parameters->$name)) {
                    if (trim($parameters->$name) == '') {
                        $parameters->$name = $value;
                    }
                } else {
                    $parameters->$name = $value;
                }
            }
        }
        return $parameters;
    }
}
