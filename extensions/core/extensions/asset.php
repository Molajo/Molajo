<?php
/**
 * @package     Molajo
 * @subpackage  Asset
 * @copyright   Copyright (C) 2012 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die;

/**
 * Molajo Asset Class
 *
 * Base class
 */
class MolajoAsset
{
    /**
     *  Request formatted for query
     *
     * @var string
     * @since 1.0
     */
    public $query_request = null;

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
     *  Format
     *
     * @var string
     * @since 1.0
     */
    public $format = null;

    /**
     *  Id
     *
     * @var integer
     * @since 1.0
     */
    public $id = null;

    /**
     *  Wrap
     *
     * @var string
     * @since 1.0
     */
    public $wrap = null;

    /**
     *  Template
     *
     * @var integer
     * @since 1.0
     */
    public $template_name = null;

    /**
     *  Template Page
     *
     * @var string
     * @since 1.0
     */
    public $template_page = null;

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
     *  Source Metadata
     *
     * @var integer
     * @since 1.0
     */
    public $source_metadata = null;

    /**
     *  Component
     *
     * @var integer
     * @since 1.0
     */
    public $component_id = null;

    /**
     *  Component Parameters
     *
     * @var integer
     * @since 1.0
     */
    public $component_parameters = null;

    /**
     *  Component Metadata
     *
     * @var integer
     * @since 1.0
     */
    public $component_metadata = null;

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
     *  Primary Category Parameters
     *
     * @var integer
     * @since 1.0
     */
    public $primary_category_parameters = null;


    /**
     *  Primary Category Metadata
     *
     * @var integer
     * @since 1.0
     */
    public $primary_category_metadata = null;

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
        /** request specific URL */
        if ($request == null) {
        } else {
            $this->query_request = $request;
        }

        /** request specific asset */
        if ((int)$asset_id == 0) {
            $this->asset_id = 0;
        } else {
            $this->asset_id = $asset_id;
        }

        /** home */
        $this->home = MolajoFactory::getApplication()->get('application_home_asset_id', 0);

        /** retrieve request */
        $this->getRequest();

        /** use home asset id, if needed */
        if ($this->query_request == '' && $this->asset_id == 0) {
            $this->asset_id = $this->home;
        }

        /** get asset information */
        $this->getAsset();

        /** act on redirect_to_id */

        /** get redirect to if not logged on */

        /** retrieve metadata and other information */
        $this->getMetaData();

        /** check if authorised */

        /** redirect for not authorised */

