<?php
/**
 * @version     $id: single.php
 * @package     Molajo
 * @subpackage  Single Model
 * @copyright   Copyright (C) 2011 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die;

/**
 * MolajoModelEdit
 *
 * @package        Molajo
 * @subpackage    Single Model
 * @since       1.0
 */
class MolajoModelEdit extends JModel
{
    /**
     * @var    object    params
     * @since    1.0
     */
    protected $params;

    /**
     * Array of form objects.
     */
    protected $_forms = array();

    /**
     * Constructor.
     *
     * @param    array    $config    An optional associative array of configuration settings.
     *
     * @see    JController
     * @since    1.0
     */
    public function __construct($config = array())
    {
        parent::__construct($config);
        JRequest::setVar('view', JRequest::getCmd('EditView'));
        $this->params = MolajoComponentHelper::getParams(JRequest::getVar('option'));
    }

    /**
     * READ DATA SECTION
     */

    /**
     * getItem
     *
     * Method to get a single record or to initialize an empty record
     *
     * @param    integer    The id of the primary key.
     *
     * @return    mixed    Object on success, false on failure.
     */
    public function getItem($id = null)
    {
        $table = $this->getTable();
        if (empty($id)) {
            $id = (int)JRequest::getInt('id');
        }
        if ($id > 0) {
            if ($table->load($id)) {
            } else {
                $this->setError($table->getError());
                return false;
            }

            JRequest::setVar('item_category', $table->catid); // used in editor for core ACL - find something better.
        }

        /** verify checkout for edit **/
        if ($this->verifyCheckout($id)) {
        } else {
            return false;
        }

        /** retrieve column names **/
        $properties = $table->getProperties(1);
        $item = JArrayHelper::toObject($properties, 'JObject');

        /** retrieve json fields **/
        $jsonModel = JModel::getInstance('ModelConfiguration', 'Molajo', array('ignore_request' => true));
        $jsonFields = $jsonModel->getOptionList(MOLAJO_CONFIG_OPTION_ID_JSON_FIELDS);

        foreach ($jsonFields as $count => $jsonField) {

            $attribute = $jsonField->value;

            if (property_exists($item, $attribute)) {
                $registry = new JRegistry;
                $registry->loadJSON($item->$attribute);
                $item->$attribute = $registry->toArray();
            }
        }

        /** acl-append item-specific task permissions **/
        $aclClass = 'MolajoACL' . ucfirst(JRequest::getCmd('DefaultView'));
        // amy       $aclClass::getUserItemPermissions (JRequest::getVar('option'), JRequest::getVar('EditView'), JRequest::getVar('task'), $item->catid, $item->id, $item);

        return $item;
    }

    /**
     * verifyCheckout
     *
     * Method to verify if the content has been correctly checked out to the user
     *
     * @param    integer    The id of the primary key.
     *
     * @return    mixed    Object on success, false on failure.
     */
    public function verifyCheckout($id = null)
    {
        $table = $this->getTable();
        if (empty($id)) {
            $id = (int)JRequest::getInt('id');
        }
        if ($id == 0) {
            return true;
        }
        if ($table->load($id)) {
        } else {
            $this->setError($table->getError());
            return false;
        }
        if ($table->checked_out == MolajoFactory::getUser()->get('id')) {
            return true;
        } else {
            $this->setError(MolajoText::_('MOLAJO_ERROR_ROW_NOT_CHECKED_OUT_FOR_EDIT'));
            return false;
        }
    }

    /**
     * FORM INTERACTION SECTION
     */

    /**
     * getForm
     *
     * Method to get form and load data, if provided
     *
     * @param    array    $data        Data for the form.
     * @param    boolean    $loadData    True if the form is to load its own data (default case), false if not.
     *
     * @return    mixed    A MolajoForm object on success, false on failure
     * @since    1.0
     */
    public function getForm($data = array(), $loadData = true)
    {
        $datakey = JRequest::getInt('datakey');
        if ((int)$datakey > 0) {
            $data = MolajoFactory::getApplication()->getUserState($datakey, array());
        }
        $formName = JRequest::getVar('option') . '.' . JRequest::getCmd('view') . '.' . JRequest::getCmd('layout') . '.' . JRequest::getCmd('task') . '.' . JRequest::getInt('id') . '.' . JRequest::getVar('datakey');

        $form = $this->loadForm($formName, JRequest::getCmd('view'), array('control' => 'jform', 'load_data' => $loadData));
        if (empty($form)) {
            return false;
        }

        MolajoPluginHelper::importPlugin('content');
        $dispatcher = JDispatcher::getInstance();

        $dispatcher->trigger('onContentPrepareData', array($formName, $data));
        $dispatcher->trigger('onContentPrepareForm', array($form, $data));

        return $form;
    }

