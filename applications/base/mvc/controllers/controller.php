<?php
/**
 * @package     Molajo
 * @subpackage  Controller
 * @copyright   Copyright (C) 2012 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die;

/**
 * Primary Controller
 *
 * @package      Molajo
 * @subpackage   Controller
 * @since        1.0
 */
class MolajoController
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
     * $rowset
     *
     * @var    object
     * @since  1.0
     */
    protected $rowset;

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
        $this->task_request = new Registry;
        $this->task_request->loadString($task_request);

        $this->parameters = new Registry;
        $this->parameters->loadString($parameters);

        // todo: amy look at redirect
//        $this->redirectClass = new MolajoRedirectController($this->task);

        /** model */
        $mc = (string)$this->get('model');

        $this->model = new $mc();
        $this->model->task_request = $this->task_request;
        $this->model->parameters = $this->parameters;

//        $this->model->load((int)$this->get('id'));

        /** dispatch events
        if ($this->dispatcher
        || $this->get('task_plugin_type') == ''
        ) {
        } else {
        $this->dispatcher = Services::Dispatcher();
        MolajoPluginHelper::importPlugin($this->get('task_plugin_type'));
        }
         */

        /** check authorisation **/
        if (MOLAJO_APPLICATION == 'installation') {
        } else {
//            $results = $this->checkTaskAuthorisation();
//            if ($results === false) {
//                return false;
//            }
        }

        /** set redirects **/
// $this->redirectClass->initialise();

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
        //echo 'Set '.$key.' '.$value.'<br />';
        return $this->task_request->set($key, $value);
    }

    /**
     * checkTaskAuthorisation
     *
     * Method to verify user's authorisation to perform a specific task
     *
     * @return bool
     */
    public function checkTaskAuthorisation()
    {
        echo 'in checkTaskAuthorisation in Controller AssetID: '.$this->get('asset_id').' see it?';
        Services::Access()
            ->authoriseTask(
                $this->get('task'),
                $this->get('asset_id')
            );
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
            $this->redirectClass->setRedirectMessage(Services::Language()->_('MOLAJO_CHECK_IN_FAILED'));
            $this->redirectClass->setRedirectMessageType('warning');
            return false;
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

        if ($this->model->checked_out
            == Services::User()->get('id')) {

        } else {
            $this->redirectClass->setRedirectMessage(
                Services::Language()->_('MOLAJO_ERROR_DATA_NOT_CHECKED_OUT_BY_USER')
                    . ' '
                    . $this->getTask()
            );
            $this->redirectClass->setRedirectMessageType('warning');
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
            $this->redirectClass->setRedirectMessage
            (Services::Language()->_('MOLAJO_ERROR_CHECKOUT_FAILED'));
            $this->redirectClass->setRedirectMessageType('error');
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
        if ($this->parameters->def('version_management', 1) == 1) {
        } else {
            return true;
        }

        /** create **/
        if ((int)$this->get('id') == 0) {
            return true;
        }

        /** versions deleted with delete **/
        if ($this->get('task') == 'delete'
            && $this->parameters->def('retain_versions_after_delete', 1) == 0
        ) {
            return true;
        }

        /** create version **/
        $versionKey = $this->model->createVersion($this->get('id'));

        /** error processing **/
        if ($versionKey === false) {
            $this->redirectClass->setRedirectMessage(
                Services::Language()->_('MOLAJO_ERROR_VERSION_SAVE_FAILED')
            );
            $this->redirectClass->setRedirectMessageType('error');
            return false;
        }

        /** Trigger_Event: onContentCreateVersion
        $results = $this->dispatcher->trigger('onContentCreateVersion', array($context, $this->get('id'), $versionKey));
        if (count($results) && in_array(false, $results, true)) {
        $this->redirectClass->setRedirectMessage(Services::Language()->_('MOLAJO_ERROR_ON_CONTENT_CREATE_VERSION_EVENT_FAILED'));
        $this->redirectClass->setRedirectMessageType('error');
        return false;
        }
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
        if ($this->parameters->def('version_management', 1) == 1) {
        } else {
            return true;
        }

        /** no versions to delete for create **/
        if ((int)$this->get('id') == 0) {
            return true;
        }

        /** versions deleted with delete **/
        if ($this->get('task') == 'delete'
            && $this->parameters->def('retain_versions_after_delete', 1) == 0
        ) {
            $maintainVersions = 0;
        } else {
            /** retrieve versions desired **/
            $maintainVersions = $this->parameters->def('maintain_version_count', 5);
        }

        /** delete extra versions **/
        $results = $this->model
            ->maintainVersionCount(
            $this->get('id'),
            $maintainVersions
        );

        /** version delete failed **/
        if ($results === false) {
            $this->redirectClass->setRedirectMessage(
                Services::Language()->_('MOLAJO_ERROR_VERSION_DELETE_VERSIONS_FAILED') . ' ' .
                    $this->model->getError()
            );
            $this->redirectClass->setRedirectMessageType('warning');
            return false;
        }

        /** Trigger_Event: onContentMaintainVersions
        return $this->dispatcher->trigger('onContentMaintainVersions',
         * array($context, $this->get('id'), $maintainVersions));
         **/
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
