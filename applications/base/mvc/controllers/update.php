<?php
/**
 * @package     Molajo
 * @subpackage  Controller
 * @copyright   Copyright (C) 2012 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die;

/**
 * Update
 *
 * @package     Molajo
 * @subpackage  Controller
 * @since       1.0
 */
class MolajoUpdateController extends MolajoController
{

    /**
     *  save
     */
    function save()
    {
        /** Test */
        $this->id = 113;
        $valid = true;

        /** load data into model */
        $valid = $this->load();
/**
        $this->id = 0;
        $this->model->id = 0;
        $this->model->row->id = 0;
*/
        $this->model->row->title = 'Puff it up.';
        /** filter input, validate values and foreign keys */
        if ($valid === true) {
            $valid = $this->_filter_and_validate($this->model->row);
        }

        /** insert or update model data */
        if ($valid === true) {
            //$valid = $this->model->store();
        }

        /** redirect */
        if ($valid === true) {
            if ($this->model->row->id == 0
                || $this->model->row->status == 0) {
                Molajo::Responder()->redirect($this->task_request->get('redirect_on_success'), 301);
            } else {
                Molajo::Responder()->redirect(
                     AssetHelper::getURL($this->task_request->get('request_asset_id')),
                     301
                 );
            }
        } else {
            $link = $this->task_request->get('redirect_on_failure');
            if ((int) $this->id == 0) {
                $link .= '&task=add';
            } else {
                $link .= '&task=edit';
            }
            Molajo::Responder()
                ->redirect($link, 301);
        }

        /**
        echo $action;
        die;


        $hash = Services::Security()->getHash(MOLAJO_APPLICATION.get_class($this));

        $session = Services::Session()->create($hash);
        var_dump($session);
        echo 'back in app';
        die;*/
    }

    /**
     * load
     *
     * load existing data from model
     *
     * @return boolean
     * @since  1.0
     */
    function load()
    {
        $valid = true;

        /** new or update? */
        $this->model->query->where($this->model->db->qn('id')
            . ' = ' . (int)$this->id);

        $results = $this->model->load();

        if (empty($results)) {
            $this->model->row = new stdClass;
            $this->model->row->id = 0;
            $this->model->row->title = 'One long summer';
            $this->model->row->protected = 0;
            $this->model->row->asset_type_id = 10000;
            $this->model->row->checked_out_by = 0;
            $this->model->row->created_datetime = '2012-02-14';
            $this->model->row->created_by = 42;
            $this->model->row->extension_instance_id = 2;
            $this->model->row->modified_by = 42;

            return $valid;
        } else {

            try {
                $this->model->query = $this->model->db->getQuery(true);
                $this->model->id = $this->id;
                $this->model->query->where('id = ' . (int)$this->id);
                $this->model->row = $this->model->loadObject();

            } catch (Exception $e) {
                $valid = false;
                if (Services::Configuration()->get('debug', 0) == 1) {
                    debug(' ');
                    debug('MolajoUpdateController::load Failed');
                    debug('Model: ' . $this->model->name . ' ID: ' . $this->id);
                    debug(Services::Language()->translate($e->getMessage()));
                }
                Services::Message()
                    ->set(
                    $message =
                        Services::Language()->translate($e->getMessage()),
                    $type = MOLAJO_MESSAGE_TYPE_ERROR
                );
            }
        }

        return $valid;
    }

