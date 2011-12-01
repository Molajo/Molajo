<?php
/**
 * @package     Molajo
 * @subpackage  Extend
 * @copyright   Copyright (C) 2010-2011 Amy Stephen. All rights reserved. See http://molajo.org/copyright
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die;

/** load mvc classes **/
require_once dirname(__FILE__) . '/controllerParameters.php';
require_once dirname(__FILE__) . '/helper.php';
require_once dirname(__FILE__) . '/modelContentItem.php';
require_once dirname(__FILE__) . '/modelContentType.php';
require_once dirname(__FILE__) . '/modelForm.php';
require_once dirname(__FILE__) . '/modelParameter.php';

/**
 * extendController
 *
 * extend Listener responds to Molajo events and passes control to extendController::initiation
 * The initiation determines the course of action and hands of control to one of the controller task methods
 *
 */
class extendController extends plgSystemExtend
{

    /**
     * @var object
     * @since    1.6
     */
    public $app;

    /**
     * @var object
     * @since    1.6
     */
    public $user;

    /**
     * @var object
     * @since    1.6
     */
    public $db;

    /**
     * @var object
     * @since    1.6
     */
    protected $sql_table_name;

    /**
     * @var object
     * @since    1.6
     */
    protected $asset_type_ids;

    /**
     * @var object
     * @since    1.6
     */
    protected $custom_fields;

    /**
     * @var object
     * @since    1.6
     */
    protected $task;

    /**
     * @var object
     * @since    1.6
     */
    protected $component_option;

    /**
     * @var object
     * @since    1.6
     */
    protected $component_view;

    /**
     * @var object
     * @since    1.6
     */
    protected $component_layout;

    /**
     * @var object
     * @since    1.6
     */
    protected $category;

    /**
     * @var object
     * @since    1.6
     */
    protected $id;

    /**
     * @var object
     * @since    1.6
     */
    protected $nameParameters;

