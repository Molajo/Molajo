<?php
/**
 * @version     controller.php
 * @package     Molajo
 * @subpackage  Primary Controller
 * @copyright   Copyright (C) 2011 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die;

/**
 * Primary Controller
 *
 * @package	Molajo
 * @subpackage	Controller
 * @since	1.0
 */
class MolajoController extends JController
{
    /**
    * $table
    *
    * @var object
    */
    protected $table = null;

    /**
    * $model
    *
    * @var object
    */
    public $model = null;

    /**
    * $params
    *
    * @var object
    */
    protected $params = null;

    /**
    * $catid
    *
    * @var int
    */
    protected $catid = null;

    /**
    * $id
    *
    * @var int
    */
    protected $id = null;

    /**
    * $isNew
    *
    * @var int
    */
    protected $isNew = null;

    /**
    * $existingState
    *
    * @var int
    */
    protected $existingState = null;

    /**
    * $dispatcher
    *
    * @var object
    */
    protected $dispatcher = null;

    /**
    * Constructor.
    *
    * @param	array	$config	An optional associative array of configuration settings.
    * @see	JController
    * @since	1.0
    */
    public function __construct($config = array())
    {
        parent::__construct($config);
        $this->params = MolajoComponentHelper::getParams(JRequest::getVar('option'));
    }

