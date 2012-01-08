<?php
/**
 * @package     Molajo
 * @subpackage  Messages Model
 * @copyright   Copyright (C) 2012 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die;

/**
 * Messages
 *
 * Does not process data
 *
 * @package     Molajo
 * @subpackage  Model
 * @since       1.0
 */
class MolajoModelMessages extends MolajoModel
{
    /**
     * $request
     *
     * @var        array
     * @since    1.0
     */
    public $requestArray = array();

    /**
     * $parameters
     *
     * @var        string
     * @since    1.0
     */
    public $parameters = array();

    /**
     * $items
     *
     * @var        string
     * @since    1.0
     */
    public $items = array();

    /**
     * $pagination
     *
     * @var        string
     * @since    1.0
     */
    public $pagination = array();

    /**
     * $context
     *
     * @var        string
     * @since    1.0
     */
    public $context = null;

    /**
     * $task
     *
     * @var        string
     * @since    1.0
     */
    public $task = null;

    /**
     * populateState
     *
     * Method to auto-populate the model state.
     *
     * @return    void
     * @since    1.0
     */
    protected function populateState()
    {
        $this->context = strtolower('Messages' . '.' . $this->getName()) . '.' . $this->requestArray['view'];
    }

    /**
     * getRequest
     *
     * @return    array    An empty array
     *
     * @since    1.0
     */
    public function getRequest()
    {
        return $this->requestArray;
    }

    /**
     * getParameters
     *
     * @return    array    An empty array
     *
     * @since    1.0
     */
    public function getParameters()
    {
        return $this->parameters;
    }

    /**
     * getItems
     *
     * @return    array    An empty array
     *
     * @since    1.0
     */
    public function getItems()
    {
        $this->items = array();

        $messages = MolajoController::getApplication()->getMessages();

        foreach($messages as $message) {
            $tempObject = new JObject();
            $tempObject->set('title', $message['type']);
            $tempObject->set('type', $message['type']);
            $tempObject->set('content_text', $message['message']);
            $this->items[] = $tempObject;
        }

        return $this->items;
    }

    /**
     * getPagination
     *
     * @return    array    An empty array
     *
     * @since    1.0
     */
    public function getPagination()
    {
        return $this->pagination;
    }
}