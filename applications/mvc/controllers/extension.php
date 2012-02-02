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
class MolajoControllerExtension
{
    /**
     * $request
     *
     * Values used to generate the primary content on the page
     *
     * @var    object
     * @since  1.0
     */
    public $request;

    /**
     * $mvc
     *
     * Values to use for generating content for this specific request
     *
     * @var    object
     * @since  1.0
     */
    public $mvc;

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
     * @param    array   $request
     * @since    1.0
     */
    public function __construct($mvc, $request, $parameters)
    {
        if (is_object($mvc)) {
            $this->mvc = $mvc;
        } else {
            //error
        }
        if (is_object($request)) {
            $this->request = $request;
        } else {
            //error
        }
        $this->parameters = new JRegistry;
        $this->parameters->loadJSON($parameters);

        // todo: amy look at redirect
//        $this->redirectClass = new MolajoControllerRedirect($this->mvc);

        /** load table */
        if ($this->mvc->get('mvc_task') == 'display'
            || $this->mvc->get('mvc_task') == 'add'
            || $this->mvc->get('mvc_task') == 'login'
            || $this->mvc->get('mvc_model') == 'static'
        ) {
            $this->isNew = false;

        } else {
            $this->table = $this->model->getModel();
            $this->table->reset();
            $this->table->load((int)$this->mvc->get('mvc_id'));

            if ($this->mvc->get('mvc_id') == 0) {
                $this->isNew = true;
                $this->existing_status = 0;
            } else {
                $this->isNew = false;
                $this->existing_status = $this->table->state;
            }
        }

        /** dispatch events
        if ($this->dispatcher
        || $this->mvc->get('mvc_plugin_type') == ''
        ) {
        } else {
        $this->dispatcher = JDispatcher::getInstance();
        MolajoPluginHelper::importPlugin($this->mvc->get('mvc_plugin_type'));
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
        if ($this->mvc->get('mvc_id') == 0) {
            return true;
        }

        if (property_exists($this->table, 'checked_out')) {
        } else {
            return true;
        }

        $results = $this->model->checkin($this->mvc->get('mvc_id'));

        if ($results === false) {
            $this->redirectClass->setRedirectMessage(MolajoTextHelper::_('MOLAJO_CHECK_IN_FAILED'));
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
        if ($this->mvc->get('mvc_id') == 0) {
            return true;
        }

        if (property_exists($this->table, 'checked_out')) {
        } else {
            return true;
        }

        if ($this->table->checked_out == MolajoController::getUser()->get('id')) {
        } else {
            $this->redirectClass->setRedirectMessage(MolajoTextHelper::_('MOLAJO_ERROR_DATA_NOT_CHECKED_OUT_BY_USER') . ' ' . $this->getTask());
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
        if ($this->mvc->get('mvc_id') == 0) {
            return true;
        }

        if (property_exists($this->table, 'checked_out')) {
        } else {
            return true;
        }

        $results = $this->model->checkout($this->mvc->get('mvc_id'));
        if ($results === false) {
            $this->redirectClass->setRedirectMessage(MolajoTextHelper::_('MOLAJO_ERROR_CHECKOUT_FAILED'));
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
        if ((int)$this->mvc->get('mvc_id') == 0) {
            return true;
        }

        /** versions deleted with delete **/
        if ($this->mvc->get('mvc_task') == 'delete'
            && $this->parameters->def('retain_versions_after_delete', 1) == 0
        ) {
            return true;
        }

        /** create version **/
        $versionKey = $this->model->createVersion($this->mvc->get('mvc_id'));

        /** error processing **/
        if ($versionKey === false) {
            $this->redirectClass->setRedirectMessage(MolajoTextHelper::_('MOLAJO_ERROR_VERSION_SAVE_FAILED'));
            $this->redirectClass->setRedirectMessageType('error');
            return false;
        }

        /** Trigger_Event: onContentCreateVersion
        $results = $this->dispatcher->trigger('onContentCreateVersion', array($context, $this->mvc->get('mvc_id'), $versionKey));
        if (count($results) && in_array(false, $results, true)) {
        $this->redirectClass->setRedirectMessage(MolajoTextHelper::_('MOLAJO_ERROR_ON_CONTENT_CREATE_VERSION_EVENT_FAILED'));
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
        if ((int)$this->mvc->get('mvc_id') == 0) {
            return true;
        }

        /** versions deleted with delete **/
        if ($this->mvc->get('mvc_task') == 'delete'
            && $this->parameters->def('retain_versions_after_delete', 1) == 0
        ) {
            $maintainVersions = 0;
        } else {
            /** retrieve versions desired **/
            $maintainVersions = $this->parameters->def('maintain_version_count', 5);
        }

        /** delete extra versions **/
        $results = $this->model->maintainVersionCount($this->mvc->get('mvc_id'), $maintainVersions);

        /** version delete failed **/
        if ($results === false) {
            $this->redirectClass->setRedirectMessage(MolajoTextHelper::_('MOLAJO_ERROR_VERSION_DELETE_VERSIONS_FAILED') . ' ' . $this->model->getError());
            $this->redirectClass->setRedirectMessageType('warning');
            return false;
        }

        /** Trigger_Event: onContentMaintainVersions
        return $this->dispatcher->trigger('onContentMaintainVersions', array($context, $this->mvc->get('mvc_id'), $maintainVersions));
         **/
    }

    /**
     * cleanCache
     *
     * @return    void
     */
    public function cleanCache()
    {
//        $cache = MolajoController::getCache($this->mvc->get('extension_instance_name'));
//        $cache->clean();
    }
}