    /**
     * _filter_and_validate
     *
     * Runs custom validation methods
     *
     * @return  object
     * @since   1.0
     */
    protected function _filter_and_validate()
    {
        $valid = true;

        $v = simplexml_load_file(
            MOLAJO_APPLICATIONS_MVC
                . '/models/tables/'
                . substr($this->model->table_name, 3, 99)
                . '.xml'
        );
        if (count($v) == 0) {
            return true;
        }

        /** filters and defaults */
        $valid = true;
        if (isset($v->filters->filter)) {
            foreach ($v->filters->filter as $f) {

                $name = (string)$f['name'];
                $datatype = (string)$f['filter'];
                $null = (string)$f['null'];
                $default = (string)$f['default'];

                if (isset($this->model->row->$name)) {
                    $value = $this->model->row->$name;
                } else {
                    $value = null;
                }

                if ($datatype == null) {
                } else {
                    try {
                        $value =
                            Services::Security()->filter(
                                $name, $value, $datatype, $null, $default
                            );

                    } catch (Exception $e) {
                        $valid = false;
                        if (Services::Configuration()->get('debug', 0) == 1) {
                            debug(' ');
                            debug('MolajoUpdateController::_filter_and_validate Filter Failed');
                            debug(Services::Language()->translate($e->getMessage()) . ' ' . $f['name']);
                        }
                        Services::Message()
                            ->set(
                            $message =
                                Services::Language()->translate($e->getMessage())
                                    . ' ' . $name,
                            $type = MOLAJO_MESSAGE_TYPE_ERROR
                        );
                    }

                    if (Services::Configuration()->get('debug', 0) == 1) {
                        debug('MolajoUpdateController::_filter_and_validate Filter Name: ' . $name . ' Source: ' . $value . ' Datatype: ' . $datatype . ' Null: ' . $null . ' Default: ' . $default . ' Value: ' . $this->model->row->$name);
                    }
                }
            }
        }
        if (Services::Configuration()->get('debug', 0) == 1) {
            debug(' ');
            debug('MolajoUpdateController::_filter_and_validate Filter::Success: ' . $valid);
        }

        /** Helper Functions */
        if (isset($v->helpers->helper)) {
            foreach ($v->helpers->helper as $h) {

                $name = (string)$h['name'];

                try {
                    $this->_validateHelperFunction($name);

                } catch (Exception $e) {
                    $valid = false;
                    if (Services::Configuration()->get('debug', 0) == 1) {
                        debug(' ');
                        debug('MolajoUpdateController::_filter_and_validate Helper Failed');
                        debug(Services::Language()->translate($e->getMessage()) . ' ' . $name);
                    }
                    Services::Message()
                        ->set(
                        $message =
                            Services::Language()->translate($e->getMessage()) . ' ' . $name,
                        $type = MOLAJO_MESSAGE_TYPE_ERROR
                    );
                }
            }
        }
        if (Services::Configuration()->get('debug', 0) == 1) {
            debug(' ');
            debug('MolajoUpdateController::_filter_and_validate Helper::Success: ' . $valid);
        }

        /** Foreign Keys */
        if (isset($v->fks->fk)) {
            foreach ($v->fks->fk as $f) {

                $name = (string)$f['name'];
                $source_id = (string)$f['source_id'];
                $source_model = (string)$f['source_model'];
                $required = (string)$f['required'];
                $message = (string)$f['message'];

                try {
                    $this->_validateForeignKey($name, $source_id,
                        $source_model, $required, $message);

                } catch (Exception $e) {
                    $valid = false;
                    if (Services::Configuration()->get('debug', 0) == 1) {
                        debug(' ');
                        debug('MolajoUpdateController::_filter_and_validate Foreign Key Failed');
                        debug(Services::Language()->translate($e->getMessage()) . ' ' . $f['name']);
                    }
                    Services::Message()
                        ->set(
                        $message =
                            Services::Language()->translate($e->getMessage()),
                        $type = MOLAJO_MESSAGE_TYPE_ERROR
                    );
                }
            }
        }
        if (Services::Configuration()->get('debug', 0) == 1) {
            debug(' ');
            debug('MolajoUpdateController::Validate FK::Success: ' . $valid);
        }

        return $valid;
    }