    /**
     * loadForm
     *
     * Method to get a form object.
     *
     * @param    string        $name        The name of the form.
     * @param    string        $source        The form source. Can be XML string if file flag is set to false.
     * @param    array        $options    Optional array of options for the form creation.
     * @param    boolean        $clear        Optional argument to force load a new form.
     * @param    string        $xpath        An optional xpath to search for the fields.
     * @return    mixed        MolajoForm object on success, False on error.
     */
    protected function loadForm($name, $source = null, $options = array(), $clear = false, $xpath = false)
    {
        $options['control'] = JArrayHelper::getValue($options, 'control', false);

        $hash = md5($source . serialize($options));
        if (isset($this->_forms[$hash]) && !$clear) {
            return $this->_forms[$hash];
        }

        $form = MolajoForm::getInstance($name, $source, $options, false, $xpath);

        if (isset($options['load_data']) && $options['load_data']) {
            $data = $this->loadFormData();
        } else {
            $data = array();
        }

        $form->bind($data);

        $this->_forms[$hash] = $form;

        return $form;
    }

    /**
     * loadFormData
     *
     * Method to get the data that should be injected in the form.
     * datakey - random value used as key to store form contents that failed previous save processing
     * getItem - read given request id or return empty structure
     *
     * @return    mixed    The data for the form.
     * @since    1.0
     */
    protected function loadFormData()
    {
        $datakey = JRequest::getInt('datakey');
        if ((int)$datakey > 0) {
            $data = MolajoFactory::getApplication()->getUserState($datakey, array());
        }

        if (empty($data)) {
            $data = $this->getItem();
        } else {
            MolajoFactory::getApplication()->setUserState($datakey, null);
            JRequest::setVar('datakey', null);
        }
        return $data;
    }

    /**
     * validate
     *
     * Method to filter and validate the form data, loading it into a table in preparation for the save method
     *
     * @param    object        $form        The form to validate against.
     * @param    array        $data        The data to validate.
     * @return    mixed        Array of table data ready to save, if valid, false otherwise.
     * @since    1.1
     */
    function validate($form, $data)
    {
        $data = $form->filter($data);

        $return = $form->validate($data);

        if ($return === false) {
            foreach ($form->getErrors() as $message) {
                $this->setError(MolajoText::_($message));
            }
            return false;
        }

        /** prepare data for save **/
        $table = $this->getTable();
        $key = $table->getKeyName();
        $id = $data[$key];
        if ($id > 0) {
            $table->load($id);
        }

        /** bind valid data to table **/
        if ($table->bind($data)) {
        } else {
            $this->setError($table->getError());
            return false;
        }

        /** prepare the table **/
        $this->prepareTable($table);

        /** check the table **/
        if ($table->check()) {
        } else {
            $this->setError($table->getError());
            return false;
        }

        return $table;
    }

    /**
     * prepareTable
     *
     * Prepares table data prior to saving.
     *
     * @param    MolajoTable    A MolajoTable object.
     *
     * @return    void
     * @since    1.0
     */
    protected function prepareTable($table)
    {
        /** publish up defaults to now **/
        if ($table->state == 1 && intval($table->start_publishing_datetime) == 0) {
            $table->start_publishing_datetime = MolajoFactory::getDate()->toMySQL();
        }

        /** version **/
        $table->version++;

        /** reorder - new content is first **/
        if (empty($table->id)) {
            $table->reorder('catid = ' . (int)$table->catid . ' AND state >= 0');
        }
    }

    /**
     * save
     *
     * Method to save the prepared table data from validated form input or from restore process.
     *
     * @table    array    The table data.
     *
     * @return    boolean    True on success.
     * @since    1.0
     */
    public function save($table)
    {
        if ($table->store()) {
        } else {
            $this->setError($table->getError());
            return false;
        }

        return $table->id;
    }