    /**
     * initiation
     *
     * determines action needed and passes control to task events
     *
     * @param string $task
     * @param object $form
     * @param string $context
     * @param object $content
     * @param object $nameParameters
     * @param int $limitstart
     * @param int $isNew
     *
     * @return boolean
     */
    public function initiation($task, $form, $context, $content, $param, $limitstart, $isNew, $event)
    {

        $systemPlugin =& MolajoPlugin::getPlugin('system', 'extend');
        $this->fieldParameters = new JParameter($systemPlugin->parameters);
        /**
        echo 'Task: '.$task.'<br />';
        echo 'Context: '.$context.'<br />';
        echo 'Limit Start: '.$limitstart.'<br />';

        echo 'IsNew: '.$isNew.'<br />';
        echo '$event: '.$event.'<br />';

        echo 'Content: <br />';
        echo var_dump($content);
        echo '<br />';

        echo 'Form: <br />';
        echo var_dump($form);
        echo '<br />';

        echo 'Parameters: <br />';
        echo var_dump($this->fieldParameters);
        echo '<br />';

         **/
        /** verify enabled **/
        if ((int)$this->fieldParameters->def('basic_enable', 0) == 0) {
            return;
        }

        /** load application support **/
        $this->loadLanguage();
        $this->app = MolajoFactory::getApplication();
        $this->db = MolajoFactory::getDbo();
        $this->user = MolajoFactory::getUser();
        $this->sql_table_name = $this->fieldParameters->def('sql_table_name', '#__molajo_custom_fields');

        /** verify table exists **/
        modelContentItem::checkTable($this->sql_table_name);

        /** verify task values **/
        if (!($task == 'display' || $task == 'add' || $task == 'edit' || $task == 'save' || $task == 'delete')) {
            return false;
        }
        $this->task = $task;

        /** retrieve and verify option value **/
        $this->component_option = extendHelper::getComponentOption();
        if ($this->component_option == false) {
            return false;
        }
        /** retrieve and verify view value **/
        $this->component_view = extendHelper::getComponentView($this->component_option);
        if ($this->component_view == false) {
            return false;
        }
        /** retrieve and verify layout value **/
        $this->component_layout = extendHelper::getComponentLayout($this->component_option, $this->component_view);
        if ($this->component_layout == false) {
            return false;
        }

        if ($context == 'plugins.plugin') {
            echo 'component_option: ' . $this->component_option . '<br />';
            echo 'component_view: ' . $this->component_view . '<br />';
            echo 'component_layout: ' . $this->component_layout . '<br />';
            die();
        }

        /** retrieve content type files **/
        $contentTypeFilenames = modelContentType::getFolderFilenames();
        if ($contentTypeFilenames == false) {
            return;
        } /* no content types defined */

        /** extend plugin: load content type parameters  **/
        if ($this->component_option == 'plugins' && $this->app->getName() == 'administrator') {
            if ($content->element == 'extend' && $content->folder == 'system') {

                /** extend plugin: edit extensions fieldParameters field **/
                if ($this->task == 'edit' || $this->task == 'add') {
                    /** labeled an add because no $id **/
                    return extendControllerParameters::display($contentTypeFilenames, $form);
                }
                echo 'task' . $task;
                echo 'event' . $event;
                die();

                /** extend plugin: save extensions fieldParameters field **/
                if ($this->task == 'save') {
                    return extendControllerParameters::save($contentTypeFilenames);
                }
                return;
            } else {
                return;
            }
        }

        /** new content does not have an id or category value **/
        /** TODO: identify components without categories **/
        /** TODO: how to find out primary key (if not id) **/
        if ($task == 'add') {
            $this->category = null;
            $this->id = null;
        } else {
            /** get category id **/
            $this->category = extendHelper::getComponentCategory($context, $content, $form, $isNew, $task, $this->component_option);
            if ($this->category == false) {
                return false;
            }

            /** get id **/
            $this->id = extendHelper::getComponentID($context, $content, $form, $isNew, $this->component_option);
            if ($this->id == false) {
                return false;
            }
        }

        /** verify global parameters **/
        if (!extendControllerParameters::verifyGlobal($this->task, $this->component_option, $this->category, $form)) {
            return;
        }

        /* look thru contenttype folder filenames */
        $this->asset_type_ids = array();

        foreach ($contentTypeFilenames as $contentTypeFilename) {

            /** extract content type name from file **/
            $contentType = substr($contentTypeFilename, 0, stripos($contentTypeFilename, '.xml'));

            /** verify content type parameters **/
            $results = extendControllerParameters::verifyContentType($this->task, $this->component_option, $this->category, $form, $contentType);

            /** add to valid content type array **/
            if ($results == true) {
                /** TODO: Add event for OnAfterParameterValidation **/
                $this->asset_type_ids[] = $contentType;
            }
        }

        /** no content types to process **/
        if (count($this->asset_type_ids) == 0) {
            return;
        }

        /** task based subcontrollers process content types **/
        if ($this->task == 'display' || $this->task == 'add' || $this->task == 'edit') {
            return extendController::display($form, $context, $content, $nameParameters, $limitstart);
        }
        if ($this->task == 'save') {
            return extendController::save($context, &$content, $isNew);
        }
        if ($this->task == 'delete') {
            return extendController::delete($context, $content);
        }

        /** this will never happen. really. #flw **/
        return false;
    }

