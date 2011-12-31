<?php
/**
 * @package     Molajo
 * @subpackage  Controller
 * @copyright   Copyright (C) 2012 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die;

/**
 * Extension Controller
 *
 * @package      Molajo
 * @subpackage   Controller
 * @since        1.0
 */
class MolajoControllerExtension
{
    /**
     * @var object $request
     *
     * @since 1.0
     */
    public $request = array();

    /**
     * @var object $parameters
     *
     * @since 1.0
     */
    public $parameters = array();

    /**
     * @var object $table
     *
     * @since 1.0
     */
    public $table = null;

    /**
     * @var object $model
     *
     * @since 1.0
     */
    public $model = null;

    /**
     * @var object $isNew
     *
     * @since 1.0
     */
    public $isNew = null;

    /**
     * @var object $existing_status
     *
     * @since 1.0
     */
    public $existing_status = null;

    /**
     * $redirectClass
     *
     * @var string
     */
    public $redirectClass = null;

    /**
     * __construct
     *
     * Constructor.
     *
     * @param    array   $config    An optional associative array of configuration settings.
     *
     * @since    1.0
     */
    public function __construct($request = array())
    {
        $this->request = $request;

        // todo: amy look at redirect
        $this->redirectClass = new MolajoControllerRedirect();
        $this->redirectClass->request = $this->request;

        /** load table */
        if ($this->request['task'] == 'display'
            || $this->request['task'] == 'add'
            || $this->request['task'] == 'login'
            || $this->request['component_table'] == '__dummy'
        ) {
            $this->isNew = false;

        } else {
            $this->table = $this->model->getTable();
            $this->table->reset();
            $this->table->load((int)$this->request['id']);

            if ($this->request['id'] == 0) {
                $this->isNew = true;
                $this->existing_status = 0;
            } else {
                $this->isNew = false;
                $this->existing_status = $this->table->state;
            }
        }

        /** dispatch events
        if ($this->dispatcher
        || $this->request['plugin_type'] == ''
        ) {
        } else {
        $this->dispatcher = JDispatcher::getInstance();
        MolajoPluginHelper::importPlugin($this->request['plugin_type']);
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
        $this->redirectClass->initialise();

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
//public function checkTaskAuthorisation($checkTask = null, $checkId = null, $checkCatid = null, $checkTable = null)

    public function checkTaskAuthorisation()
    {
        $acl = new MolajoACL ();
        $results = $acl->authoriseTask(
                            $this->request['option'],
                            $this->request['controller'],
                            $this->request['task'],
                            $this->request['id'],
                            $this->request['ids'],
                            $this->request['category'],
                            $this->table);

        if ($results === false) {
            $this->redirectClass->setRedirectMessage(MolajoTextHelper::_('MOLAJO_ACL_ERROR_ACTION_NOT_PERMITTED') . ' ' . $this->request['task']);
            $this->redirectClass->setRedirectMessageType('warning');
            return false;
        }

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
        if ($this->request['id'] == 0) {
            return true;
        }

        if (property_exists($this->table, 'checked_out')) {
        } else {
            return true;
        }

        $results = $this->model->checkin($this->request['id']);

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
     * @return    boolean
     */
    public function verifyCheckout()
    {
        if ($this->request['id'] == 0) {
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
        if ($this->request['id'] == 0) {
            return true;
        }

        if (property_exists($this->table, 'checked_out')) {
        } else {
            return true;
        }

        $results = $this->model->checkout($this->request['id']);
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
     * Components have version management save and restore processes as an
     *  automatic option
     *
     * @return    boolean
     * @since    1.0
     */
    public function createVersion($context)
    {
        if ($this->parameters->def('version_management', 1) == 1) {
        } else {
            return true;
        }

        /** create **/
        if ((int)$this->request['id'] == 0) {
            return true;
        }

        /** versions deleted with delete **/
        if ($this->request['task'] == 'delete'
            && $this->parameters->def('retain_versions_after_delete', 1) == 0
        ) {
            return true;
        }

        /** create version **/
        $versionKey = $this->model->createVersion($this->request['id']);

        /** error processing **/
        if ($versionKey === false) {
            $this->redirectClass->setRedirectMessage(MolajoTextHelper::_('MOLAJO_ERROR_VERSION_SAVE_FAILED'));
            $this->redirectClass->setRedirectMessageType('error');
            return false;
        }

        /** Trigger_Event: onContentCreateVersion
        $results = $this->dispatcher->trigger('onContentCreateVersion', array($context, $this->request['id'], $versionKey));
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
        if ((int)$this->request['id'] == 0) {
            return true;
        }

        /** versions deleted with delete **/
        if ($this->request['task'] == 'delete'
            && $this->parameters->def('retain_versions_after_delete', 1) == 0
        ) {
            $maintainVersions = 0;
        } else {
            /** retrieve versions desired **/
            $maintainVersions = $this->parameters->def('maintain_version_count', 5);
        }

        /** delete extra versions **/
        $results = $this->model->maintainVersionCount($this->request['id'], $maintainVersions);

        /** version delete failed **/
        if ($results === false) {
            $this->redirectClass->setRedirectMessage(MolajoTextHelper::_('MOLAJO_ERROR_VERSION_DELETE_VERSIONS_FAILED') . ' ' . $this->model->getError());
            $this->redirectClass->setRedirectMessageType('warning');
            return false;
        }

        /** Trigger_Event: onContentMaintainVersions
        return $this->dispatcher->trigger('onContentMaintainVersions', array($context, $this->request['id'], $maintainVersions));
         **/
    }

    /**
     * cleanCache
     *
     * @return    void
     */
    public function cleanCache()
    {
        $cache = MolajoController::getCache($this->request['option']);
        $cache->clean();
    }

    // crap follows

    /**
     * Method to get a model object, loading it if required.
     *
     * @param   string  $name    The model name. Optional.
     * @param   string  $prefix  The class prefix. Optional.
     * @param   array   $config  Configuration array for model. Optional.
     *
     * @return  object  The model.
     *
     * @since   1.0
     */
    public function getModel($name = '', $prefix = '', $config = array())
    {
        if (empty($name)) {
            $name = $this->getName();
        }

        if (empty($prefix)) {
            $prefix = $this->model_prefix;
        }

        return $this->createModel($name, $prefix, $config);
    }

    /**
     * Method to load and return a model object.
     *
     * @param   string  $name    The name of the model.
     * @param   string  $prefix  Optional model prefix.
     * @param   array   $config  Configuration array for the model. Optional.
     *
     * @return  mixed   Model object on success; otherwise null failure.
     *
     * @since   1.0
     * @note    Replaces _createModel.
     */
    protected function createModel($name, $prefix = '', $config = array())
    {
        // Clean the model name
        $modelName = preg_replace('/[^A-Z0-9_]/i', '', $name);
        $classPrefix = preg_replace('/[^A-Z0-9_]/i', '', $prefix);

        $result = MolajoModel::getInstance($modelName, $classPrefix, $config);

        return $result;
    }

    /**
     * Method to get the controller name
     *
     * The dispatcher name is set by default parsed using the classname, or it can be set
     * by passing a $config['name'] in the class constructor
     *
     * @return  string  The name of the dispatcher
     *
     * @since   1.0
     */
    public function getName()
    {
        if (empty($this->name)) {
            $r = null;
            if (!preg_match('/(.*)Controller/i', get_class($this), $r)) {
                MolajoError::raiseError(500, JText::_('JLIB_APPLICATION_ERROR_CONTROLLER_GET_NAME'));
            }
            $this->name = strtolower($r[1]);
        }

        return $this->name;
    }
}
