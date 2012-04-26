<?php
/**
 * @package   Molajo
 * @copyright   2012 Amy Stephen. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */
namespace Molajo\MVC\Controller;

use Molajo\Service;

defined('MOLAJO') or die;

/**
 * Controller
 *
 * @package      Molajo
 * @subpackage   Controller
 * @since        1.0
 */
class Controller
{
    /**
     * Request array for current Task Request
     *
     * @var    object
     * @since  1.0
     */
    protected $task_request;

    /**
     * $parameters
     *
     * @var    object
     * @since  1.0
     */
    protected $parameters;

    /**
     * $model
     *
     * @var    object
     * @since  1.0
     */
    protected $model;

    /**
     * $resultset
     *
     * @var    object
     * @since  1.0
     */
    protected $resultset;

    /**
     * $pagination
     *
     * @var    object
     * @since  1.0
     */
    protected $pagination;

    /**
     * $model_state
     *
     * @var    object
     * @since  1.0
     */
    protected $model_state;

    /**
     * $redirectClass
     *
     * @var    string
     * @since  1.0
     */
    public $redirectClass;

    /**
     * __construct
     *
     * Constructor.
     *
     * @param  array  $task_request
     * @param  array  $parameters
     *
     * @since  1.0
     */
    public function __construct($task_request, $parameters)
    {
        $this->task_request = Service::Registry()->initialise();
        $this->task_request->loadString($task_request);

        $this->parameters = Service::Registry()->initialise();
        $this->parameters->loadString($parameters);

        // todo: amy look at redirect

        /** model */
        $mc = (string)$this->get('model');

        $this->model = new $mc();
        $this->model->task_request = $this->task_request;
        $this->model->parameters = $this->parameters;
        $this->model->id = $this->task_request->get('id');

        /** success **/
        return true;
    }

    /**
     * display
     *
     * Display task is used to render view output
     *
     * @return  string  Rendered output
     * @since   1.0
     */
    public function add()
    {
        return $this->display();
    }

    public function edit()
    {
        return $this->display();
    }

    public function display()
    {
    }

    /**
     * get
     *
     * Returns a property of the Task Request object
     * or the default value if the property is not set.
     *
     * @param   string  $key
     * @param   mixed   $default
     *
     * @since   1.0
     */
    public function get($key, $default = null)
    {
        return $this->task_request->get($key, $default);
    }

    /**
     * set
     *
     * Modifies a property of the Task Request object,
     * creating it if it does not already exist.
     *
     * @param   string  $key
     * @param   mixed   $value
     *
     * @since   1.0
     */
    public function set($key, $value = null)
    {
        return $this->task_request->set($key, $value);
    }

    /**
     * checkinItem
     *
     * Method to check in an item after processing
     *
     * @return bool
     */
    public function checkinItem()
    {
        if ($this->get('id') == 0) {
            return true;
        }

        if (property_exists($this->model, 'checked_out')) {
        } else {
            return true;
        }

        $results = $this->model->checkin($this->get('id'));

        if ($results === false) {
            // redirect
        }

        return true;
    }

    /**
     * verifyCheckout
     *
     * Checks that the current user is the checked_out user for item
     *
     * @return  boolean
     * @since   1.0
     */
    public function verifyCheckout()
    {
        if ($this->get('id') == 0) {
            return true;
        }

        if (property_exists($this->model, 'checked_out')) {
        } else {
            return true;
        }
// or super admin
        if ($this->model->checked_out
            == Service::Registry()->get('User', 'id')
        ) {

        } else {
            // redirect error
            return false;
        }

        return true;
    }

    /**
     * checkoutItem
     *
     * method to set the checkout_time and checked_out values of the item
     *
     * @return    boolean
     * @since    1.0
     */
    public function checkoutItem()
    {
        if ($this->get('id') == 0) {
            return true;
        }

        if (property_exists($this->model, 'checked_out')) {
        } else {
            return true;
        }

        $results = $this->model->checkout($this->get('id'));
        if ($results === false) {
            // redirect error
            return false;
        }
        return true;
    }

    /**
     * createVersion
     *
     * Automatic version management save and restore processes for components
     *
     * @return  boolean
     * @since   1.0
     */
    public function createVersion()
    {
        if ($this->parameters->get('version_management', 1) == 1) {
        } else {
            return true;
        }

        /** create **/
        if ((int)$this->get('id') == 0) {
            return true;
        }

        /** versions deleted with delete **/
        if ($this->get('task') == 'delete'
            && $this->parameters->get('retain_versions_after_delete', 1) == 0
        ) {
            return true;
        }

        /** create version **/
        $versionKey = $this->model->createVersion($this->get('id'));

        /** error processing **/
        if ($versionKey === false) {
            // redirect error
            return false;
        }

        /** Trigger_Event: onContentCreateVersion
         **/
        return true;
    }

    /**
     * maintainVersionCount
     *
     * Prune version history, if necessary
     *
     * @return boolean
     */
    public function maintainVersionCount()
    {
        if ($this->parameters->get('version_management', 1) == 1) {
        } else {
            return true;
        }

        /** no versions to delete for create **/
        if ((int)$this->get('id') == 0) {
            return true;
        }

        /** versions deleted with delete **/
        if ($this->get('task') == 'delete'
            && $this->parameters->get('retain_versions_after_delete', 1) == 0
        ) {
            $maintainVersions = 0;
        } else {
            /** retrieve versions desired **/
            $maintainVersions = $this->parameters->get('maintain_version_count', 5);
        }

        /** delete extra versions **/
        $results = $this->model
            ->maintainVersionCount($this->get('id'), $maintainVersions);

        /** version delete failed **/
        if ($results === false) {
            // redirect false
            return false;
        }

        /** Trigger_Event: onContentMaintainVersions
         **/
        return true;
    }

    /**
     * cleanCache
     *
     * @return    void
     */
    public function cleanCache()
    {
//        $cache = Molajo::getCache($this->get('extension_instance_name'));
//        $cache->clean();
    }
}