    /**
     * _validateHelperFunction
     *
     * @param $method
     *
     * @return  boolean
     * @since   1.0
     */
    protected function _validateHelperFunction($method)
    {
        $helperClass = 'Molajo'
            . ucfirst(substr($this->model->table_name, 3, 999))
            . 'ModelHelper';

        if (class_exists($helperClass)) {
        } else {
            $helperClass = 'MolajoModelHelper';
        }

        if (method_exists($helperClass, $method)) {
        } else {
            throw new Exception('VALIDATE_HELPER_FUNCTION_NOT_FOUND');
        }

        if (Services::Configuration()->get('debug', 0) == 1) {
            debug(' ');
            debug('MolajoUpdateController::_validateHelperFunction Helper Class: ' . $helperClass . ' Method: ' . $method);
        }

        $h = new $helperClass();
        $h->row = $this->model->row;
        $return = $h->$method();
        //get your helper class data back
        if ($return === false) {
            throw new Exception('VALIDATE_HELPER_FUNCTION');
        }
    }

    /**
     * _validateForeignKey
     *
     * @param $name
     * @param $source_id
     * @param $source_table
     * @param $required
     * @param $message
     *
     * @return  boolean
     * @since   1.0
     */
    protected function _validateForeignKey($name, $source_id, $source_model,
                                           $required, $message)
    {
        if (Services::Configuration()->get('debug', 0) == 1) {
            debug(' ');
            debug('MolajoUpdateController::_validateForeignKey Field: ' . $name . ' Value: ' . $this->model->row->$name . ' Source: ' . $source_id . ' Model: ' . $source_model . ' Required: ' . $required);
        }

        if ($this->model->row->$name == 0
            && $required == 0
        ) {
            return true;
        }

        if (isset($this->model->row->$name)) {

            $m = new $source_model ();

            $m->query->where($m->db->qn('id')
                . ' = ' . $m->db->q($this->model->row->$name));

            $value = $m->loadResult();

            if (empty($value)) {
            } else {
                return true;
            }
        } else {
            if ($required == 0) {
                return true;
            }
        }

        throw new Exception('VALIDATE_FOREIGN_KEY');
    }

    /**
     * _storeRelated
     *
     * Method to store a row in the related table
     *
     * @return  boolean  True on success.
     *
     * @return bool
     * @since   1.0
     */
    private function _storeRelated()
    {
        $asset = new MolajoAssetModel();

        $asset->asset_type_id = $this->model->table_name->asset_type_id;

        $this->asset_id = $asset->save();

        $asset->load();
        if ($asset->getError()) {
            $this->setError($asset->getError());
            return false;
        }

        //
        // View Access
        //
        //		$grouping = MolajoModel::getInstance('Grouping');

        //       if ((int) $this->access == 0) {
        //            $asset->content_table = $this->model->table_name;
        //            $this->asset_id = $asset->save();
        //        } else {
        //            $asset->load();
        //        }

        //        if ($asset->getError()) {
        //            $this->setError($asset->getError());
        //            return false;
        //       }

        //        if ((int) $this->asset_id == 0) {
        //			$this->query = $this->db->getQuery(true);
        //			$this->query->update($this->db->qn($this->model->table_name));
        //			$this->query->set('asset_id = '.(int) $this->asset_id);
        //			$this->query->where($this->db->qn($k).' = '.(int) $this->$k);
        //			$this->db->setQuery($this->query->__toString());

        //			if ($this->db->query()) {
        //            } else {
        //				$e = new MolajoException(Services::Language()->sprintf('MOLAJO_DB_ERROR_STORE_FAILED_UPDATE_ASSET_ID', $this->db->getErrorMsg()));
        //				$this->setError($e);
        //				return false;
        //			}
        //        }
    }