        /** results */
        return $this->found;
    }

    /**
     * getRequest
     *
     * Get the current request and split it into Host, Folder, Query, and Path
     *
     * @param null $request
     * @return mixed
     */
    public function getRequest($request = null)
    {
        /** Application SEF Options */
        $sef = MolajoFactory::getApplication()->get('sef', 1);
        $sef_rewrite = MolajoFactory::getApplication()->get('sef_rewrite', 0);
        $sef_suffix = MolajoFactory::getApplication()->get('sef_suffix', 0);
        $unicodeslugs = MolajoFactory::getApplication()->get('unicodeslugs', 0);
        $force_ssl = MolajoFactory::getApplication()->get('force_ssl' . 0);

        /** Path ex. index.php?option=login or access/groups */
        $path = MOLAJO_PAGE_REQUEST;
        if (substr($path, 0, 10) == 'index.php/') {
            $path = substr($path, 10, 999);
        }
        /** duplicate content: could redirect on this */
        if ($path == 'index.php') {
            $path = '';
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
     * getAsset
     *
     * Function to retrieve asset information for the Request or Asset ID
     *
     * @return  boolean
     * @since   1.0
     */
    public function getAsset()
    {
        $db = MolajoFactory::getDbo();
        $query = $db->getQuery(true);

        $query->select('a.' . $db->nameQuote('id') . ' as asset_id');
        $query->select('a.' . $db->nameQuote('asset_type_id'));
        $query->select('a.' . $db->nameQuote('source_id'));
        $query->select('a.' . $db->nameQuote('primary_category_id'));
        $query->select('a.' . $db->nameQuote('template_name'));
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
            if (MolajoFactory::getApplication()->get('sef', 1) == 0) {
                $query->where('a.' . $db->nameQuote('sef_request') . ' = ' . $db->Quote($this->query_request));
            } else {
                $query->where('a.' . $db->nameQuote('request') . ' = ' . $db->Quote($this->query_request));
            }
        } else {
            $query->where('a.' . $db->nameQuote('id') . ' = ' . (int)$this->asset_id);
        }

        $db->setQuery($query->__toString());
        $results = $db->loadObjectList();

        //    MolajoFactory::getApplication()->enqueueMessage($db->getErrorMsg(), 'error');
        //    return false;

        if (count($results) > 0) {
            foreach ($results as $result) {

                if ($this->asset_id == MolajoFactory::getApplication()->get('application_home_asset_id')) {
                    $this->home = true;
                } else {
                    $this->home = false;
                }
                $this->option = $result->option;
                $this->template_name = $result->template_name;
                $this->template_page = $result->template_page;
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

                $this->found = true;
            }
        }
    }

    /**
     *  getMetaData
     *
     *  Retrieves data needed to generate the page
     *
     *  Priority Sequence:
     *      URL Request Parameters
     *      Asset Table Row
     *      Source Table Parameter and Metadata Values
     *      Primary Category ID Parameter and Metadata Values
     *      Menu Item Parameter Values
     *      Component Parameter and Metadata Values
     *      Application Configuration and Metadata Values
     *      System defaults
     */
    function getMetaData()
    {
        /** Priority 1: Request Override (not found) */
        $this->getRequestParameters();

        /** Priority 2: Asset (not found) */
        // already processed

        /** Priority 3: Source Table ID */
        $this->getSourceData();

        /** Priority 4: Menu Item */

        /** Priority 5: Primary List Category ID */
        if ((int)$this->primary_category_id == 0) {
        } else {
            $this->getPrimaryCategory();
        }

        /** Priority 6: Component */
        $this->getComponent();

        /** Priority 7: Application (static, items, item, edit) */
        $this->application_template_name = MolajoFactory::getApplication()->get('default_template_name');
        $this->application_template_page = MolajoFactory::getApplication()->get('default_template_page');
        $this->default_layout_items = MolajoFactory::getApplication()->get('default_layout_items');
        $this->default_wrap_items = MolajoFactory::getApplication()->get('default_wrap_items');

        /** Priority 8: System-defined */
        if ($this->template_name == null) {
            $this->template_name = 'system';
        }
        if ($this->template_page == null) {
            $this->template_page = 'default';
        }

        $db = MolajoFactory::getDbo();
        $query = $db->getQuery(true);
    }

    /**
     *  getRequestParameters
     *
     *  Retrieve Template and Template Page overrides from URL
     *  todo: amy add parameter to turn this off in the template manager
     */
    protected function getRequestParameters()
    {
        $input = JFactory::getApplication()->input;
        $this->template_name = $input->get('template', '', 'CMD');
        $this->template_page = $input->get('page', '', 'CMD');
    }

    /**
     * getSourceData
     *
     * Retrieve Parameters and MetaData for Source Detail Row
     *
     * @return  array
     * @since   1.0
     */
    public function getSourceData()
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
                $this->source_parameters = $result->parameters;
                $this->source_metadata = $result->metadata;
                $this->component_id = $result->extension_instance_id;
            }
        }

        //    MolajoFactory::getApplication()->enqueueMessage($db->getErrorMsg(), 'error');
        //    return false;
    }

    /**
     * getComponent
     *
     * Retrieve the Parameters and Meta Data for Component
     *
     * @return  array
     * @since   1.0
     */
    public function getComponent()
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
                $this->component_parameters = $result->parameters;
                $this->component_metadata = $result->metadata;
            }
        }
        //    MolajoFactory::getApplication()->enqueueMessage($db->getErrorMsg(), 'error');
        //    return false;
    }

    /**
     * getMenuItem
     *
     * Retrieve the Menu Item Parameters and Meta Data
     *
     * @return  array
     * @since   1.0
     */
    public function getPrimaryCategory()
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
                $this->primary_category_parameters = $result->parameters;
                $this->primary_category_metadata = $result->metadata;
            }
        }

        //    MolajoFactory::getApplication()->enqueueMessage($db->getErrorMsg(), 'error');
        //    return false;
    }
}