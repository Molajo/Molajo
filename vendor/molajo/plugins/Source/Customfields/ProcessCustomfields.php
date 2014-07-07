<?php
/**
 * Determine whether or not the Customfields Plugin should be processed
 *
 * @package    Molajo
 * @copyright  2014 Amy Stephen. All rights reserved.
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 */
namespace Molajo\Plugins\Customfields;

use Molajo\Plugins\ReadEventPlugin;

/**
 * Determine whether or not the Customfields Plugin should be processed
 *
 * @package  Molajo
 * @license  http://www.opensource.org/licenses/mit-license.html MIT License
 * @since    1.0
 */
abstract class ProcessCustomFields extends ReadEventPlugin
{
    /**
     * Should plugin be executed?
     *
     * @return  boolean
     * @since   1.0.0
     */
    protected function processCustomfieldsPlugin()
    {
        $methods = array(
            'verifyRow',
            'verifyQueryObject',
            'verifyGetCustomFields',
            'verifyCustomFieldGroups'
        );

        foreach ($methods as $method) {
            if ($this->$method() === false) {
                return false;
            }
        }

        return true;
    }

    /**
     * Verify Row
     *
     * @return  boolean
     * @since   1.0.0
     */
    protected function verifyRow()
    {
        if (count($this->row) === 0) {
            return false;
        }

        return true;
    }

    /**
     * Verify Query Object
     *
     * @return  boolean
     * @since   1.0.0
     */
    protected function verifyQueryObject()
    {
        if ($this->model_registry['query_object'] === 'result') {
            return false;
        }

        return true;
    }

    /**
     * Verify Custom Fields are requested
     *
     * @return  boolean
     * @since   1.0.0
     */
    protected function verifyGetCustomFields()
    {
        if (isset($this->model_registry['get_customfields'])) {
        } else {
            return false;
        }

        if ((int)$this->model_registry['get_customfields'] === 0) {
            return false;
        }

        return true;
    }

    /**
     * Process Customfield Groups
     *
     * @return  boolean
     * @since   1.0.0
     */
    public function verifyCustomFieldGroups()
    {
        $customfieldgroups = $this->model_registry['customfieldgroups'];

        if (is_array($customfieldgroups) && count($customfieldgroups) > 0) {
            return true;
        }

        return false;
    }
}
