<?php
/**
 * Abstract Plugin
 *
 * @package    Molajo
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2014 Amy Stephen. All rights reserved.
 */
namespace Molajo\Plugins;

use CommonApi\Exception\RuntimeException;
use Exception;
use stdClass;

/**
 * Abstract Plugin - Overrides Abstract Plugin in Event Package
 *
 * @author     Amy Stephen
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2014 Amy Stephen. All rights reserved.
 * @since      1.0.0
 */
abstract class AbstractPlugin
{
    /**
     * Plugin Name
     *
     * @var    string
     * @since  1.0.0
     */
    protected $plugin_name = null;

    /**
     * Event Name
     *
     * @var    string
     * @since  1.0.0
     */
    protected $event_name = null;

    /**
     * Resource
     *
     * @var    object
     * @since  1.0.0
     */
    protected $resource = null;

    /**
     * User
     *
     * @var    object  CommonApi\User\UserInterface
     * @since  1.0.0
     */
    protected $user = null;

    /**
     * Fieldhandler
     *
     * @var    object  CommonApi\Model\FieldhandlerInterface
     * @since  1.0.0
     */
    protected $fieldhandler = null;

    /**
     * Date Controller
     *
     * @var    object  CommonApi\Controller\DateInterface
     * @since  1.0.0
     */
    protected $date_controller = null;

    /**
     * Url Controller
     *
     * @var    object  CommonApi\Controller\UrlInterface
     * @since  1.0.0
     */
    protected $url_controller = null;

    /**
     * Language Instance
     *
     * @var    object CommonApi\Language\LanguageInterface
     * @since  1.0.0
     */
    protected $language_controller;

    /**
     * Authorisation Controller
     *
     * @var    object  CommonApi\Authorisation\AuthorisationInterface
     * @since  1.0.0
     */
    protected $authorisation_controller;

    /**
     * Runtime Data
     *
     * @var    object
     * @since  1.0.0
     */
    protected $runtime_data = null;

    /**
     * Plugin Data
     *
     * @var    object
     * @since  1.0.0
     */
    protected $plugin_data = null;

    /**
     * Parameters
     *
     * @var    object
     * @since  1.0.0
     */
    protected $parameters = null;

    /**
     * Query
     *
     * @var    object
     * @since  1.0.0
     */
    protected $query = null;

    /**
     * Model Registry
     *
     * @var    object
     * @since  1.0.0
     */
    protected $model_registry = null;

    /**
     * Query Results
     *
     * @var    array
     * @since  1.0.0
     */
    protected $query_results = array();

    /**
     * Query Results
     *
     * @var    object
     * @since  1.0.0
     */
    protected $row = null;

    /**
     * View Rendered Output
     *
     * @var    string
     * @since  1.0.0
     */
    protected $rendered_view = null;

    /**
     * Page Rendered Output
     *
     * @var    string
     * @since  1.0.0
     */
    protected $rendered_page = null;

    /**
     * Constructor
     *
     * @param  string $plugin_name
     * @param  string $event_name
     * @param  array  $data
     *
     * @since  1.0.0
     */
    public function __construct(
        $plugin_name = '',
        $event_name = '',
        array $data = array()
    ) {
        $this->plugin_name = $plugin_name;
        $this->event_name  = $event_name;

        foreach ($data as $key => $value) {
            $this->$key = $value;
        }
    }

    /**
     * Get the current value (or default) of the specified property
     *
     * @param   string $key
     *
     * @return  string
     * @since   1.0.0
     */
    public function get($key = null)
    {
        if (isset($this->$key)) {
            return $this->$key;
        }

        $results = array();

        $results['runtime_data']   = $this->runtime_data;
        $results['plugin_data']    = $this->plugin_data;
        $results['parameters']     = $this->parameters;
        $results['query']          = $this->query;
        $results['model_registry'] = $this->model_registry;
        $results['query_results']  = $this->query_results;
        $results['row']            = $this->row;
        $results['rendered_page']  = $this->rendered_page;
        $results['rendered_view']  = $this->rendered_view;

        return $results;
    }

    /**
     * Filter Input
     *
     * @param   string $key
     * @param   mixed  $value
     * @param   string $filter
     * @param   array  $filter_options
     *
     * @return  mixed
     * @since   1.0.0
     * @throws  \CommonApi\Exception\RuntimeException
     */
    protected function filter($key, $value = null, $filter = 'String', $filter_options = array())
    {
        $results = $this->fieldhandler->sanitize($key, $value, $filter, $filter_options);

        return $results->getFieldValue();
    }

    /**
     * Verify object or create object
     *
     * @param   string $name
     *
     * @return  $this
     * @since   1.0.0
     */
    protected function setObject($name)
    {
        if (is_object($this->$name)) {
        } else {
            $this->$name = new stdClass();
        }

        return $this;
    }

    /**
     * Verify array or create array
     *
     * @param   string $name
     *
     * @return  $this
     * @since   1.0.0
     */
    protected function setArray($name)
    {
        if (is_array($this->$name)) {
        } else {
            $this->$name = array();
        }

        return $this;
    }

    /**
     * Verify array or create array
     *
     * @param   string $name
     * @param string $member
     *
     * @return  $this
     * @since   1.0.0
     */
    protected function setArrayMember($name, $member)
    {
        if (is_array($this->$name[$member])) {
        } else {
            $this->$name[$member] = array();
        }

        return $this;
    }


    /**
     * Level dots
     *
     * @param   integer  $lvl
     * @param   string  $name
     *
     * @return  string
     * @since   1.0.0
     */
    protected function setLevelDots($lvl, $name)
    {
        if ($lvl > 0) {
            for ($i = 0; $i < $lvl; $i ++) {
                $name = ' ..' . $name;
            }
        }

        return $name;
    }

    /**
     * Create Task URL
     *
     * @param   string  $sef
     * @param   string  $url
     * @param   string  $action
     *
     * @return  string
     * @since   1.0.0
     */
    protected function setTaskURL($sef, $url, $action)
    {
        if ($sef === 1) {
            return $url . '/task/' . $action;
        }

        return $url . '&task=' . $action;
    }

    /**
     * Run Query
     *
     * @param   object  $controller
     *
     * @return  mixed
     * @since   1.0.0
     * @throws  \CommonApi\Exception\RuntimeException
     */
    protected function runQuery($controller)
    {
        try {
            return $controller->getData();

        } catch (Exception $e) {
            throw new RuntimeException($e->getMessage());
        }
    }
}
