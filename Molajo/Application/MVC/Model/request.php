<?php
/**
 * @package     Molajo
 * @subpackage  Model
 * @copyright   Copyright (C) 2012 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
namespace Molajo\Application\MVC\Model;

defined('MOLAJO') or die;

/**
 * Request
 *
 * Model returns data that was saved in the Controller Display
 *  method for the primary content (the extension determined in
 *  Molajo Request.)
 *
 * @package     Molajo
 * @subpackage  Model
 * @since       1.0
 */
Class RequestModel extends DisplayModel
{
    /**
     * __construct
     *
     * Constructor.
     *
     * @param  $id
     * @since  1.0
     */
    public function __construct($id = null)
    {
        $this->name = get_class($this);

        return parent::__construct($id);
    }

    /**
     * getData
     *
     * @return    array
     *
     * @since    1.0
     */
    public function getData()
    {
        /** input for events is stored in the task request object */
        $this->query_results = Molajo::Request()->get('query_rowset');
        return $this->query_results;
    }

    /**
     * getPagination
     *
     * @return    array
     *
     * @since    1.0
     */
    public function getPagination()
    {
        /** input for events is stored in the task request object */
        $this->pagination = Molajo::Request()->get('query_pagination');
        return $this->pagination;
    }

    /**
     * getState
     *
     * @return    array
     *
     * @since    1.0
     */
    public function getState()
    {
        /** input for events is stored in the task request object */
        $this->state = Molajo::Request()->get('query_state');
        return $this->state;
    }
}
