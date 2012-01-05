<?php
/**
 * @package     Molajo
 * @subpackage  Header
 * @copyright   Copyright (C) 2012 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die;

/**
 * Dummy
 *
 * Does not process data
 *
 * @package     Molajo
 * @subpackage  Model
 * @since       1.0
 */
class HeaderModelModule extends MolajoModelDisplay
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
        //$this->context = strtolower($this->requestArray['option'] . '.' . $this->getName()) . '.' . $this->requestArray['view'];
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
        $rows = array();
        $tmpobj = new JObject();
        $tmpobj->set('title', MolajoController::getApplication()->get('site_title', 'Molajo'));
        $rows[] = $tmpobj;
        $this->items = $rows;
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