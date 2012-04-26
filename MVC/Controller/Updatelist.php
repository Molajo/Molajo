<?php
/**
 * @package   Molajo
 * @copyright 2012 Amy Stephen. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */
namespace Molajo\MVC\Controller;

use Molajo\Service;

defined('MOLAJO') or die;

/**
 * Update List Controller
 *
 * Handles the standard list tasks, typically applied to multiple items
 *
 * Tasks processed:
 * - Order: reorder, orderup, orderdown, saveorder
 * - Checkin: checkin and checkout
 * - Sticky: sticky and unsticky
 * - Feature: feature and unfeature
 * - State: archive, publish, unpublish, spam, trash (Note: version is automatic with save and delete)
 *
 * @package   Molajo
 * @subpackage  Controller
 * @since       1.0
 */
class UpdatelistController extends UpdateController
{
    /**
     * Tasks: reorder, orderup, orderdown, saveorder
     */
    public function reorder()
    {
        return $this->orderItems();
    }

    public function orderup()
    {
        return $this->orderItems();
    }

    public function orderdown()
    {
        return $this->orderItems();
    }

    public function saveorder()
    {
        return $this->orderItems();
    }

    public function orderItems()
    {
        return;
    }

    /**
     * copy, move or delete -> processFeatureChange -> processIems
     *
     * call processItems which loops through the ids and calls saveItemBatch or deleteItemBatch
     *
     * saveItemBatch prepares the copy or move data and calls saveItem
     * deleteItemBatch handles the delete processing
     *
     * @return    Boolean
     * @since    1.0
     */
    public function copy()
    {
        return $this->processItems();
    }

    public function move()
    {
        return $this->processItems();
    }

    public function delete()
    {
        return $this->processItems();
    }

    /**
     * archive, publish, unpublish, spam, trash (state) -> processFeatureChange -> processIems
     *
     * call processStateChange which then calls processItems which loops through the ids
     *
     *
     * @return    Boolean
     * @since    1.0
     */
    public function archive()
    {
        return $this->processStateChange();
    }

    public function publish()
    {
        return $this->processStateChange();
    }

    public function unpublish()
    {
        return $this->processStateChange();
    }

    public function spam()
    {
        return $this->processStateChange();
    }

    public function trash()
    {
        return $this->processStateChange();
    }

    public function processStateChange()
    {
        return $this->processItems($column = 'state');
    }

    /**
     * feature, unfeature -> processFeatureChange -> processItems
     *
     * call processStateChange which then calls processItems which loops through the ids
     *
     *
     * @return    Boolean
     * @since    1.0
     */
    public function feature()
    {
        return $this->processFeatureChange();
    }

    public function unfeature()
    {
        return $this->processFeatureChange();
    }

    public function processFeatureChange()
    {
        return $this->processItems($column = 'featured');
    }

    /**
     * sticky, unsticky -> processStickyChange -> processItems
     *
     * call processStateChange which then calls processItems which loops through the ids
     *
     * @return    Boolean
     * @since    1.0
     */
    public function sticky()
    {
        return $this->processStickyChange();
    }

    public function unsticky()
    {
        return $this->processStickyChange();
    }

    public function processStickyChange()
    {
        return $this->processItems($column = 'stickied');
    }

    /**
     * checkin -> processItems
     *
     * call processStateChange which then calls processItems which loops through the ids
     *
     * @return    Boolean
     * @since    1.0
     */
    public function checkin()
    {
        return $this->processItems($column = 'checkin');
    }

