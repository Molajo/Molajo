<?php
/**
 * @package     Molajo
 * @subpackage  Primary Controller
 * @copyright   Copyright (C) 2011 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die;

/**
 * Primary Controller
 *
 * @package        Molajo
 * @subpackage    Controller
 * @since        1.0
 */
class MolajoController extends JController
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
     * @var object $document
     *
     * @since 1.0
     */
    protected $document = null;

    /**
     * @var object $table
     *
     * @since 1.0
     */
    protected $table = null;

    /**
     * @var object $model
     *
     * @since 1.0
     */
    public $view = null;

    /**
     * @var object $model
     *
     * @since 1.0
     */
    public $model = null;

    /**
     * @var object $catid
     *
     * @since 1.0
     */
    protected $catid = null;

    /**
     * @var object $id
     *
     * @since 1.0
     */
    protected $id = null;

    /**
     * @var object $isNew
     *
     * @since 1.0
     */
    protected $isNew = null;

    /**
     * @var object $existingState
     *
     * @since 1.0
     */
    protected $existingState = null;

    /**
     * @var object $dispatcher
     *
     * @since 1.0
     */
    protected $dispatcher = null;

    /**
     * $redirectClass
     *
     * @var string
     */
    protected $redirectClass = null;

    /**
     * __construct
     *
     * Constructor.
     *
     * @param    array   $config    An optional associative array of configuration settings.
     * @see        JController
     *
     * @since    1.0
     */
    public function __construct($config = array())
    {
        parent::__construct($config);
    }

    /**
     * display
     *
     * Method to handle display, edit, and add tasks
     *
     * @param    boolean        $cachable    If true, the view output will be cached
     * @param    array        $urlparameters    An array of safe url parameters and their variable types, for valid values see {@link JFilterInput::clean()}.
     *
     * @return    JController    This object to support chaining.
     *
     * @since    1.0
     */
    public function display($cachable = false, $urlparameters = false)
    {
        /** language files */
        $lang = MolajoFactory::getLanguage();

        /** template */
        $template = MolajoFactory::getApplication()->getTemplate();
        $templateTitle = $template[0]->title;
        $lang->load('template_'.$templateTitle, MOLAJO_EXTENSION_TEMPLATES.'/'.$templateTitle, $lang->getDefault(), false, false);

        /** component */
        $lang->load($this->request['option'], $this->request['component_path'], $lang->getDefault(), false, false);

        if ($this->request['task'] == 'edit') {
            $results = $this->checkOutItem();
            if ($results === false) {
                return $this->redirectClass->setSuccessIndicator(false);
            }
        }

        /** push model results into view */

        /** 1. Application */
        $this->view->app = MolajoFactory::getApplication();

        /** 2. Document */
        $this->view->document = MolajoFactory::getDocument();

        /** 3. User */
        $this->view->user = MolajoFactory::getUser();

        /** 4. Request */
        $this->view->request = $this->view->get('Request');

        /** 5. State */
        $this->view->state = $this->view->get('State');

        /** 6. Parameters */
        $this->view->parameters = $this->view->get('Parameters');

        /** 7. Query Results */
        $this->view->rowset = $this->view->get('Items');

        /** 8. Pagination */
        $this->view->pagination = $this->view->get('Pagination');

        /** 9. Layout Type */
        $this->view->layout_type = 'extensions';

        /** 10. Layout */
        $this->view->layout = $this->request['layout'];

        /** 11. Wrap */
        $this->view->wrap = $this->request['wrap'];

        /** display view */
        parent::display($cachable, $urlparameters);

        return $this;
    }

    /**
     * Shared methods for all controllers follow:
     */

    /**
     * initialise
     *
     * initialisation code needed for all tasks
     *
     * @param null $task
     * @return bool
     */
    public function initialise($request)
    {
        $this->document = MolajoFactory::getDocument();
        $this->request = $request;
        $this->parameters = $this->request['parameters'];
        $this->redirectClass = new MolajoControllerRedirect();
        $this->redirectClass->request = $this->request;

        $this->id = $this->request['id'];
        if ((int)$this->id == 0) {
            $this->id = 0;
            $this->catid = 0;
        }

        $this->catid = $this->request['catid'];
        if ((int)$this->catid == 0) {
            $this->catid = 0;
        }

        /** set model and view for display controller */
        if ($this->request['controller'] == 'display') {

            /** model */
            $this->model = $this->getModel(ucfirst($this->request['model']), ucfirst($this->request['no_com_option'].'Model'), array());
            $this->model->request = $this->request;
            $this->model->parameters = $this->request['parameters'];

            /** view format */
            $this->view = $this->getView($this->request['view'], $this->document->getType());
            $this->view->setModel($this->model, true);
            $this->view->setLayout($this->request['layout']);
        }

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
            $this->table->load($this->id);
            $this->catid = $this->table->catid;

            if ($this->id == 0) {
                $this->isNew = true;
                $this->existingState = 0;
            } else {
                $this->isNew = false;
                $this->catid = $this->table->catid;
                $this->existingState = $this->table->state;
            }
        }

        /** dispatch events */
        if ($this->dispatcher
            || $this->request['plugin_type'] == ''
        ) {
        } else {
            $this->dispatcher = JDispatcher::getInstance();
            MolajoPluginHelper::importPlugin($this->request['plugin_type']);
        }

        /** check authorisation **/
        if (MOLAJO_APPLICATION_ID == 2) {
        } else {
            $results = MolajoController::checkTaskAuthorisation($this->request['task']);
            if ($results === false) {
                return false;
            }
        }

        /** set redirects **/
        $this->redirectClass->initialize();

        /** success **/
        return true;
    }

    /**
     * checkTaskAuthorisation
     *
     * Method to verify the user's authorisation to perform a specific task
     *
     * Molajo_Note: Task and content shared with ACL for authorisation verification, ACL Implementation data removed from CMS
     *
     * @param null $checkTask
     * @param null $checkId
     * @param null $checkCatid
     * @param null $checkTable
     *
     * @return bool
     */
    public function checkTaskAuthorisation($checkTask = null, $checkId = null, $checkCatid = null, $checkTable = null)
    {
        if ($checkTask == null) {
            $checkTask = $this->getTask();
        }

        if ($this->request['component_table'] == '__dummy') {
            $checkId = 0;
            $checkCatid = 0;
            $checkTable = array();
        } else {

            if ($checkId == null) {
                $checkId = $this->id;
            }

            if ($checkCatid == null) {
                if ((int)$this->catid == 0) {
                    $checkCatid = (int)$this->table->catid;
                } else {
                    $checkCatid = (int)$this->catid;
                }
            }

            if ($checkTable == null) {
                $checkTable = $this->table;
            }
        }

        $acl = new MolajoACL ();
        $results = $acl->authoriseTask($this->request['option'], $this->request['view'], $checkTask, $checkId, $checkCatid, $checkTable);

        if ($results === false) {
            $this->redirectClass->setRedirectMessage(MolajoText::_('MOLAJO_ACL_ERROR_ACTION_NOT_PERMITTED').' '.$checkTask);
            $this->redirectClass->setRedirectMessageType('warning');
            return false;
        }

        return true;
    }

    /**
     * getModel
     *
     * Proxy for getModel.
     *
     * @param    string    $name    The name of the model.
     * @param    string    $prefix    The prefix for the PHP class name.
     * @param    array    $config    Configuration data
     *
     * @return object model
     */
    public function getModel($name = '', $prefix = '', $config = array())
    {
        return parent::getModel($name, $prefix, $config);
    }

    /**
     * checkInItem
     *
     * Used to check in item if it is already checked out
     *
     * @return bool
     */
    public function checkInItem()
    {
        /** no checkin for new row **/
        if ($this->id == 0) {
            return;
        }

        /** see if table supports checkin **/
        if (property_exists($this->table, 'checked_out')) {
        } else {
            return;
        }

        /** model: checkin **/
        $results = $this->model->checkin($this->id);

        /** error processing **/
        if ($results === false) {
            $this->redirectClass->setRedirectMessage(MolajoText::_('MOLAJO_CHECK_IN_FAILED'));
            $this->redirectClass->setRedirectMessageType('warning');
            return false;
        }

        /** success **/
        return true;
    }

    /**
     * verifyCheckOut
     *
     * method to verify that the current user is recorded in the checked_out column of the item
     *
     * @return    boolean
     */
    public function verifyCheckOut()
    {
        /** no checkout for new row **/
        if ($this->id == 0) {
            return;
        }

        /** no checkout if table does not supports it **/
        if (property_exists($this->table, 'checked_out')) {
        } else {
            return;
        }

        /** model: checkin **/
        if ($this->table->checked_out == MolajoFactory::getUser()->get('id')) {
        } else {
            $this->redirectClass->setRedirectMessage(MolajoText::_('MOLAJO_ERROR_DATA_NOT_CHECKED_OUT_BY_USER').' '.$this->getTask());
            $this->redirectClass->setRedirectMessageType('warning');
            return false;
        }

        return true;
    }

    /**
     * checkOutItem
     *
     * method to set the checkout_time and checked_out values of the item
     *
     * @return    boolean
     * @since    1.0
     */
    public function checkOutItem()
    {
        /** no checkin for new row **/
        if ($this->id == 0) {
            return true;
        }

        /** see if table supports checkin **/
        if (property_exists($this->table, 'checked_out')) {
        } else {
            return true;
        }

        /** model: checkout **/
        $results = $this->model->checkout($this->id);

        /** error processing **/
        if ($results === false) {
            $this->redirectClass->setRedirectMessage(MolajoText::_('MOLAJO_ERROR_CHECKOUT_FAILED'));
            $this->redirectClass->setRedirectMessageType('error');
            return false;
        }
        return true;
    }

    /**
     * createVersion
     *
     * Molajo_Note: All Components have version management save and restore processes as an automatic option
     *
     * @return    void
     * @since    1.0
     */
    public function createVersion($context)
    {
        /** activated? **/
        if ($this->parameters->def('config_component_version_management', 1) == 1) {
        } else {
            return true;
        }

        /** no version for create **/
        if ((int)$this->id == 0) {
            return true;
        }

        /** versions deleted with delete **/
        if ($this->task == 'delete'
            && $this->parameters->def('config_component_retain_versions_after_delete', 1) == 0
        ) {
            return true;
        }

        /** create version **/
        $versionKey = $this->model->createVersion($this->id);

        /** error processing **/
        if ($versionKey === false) {
            $this->redirectClass->setRedirectMessage(MolajoText::_('MOLAJO_ERROR_VERSION_SAVE_FAILED'));
            $this->redirectClass->setRedirectMessageType('error');
            return false;
        }

        /** Trigger_Event: onContentCreateVersion **/
        /** Molajo_Note: New Event onContentCreateVersion so that all data stays in sync **/
        $results = $this->dispatcher->trigger('onContentCreateVersion', array($context, $this->id, $versionKey));
        if (count($results) && in_array(false, $results, true)) {
            $this->redirectClass->setRedirectMessage(MolajoText::_('MOLAJO_ERROR_ON_CONTENT_CREATE_VERSION_EVENT_FAILED'));
            $this->redirectClass->setRedirectMessageType('error');
            return false;
        }

        return true;
    }

    /**
     * maintainVersionCount
     *
     * Molajo_Note: All Components have version management save and restore processes as an automatic option
     *
     * @param  $context
     * @return bool
     */
    public function maintainVersionCount($context)
    {
        /** activiated? **/
        if ($this->parameters->def('config_component_version_management', 1) == 1) {
        } else {
            return;
        }

        /** no versions to delete for create **/
        if ((int)$this->id == 0) {
            return;
        }

        /** versions deleted with delete **/
        if ($this->task == 'delete' && $this->parameters->def('config_component_retain_versions_after_delete', 1) == 0) {
            $maintainVersions = 0;
        } else {
            /** retrieve versions desired **/
            $maintainVersions = $this->parameters->def('config_component_maintain_version_count', 5);
        }

        /** delete extra versions **/
        $results = $this->model->maintainVersionCount($this->id, $maintainVersions);

        /** version delete failed **/
        if ($results === false) {
            $this->redirectClass->setRedirectMessage(MolajoText::_('MOLAJO_ERROR_VERSION_DELETE_VERSIONS_FAILED').' '.$this->model->getError());
            $this->redirectClass->setRedirectMessageType('warning');
            return false;
        }

        /** Trigger_Event: onContentMaintainVersions **/
        return $this->dispatcher->trigger('onContentMaintainVersions', array($context, $this->id, $maintainVersions));
    }

    /**
     * cleanCache
     *
     * Molajo_Note: All Components have version management save and restore processes as an automatic option
     *
     * @return    void
     */
    public function cleanCache()
    {
        $cache = MolajoFactory::getCache($this->request['option']);
        $cache->clean();
    }
}
