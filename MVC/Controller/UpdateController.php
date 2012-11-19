<?php
/**
 * @package    Molajo
 * @copyright  2012 Individual Molajo Contributors. All rights reserved.
 * @license    GNU GPL v 2, or later and MIT, see License folder
 */
namespace Molajo\MVC\Controller;

use Molajo\Service\Services;

defined('MOLAJO') or die;

/**
 * Update List Controller
 *
 * Handles the standard list actions, typically applied to multiple items
 *
 * Tasks processed:
 * - Order: reorder, orderup, orderdown, saveorder
 * - Checkin: checkin and checkout
 * - Sticky: sticky and unsticky
 * - Feature: feature and unfeature
 * - State: archive, publish, unpublish, spam, trash (Note: version is automatic with save and delete)
 *
 * @package       Molajo
 * @subpackage  Controller
 * @since       1.0
 */
class UpdateController extends Controller
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
     * copy, move or delete -> processFeatureChange -> processItems
     *
     * call processItems which loops through the ids and calls saveItemBatch or deleteItemBatch
     *
     * saveItemBatch prepares the copy or move data and calls saveItem
     * deleteItemBatch handles the delete processing
     *
     * @return Boolean
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
     * @return Boolean
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
     * @return Boolean
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
     * @return Boolean
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
     * @return Boolean
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
     * @return Boolean
     * @since    1.0
     */
    public function processItems($column = null)
    {
        /** security token **/
        JRequest::checkToken() or die;

        /** initialise */
        $results = $this->initialise($this->data['action']);
        if ($results === false) {
            return $this->redirectClass->setSuccessIndicator(false);
        }

        /** action **/
        $action = $this->getTask();

        /** initialisation */
        $results = parent::initialise($action);
        if ($results === false) {
            return $this->redirectClass->setSuccessIndicator(false);
        }

        /** context **/
        $context = $this->data['option'] . '.' . JRequest::getCmd('view') . '.' . JRequest::getCmd('view') . '.' . $action;

        /** ids **/
        $idArray = JRequest::getVar('cid', array(), '', 'array');
        JArrayHelper::toInteger($idArray);
        if (empty($idArray)) {
            $this->redirectClass->setRedirectMessage(Services::Language()->translate('BATCH_SELECT_ITEMS_TASK'));
            $this->redirectClass->setRedirectMessageType('message');
            $this->redirectClass->setSuccessIndicator(false);
        }

        /** target category **/
        if ($action == 'copy' || $action == 'delete') {
            $this->batch_category_id = JRequest::getInt('batch_category_id');
            if ((int) $this->batch_category_id == 0) {
                $this->redirectClass->setRedirectMessage(Services::Language()->translate('BATCH_SELECT_CATEGORY_FOR_MOVE_OR_COPY'));
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
            if ((int) $this->table->id == 0) {
                $errorFoundForItem = true;
            }

            /** edit: permissions **/
            if ($errorFoundForItem === true) {
            } else {

                /** View access (copy or move will be checked, too) **/
                $results = $this->checkTaskAuthorisation(ACTION_VIEW);
                if ($results === false) {
                    $errorFoundForItem = true;
                }
            }

            /** edit: checked out? **/
            if ($errorFoundForItem === true) {
            } else {
                /** Check In Item **/
                if ($action == 'copy' || $action == 'checkin') {
                } else {
                    $results = $this->checkoutItem();
                    if ($results === false) {
                        $errorFoundForItem = true;
                    }
                }
            }

            /** action: do it **/
            if ($errorFoundForItem === true) {
            } else {

                $results = $this->processItem($action, $column);
                if ($results === false) {
                    $errorFoundForItem = true;
                }
            }

            /** check in **/
            if ($errorFoundForItem === true) {
            } else {
                /** Check In Item **/
                if ($action == 'copy' || $action == 'checkin') {
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
            $this->redirectClass->setRedirectMessage(Services::Language()->plural('N_ITEMS_' . strtoupper($action), count($idArray)));
            $this->redirectClass->setRedirectMessageType(Services::Language()->translate('message'));

            return $this->redirectClass->setSuccessIndicator(true);
        } else {
            $this->redirectClass->setRedirectMessage(Services::Language()->translate('ERROR_PROCESSING_ITEMS'));
            $this->redirectClass->setRedirectMessageType(Services::Language()->translate('warning'));

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
     * @return Boolean
     * @since    1.0
     */
    public function processItem($action, $column = null)
    {
        /** full row **/
        if ($action == 'copy' || $action == 'move' || $action == 'delete') {
            $results = parent::saveItemBatch($action);
            if ($results === false) {
                return false;
            }

            /** checking */
        } elseif ($action == 'checkin') {
            $results = parent::checkinItem();
            if ($results === false) {
                return false;
            }

        } else {
            /** single column value change (state, featured, sticky) **/
            $previous = $this->model->$action;
            $newValue = $this->model->$action($this->mvc['id']);
            if ($newValue === false) {
                return false;
            }
        }

        /** Molajo_Note: Testing added to ensure state change before onContentChangeState event is plugined  **/
        if ($previous == $newValue || $this->isNew) {
        } else {
            /** Event: onContentChangeState **/
            $this->dispatcher->plugin('onContentChangeState', array($context, $this->mvc['id'], $validData->state));
        }

        if ($column == 'state') {
            if ($newValue) {
                $event = 'onContentChange' . ucfirst(strtolower($column));
                $this->dispatcher->plugin($event, array($context, $this->mvc['id'], $newValue));
            }
        }

        return true;
    }
}