    /**
     * processItems
     *
     * called by single item methods to loop through the ids and processed by processItem
     *
     * @return    Boolean
     * @since    1.0
     */
    public function processItems($column = null)
    {
        /** security token **/
        JRequest::checkToken() or die;

        /** initialise */
        $results = $this->initialise($this->data['task']);
        if ($results === false) {
            return $this->redirectClass->setSuccessIndicator(false);
        }

        /** task **/
        $task = $this->getTask();

        /** initialisation */
        $results = parent::initialise($task);
        if ($results === false) {
            return $this->redirectClass->setSuccessIndicator(false);
        }

        /** context **/
        $context = $this->data['option'] . '.' . JRequest::getCmd('view') . '.' . JRequest::getCmd('view') . '.' . $task;

        /** ids **/
        $idArray = JRequest::getVar('cid', array(), '', 'array');
        JArrayHelper::toInteger($idArray);
        if (empty($idArray)) {
            $this->redirectClass->setRedirectMessage(Service::Language()->translate('BATCH_SELECT_ITEMS_TASK'));
            $this->redirectClass->setRedirectMessageType('message');
            $this->redirectClass->setSuccessIndicator(false);
        }

        /** target category **/
        if ($task == 'copy' || $task == 'delete') {
            $this->batch_category_id = JRequest::getInt('batch_category_id');
            if ((int)$this->batch_category_id == 0) {
                $this->redirectClass->setRedirectMessage(Service::Language()->translate('BATCH_SELECT_CATEGORY_FOR_MOVE_OR_COPY'));
                $this->redirectClass->setRedirectMessageType('message');
                return $this->redirectClass->setSuccessIndicator(false);
            }
        }

        /** loop through ids **/
        $errorFoundForBatch = false;
        foreach ($idArray as $this->mvc['id']) {

            /** ok indicator **/
            $errorFoundForItem = false;

            /** set request object */
            JRequest::setVar('id', $this->mvc['id']);

            /** load row **/
            $this->table->reset();
            $this->table->load($this->mvc['id']);

            /** edit: valid id **/
            if ((int)$this->table->id == 0) {
                $errorFoundForItem = true;
            }

            /** edit: acl **/
            if ($errorFoundForItem === true) {
            } else {

                /** View access (copy or move will be checked, too) **/
                $results = $this->checkTaskAuthorisation('display');
                if ($results === false) {
                    $errorFoundForItem = true;
                }
            }

            /** edit: checked out? **/
            if ($errorFoundForItem === true) {
            } else {
                /** Check In Item **/
                if ($task == 'copy' || $task == 'checkin') {
                } else {
                    $results = $this->checkoutItem();
                    if ($results === false) {
                        $errorFoundForItem = true;
                    }
                }
            }

            /** task: do it **/
            if ($errorFoundForItem === true) {
            } else {

                $results = $this->processItem($task, $column);
                if ($results === false) {
                    $errorFoundForItem = true;
                }
            }

            /** check in **/
            if ($errorFoundForItem === true) {
            } else {
                /** Check In Item **/
                if ($task == 'copy' || $task == 'checkin') {
                } else {
                    $results = $this->checkinItem();
                    if ($results === false) {
                        $errorFoundForItem = true;
                    }
                }
            }

            /** failed **/
            if ($errorFoundForItem === true) {
                unset($idArray[$this->mvc['id']]);
                $errorFoundForBatch = true;
            }
        }

        /** Cache: clear cache **/
//        $results = $this->cleanCache();

        if ($errorFoundForBatch === false) {
            $this->redirectClass->setRedirectMessage(Service::Language()->plural('N_ITEMS_' . strtoupper($task), count($idArray)));
            $this->redirectClass->setRedirectMessageType(Service::Language()->translate('message'));
            return $this->redirectClass->setSuccessIndicator(true);
        } else {
            $this->redirectClass->setRedirectMessage(Service::Language()->translate('ERROR_PROCESSING_ITEMS'));
            $this->redirectClass->setRedirectMessageType(Service::Language()->translate('warning'));
            return $this->redirectClass->setSuccessIndicator(false);
        }
    }

    /**
     * processItem
     *
     * called by copy, move, and delete to loop through the ids and call either saveItemBatch or deleteItemBatch
     *
     * saveItemBatch prepares the copy or move data and calls saveItem
     * deleteItemBatch handles the delete processing
     *
     * @return    Boolean
     * @since    1.0
     */
    public function processItem($task, $column = null)
    {
        /** full row **/
        if ($task == 'copy' || $task == 'move' || $task == 'delete') {
            $results = parent::saveItemBatch($task);
            if ($results === false) {
                return false;
            }

            /** checking */
        } else if ($task == 'checkin') {
            $results = parent::checkinItem();
            if ($results === false) {
                return false;
            }

        } else {
            /** single column value change (state, featured, sticky) **/
            $previous = $this->model->$task;
            $newValue = $this->model->$task($this->mvc['id']);
            if ($newValue === false) {
                return false;
            }
        }

        /** Molajo_Note: Testing added to ensure state change before onContentChangeState event is triggered  **/
        if ($previous == $newValue || $this->isNew) {
        } else {
            /** Event: onContentChangeState **/
            $this->dispatcher->trigger('onContentChangeState', array($context, $this->mvc['id'], $validData->state));
        }

        if ($column == 'state') {
            if ($newValue) {
                $event = 'onContentChange' . ucfirst(strtolower($column));
                $this->dispatcher->trigger($event, array($context, $this->mvc['id'], $newValue));
            }
        }

        return true;
    }
}
