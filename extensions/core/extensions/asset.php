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
     *  Option
     *
     * @var string
     * @since 1.0
     */
    public $option = null;

    /**
     *  View
     *
     * @var string
     * @since 1.0
     */
    public $view = null;

    /**
     *  Task
     *
     * @var string
     * @since 1.0
     */
    public $task = null;

    /**
     *  Layout
     *
     * @var string
     * @since 1.0
     */
    public $layout = null;

    /**
     *  Template
     *
     * @var integer
     * @since 1.0
     */
    public $template_id = null;

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
     *  Found
     *
     * @var boolean
     * @since 1.0
     */
    public $found = false;

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
        /** specific request */
        if ($request == null) {
        } else {
            $this->query_request = $request;
        }

        /** specific asset */
        if ((int) $asset_id == 0) {
            $this->asset_id = 0;
        } else {
            $this->asset_id = $asset_id;
        }

        /** retrieve request */
        $this->getRequest();

        /** get home menu item asset id, if necessary */
        if ($this->query_request == '') {
            $this->getHomeMenu();
        }

        /** get asset information */
        if ($this->query_request == ''
            && $this->asset_id = 0) {
            $this->found = false;

        } else {
            $this->getAsset();
        }

        /** get request parameters */
        if ($this->found === true) {
            $this->getRequestParameters();
        }

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
    public function getRequest ($request = null)
    {
        /** Full ex. http://localhost/molajo/index.php/access/groups */
        $uri = JUri::getInstance();

        /** Host ex. http://localhost */
        $host = $uri->toString(array('scheme', 'user', 'pass', 'host', 'port'));

        /** Base */
        $base = JUri::base();

        /** Folder ex. molajo */
        $folder = rtrim(substr($base, strlen($host) + 1, 999), '/\\');

        /** Query ex. ?option=com_login */
        $query = $uri->toString(array('query', 'fragment'));

        /** Path ex. index.php?option=com_login or access/groups */
        $path = $uri->toString(array('path', 'query', 'fragment'));

        if ($path === '') {
        } else {
            $path = rtrim(substr($path, strlen($folder) + 2, 999), '/\\');
        }
        if (substr($path, 0, 10) == 'index.php/') {
            $path = substr($path, 10, 999);
        }

        /**
         *  Create DB lookup for Request based on SEF Options
         */
        $sef = MolajoFactory::getApplication()->get('sef', 1);
        $sef_rewrite = MolajoFactory::getApplication()->get('sef_rewrite', 0);
        $sef_suffix = MolajoFactory::getApplication()->get('sef_suffix', 0);
        $unicodeslugs = MolajoFactory::getApplication()->get('unicodeslugs', 0);
        $force_ssl = MolajoFactory::getApplication()->get('force_ssl'. 0);

        $this->query_request = '';

        if ($sef == 0) {
            $this->query_request .= 'index.php'.$query;
        } else {
            $this->query_request .= $path;
        }

        return;
    }

    /**
     * getHomeMenu
     *
     * Retrieve the Home Menu Item and related Asset ID
     *
     * @return  array
     * @since   11.1
     */
    public function getHomeMenu()
    {
        $db = MolajoFactory::getDbo();
        $query = $db->getQuery(true);

        $query->select('c.'.$db->nameQuote('id'));
        $query->from($db->nameQuote('#__applications').' as a');
        $query->from($db->nameQuote('#__content').' as b');
        $query->from($db->nameQuote('#__assets').' as c');

        $query->where('a.'.$db->nameQuote('home_menu_id').' = b.'.$db->nameQuote('id'));
        $query->where('b.'.$db->nameQuote('id').' = c.'.$db->nameQuote('source_id'));
        $query->where('b.'.$db->nameQuote('asset_type_id').' = b.'.$db->nameQuote('asset_type_id'));
        $query->where('a.'.$db->nameQuote('id').' = '.MOLAJO_APPLICATION_ID);

        $db->setQuery($query->__toString());

        $this->asset_id = $db->loadResult();

        //    MolajoFactory::getApplication()->enqueueMessage($db->getErrorMsg(), 'error');
        //    return false;
    }

    /**
     *  Function to retrieve asset information given the request
     *
     * @param   string   $uri
     *
     * @return  array
     * @since   11.1
     */
    public function getAsset()
    {
        $db = MolajoFactory::getDbo();
        $query = $db->getQuery(true);

        $query->select('a.'.$db->nameQuote('id').' as asset_id');
        $query->select('a.'.$db->nameQuote('asset_type_id'));
        $query->select('a.'.$db->nameQuote('source_id'));
        $query->select('a.'.$db->nameQuote('primary_category_id'));
        $query->select('a.'.$db->nameQuote('template_id'));
        $query->select('a.'.$db->nameQuote('template_page'));
        $query->select('a.'.$db->nameQuote('language'));
        $query->select('a.'.$db->nameQuote('translation_of_id'));
        $query->select('a.'.$db->nameQuote('redirect_to_id'));
        $query->select('a.'.$db->nameQuote('view_group_id'));
        $query->select('a.'.$db->nameQuote('primary_category_id'));
        $query->select('a.'.$db->nameQuote('sef_request'));
        $query->select('a.'.$db->nameQuote('request'));
        
        $query->select('b.'.$db->nameQuote('component_option').' as '.$db->nameQuote('option'));
        $query->select('b.'.$db->nameQuote('source_table'));

        $query->from($db->nameQuote('#__assets').' as a');
        $query->from($db->nameQuote('#__asset_types').' as b');

        $query->where('a.'.$db->nameQuote('asset_type_id').' = b.'.$db->nameQuote('id'));

        if ((int) $this->asset_id == 0) {
            if (MolajoFactory::getApplication()->get('sef', 1) == 0) {
                $query->where('a.'.$db->nameQuote('sef_request').' = ' . $db->Quote($this->query_request));
            } else {
                $query->where('a.'.$db->nameQuote('request').' = ' . $db->Quote($this->query_request));
            }
        } else {
            $query->where('a.'.$db->nameQuote('id').' = ' . (int) $this->asset_id);
        }

        $db->setQuery($query->__toString());
        $results = $db->loadObjectList();

        //    MolajoFactory::getApplication()->enqueueMessage($db->getErrorMsg(), 'error');
        //    return false;

        if (count($results) > 0) {
            foreach ($results as $result) {

                $this->option = $result->option;
                $this->template_id  = $result->template_id;
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

                $this->found = true;
            }
        }
    }

    /**
     *  Get Request values from JInput
     */
    function getRequestParameters ()
    {
        $input = JFactory::getApplication()->input;

        $this->view = $input->get('view', 'list', 'CMD');
        $this->task = $input->get('task', 'display', 'CMD');
        $this->layout = $input->get('layout', 'default', 'CMD');
    }
}