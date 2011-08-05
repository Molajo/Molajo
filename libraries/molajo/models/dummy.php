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
    public $request = null;

    /**
     * $params
     *
     * @var		string
     * @since	1.0
     */
    protected $params = null;

    /**
     * $context
     *
     * @var		string
     * @since	1.0
     */
    protected $context = null;

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
        $this->params = $this->request['params'];
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
        return array();
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
        return array();
    }
}