    /**
     * delete
     *
     * Method to delete a record.
     *
     * @param    int    $ids    An array of record primary keys.
     *
     * @return    boolean    True if successful, false if an error occurs.
     * @since    1.0
     */
    public function delete($id)
    {
        $table = $this->getTable();
        if ($table->load($id)) {
        } else {
            $this->setError($table->getError());
            return false;
        }

        if ($table->delete($id)) {
        } else {
            $this->setError($table->getError());
            return false;
        }

        return true;
    }

    /**
     * copy
     *
     * Copy existing item and load it in a table object for later saving
     *
     * @param    $id of copy requested
     *
     * @return    void
     * @since    1.0
     */
    public function copy($id, $catid)
    {
        /** load requested copy **/
        $fromTable = $this->getTable();

        if ($fromTable->load($id)) {
        } else {
            $this->setError(MolajoText::_('MOLAJO_ERROR_REQUESTED_VERSION_NOT_AVAILABLE_FOR_RESTORE'));
            return false;
        }

        $columns = $fromTable->getProperties();

        /** load empty row with requested data **/
        $toTable = $this->getTable();

        foreach ($columns as $column_name => $column_value) {

            if ($column_name == 'id') {
            } else {

                if ($column_name == 'catid') {
                    $toTable->$column_name = $catid;

                } else if ($column_name == 'alias' && $catid == $fromTable->catid) {
                    $toTable->alias = '';

                } else if ($column_name == 'state') {
                    $toTable->$column_name = 0;

                } else if ($column_name == 'version') {
                    $toTable->$column_name = 1;

                } else if ($column_name == 'modified') {
                    $toTable->$column_name = MolajoFactory::getDate()->toMySQL();

                } else if ($column_name == 'modified_by') {
                    $toTable->$column_name = MolajoFactory::getUser()->get('id');

                } else {
                    $toTable->$column_name = $column_value;
                }
            }
        }

        return $toTable;
    }

    /**
     * move
     *
     * Move existing item into a different category
     *
     * @param    $id of copy requested
     *
     * @return    void
     * @since    1.0
     */
    public function move($id, $catid)
    {
        $table = $this->getTable();
        if ($table->load($id)) {
        } else {
            $this->setError($table->getError());
            return false;
        }

        $table->catid = $catid;

        if ($table->store()) {
        } else {
            $this->setError($table->getError());
            return false;
        }

        return true;
    }

    /**
     * VERSION HISTORY SECTION
     */

    /**
     * createVersion
     *
     * Create version of item prior to save
     *
     * @param    $id of restore requested
     *
     * @return    void
     * @since    1.0
     */
    public function createVersion($id)
    {
        $table = $this->getTable();
        if ($table->load($id)) {
        } else {
            $this->setError(MolajoText::_('MOLAJO_ERROR_ROW_REQUESTED_IS_NOT_AVAILABLE_TO_SAVE_AS_A_VERSION'));
            return false;
        }

        if ($table->state == MOLAJO_STATE_VERSION) {
            $this->setError(MolajoText::_('MOLAJO_ERROR_ROW_IS_A_VERSION_COPY'));
            return false;
        }

        /** INSERT INTO **/
        $db = $this->getDbo();
        $columns = $table->getProperties();

        $columnList = '';
        foreach ($columns as $column_name => $column_value) {

            if ($column_name == 'id') {
            } else {
                if ($columnList == '') {
                } else {
                    $columnList .= ', ';
                }
                $columnList .= $db->namequote($column_name);
            }
        }
        $insertQuery = ' INSERT INTO ' . $db->namequote('#' . JRequest::getVar('ComponentTable')) . '(' . $columnList . ')';

        /** SELECT AND VALUES **/
        $columnList = '';
        foreach ($columns as $column_name => $column_value) {

            if ($column_name == 'id') {

            } else {
                /** comma **/
                if ($columnList == '') {
                } else {
                    $columnList .= ', ';
                }

                /** columns **/
                if ($column_name == 'title') {
                    $column_name = 'CONCAT(' . $db->namequote($column_name) . ', " "' . ', "' . MolajoText::_('MOLAJO_TITLE_VERSION_LITERAL') . '") as title';

                } else if ($column_name == 'version_of_id') {
                    $column_name = $db->namequote('id') . ' as ' . $db->namequote('version_of_id');

                } else if ($column_name == 'state_prior_to_version') {
                    $column_name = $db->namequote('state') . ' as ' . $db->namequote('state_prior_to_version');

                } else if ($column_name == 'state') {
                    $column_name = MOLAJO_STATE_VERSION . ' as ' . $db->namequote('state');

                } else if ($column_name == 'modified') {
                    $column_name = '"' . MolajoFactory::getDate()->toMySQL() . '" as ' . $db->namequote('modified');

                } else if ($column_name == 'modified_by') {
                    $column_name = MolajoFactory::getUser()->get('id') . ' as ' . $db->namequote('modified_by');

                } else if ($column_name == 'ordering') {
                    $column_name = $db->namequote('version') . ' as ' . $db->namequote('ordering');

                } else {
                    $column_name = $db->namequote($column_name);
                }

                $columnList .= $column_name;
            }
        }
        $insertQuery .= ' SELECT ' . $columnList . ' FROM ' . $db->namequote('#' . JRequest::getVar('ComponentTable')) . ' WHERE id = ' . (int)$id;

        $db->setQuery($insertQuery);
        if ($db->query()) {
        } else {
            MolajoFactory::getApplication()->enqueueMessage($db->getErrorMsg(), 'error');
            return false;
        }

        /** retrieve new id **/
        $db->setQuery(
            'SELECT MAX(id) as newID ' .
            ' FROM ' . $db->namequote('#' . JRequest::getVar('ComponentTable')) .
            ' WHERE version_of_id = ' . (int)$id
        );
        $newID = $db->loadResultArray();

        return $newID[0];
    }