    /**
     * checkOut
     *
     * Method to check a row out if the necessary properties/fields exist.  To
     * prevent race conditions while editing rows in a database, a row can be
     * checked out if the fields 'checked_out' and 'checked_out_time' are available.
     * While a row is checked out, any attempt to store the row by a user other
     * than the one who checked the row out should be held until the row is checked
     * in again.
     *
     * @param   integer  The Id of the user checking out the row.
     * @param   mixed    An optional primary key value to check out.  If not set
     *                    the instance property value is used.
     * @return  boolean  True on success.
     * @since   1.0
     */
    public function checkOut($userId, $pk = null)
    {
        // If there is no checked_out or checked_out_time field, just return true.
        if (property_exists($this, 'checked_out')
            && property_exists($this, 'checked_out_time')
        ) {
        } else {
            return true;
        }

        // Initialise variables.
        $k = $this->primary_key;
        $pk = (is_null($pk)) ? $this->$k : $pk;

        // If no primary key is given, return false.
        if ($pk === null) {
            $e = new MolajoException(Services::Language()->_('MOLAJO_DB_ERROR_NULL_PRIMARY_KEY'));
            $this->setError($e);
            return false;
        }

        // Get the current time in MySQL format.
        $time = Services::Date()->toSql();

        // Check the row out by primary key.
        $this->query = $this->db->getQuery(true);
        $this->query->update($this->model->table_name);
        $this->query->set($this->db->qn('checked_out') . ' = ' . (int)$userId);
        $this->query->set($this->db->qn('checked_out_time') . ' = ' . $this->db->q($time));
        $this->query->where($this->primary_key . ' = ' . $this->db->q($pk));
        $this->db->setQuery($this->query->__toString());

        if ($this->db->query()) {
        } else {
            $e = new MolajoException(Services::Language()->sprintf('MOLAJO_DB_ERROR_CHECKOUT_FAILED', get_class($this), $this->db->getErrorMsg()));
            $this->setError($e);
            return false;
        }

        // Set table values in the object.
        $this->checked_out = (int)$userId;
        $this->checked_out_time = $time;

        return true;
    }

    /**
     * checkIn
     *
     * Method to check a row in if the necessary properties/fields exist.  Checking
     * a row in will allow other users the ability to edit the row.
     *
     * @param   mixed    An optional primary key value to check out.  If not set
     *                    the instance property value is used.
     * @return  boolean  True on success.
     * @since   1.0
     */
    public function checkIn($pk = null)
    {
        // If there is no checked_out or checked_out_time field, just return true.
        if (property_exists($this, 'checked_out')
            && property_exists($this, 'checked_out_time')
        ) {
        } else {
            return true;
        }

        // Initialise variables.
        $k = $this->primary_key;
        $pk = (is_null($pk)) ? $this->$k : $pk;

        // If no primary key is given, return false.
        if ($pk === null) {
            $e = new MolajoException(Services::Language()->_('MOLAJO_DB_ERROR_NULL_PRIMARY_KEY'));
            $this->setError($e);
            return false;
        }

        // Check the row in by primary key.
        $this->query = $this->db->getQuery(true);
        $this->query->update($this->model->table_name);
        $this->query->set($this->db->qn('checked_out') . ' = 0');
        $this->query->set($this->db->qn('checked_out_time') . ' = ' . $this->db->q($this->db->getNullDate()));
        $this->query->where($this->primary_key . ' = ' . $this->db->q($pk));
        $this->db->setQuery($this->query->__toString());

        // Check for a database error.
        if ($this->db->query()) {
        } else {
            $e = new MolajoException(Services::Language()->sprintf('MOLAJO_DB_ERROR_CHECKIN_FAILED', get_class($this), $this->db->getErrorMsg()));
            $this->setError($e);
            return false;
        }

        // Set table values in the object.
        $this->checked_out = 0;
        $this->checked_out_time = '';

        return true;
    }

    /**
     * getNextOrder
     *
     * Method to get the next ordering value for a group of rows defined by an SQL WHERE clause.
     * This is useful for placing a new item last in a group of items in the table.
     *
     * @param   string   WHERE clause to use for selecting the MAX(ordering) for the table.
     * @return  mixed    Boolean false an failure or the next ordering value as an integer.
     * @since   1.0
     */
    public function getNextOrder($where = '')
    {
        // If there is no ordering field set an error and return false.
        if (property_exists($this, 'ordering')) {
        } else {
            $e = new MolajoException(Services::Language()->sprintf('MOLAJO_DB_ERROR_CLASS_DOES_NOT_SUPPORT_ORDERING', get_class($this)));
            $this->setError($e);
            return false;
        }

        // Get the largest ordering value for a given where clause.
        $this->query = $this->db->getQuery(true);
        $this->query->select('MAX(ordering)');
        $this->query->from($this->model->table_name);

        if ($where) {
            $this->query->where($where);
        }

        $this->db->setQuery($this->query->__toString());
        $max = (int)$this->db->loadResult();

        // Check for a database error.
        if ($this->db->getErrorNum()) {
            $e = new MolajoException(
                Services::Language()->sprintf('MOLAJO_DB_ERROR_GET_NEXT_ORDER_FAILED', get_class($this), $this->db->getErrorMsg())
            );
            $this->setError($e);

            return false;
        }

        // Return the largest ordering value + 1.
        return ($max + 1);
    }