    /**
     * display
     *
     * Method to handle display, edit, and add tasks
     *
     * @param	boolean		$cachable	If true, the view output will be cached
     * @param	array		$urlparams	An array of safe url parameters and their variable types, for valid values see {@link JFilterInput::clean()}.
     *
     * @return	JController	This object to support chaining.
     * @since	1.0
     */
    public function display($cachable = false, $urlparams = false)
    {
        if (JRequest::getVar('task') == 'edit') {
            $results = $this->checkOutItem();
            if ($results === false) {
                return $this->redirectClass->setSuccessIndicator(false);
            }
        }

       /** display view **/
       parent::display($cachable, $urlparams);

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
    public function initialise($task = null)
    {
        /** task **/
        if ($task == null) {
            $task = JRequest::getCmd('task', 'display');
        }

        /** ids **/
        $this->id = JRequest::getInt('id');
        if ((int) $this->id == 0) {
            $this->id = 0;
            $this->catid = 0;
        }

        /** catid **/
        $this->catid = JRequest::getInt('catid');

        if ((int) $this->catid == 0) {
            $this->catid = 0;
        }

        /** model **/
        if ($this->model) {

        } else {
            if ($task == 'display') {
                $modelName = JRequest::getCmd('default_view');
            } else {
                $modelName = JRequest::getCmd('single_view');
            }

            $this->model = $this->getModel($modelName, ucfirst(JRequest::getCmd('default_view').'Model'), array());
        }

        /** table **/
        $this->table = $this->model->getTable();

        if ($task == 'display' || $task == 'add') {
        } else {
            /** load row **/
            $this->table->reset();
            $this->table->load($this->id);
            $this->catid = $this->table->catid;
        }

        /** Preparation: $isNew **/
        if ($this->id == 0) {
            $this->isNew = true;
            $this->existingState  = 0;
        } else {
            $this->isNew = false;
            $this->catid = $this->table->catid;
            $this->existingState = $this->table->state;
        }

        /** event dispatcher **/
        if ($this->dispatcher) {
        } else {
            $this->dispatcher = JDispatcher::getInstance();
            MolajoPluginHelper::importPlugin('content');
        }

        /** authorisation **/
        $results = MolajoController::checkTaskAuthorisation($task);
        if ($results === false) {
            return false;
        }

        /** redirects **/
        $this->redirectClass = new MolajoControllerRedirect ();
        $this->redirectClass->initializeRedirectLinks($task);
 
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
     * @return bool
     */
    public function checkTaskAuthorisation($checkTask=null, $checkId=null, $checkCatid=null, $checkTable=null)
    {
        if ($checkTask == null) {
            $checkTask = $this->getTask();
        }

        if ($checkId == null) {
            $checkId = $this->id;
        }

        if ($checkCatid == null) {
            if ((int) $this->catid == 0) {
                $checkCatid = (int) $this->table->catid;
            } else {
                $checkCatid = (int) $this->catid;
            }
        }

        if ($checkTable == null) {
            $checkTable = $this->table;
        }

        $aclClass = ucfirst(JRequest::getCmd('default_view')).'ACL';
        $acl = new $aclClass ();
        $results = $acl->authoriseTask (JRequest::getCmd('option'), JRequest::getCmd('single_view'), $checkTask, $checkId, $checkCatid, $checkTable);
        if ($results === false) {
            $this->redirectClass = new MolajoControllerRedirect ();
            $this->redirectClass->setRedirectMessage(JText::_('MOLAJO_ACL_ERROR_ACTION_NOT_PERMITTED').' '.$checkTask);
            $this->redirectClass->setRedirectMessageType('warning');
            return false;
        }
 
        return true;
    }

    /**
     * Proxy for getModel.
     *
     * @param	string	$name	The name of the model.
     * @param	string	$prefix	The prefix for the PHP class name.
     * @param	array	$config	Configuration data
     *
     * @param string $name
     * @param string $prefix
     * @param array $config
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
            $this->redirectClass->setRedirectMessage(JText::_('MOLAJO_CHECK_IN_FAILED'));
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
     * @return	boolean
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
        if ($this->table->checked_out == JFactory::getUser()->get('id')) {
        } else {
            $this->redirectClass->setRedirectMessage(JText::_('MOLAJO_ERROR_DATA_NOT_CHECKED_OUT_BY_USER').' '.$this->getTask());
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
     * @return	boolean
     * @since	1.0
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
            $this->redirectClass->setRedirectMessage(JText::_('MOLAJO_ERROR_CHECKOUT_FAILED'));
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
    * @return	void
    * @since	1.0
    */
    public function createVersion ($context)
    {
        /** activated? **/
        if ($this->params->def('config_component_version_management', 1) == 1) {
        } else {
            return true;
        }

        /** no version for create **/
        if ((int) $this->id == 0) {
            return true;
        }

        /** versions deleted with delete **/
        if ($this->task == 'delete' && $this->params->def('config_component_retain_versions_after_delete', 1) == 0) {
            return true;
        }

        /** create version **/
        $versionKey = $this->model->createVersion($this->id);

        /** error processing **/
        if ($versionKey === false) {
            $this->redirectClass->setRedirectMessage(JText::_('MOLAJO_ERROR_VERSION_SAVE_FAILED'));
            $this->redirectClass->setRedirectMessageType('error');
            return false;
        }

        /** Trigger_Event: onContentCreateVersion **/
        /** Molajo_Note: New Event onContentCreateVersion so that all data stays in sync **/
        $results = $this->dispatcher->trigger('onContentCreateVersion', array($context, $this->id, $versionKey));
        if (count($results) && in_array(false, $results, true)) {
            $this->redirectClass->setRedirectMessage(JText::_('MOLAJO_ERROR_ON_CONTENT_CREATE_VERSION_EVENT_FAILED'));
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
    public function maintainVersionCount ($context)
    {
        /** activiated? **/
        if ($this->params->def('config_component_version_management', 1) == 1) {
        } else {
            return;
        }

        /** no versions to delete for create **/
        if ((int) $this->id == 0) {
            return;
        }

        /** versions deleted with delete **/
        if ($this->task == 'delete' && $this->params->def('config_component_retain_versions_after_delete', 1) == 0) {
            $maintainVersions = 0;
        } else {
            /** retrieve versions desired **/
            $maintainVersions = $this->params->def('config_component_maintain_version_count', 5);
        }

        /** delete extra versions **/
        $results = $this->model->maintainVersionCount ($this->id, $maintainVersions);

        /** version delete failed **/
        if ($results === false) {
            $this->redirectClass->setRedirectMessage(JText::_('MOLAJO_ERROR_VERSION_DELETE_VERSIONS_FAILED').' '.$this->model->getError());
            $this->redirectClass->setRedirectMessageType('warning');
            return false;
        }

        /** Trigger_Event: onContentMaintainVersions **/
        /** Molajo_Note: New Event onContentMaintainVersions so that all data stays in sync **/
        return $this->dispatcher->trigger('onContentMaintainVersions', array($context, $this->id, $maintainVersions));
    }

    /**
    * cleanCache
    *
    * Molajo_Note: All Components have version management save and restore processes as an automatic option
    *
    * @return	void
    */
    public function cleanCache ()
    {
        $cache = JFactory::getCache(JRequest::getCmd('option'));
        $cache->clean();
    }

    /**
    * postSaveHook
    *
    * Function that allows child controller access to model data after the data has been saved.
    *
    * @param	JModel	$this->model		The data model object.
    * @param	array	$validData	The validated data.
    *
    * @return	void
    */
    protected function postSaveHook(JModel $model, $validData = array())
    {
    }
}