    /**
     *
     * display
     *
     * Method invoked by initiation method
     *
     * - Locates Content Types relevant to Component Item
     * - Retrieves a list of Custom Fields defined for those Content Types
     * - Appends Custom Fields onto the Component Item Form Object for Edit Layout Display Requests
     * - Queries the Custom Fields Table for Component Item Custom Fields matching the Custom Fields list
     * - Appends retrieved Custom Field values into the Component Content Object
     *
     *  Molajo 1.7 Wish List:
     *
     *  Provide a Model Event On BeforeQuery with access to state information that shares the Query object
     *      so that the select list, table list, and where clause can be modified
     *
     * @param    object        Form object for Edit Layout Requests
     * @param    string        The context for the content passed to the plugin.
     * @param    object        The content object containing the Component Query Results.
     * @param    object        The content fieldParameters for the Component
     * @param    int        The 'page' number
     * @return    string
     * @since    1.6
     */
    public function display($form = null, $context = null, $content = null, $nameParameters = null, $limitstart = null)
    {
        /** no content types to process **/
        if (count($this->asset_type_ids) == 0) {
            return;
        }
        echo 'hello';
        die();
        /** process content types identified in controller **/
        $this->custom_fields = array();
        foreach ($this->asset_type_ids as $contentType) {

            /** retrieve custom fields **/
            $customFields = modelForm::getCustomFields($contentType);

            /** add custom fields to component form **/
            if (isset($form) && (!$form == null)) {
                modelForm::addCustomFieldsForm($contentType, $form);
            }

            /** add to valid custom fields array **/
            $this->custom_fields = array_merge($this->custom_fields, (array)$customFields);
        }

        /** build where clause of custom field names **/
        $whereString = modelContentItem::buildWhereClause($this->custom_fields);

        /** get custom field query results for component item **/
        $componentItemCustomFields = modelContentItem::getData($this->component_option, $this->id, $whereString);
        if ($componentItemCustomFields == false) {
            return;
        }

        /** add custom fields to component query results object **/
        for ($i = 0; $i < count($componentItemCustomFields); $i++) {

            if (is_array($componentItemCustomFields[$i]->field_value)) {
                $content->attribs[$componentItemCustomFields[$i]->field_name] = explode(',', $componentItemCustomFields[$i]->field_value);
            } else {
                $content->attribs[$componentItemCustomFields[$i]->field_name] = $componentItemCustomFields[$i]->field_value;
            }
        }

        return true;
    }

    /**
     * save
     *
     * Removes Custom Fields in Extend Table for the Component Item
     * Retrieves Form Object from Request containing component and custom field content
     * Using Content Type files and Custom Fields within as a driver,
     *    inserts matching Form fields Values into Extend Table for Component Item
     *
     * @param object $context
     * @param object $content
     * @param boolean $isNew
     * @return boolean
     */
    public function save($context, &$content, $isNew)
    {
        /** no content types to process **/
        if (count($this->asset_type_ids) == 0) {
            return;
        }

        /** get request form object containing component and custom field content **/
        $formdata = modelForm::getRequestForm();
        if ($formdata == false) {
            return false;
            /** nothing to save **/
        }

        /** TODO: Figure out how to filter and validate the form data **/

        /** process content types identified in controller **/
        $this->custom_fields = array();
        foreach ($this->asset_type_ids as $contentType) {

            /** retrieve custom fields for content types **/
            $customFields = modelForm::getCustomFields($contentType);

            /** append custom fields into overall custom fields array **/
            $this->custom_fields = array_merge($this->custom_fields, (array)$customFields);
        }
        if (count($this->custom_fields) == 0) {
            return;
        }

        /** initialise **/
        $ordering = 1;

        /** process unique list of custom fields **/
        foreach ($this->custom_fields as $customField) {

            /** custom field array check **/
            if (isset($formdata['attribs'][$customField->name])) {
                if ($customField->multiple == true || is_array($formdata['attribs'][$customField->name])) {
                    /** implode array values for storage as a string **/
                    $value = implode(',', $formdata['attribs'][$customField->name]);
                } else {
                    /** jorm has filtered and validated by Form Field Type **/
                    $value = $formdata['attribs'][$customField->name];
                }
            } else {
                /** save custom fields for component even if not on the form (should this be a parameter?) **/
                $value = '';
            }

            $results = modelContentItem::deleteMatching($this->component_option, $this->id, $customField->name);
            $results = modelContentItem::insert($this->component_option, $this->id, $customField->name, $value, $ordering++);
        }

        return true;
    }

    /**
     * delete
     *
     * Method called controller
     *
     * Use to delete all Custom Fields for a specific Component Item
     *
     * @param    string        $context
     * @param    array           $content
     * @param    boolean        false
     */
    public function delete($context, $content)
    {
        return modelContentItem::delete($this->component_option, $this->id);
    }

}