    /**
     * reorder
     *
     * Method to compact the ordering values of rows in a group of rows
     * defined by an SQL WHERE clause.
     *
     * @param   string   WHERE clause to use for limiting the selection of rows to
     *                    compact the ordering values.
     * @return  mixed    Boolean true on success.
     * @since   1.0
     * @link    http://docs.molajo.org/MolajoModel/reorder
     */
    public function reorder($where = '')
    {
        // If there is no ordering field set an error and return false.
        if (property_exists($this, 'ordering')) {
        } else {
            $e = new MolajoException(Services::Language()->sprintf('MOLAJO_DB_ERROR_CLASS_DOES_NOT_SUPPORT_ORDERING', get_class($this)));
            $this->setError($e);
            return false;
        }

        // Initialise variables.
        $k = $this->primary_key;

        // Get the primary keys and ordering values for the selection.
        $this->query = $this->db->getQuery(true);
        $this->query->select($this->primary_key . ', ordering');
        $this->query->from($this->model->table_name);
        $this->query->where('ordering >= 0');
        $this->query->order('ordering');

        // Setup the extra where and ordering clause data.
        if ($where) {
            $this->query->where($where);
        }

        $this->db->setQuery($this->query->__toString());
        $rows = $this->db->loadObjectList();

        // Check for a database error.
        if ($this->db->getErrorNum()) {
            $e = new MolajoException(Services::Language()->sprintf('MOLAJO_DB_ERROR_REORDER_FAILED', get_class($this), $this->db->getErrorMsg()));
            $this->setError($e);
            return false;
        }

        // Compact the ordering values.
        foreach ($rows as $i => $row) {
            // Make sure the ordering is a positive integer.
            if ($row->ordering >= 0) {
                // Only update rows that are necessary.
                if ($row->ordering == $i + 1) {
                } else {
                    // Update the row ordering field.
                    $this->query = $this->db->getQuery(true);
                    $this->query->update($this->model->table_name);
                    $this->query->set('ordering = ' . ($i + 1));
                    $this->query->where($this->primary_key . ' = ' . $this->db->q($row->$k));
                    $this->db->setQuery($this->query->__toString());

                    // Check for a database error.
                    if ($this->db->query()) {
                    } else {
                        $e = new MolajoException(
                            Services::Language()->sprintf(
                                'MOLAJO_DB_ERROR_REORDER_UPDATE_ROW_FAILED', get_class($this), $i, $this->db->getErrorMsg()
                            )
                        );
                        $this->setError($e);

                        return false;
                    }
                }
            }
        }

        return true;
    }

