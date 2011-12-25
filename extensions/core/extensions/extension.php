<?php
/**
 * @package     Molajo
 * @subpackage  Extension
 * @copyright   Copyright (C) 2012 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die;

/**
 * Molajo Extension Class
 *
 * Base class
 */
class MolajoExtension
{
    /**
     *  Request formatted for query
     *
     * @var string
     * @since 1.0
     */
    protected $query_request = null;

    /**
     *  User
     *
     * @var string
     * @since 1.0
     */
    protected $user = null;

    /**
     *  Request
     *
     * @var string
     * @since 1.0
     */
    public $request = null;

    /**
     *  SEF Request
     *
     * @var string
     * @since 1.0
     */
    public $sef_request = null;

    /**
     *  Home
     *
     * @var boolean
     * @since 1.0
     */
    public $home = null;

    /**
     *  Option
     *
     * @var string
     * @since 1.0
     */
    public $option = null;

    /**
     *  Task
     *
     * @var string
     * @since 1.0
     */
    public $task = null;

    /**
     *  View
     *
     * @var string
     * @since 1.0
     */
    public $view = null;

    /**
     *  Layout
     *
     * @var string
     * @since 1.0
     */
    public $layout = null;

    /**
     *  Wrap
     *
     * @var string
     * @since 1.0
     */
    public $wrap = null;

    /**
     *  Format
     *
     * @var string
     * @since 1.0
     */
    public $format = null;

    /**
     *  Template
     *
     * @var integer
     * @since 1.0
     */
    public $template = null;

    /**
     *  Template Page
     *
     * @var string
     * @since 1.0
     */
    public $page = null;

    /**
     *  Id
     *
     * @var integer
     * @since 1.0
     */
    public $id = null;

    /**
     *  Message
     *
     * @var integer
     * @since 1.0
     */
    public $message = null;

    /**
     *  Asset
     *
     * @var integer
     * @since 1.0
     */
    public $asset_id = null;

    /**
     *  Asset Type ID
     *
     * @var integer
     * @since 1.0
     */
    public $asset_type_id = null;

    /**
     *  Source Table
     *
     * @var string
     * @since 1.0
     */
    public $source_table = null;

    /**
     *  Source ID
     *
     * @var integer
     * @since 1.0
     */
    public $source_id = null;

    /**
     *  Source Parameters
     *
     * @var integer
     * @since 1.0
     */
    public $source_parameters = null;

    /**
     *  Component
     *
     * @var integer
     * @since 1.0
     */
    public $component_id = null;

    /**
     *  Component
     *
     * @var integer
     * @since 1.0
     */
    public $component_parameters = null;

    /**
     *  Language
     *
     * @var string
     * @since 1.0
     */
    public $language = null;

    /**
     *  Translation of ID
     *
     * @var integer
     * @since 1.0
     */
    public $translation_of_id = null;

    /**
     *  Redirect to ID
     *
     * @var integer
     * @since 1.0
     */
    public $redirect_to_id = null;

    /**
     *  View Group ID
     *
     * @var integer
     * @since 1.0
     */
    public $view_group_id = null;

    /**
     *  Primary Category ID
     *
     * @var integer
     * @since 1.0
     */
    public $primary_category_id = null;

    /**
     *  Document Title
     *
     * @var boolean
     * @since 1.0
     */
    public $meta_title = null;

    /**
     *  Document Meta Description
     *
     * @var boolean
     * @since 1.0
     */
    public $meta_description = null;

    /**
     *  Document Meta Keywords
     *
     * @var boolean
     * @since 1.0
     */
    public $meta_keywords = null;

    /**
     *  Document Meta Author
     *
     * @var boolean
     * @since 1.0
     */
    public $meta_author = null;

    /**
     *  Document Meta Content Rights
     *
     * @var boolean
     * @since 1.0
     */
    public $meta_content_rights = null;

    /**
     *  Document Robots
     *
     * @var boolean
     * @since 1.0
     */
    public $meta_robots = null;

    /**
     *  Found
     *
     * @var boolean
     * @since 1.0
     */
    public $found = null;

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
        /** Specific URL path (less host, path and application) */
        if ($request == null) {
        } else {
            $this->query_request = $request;
        }