    /**
     * restore
     *
     * Retrieve prior version of item and load it in a table object for later saving
     *
     * @param    $id of restore requested
     *
     * @return    void
     * @since    1.0
     */
    public function restore($id)
    {
        /** load requested version **/
        $fromTable = $this->getTable();
        if ($fromTable->load($id)) {
        } else {
            $this->setError(MolajoText::_('MOLAJO_ERROR_REQUESTED_VERSION_NOT_AVAILABLE_FOR_RESTORE'));
            return false;
        }

        /** verify version history state **/
        if ($fromTable->state == MOLAJO_STATE_VERSION) {

        } else {
            $this->setError(MolajoText::_('MOLAJO_ERROR_REQUESTED_VERSION_IS_NOT_A_VERSION'));
            return false;
        }

        /** load row to restore or create an empty row with the id **/
        $toTable = $this->getTable();
        if ($toTable->load($fromTable->version_of_id)) {
        } else {
            $toTable->id = $fromTable->version_of_id;
        }

        $results = $this->checkout($toTable->id);
        if ($results == false) {
            return;
        }

        $columns = $toTable->getProperties();
        foreach ($columns as $column_name => $column_value) {

            if ($column_name == 'id') {
            } else {

                if ($column_name == 'version_of_id') {
                    $toTable->$column_name = 0;

                } else if ($column_name == 'state') {
                    $toTable->$column_name = $fromTable->state_prior_to_version;

                } else if ($column_name == 'state_prior_to_version') {
                    $toTable->$column_name = 0;

                } else if ($column_name == 'modified') {
                    $toTable->$column_name = MolajoFactory::getDate()->toMySQL();

                } else if ($column_name == 'modified_by') {
                    $toTable->$column_name = MolajoFactory::getUser()->get('id');

                } else {
                    $toTable->$column_name = $fromTable->$column_name;
                }
            }
        }

        return $toTable;
    }

    /**
     * maintainVersionCount
     *
     * Prunes Version History to specified parameter values
     *
     * @param    $id of version history group
     * @param    $$maintainVersions number of copies to maintain
     *
     * @return    void
     * @since    1.0
     */
    public function maintainVersionCount($id, $maintainVersions)
    {
        $db = $this->getDbo();
        $db->setQuery(
            'SELECT id' .
            ' FROM ' . $db->namequote('#' . JRequest::getVar('ComponentTable')) .
            ' WHERE version_of_id = ' . (int)$id .
            ' ORDER BY version DESC ' .
            ' LIMIT ' . (int)$maintainVersions
        );
        $versionPrimaryKeys = $db->loadResultArray();

        $saveList = '';
        foreach ($versionPrimaryKeys as $saveid) {
            if ($saveList == '') {
            } else {
                $saveList .= ', ';
            }
            $saveList .= $saveid;
        }
        if ($saveList == '') {
            return;
        }
        $deleteQuery = 'DELETE FROM ' . $db->namequote('#' . JRequest::getVar('ComponentTable')) .
                       ' WHERE version_of_id = ' . (int)$id . ' AND id NOT IN (' . $saveList . ')';

        $db->setQuery($deleteQuery);
        if ($db->query()) {
        } else {
            MolajoFactory::getApplication()->enqueueMessage($db->getErrorMsg(), 'error');
            return false;
        }

        return true;
    }