    /**
     * move
     *
     * Method to move a row in the ordering sequence of a group of rows defined by an SQL WHERE clause.
     * Negative numbers move the row up in the sequence and positive numbers move it down.
     *
     * @param   integer  The direction and magnitude to move the row in the ordering sequence.
     * @param   string   WHERE clause to use for limiting the selection of rows to compact the
     *                    ordering values.
     * @return  mixed    Boolean true on success.
     * @since   1.0
     * @link    http://docs.molajo.org/MolajoModel/move
     */
    public function move($delta, $where = '')
    {
        // If there is no ordering field set an error and return false.
        if (property_exists($this, 'ordering')) {
        } else {
            $e = new MolajoException(Services::Language()->sprintf('MOLAJO_DB_ERROR_CLASS_DOES_NOT_SUPPORT_ORDERING', get_class($this)));
            $this->setError($e);
            return false;
        }

        // If the change is none, do nothing.
        if (empty($delta)) {
            return true;
        }

        // Initialise variables.
        $k = $this->primary_key;
        $row = null;
        $this->query = $this->db->getQuery(true);

        // Select the primary key and ordering values from the table.
        $this->query->select($this->primary_key . ', ordering');
        $this->query->from($this->model->table_name);

        // If the movement delta is negative move the row up.
        if ($delta < 0) {
            $this->query->where('ordering < ' . (int)$this->ordering);
            $this->query->order('ordering DESC');
        }
        // If the movement delta is positive move the row down.
        elseif ($delta > 0) {
            $this->query->where('ordering > ' . (int)$this->ordering);
            $this->query->order('ordering ASC');
        }

        // Add the custom WHERE clause if set.
        if ($where) {
            $this->query->where($where);
        }

        // Select the first row with the criteria.
        $this->db->setQuery($this->query, 0, 1);
        $row = $this->db->loadObject();

        // If a row is found, move the item.
        if (empty($row)) {

            // Update the ordering field for this instance.
            $this->query = $this->db->getQuery(true);
            $this->query->update($this->model->table_name);
            $this->query->set('ordering = ' . (int)$this->ordering);
            $this->query->where($this->primary_key . ' = ' . $this->db->q($this->$k));
            $this->db->setQuery($this->query->__toString());

            // Check for a database error.
            if ($this->db->query()) {
            } else {
                $e = new MolajoException(Services::Language()->sprintf('MOLAJO_DB_ERROR_MOVE_FAILED', get_class($this), $this->db->getErrorMsg()));
                $this->setError($e);

                return false;
            }

        } else {
            // Update the ordering field for this instance to the row's ordering value.
            $this->query = $this->db->getQuery(true);
            $this->query->update($this->model->table_name);
            $this->query->set('ordering = ' . (int)$row->ordering);
            $this->query->where($this->primary_key . ' = ' . $this->db->q($this->$k));
            $this->db->setQuery($this->query->__toString());

            // Check for a database error.
            if ($this->db->query()) {
            } else {
                $e = new MolajoException(Services::Language()->sprintf('MOLAJO_DB_ERROR_MOVE_FAILED', get_class($this), $this->db->getErrorMsg()));
                $this->setError($e);

                return false;
            }

            // Update the ordering field for the row to this instance's ordering value.
            $this->query = $this->db->getQuery(true);
            $this->query->update($this->model->table_name);
            $this->query->set('ordering = ' . (int)$this->ordering);
            $this->query->where($this->primary_key . ' = ' . $this->db->q($row->$k));
            $this->db->setQuery($this->query->__toString());

            // Check for a database error.
            if ($this->db->query()) {
            } else {
                $e = new MolajoException(Services::Language()->sprintf('MOLAJO_DB_ERROR_MOVE_FAILED', get_class($this), $this->db->getErrorMsg()));
                $this->setError($e);

                return false;
            }

            // Update the instance value.
            $this->ordering = $row->ordering;
        }

        return true;
    }

