<?php
/**
 * Process Custom Fields
 *
 * @package    Molajo
 * @copyright  2014-2015 Amy Stephen. All rights reserved.
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 */
namespace Molajo\Plugins\Customfields;

/**
 * Process Custom Fields
 *
 * @package  Molajo
 * @license  http://www.opensource.org/licenses/mit-license.html MIT License
 * @since    1.0
 */
abstract class ProcessCustomFields extends Base
{
    /**
     * Should plugin be executed?
     *
     * @return  boolean
     * @since   1.0.0
     */
    protected function checkProcessPlugin()
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
     * Verify Row has fields
     *
     * @return  boolean
     * @since   1.0.0
     */
    protected function verifyRow()
    {
        foreach ($this->controller['row'] as $name => $value) {
            return true;
        }

        return false;
    }

    /**
     * Verify Query Object is not Result
     *
     * @return  boolean
     * @since   1.0.0
     */
    protected function verifyQueryObject()
    {
        if ($this->controller['model_registry']['query_object'] === 'result') {
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
        if (isset($this->controller['model_registry']['get_customfields'])) {
        } else {
            return false;
        }

        if ((int)$this->controller['model_registry']['get_customfields'] === 0) {
            return false;
        }

        return true;
    }

    /**
     * Verify Custom Field Groups are defined in customfieldgroups and exist in row
     *
     * @return  boolean
     * @since   1.0.0
     */
    protected function verifyCustomFieldGroups()
    {
        $customfieldgroups = $this->controller['model_registry']['customfieldgroups'];

        if (is_array($customfieldgroups) && count($customfieldgroups) > 0) {
        } else {
            return false;
        }

        foreach ($customfieldgroups as $group) {
            if (isset($this->controller['row']->$group)) {
            } else {
                unset($this->controller['model_registry']['customfieldgroups'][$group]);
            }
        }

        if (is_array($this->controller['model_registry']['customfieldgroups'])
            && count($this->controller['model_registry']['customfieldgroups']) > 0
        ) {
        } else {
            return false;
        }

        return true;
    }
}