    /**
     * INDICATOR UPDATE SECTION
     */

    /**
     * manageState
     *
     * Methods to change the published state of one or more records.
     *
     * archive: 2 Record cannot be changed
     * publish: 1
     * unpublish: 0
     * spam: -1
     * trash: -2
     *
     * version state (-10) is not processed by this method
     *
     * @param    array    $ids    A list of the primary keys to change.
     * @param    int        $value    The value of the published state.
     *
     * @return    boolean    True on success.
     * @since    1.0
     */
    public function archive($id)
    {
        return $this->manageState($id, MOLAJO_STATE_ARCHIVED);
    }

    public function publish($id)
    {
        return $this->manageState($id, MOLAJO_STATE_PUBLISHED);
    }

    public function unpublish($id)
    {
        return $this->manageState($id, MOLAJO_STATE_UNPUBLISHED);
    }

    public function spam($id)
    {
        return $this->manageState($id, MOLAJO_STATE_SPAMMED);
    }

    public function trash($id)
    {
        return $this->manageState($id, MOLAJO_STATE_TRASHED);
    }

    function manageState($id, $value)
    {
        $table = $this->getTable();
        if ($table->load($id)) {
        } else {
            $this->setError($table->getError());
            return false;
        }

        $table->state = $value;

        if ($table->store()) {

        } else {
            $this->setError($table->getError());
            return false;
        }

        return $value;
    }

    /**
     * toggleIndicator
     *
     * Method to toggle indicator
     *
     * @param    array    The ids of the items to toggle.
     * @param    string    name of column
     *
     * @return    boolean    True on success.
     */
    public function feature($id)
    {
        return $this->toggleIndicator($id, 'featured');
    }

    public function unfeature($id)
    {
        return $this->toggleIndicator($id, 'featured');
    }

    public function sticky($id)
    {
        return $this->toggleIndicator($id, 'stickied');
    }

    public function unsticky($id)
    {
        return $this->toggleIndicator($id, 'stickied');
    }

    public function toggleIndicator($id, $indicator)
    {
        $table = $this->getTable();
        if ($table->load($id)) {
        } else {
            $this->setError($table->getError());
            return false;
        }

        if ($table->$indicator == 0) {
            $table->$indicator = 1;
            $newValue = 1;
        } else {
            $table->$indicator = 0;
            $newValue = 0;
        }

        if ($table->store()) {
        } else {
            $this->setError($table->getError());
            return false;
        }
        return $newValue;
    }

    /**
     * checkin
     *
     * Method to check-in a record
     *
     * @param    integer    $id    The ID of the primary key
     *
     * @return    Boolean
     * @since    1.0
     */
    public function checkin($id)
    {
        $table = $this->getTable();
        if ($table->load($id)) {
        } else {
            $this->setError(MolajoText::_('MOLAJO_ERROR_NO_ROW_FOR_CHECKIN_TASK'));
            return false;
        }

        if (property_exists($table, 'checked_out')) {
        } else {
            $this->setError(MolajoText::_('MOLAJO_ERROR_NO_CHECKED_OUT_PROPERTY_FOR_CHECKIN_TASK'));
            return false;
        }

        if ($table->checked_out > 0) {
            if ($table->checkin($id)) {
            } else {
                $this->setError($table->getError());
                ;
                return false;
            }
        }

        return;
    }

    /**
     * checkout
     *
     * Method to check-out a record.
     *
     * @param    int $id    The ID of the primary key.
     *
     * @return    boolean    True if successful, false if an error occurs.
     * @since    1.0
     */
    public function checkout($id)
    {
        $table = $this->getTable();
        if ($table->load($id)) {
        } else {
            $this->setError(MolajoText::_('MOLAJO_ERROR_NO_ROW_FOR_CHECKIN_TASK'));
            return false;
        }

        if ($table->checked_out == 0) {
            if ($table->checkout(MolajoFactory::getUser()->get('id'), $id)) {
            } else {
                $this->setError(MolajoText::_('MOLAJO_ERROR_CHECKOUT_TASK'));
                return false;
            }
        } else {
            if ($table->checked_out == MolajoFactory::getUser()->get('id')) {
            } else {
                $this->setError(MolajoText::_('MOLAJO_ERROR_DATA_ALREADY_CHECKED_OUT_TO_SOMEONE_ELSE'));
                return false;
            }
        }

        return true;
    }