    /**
     * publish
     *
     * Method to set the publishing state for a row or list of rows in the database
     * table.  The method respects checked out rows by other users and will attempt
     * to checkin rows that it can after adjustments are made.
     *
     * @param   mixed    An optional array of primary key values to update.  If not
     *                    set the instance property value is used.
     * @param   integer The publishing state. eg. [0 = unpublished, 1 = published]
     * @param   integer The user id of the user performing the operation.
     * @return  boolean  True on success.
     * @since   1.0
     */
    public function publish($pks = null, $state = 1, $userId = 0)
    {
        // Initialise variables.
        $k = $this->primary_key;

        // Sanitize input.
        JArrayHelper::toInteger($pks);
        $userId = (int)$userId;
        $state = (int)$state;

        // If there are no primary keys set check to see if the instance key is set.
        if (empty($pks)) {
            if ($this->$k) {
                $pks = array($this->$k);
            }
            // Nothing to set publishing state on, return false.
            else {
                $e = new MolajoException(Services::Language()->_('MOLAJO_DB_ERROR_NO_ROWS_SELECTED'));
                $this->setError($e);

                return false;
            }
        }

        // Update the publishing state for rows with the given primary keys.
        $this->query = $this->db->getQuery(true);
        $this->query->update($this->model->table_name);
        $this->query->set('published = ' . (int)$state);

        // Determine if there is checkin support for the table.
        if (property_exists($this, 'checked_out') || property_exists($this, 'checked_out_time')) {
            $this->query->where('(checked_out = 0 OR checked_out = ' . (int)$userId . ')');
            $checkin = true;

        } else {
            $checkin = false;
        }

        // Build the WHERE clause for the primary keys.
        $this->query->where($k . ' = ' . implode(' OR ' . $k . ' = ', $pks));

        $this->db->setQuery($this->query->__toString());

        // Check for a database error.
        if ($this->db->query()) {
        } else {
            $e = new MolajoException(Services::Language()->sprintf('MOLAJO_DB_ERROR_PUBLISH_FAILED', get_class($this), $this->db->getErrorMsg()));
            $this->setError($e);
            return false;
        }

        // If checkin is supported and all rows were adjusted, check them in.
        if ($checkin && (count($pks) == $this->db->getAffectedRows())) {
            // Checkin the rows.
            foreach ($pks as $pk)
            {
                $this->checkin($pk);
            }
        }

        // If the MolajoModel instance value is in the list of primary keys that were set, set the instance.
        if (in_array($this->$k, $pks)) {
            $this->published = $state;
        }

        $this->setError('');
        return true;
    }

    /**
     * canDelete
     *
     * Generic check for whether dependancies exist for this object in the database schema
     *
     * Can be overloaded/supplemented by the child class
     *
     * @deprecated
     * @param   mixed    An optional primary key value check the row for.  If not
     *                    set the instance property value is used.
     * @param   array    An optional array to compiles standard joins formatted like:
     *                    [label => 'Label', name => 'table name' , idfield => 'field', joinfield => 'field']
     * @return  boolean  True on success.
     * @since   1.0
     */
    public function canDelete($pk = null, $joins = null)
    {
        // Initialise variables.
        $k = $this->primary_key;
        $pk = (is_null($pk)) ? $this->$k : $pk;

        // If no primary key is given, return false.
        if ($pk === null) {
            return false;
        }

        if (is_array($joins)) {
            // Get a query object.
            $this->query = $this->db->getQuery(true);

            // Setup the basic query.
            $this->query->select($this->db->qn($this->primary_key));
            $this->query->from($this->db->qn($this->model->table_name));
            $this->query->where($this->db->qn($this->primary_key) . ' = ' . $this->db->q($this->$k));
            $this->query->group($this->db->qn($this->primary_key));

            // For each join add the select and join clauses to the query object.
            foreach ($joins as $table) {
                $this->query->select('COUNT(DISTINCT ' . $table['idfield'] . ') AS ' . $table['idfield']);
                $this->query->join('LEFT', $table['name'] . ' ON ' . $table['joinfield'] . ' = ' . $k);
            }

            // Get the row object from the query.
            $this->db->setQuery((string)$this->query, 0, 1);
            $row = $this->db->loadObject();

            // Check for a database error.
            if ($this->db->getErrorNum()) {
                $this->setError($this->db->getErrorMsg());

                return false;
            }

            $msg = array();
            $i = 0;

            foreach ($joins as $table) {
                $k = $table['idfield'] . $i;
                if ($row->$k) {
                    $msg[] = Services::Language()->_($table['label']);
                }

                $i++;
            }

            if (count($msg)) {
                $this->setError("noDeleteRecord" . ": " . implode(', ', $msg));

                return false;
            }
            else {
                return true;
            }
        }

        return true;
    }
}
