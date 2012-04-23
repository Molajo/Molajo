<?php
/**
 * @package   Molajo
 * @subpackage  Module
 * @copyright 2012 Amy Stephen. All rights reserved.
 * @license   GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
namespace Molajo\Extension\Trigger;

defined('MOLAJO') or die;

class DisplayAdapter
{
    /**
     * Type
     *
     * @var    string
     * @since  1.0
     */
    protected $_type = 'display';

    /**
     * Parameters
     *
     * @var    object
     * @since  1.0
     */
    protected $_parameters = array();

    /**
     * Listeners
     *
     * @var    object
     * @since  1.0
     */
    protected $_listeners = array();

    /**
     * Events
     *
     * @var    object
     * @since  1.0
     */
    protected $_events = array();

    /**
     * Class constructor.
     *
     * @param  mixed   $input
     * @param  mixed   $config
     * @param  object  $_appQueryResults
     *
     * @return  null
     * @since   1.0
     */
    public function __construct(JRegistry $parameters = null)
    {
        if ($parameters instanceof JRegistry) {
            $this->_parameters = $parameters;
        } else {
            $this->_parameters = Services::Registry()->initialise();
        }

        return;
    }

    /**
     * load
     *
     * Load the application.
     *
     * @return  mixed
     * @since   1.0
     */
    public function load()
    {
        MolajoHelperListeners::get();
    }

    public function contentPrepare()
    {
    }

    public function contentBeforeDisplay()
    {
    }

    public function contentAfterDisplay($request, $data, $parameters, $limitstart)
    {
        $app = JFactory::getApplication();

        return '';
    }


    /**
     * contentBeforeChangeState
     *
     * @param    object  Request object for the current extension
     * @param    object    Array of data elements for the Item to be deleted
     * @return    boolean
     * @since    1.6
     */
    public function contentBeforeChangeState($request, $data, $parameters)
    {
        return true;
    }

    /**
     * contentAfterChangeState
     *
     * @param    object  Request object for the current extension
     * @param    object    Array of data elements for the Item just deleted
     *
     * @return    boolean
     * @since    1.6
     */
    public function contentAfterChangeState($request, $data, $parameters)
    {
        return true;
    }

    /**
     * contentBeforeInsert
     *
     * @param    object  Request object for the current extension
     * @param    object    Array of data elements for the Item to be deleted
     * @return    boolean
     * @since    1.6
     */
    public function contentBeforeInsert($request, $data, $parameters)
    {
        return true;
    }

    /**
     * contentAfterInsert
     *
     * @param    object  Request object for the current extension
     * @param    object    Array of data elements for the Item just deleted
     *
     * @return    boolean
     * @since    1.6
     */
    public function contentAfterInsert($request, $data, $parameters)
    {
        return true;
    }

    /**
     * contentBeforeUpdate
     *
     * @param    object  Request object for the current extension
     * @param    object    Array of data elements for the Item to be deleted
     * @return    boolean
     * @since    1.6
     */
    public function contentBeforeUpdate($request, $data, $parameters)
    {
        return true;
    }

    /**
     * contentAfterUpdate
     *
     * @param    object  Request object for the current extension
     * @param    object    Array of data elements for the Item just deleted
     *
     * @return    boolean
     * @since    1.6
     */
    public function contentAfterUpdate($request, $data, $parameters)
    {
        return true;
    }

    /**
     * contentBeforeDelete
     *
     * @param    object  Request object for the current extension
     * @param    object    Array of data elements for the Item to be deleted
     * @return    boolean
     * @since    1.6
     */
    public function contentBeforeDelete($request, $data, $parameters)
    {
        return true;
    }

    /**
     * contentAfterDelete
     *
     * @param    object  Request object for the current extension
     * @param    object    Array of data elements for the Item just deleted
     *
     * @return    boolean
     * @since    1.6
     */
    public function contentAfterDelete($request, $data, $parameters)
    {
        return true;
    }
}