    /**
     * ORDER SECTION
     */

    /**
     * reorder
     *
     * Method to adjust the ordering of a row.
     *
     * @param    int        $ids    The ID of the primary key to move.
     * @param    integer        $delta    Increment, usually +1 or -1
     *
     * @return    boolean|null    False on failure or error, true on success.
     * @since    1.0
     */
    public function reorder($ids, $delta = 0)
    {
        $user = MolajoFactory::getUser();
        $table = $this->getTable();
        $ids = (array)$ids;
        $result = true;

        $allowed = true;

        foreach ($ids as $i => $id) {
            $table->reset();

            if ($table->load($id) && $this->checkout($id)) {
                // Access checks.
                if (!$this->canEditState($table)) {
                    // Prune items that you can't change.
                    unset($ids[$i]);
                    $this->checkin($id);
                    MolajoError::raiseWarning(403, MolajoText::_('MOLAJO_APPLICATION_ERROR_EDITSTATE_NOT_PERMITTED'));
                    $allowed = false;
                    continue;
                }

                $where = array();
                $where = $this->getReorderConditions($table);

                if (!$table->move($delta, $where)) {
                    $this->setError($table->getError());
                    unset($ids[$i]);
                    $result = false;
                }

                $this->checkin($id);
            } else {
                $this->setError($table->getError());
                unset($ids[$i]);
                $result = false;
            }
        }

        if ($allowed === false && empty($ids)) {
            $result = null;
        }

        return $result;
    }

    /**
     *
     * getReorderConditions
     *
     * A protected method to get a set of ordering conditions.
     *
     * @param    object    A record object.
     *
     * @return    array    An array of conditions to add to add to ordering queries.
     * @since    1.0
     */
    protected function getReorderConditions($table)
    {
        $condition = array();
        $condition[] = 'content_type = ' . (int)$table->content_type;
        $condition[] = 'catid = ' . (int)$table->catid;
        return $condition;
    }

    /**
     * Saves the manually set order of records.
     *
     * @param    array    $ids    An array of primary key ids.
     * @param    int        $order    +/-1
     *
     * @return    mixed
     * @since    1.0
     */
    function saveorder($ids = null, $order = null)
    {
        $table = $this->getTable();
        $conditions = array();
        $user = MolajoFactory::getUser();

        if (empty($ids)) {
            return MolajoError::raiseWarning(500, 'MOLAJO_ERROR_NO_ITEMS_SELECTED');
        }

        // update ordering values
        foreach ($ids as $i => $id) {
            $table->load((int)$id);

            // Access checks.
            if (!$this->canEditState($table)) {
                // Prune items that you can't change.
                unset($ids[$i]);
                MolajoError::raiseWarning(403, MolajoText::_('MOLAJO_APPLICATION_ERROR_EDITSTATE_NOT_PERMITTED'));

            } else if ($table->ordering != $order[$i]) {
                $table->ordering = $order[$i];

                if (!$table->store()) {
                    $this->setError($table->getError());
                    return false;
                }

                // remember to reorder within position and application_id
                $condition = $this->getReorderConditions($table);
                $found = false;

                foreach ($conditions as $cond) {
                    if ($cond[1] == $condition) {
                        $found = true;
                        break;
                    }
                }

                if (!$found) {
                    $key = $table->getKeyName();
                    $conditions[] = array($table->$key, $condition);
                }
            }
        }

        // Execute reorder for each category.
        foreach ($conditions as $cond) {
            $table->load($cond[0]);
            $table->reorder($cond[1]);
        }

        return true;
    }

    /**
     * getTable
     *
     * Returns a Table object, always creating it.
     *
     * @param    type    The table type to instantiate
     * @param    string    A prefix for the table class name. Optional.
     * @param    array    Configuration array for model. Optional.
     *
     * @return    MolajoTable    A database object
     */
    public function getTable($type = '', $prefix = '', $config = array())
    {
        return MolajoTable::getInstance($type = ucfirst(JRequest::getCmd('view')), $prefix = ucfirst(JRequest::getVar('DefaultView') . 'Table'), $config);
    }
}