<?php
/**
 * @package     Molajo
 * @subpackage  Controller
 * @copyright   Copyright (C) 2012 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die;

/**
 * Extension
 *
 * @package      Molajo
 * @subpackage   Controller
 * @since        1.0
 */
class MolajoController
{
    /**
     * $task
     *
     * Request array for rendering content
     *
     * @var    object
     * @since  1.0
     */
    public $task;

    /**
     * $parameters
     *
     * @var    object
     * @since  1.0
     */
    public $parameters = array();

    /**
     * $table
     *
     * @var    object
     * @since  1.0
     */
    public $table = null;

    /**
     * $model
     *
     * @var    object
     * @since  1.0
     */
    public $model = null;

    /**
     * $isNew
     *
     * @var    object
     * @since  1.0
     */
    public $isNew = null;

    /**
     * $existing_status
     *
     * @var    object
     * @since  1.0
     */
    public $existing_status = null;

    /**
     * $redirectClass
     *
     * @var    string
     * @since  1.0
     */
    public $redirectClass = null;

    /**
     * __construct
     *
     * Constructor.
     *
     * @param  array  task request
     * @param  array  parameters
     *
     * @since  1.0
     */
    public function __construct($task, $parameters)
    {
        if (is_object($task)) {
            $this->task = $task;
        } else {
            //error
        }

        $this->parameters = new Registry;
        $this->parameters->loadJSON($parameters);

        // todo: amy look at redirect
//        $this->redirectClass = new MolajoRedirectController($this->task);

        /** load table */
        if ($this->task->get('task') == 'display'
            || $this->task->get('task') == 'edit'
            || $this->task->get('task') == 'login'
        ) {
            $this->isNew = false;

        } else {

            $this->table = $this->model->getModel();
            $this->table->reset();
            $this->table->load((int)$this->task->get('id'));

            if ($this->task->get('id') == 0) {
                $this->isNew = true;
                $this->existing_status = 0;
            } else {
                $this->isNew = false;
                $this->existing_status = $this->table->state;
            }
        }

        /** dispatch events
        if ($this->dispatcher
        || $this->task->get('task_plugin_type') == ''
        ) {
        } else {
        $this->dispatcher = JDispatcher::getInstance();
        MolajoPluginHelper::importPlugin($this->task->get('task_plugin_type'));
        }
         */

        /** check authorisation **/
        if (MOLAJO_APPLICATION == 'installation') {
        } else {
            $results = $this->checkTaskAuthorisation();
            if ($results === false) {
                return false;
            }
        }

        /** set redirects **/
// $this->redirectClass->initialise();

        /** success **/
        return true;
    }

    /**
     * checkTaskAuthorisation
     *
     * Method to verify the user's authorisation to perform a specific task
     *
     * @return bool
     */
    public function checkTaskAuthorisation()
    {
        return true;
    }

    /**
     * checkinItem
     *
     * Used to check in item if it is already checked out
     *
     * @return bool
     */
    public function checkinItem()
    {
        if ($this->task->get('id') == 0) {
            return true;
        }

        if (property_exists($this->table, 'checked_out')) {
        } else {
            return true;
        }

        $results = $this->model->checkin($this->task->get('id'));

        if ($results === false) {
            $this->redirectClass->setRedirectMessage(TextServices::_('MOLAJO_CHECK_IN_FAILED'));
            $this->redirectClass->setRedirectMessageType('warning');
            return false;
        }

        return true;
    }

    /**
     * verifyCheckout
     *
     * method to verify that the current user is recorded in the checked_out column of the item
     *
     * @return  boolean
     * @since   1.0
     */
    public function verifyCheckout()
    {
        if ($this->task->get('id') == 0) {
            return true;
        }

        if (property_exists($this->table, 'checked_out')) {
        } else {
            return true;
        }

        if ($this->table->checked_out
            == Molajo::Application()->get('User', '', 'services')->get('id')) {
        } else {
            $this->redirectClass->setRedirectMessage(
                TextServices::_('MOLAJO_ERROR_DATA_NOT_CHECKED_OUT_BY_USER')
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
        if ($this->task->get('id') == 0) {
            return true;
        }

        if (property_exists($this->table, 'checked_out')) {
        } else {
            return true;
        }

        $results = $this->model->checkout($this->task->get('id'));
        if ($results === false) {
            $this->redirectClass->setRedirectMessage(TextServices::_('MOLAJO_ERROR_CHECKOUT_FAILED'));
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
    public function createVersion($context)
    {
        if ($this->parameters->def('version_management', 1) == 1) {
        } else {
            return true;
        }

        /** create **/
        if ((int)$this->task->get('id') == 0) {
            return true;
        }

        /** versions deleted with delete **/
        if ($this->task->get('task') == 'delete'
            && $this->parameters->def('retain_versions_after_delete', 1) == 0
        ) {
            return true;
        }

        /** create version **/
        $versionKey = $this->model->createVersion($this->task->get('id'));

        /** error processing **/
        if ($versionKey === false) {
            $this->redirectClass->setRedirectMessage(TextServices::_('MOLAJO_ERROR_VERSION_SAVE_FAILED'));
            $this->redirectClass->setRedirectMessageType('error');
            return false;
        }

        /** Trigger_Event: onContentCreateVersion
        $results = $this->dispatcher->trigger('onContentCreateVersion', array($context, $this->task->get('id'), $versionKey));
        if (count($results) && in_array(false, $results, true)) {
        $this->redirectClass->setRedirectMessage(TextServices::_('MOLAJO_ERROR_ON_CONTENT_CREATE_VERSION_EVENT_FAILED'));
        $this->redirectClass->setRedirectMessageType('error');
        return false;
        }
         **/
        return true;
    }

    /**
     * maintainVersionCount
     *
     * Molajo_Note: All Components have version management save and restore processes as
     * an automatic option
     *
     * @param  $context
     * @return boolean
     */
    public function maintainVersionCount($context)
    {
        if ($this->parameters->def('version_management', 1) == 1) {
        } else {
            return true;
        }

        /** no versions to delete for create **/
        if ((int)$this->task->get('id') == 0) {
            return true;
        }

        /** versions deleted with delete **/
        if ($this->task->get('task') == 'delete'
            && $this->parameters->def('retain_versions_after_delete', 1) == 0
        ) {
            $maintainVersions = 0;
        } else {
            /** retrieve versions desired **/
            $maintainVersions = $this->parameters->def('maintain_version_count', 5);
        }

        /** delete extra versions **/
        $results = $this->model->maintainVersionCount(
            $this->task->get('id'),
            $maintainVersions
        );

        /** version delete failed **/
        if ($results === false) {
            $this->redirectClass->setRedirectMessage(
                TextServices::_('MOLAJO_ERROR_VERSION_DELETE_VERSIONS_FAILED') . ' ' .
                    $this->model->getError()
            );
            $this->redirectClass->setRedirectMessageType('warning');
            return false;
        }

        /** Trigger_Event: onContentMaintainVersions
        return $this->dispatcher->trigger('onContentMaintainVersions', array($context, $this->task->get('id'), $maintainVersions));
         **/
    }

    /**
     * cleanCache
     *
     * @return    void
     */
    public function cleanCache()
    {
//        $cache = Molajo::getCache($this->task->get('extension_instance_name'));
//        $cache->clean();
    }
}