        /** Specific asset */
        if ((int)$asset_id == 0) {
            $this->asset_id = 0;
        } else {
            $this->asset_id = $asset_id;
        }
    }

    /**
     * load
     *
     * Using the MOLAJO_PAGE_REQUEST value, retrieve the asset record, set the variables needed to render output
     *
     * @return bool|null
     */
    public function load()
    {
        /** Request */
        $this->_getSEFOptions();

        /** home: duplicate content - redirect */
        if ($this->query_request == 'index.php'
            || $this->query_request == 'index.php/'
            || $this->query_request == 'index.php?'
            || $this->query_request == '/index.php/') {
            MolajoFactory::getApplication()->redirect(MolajoFactory::getApplication()->get('home_asset_id'), 301);
            return;
        }

        /** Home */
        if ($this->query_request == ''
            && $this->asset_id == 0) {
            $this->asset_id = MolajoFactory::getApplication()->get('home_asset_id', 0);
            $this->home = true;
        }

        /** user object */
        $this->_loadUser();

        /** Site offline */
        if (MolajoFactory::getApplication()->get('offline', 0) == 1) {
            MolajoFactory::getApplication()->setHeader('Status', '503 Service Temporarily Unavailable', 'true');
            $this->asset_id = MolajoFactory::getApplication()->get('asset_id', 0);
            $this->template = MolajoFactory::getApplication()->get('offline_template', 'system');
            $this->page = MolajoFactory::getApplication()->get('offline_page', 'full');
            $this->layout = MolajoFactory::getApplication()->get('offline_layout', 'offline');
            $this->wrap = MolajoFactory::getApplication()->get('offline_wrap', 'div');
            $this->format = MolajoFactory::getApplication()->get('offline_format', 'static');
            $this->message = MolajoFactory::getApplication()->get('offline_message', 'This site is not available.<br /> Please check back again soon.');
        }

        /** Get Asset Information */
        $this->_getAsset();

        /** Logged on Requirement */
        if (MolajoFactory::getApplication()->get('logon_requirement', 0) > 0
            && $this->user->get('guest', true) === true
            && $this->asset_id <> MolajoFactory::getApplication()->get('logon_requirement', 0)
        ) {
            MolajoFactory::getApplication()->redirect(MolajoFactory::getApplication()->get('logon_requirement', 0), 303);
            return;
        }

        /** Route */
        //        $this->_route($this);

        /** 404 Not Found */
        if ($this->found === false) {
            MolajoFactory::getApplication()->setHeader('Status', '404 Not Found', 'true');
            $this->template = MolajoFactory::getApplication()->get('error_template', 'system');
            $this->page = MolajoFactory::getApplication()->get('error_page', 'print');
            $this->layout = MolajoFactory::getApplication()->get('error_layout', 'error');
            $this->wrap = MolajoFactory::getApplication()->get('error_wrap', 'none');
        }

        /** act on redirect_to_id */
        if ($this->redirect_to_id == 0) {
        } else {
            MolajoFactory::getApplication()->redirect($this->redirect_to_id, 301);
            return;
        }

        /** acl check */
        $this->_authorise();

        /** 403 Not Found */
        if ($this->found === false) {
            MolajoFactory::getApplication()->setHeader('Status', '403 Not Authorised', 'true');
            $this->template = MolajoFactory::getApplication()->get('error_template', 'system');
            $this->page = MolajoFactory::getApplication()->get('error_page', 'print');
            $this->layout = MolajoFactory::getApplication()->get('error_layout', 'error');
            $this->wrap = MolajoFactory::getApplication()->get('error_wrap', 'none');
        }

        /** retrieve metadata and parameters */
        $this->_getMetaData();

        /** render output */
        $this->_setMetaData();
//echo '<pre>';var_dump($this);echo '</pre>';
        $this->_renderDocumentType();

        /** return to application */
        return;
    }

    /**
     * _getSEFOptions
     *
     * Request is already stripped of Host, Folder, and Application
     *
     * @param null $request
     * @return mixed
     */
    protected function _getSEFOptions($request = null)
    {
        /** Application SEF Options */
        $sef = MolajoFactory::getApplication()->get('sef', 1);
        $sef_rewrite = MolajoFactory::getApplication()->get('sef_rewrite', 0);
        $sef_suffix = MolajoFactory::getApplication()->get('sef_suffix', 0);
        $unicodeslugs = MolajoFactory::getApplication()->get('unicodeslugs', 0);
        $force_ssl = MolajoFactory::getApplication()->get('force_ssl' . 0);

        /** Path ex. index.php?option=login or access/groups */
        if ($request == null) {
            $path = MOLAJO_PAGE_REQUEST;
        } else {
            $path = $request;
        }

        /** duplicate content: URL's without the .html */
        $sef_suffix = 1;
        if ($sef_suffix == 1 && substr($path, -11) == '/index.html') {
            $path = substr($path, 0, (strlen($path) - 11));
        }
        if ($sef_suffix == 1 && substr($path, -5) == '.html') {
            $path = substr($path, 0, (strlen($path) - 5));
        }

        /** populate value used in query  */
        $this->query_request = $path;

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
        $db = MolajoFactory::getDbo();
        $query = $db->getQuery(true);

        $query->select('a.' . $db->nameQuote('id') . ' as asset_id');
        $query->select('a.' . $db->nameQuote('asset_type_id'));
        $query->select('a.' . $db->nameQuote('source_id'));
        $query->select('a.' . $db->nameQuote('primary_category_id'));
        $query->select('a.' . $db->nameQuote('template_id'));
        $query->select('a.' . $db->nameQuote('template_page'));
        $query->select('a.' . $db->nameQuote('language'));
        $query->select('a.' . $db->nameQuote('translation_of_id'));
        $query->select('a.' . $db->nameQuote('redirect_to_id'));
        $query->select('a.' . $db->nameQuote('view_group_id'));
        $query->select('a.' . $db->nameQuote('primary_category_id'));
        $query->select('a.' . $db->nameQuote('sef_request'));
        $query->select('a.' . $db->nameQuote('request'));

        $query->select('b.' . $db->nameQuote('component_option') . ' as ' . $db->nameQuote('option'));
        $query->select('b.' . $db->nameQuote('source_table'));

        $query->from($db->nameQuote('#__assets') . ' as a');
        $query->from($db->nameQuote('#__asset_types') . ' as b');

        $query->where('a.' . $db->nameQuote('asset_type_id') . ' = b.' . $db->nameQuote('id'));

        if ((int)$this->asset_id == 0) {
            if (MolajoFactory::getApplication()->get('sef', 1) == 1) {
                $query->where('a.' . $db->nameQuote('sef_request') . ' = ' . $db->Quote($this->query_request));
            } else {
                $query->where('a.' . $db->nameQuote('request') . ' = ' . $db->Quote($this->query_request));
            }
        } else {
            $query->where('a.' . $db->nameQuote('id') . ' = ' . (int)$this->asset_id);
        }

        $db->setQuery($query->__toString());
        $results = $db->loadObjectList();
//echo '<pre>';var_dump($results);echo '</pre>';

        //    MolajoFactory::getApplication()->enqueueMessage($db->getErrorMsg(), 'error');
        //    return false;

        if (count($results) == 0) {
            $this->found = false;
        } else {
            $this->found = true;
            foreach ($results as $result) {

                if ($this->asset_id == MolajoFactory::getApplication()->get('home_asset_id')) {
                    $this->home = true;
                } else {
                    $this->home = false;
                }
                $this->option = $result->option;
                $template_id = $result->template_id;
                if ((int)$template_id == 0) {
                    $this->template = $this->_getTemplate($template_id);
                }
                $this->page = $result->template_page;
                $this->asset_id = $result->asset_id;
                $this->asset_type_id = $result->asset_type_id;
                $this->source_table = $result->source_table;
                $this->source_id = $result->source_id;
                $this->language = $result->language;
                $this->translation_of_id = $result->translation_of_id;
                $this->redirect_to_id = $result->redirect_to_id;
                $this->view_group_id = $result->view_group_id;
                $this->primary_category_id = $result->primary_category_id;

                $this->request = $result->request;
                $this->sef_request = $result->sef_request;

                $parameterArray = array();
                $temp = substr($this->request, 10, (strlen($this->request) - 10));
                $parameterArray = explode('&', $temp);
                foreach ($parameterArray as $parameter) {
                    $pair = explode('=', $parameter);
                    if ($pair[0] == 'option') {
                        $this->option = $pair[1];
                    } elseif ($pair[0] == 'task') {
                        $this->task = $pair[1];
                    } elseif ($pair[0] == 'view') {
                        $this->view = $pair[1];
                    } elseif ($pair[0] == 'format') {
                        $this->format = $pair[1];
                    } elseif ($pair[0] == 'layout') {
                        $this->layout = $pair[1];
                    } elseif ($pair[0] == 'wrap') {
                        $this->wrap = $pair[1];
                    } elseif ($pair[0] == 'id') {
                        $this->id = $pair[1];
                    }
                }

                if ($this->task == '' || $this->task == null) {
                    $this->task = 'display';
                }
                if ($this->format == '' || $this->format == null) {
                    $this->format = MolajoFactory::getApplication()->get('default_format', 'html');
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
    protected function _getTemplate($template_id)
    {
        $db = MolajoFactory::getDbo();
        $query = $db->getQuery(true);

        $query->select('a.' . $db->nameQuote('title'));
        $query->from($db->nameQuote('#__extension_instances') . ' as a');
        $query->where('a.' . $db->nameQuote('id') . ' = ' . (int)$template_id);

        $db->setQuery($query->__toString());

        $result = $db->loadResult();
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
        if ((int)$this->primary_category_id == 0) {
        } else {
            $this->getPrimaryCategory();
        }

        /** Priority 6: Component */
        $this->_getComponent();

        /** Priority 7: Application (static, items, item, edit) */
        $this->application_template = MolajoFactory::getApplication()->get('default_template');
        $this->application_page = MolajoFactory::getApplication()->get('default_page');
        $this->default_layout_items = MolajoFactory::getApplication()->get('default_layout_items');
        $this->default_wrap_items = MolajoFactory::getApplication()->get('default_wrap_items');

        /** Priority 8: System-defined */
        if ($this->template == null) {
            $this->template = MolajoFactory::getApplication()->get('default_template');
        }
        if ($this->page == null) {
            $this->page = MolajoFactory::getApplication()->get('default_page');;
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
            if ($pair[0] == 'layout') {
                $this->layout = $pair[1];
            } elseif ($pair[0] == 'wrap') {
                $this->wrap = $pair[1];
            } elseif ($pair[0] == 'template') {
                $this->template = $pair[1];
            } elseif ($pair[0] == 'page') {
                $this->page = $pair[1];
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
        $db = MolajoFactory::getDbo();
        $query = $db->getQuery(true);

        $query->select('a.' . $db->nameQuote('extension_instance_id'));
        $query->select('a.' . $db->nameQuote('parameters'));
        $query->select('a.' . $db->nameQuote('metadata'));
        $query->from($db->nameQuote('#' . $this->source_table) . ' as a');
        $query->where('a.' . $db->nameQuote('id') . ' = ' . (int)$this->source_id);

        $db->setQuery($query->__toString());

        $results = $db->loadObjectList();
        if (count($results) > 0) {
            foreach ($results as $result) {
                $this->_setPageValues($result->parameters, $result->metadata);

                $parameters = new JRegistry;
                $parameters->loadString($result->parameters);
                $this->source_parameters = $parameters;

                $this->component_id = $result->extension_instance_id;
            }
        }
        //    echo '<pre>';var_dump($this->source_parameters);'</pre>';
        //    MolajoFactory::getApplication()->enqueueMessage($db->getErrorMsg(), 'error');
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
        $db = MolajoFactory::getDbo();
        $query = $db->getQuery(true);

        $query->select('a.' . $db->nameQuote('parameters'));
        $query->select('a.' . $db->nameQuote('metadata'));
        $query->from($db->nameQuote('#__content') . ' as a');
        $query->where('a.' . $db->nameQuote('id') . ' = ' . (int)$this->primary_category_id);

        $db->setQuery($query->__toString());

        $results = $db->loadObjectList();
        if (count($results) > 0) {
            foreach ($results as $result) {
                $this->_setPageValues($result->parameters, $result->metadata);
            }
        }

        //    MolajoFactory::getApplication()->enqueueMessage($db->getErrorMsg(), 'error');
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
        $db = MolajoFactory::getDbo();
        $query = $db->getQuery(true);

        $query->select('a.' . $db->nameQuote('parameters'));
        $query->select('a.' . $db->nameQuote('metadata'));
        $query->from($db->nameQuote('#__extension_instances') . ' as a');
        $query->where('a.' . $db->nameQuote('id') . ' = ' . (int)$this->component_id);

        $db->setQuery($query->__toString());

        $results = $db->loadObjectList();
        if (count($results) > 0) {
            foreach ($results as $result) {
                $this->_setPageValues($result->parameters, $result->metadata);
                $parameters = new JRegistry;
                $parameters->loadString($result->parameters);
                $this->component_parameters = $parameters;
            }
        }
        //    MolajoFactory::getApplication()->enqueueMessage($db->getErrorMsg(), 'error');
        //    return false;
    }

    /**
     * _setPageValues
     *
     * Set the values needed to generate the page (template, page, layout, wrap, and various metadata)
     *
     * @param null $sourceParameters
     * @param null $sourceMetadata
     */
    protected function _setPageValues($sourceParameters = null, $sourceMetadata = null)
    {
        $parameters = new JRegistry;
        $parameters->loadString($sourceParameters);

        if ($this->template == '' || $this->template == null) {
            $this->template = $parameters->get('template', '');
        }
        if ($this->page == '' || $this->page == null) {
            $this->page = $parameters->get('page', '');
        }
        if ($this->layout == '' || $this->layout == null) {
            $this->layout = $parameters->get('layout', '');
        }
        if ($this->wrap == '' || $this->wrap == null) {
            $this->wrap = $parameters->get('wrap', '');
        }

        $metadata = new JRegistry;
        $metadata->loadString($sourceMetadata);

        if ($this->meta_title == '' || $this->meta_title == null) {
            $this->meta_title = $metadata->get('meta_title', '');
        }
        if ($this->meta_description == '' || $this->meta_description == null) {
            $this->meta_description = $metadata->get('meta_description', '');
        }
        if ($this->meta_keywords == '' || $this->meta_keywords == null) {
            $this->meta_keywords = $metadata->get('meta_keywords', '');
        }
        if ($this->meta_author == '' || $this->meta_author == null) {
            $this->meta_author = $metadata->get('meta_author', '');
        }
        if ($this->meta_content_rights == '' || $this->meta_content_rights == null) {
            $this->meta_content_rights = $metadata->get('meta_content_rights', '');
        }
        if ($this->meta_robots == '' || $this->meta_robots == null) {
            $this->meta_robots = $metadata->get('meta_robots', '');
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
        $db = MolajoFactory::getDbo();
        $query = $db->getQuery(true);

        if ((int)$asset_id == MolajoFactory::getApplication()->get('home_asset_id', 0)) {
            return '';
        }

        if (MolajoFactory::getApplication()->get('sef', 1) == 0) {
            $query->select('a.' . $db->nameQuote('sef_request'));
        } else {
            $query->select('a.' . $db->nameQuote('request'));
        }

        $query->from($db->nameQuote('#__assets') . ' as a');
        $query->where('a.' . $db->nameQuote('id') . ' = ' . (int)$asset_id);

        $db->setQuery($query->__toString());

        return $db->loadResult();

        //    MolajoFactory::getApplication()->enqueueMessage($db->getErrorMsg(), 'error');
        //    return false;
    }

    /**
     *  _setMetaData
     *
     * Establish the meta data for this web page
     */
    protected function _setMetaData()
    {
        MolajoFactory::getApplication()->setTitle($this->meta_title);
        MolajoFactory::getApplication()->setDescription($this->meta_description);
        MolajoFactory::getApplication()->setMetaData('meta_keywords', $this->meta_keywords);
        MolajoFactory::getApplication()->setMetaData('meta_author', $this->meta_author);
        MolajoFactory::getApplication()->setMetaData('meta_content_rights', $this->meta_content_rights);
        MolajoFactory::getApplication()->setMetaData('meta_robots', $this->meta_robots);
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
        MolajoPlugin::importPlugin('system');
        MolajoFactory::getApplication()->triggerEvent('onAfterRoute');
    }

    /**
     * _loadUser
     *
     * Load User
     *
     * @since   1.0
     */
    private function _loadUser()
    {
        $this->user = MolajoFactory::getUser();
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
        if (in_array($this->view_group_id, $this->user->view_groups)) {
            return true;
        } else {
            return false;
        }
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
        $documentTypeClass = 'Molajo' . ucfirst($this->format) . 'Format';
        $results = new $documentTypeClass ($this);
    }
}
