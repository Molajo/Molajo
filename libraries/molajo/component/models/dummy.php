<?php
/**
 * @package     Molajo
 * @subpackage  Dummy Model
 * @copyright   Copyright (C) 2011 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die;

/**
 * MolajoModelDummy
 *
 * Component Model for Dummy Views
 *
 * @package	    Molajo
 * @subpackage	Model
 * @since 1.0
 */
class MolajoModelDummy extends JModel
{
    /**
     * $request
     *
     * @var		array
     * @since	1.0
     */
    public $request = array();

    /**
     * $params
     *
     * @var		string
     * @since	1.0
     */
    public $params = array();

    /**
     * $items
     *
     * @var		string
     * @since	1.0
     */
    public $items = array();

    /**
     * $pagination
     *
     * @var		string
     * @since	1.0
     */
    public $pagination = array();

    /**
     * $context
     *
     * @var		string
     * @since	1.0
     */
    public $context = null;

    /**
     * populateState
     *
     * Method to auto-populate the model state.
     *
     * @return	void
     * @since	1.0
     */
    protected function populateState ()
    {
        $this->context = strtolower($this->request['option'].'.'.$this->getName()).'.'.$this->request['layout'];
    }

    /**
     * getRequest
     *
     * @return	array	An empty array
     *
     * @since	1.0
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     * getParams
     *
     * @return	array	An empty array
     *
     * @since	1.0
     */
    public function getParams()
    {
        return $this->params;
    }
    /**
     * getItems
     *
     * @return	array	An empty array
     *
     * @since	1.0
     */
    public function getItems()
    {
        return $this->items;
    }

    /**
     * getPagination
     *
     * @return	array	An empty array
     *
     * @since	1.0
     */
    public function getPagination ()
    {
        return $this->pagination;
    }
}