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
 * @package	    Molajo
 * @subpackage	Controller
 * @since	    1.0
 */
class MolajoController extends JController
{
    /**
     * @var object $this->request
     *
     * ["application_id"]=> int(1)
     * ["current_url"]=> string(38) "http://localhost/molajo/administrator/"
     * ["component_path"]=> string(65) "/users/amystephen/sites/molajo/administrator/components/com_login"
     * ["base_url"]=> string(38) "http://localhost/molajo/administrator/"
     * ["item_id"]=> int(0)
     * ["controller"]=> string(7) "display"
     * ["option"]=> string(9) "com_login"
     * ["no_com_option"]=> string(5) "login"
     * ["view"]=> string(7) "display"
     * ["layout"]=> string(7) "default"
     * ["model"]=> string(5) "dummy"
     * ["task"]=> string(7) "display"
     * ["format"]=> string(4) "html"
     * ["plugin_type"]=> string(0) ""
     * ["id"]=> int(0)
     * ["cid"]=> array(0) { }
     * ["catid"]=> int(0)
     * ["params"]=> object(JRegistry)#83 (1) { ["data":protected]=> object(stdClass)#84 (0) { } }
     * ["acl_implementation"]=> string(4) "core"
     * ["component_table"]=> string(8) "__common"
     * ["filter_fieldname"]=> string(27) "config_manager_list_filters"
     * ["select_fieldname"]=> string(26) "config_manager_grid_column"
     *
     * @since 1.0
     */
    public $request = array();

    /**
     * @var object $params
     *
     * @since 1.0
     */
    public $params = array();

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
     * @param	array   $config	An optional associative array of configuration settings.
     * @see	    JController
     *
     * @since	1.0
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
     * @param	boolean		$cachable	If true, the view output will be cached
     * @param	array		$urlparams	An array of safe url parameters and their variable types, for valid values see {@link JFilterInput::clean()}.
     *
     * @return	JController	This object to support chaining.
     *
     * @since	1.0
     */
    public function display($cachable = false, $urlparams = false)
    {
        /** language files */
        $lang = MolajoFactory::getLanguage();
		
		$template = MolajoFactory::getApplication()->getTemplate(true)->template;

		$lang->load('tpl_'.$template, MOLAJO_PATH_BASE, null, false, false)
		||	$lang->load('tpl_'.$template, MOLAJO_PATH_THEMES."/$template", null, false, false)
		||	$lang->load('tpl_'.$template, MOLAJO_PATH_BASE, $lang->getDefault(), false, false)
		||	$lang->load('tpl_'.$template, MOLAJO_PATH_THEMES."/$template", $lang->getDefault(), false, false);

		$lang->load($this->request['option'], MOLAJO_PATH_BASE, null, false, false)
		||	$lang->load($this->request['option'], $this->request['component_path'], null, false, false)
		||	$lang->load($this->request['option'], MOLAJO_PATH_BASE, $lang->getDefault(), false, false)
		||	$lang->load($this->request['option'], $this->request['component_path'], $lang->getDefault(), false, false);
        
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
        $this->view->params = $this->view->get('Params');

        /** 7. Query Results */
        $this->view->rowset = $this->view->get('Items');

        /** 8. Pagination */
        $this->view->pagination = $this->view->get('Pagination');

        /** 9. Layout */
        $this->view->layout = $this->request['layout'];

        /** 10. Wrap */
        $this->view->wrap = $this->request['wrap'];

        /** display view */
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
    public function initialise($request)
    {
        $this->document = MolajoFactory::getDocument();        
        $this->request = $request;
        $this->params = $this->request['params'];
        $this->redirectClass = new MolajoControllerRedirect();

        $this->id = $this->request['id'];
        if ((int) $this->id == 0) {
            $this->id = 0;
            $this->catid = 0;
        }

        $this->catid = $this->request['catid'];
        if ((int) $this->catid == 0) {
            $this->catid = 0;
        }

        /** set model and view for display controller */
        if ($this->request['controller'] == 'display') {

            /** model */
            $this->model = $this->getModel(ucfirst($this->request['model']), ucfirst($this->request['no_com_option'].'Model'), array());
            $this->model->request = $this->request;
            $this->model->params = $this->request['params'];

            /** view format */
            $this->view = $this->getView($this->request['view'], $this->document->getType());
            $this->view->setModel($this->model, true);
		    $this->view->setLayout($this->request['layout']);
        }

        /** load table */
        if ($this->request['task'] == 'display'
            || $this->request['task'] == 'add'
            || $this->request['task'] == 'login') {

            $this->isNew = false;

        } else {
            $this->table = $this->model->getTable();
            $this->table->reset();
            $this->table->load($this->id);
            $this->catid = $this->table->catid;

            if ($this->id == 0) {
                $this->isNew = true;
                $this->existingState  = 0;
            } else {
                $this->isNew = false;
                $this->catid = $this->table->catid;
                $this->existingState = $this->table->state;
            }
        }
echo 'plugin type'.$this->request['plugin_type'];
die();
        /** dispatch events */
        if ($this->dispatcher) {
        } else {
            $this->dispatcher = JDispatcher::getInstance();
            MolajoPluginHelper::importPlugin($this->request['plugin_type']);
        }

        /** check authorisation **/
        $results = MolajoController::checkTaskAuthorisation($this->request['task']);
        if ($results === false) {
            return false;
        }

        /** set redirects **/
        $this->redirectClass->initialize($this->request['task']);
 
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
    public function checkTaskAuthorisation($checkTask=null, $checkId=null, $checkCatid=null, $checkTable=null)
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
                if ((int) $this->catid == 0) {
                    $checkCatid = (int) $this->table->catid;
                } else {
                    $checkCatid = (int) $this->catid;
                }
            }

            if ($checkTable == null) {
                $checkTable = $this->table;
            }
        }

        $acl = new MolajoACL ();
        $results = $acl->authoriseTask ($this->request['option'], $this->request['view'], $checkTask, $checkId, $checkCatid, $checkTable);

        if ($results === false) {
            $this->redirectClass->setRedirectMessage(JText::_('MOLAJO_ACL_ERROR_ACTION_NOT_PERMITTED').' '.$checkTask);
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
     * @param	string	$name	The name of the model.
     * @param	string	$prefix	The prefix for the PHP class name.
     * @param	array	$config	Configuration data
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
        if ($this->table->checked_out == MolajoFactory::getUser()->get('id')) {
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
        if ($this->task == 'delete'
            && $this->params->def('config_component_retain_versions_after_delete', 1) == 0) {
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
        $cache = MolajoFactory::getCache($this->request['option']);
        $cache->clean();
    }
}