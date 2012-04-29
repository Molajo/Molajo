<?php
/**
 * @package   Molajo
 * @subpackage  Model
 * @copyright 2012 Amy Stephen. All rights reserved.
 * @license   GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
namespace Molajo\MVC\Model;

defined('MOLAJO') or die;

/**
 * Request
 *
 * Model returns data that was saved in the Controller Display
 *  method for the primary content (the extension determined in
 *  Molajo Request.)
 *
 * @package   Molajo
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
    public function __construct()
    {
        $this->class_name = get_class($this);

        return parent::__construct();
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
        $this->query_results = Services::Registry()->get('Request', 'query_resultset');
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
        $this->pagination = Services::Registry()->get('Request', 'query_pagination');
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
        $this->state = Services::Registry()->get('Request', 'query_state');
        return $this->state;
    }
}
