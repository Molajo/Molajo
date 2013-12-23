<?php
/**
 * Item Url Permissions
 *
 * @package    Molajo
 * @copyright  2013 Amy Stephen. All rights reserved.
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 */
namespace Molajo\Plugin\Itemuserpermissions;

use Molajo\Plugin\AbstractPlugin;

/**
 * Item Url Permissions
 *
 * @package     Molajo
 * @license     http://www.opensource.org/licenses/mit-license.html MIT License
 * @since       1.0
 */
class ItemuserpermissionsPlugin extends AbstractPlugin
{
    /**
     * Use with Grid to determine permissions for buttons and items
     * Validate action-level user permissions on each row - relies upon catalog_id
     *
     * @return  $this
     * @since   1.0
     */
    public function onAfterRead()
    {
        $this->runtime_data->permissions = array();

        if (isset($this->query_results->catalog_id)) {
        } else {
            return $this;
        }

        if (count($this->runtime_data->reference_data->task_to_action) > 0) {
        } else {
            return $this;
        }

        $permissions = array();
        foreach ($this->runtime_data->reference_data->task_to_action as $task => $action) {

            if ($task == 'none' || $task == 'cancel') {
                $permissions[$task]     = true;
            } else {
                $options                = array();
                $options['resource_id'] = $this->query_results->catalog_id;
                $options['action']      = $action;

                $permissions[$task]     = $this->authorisation_controller->isUserAuthorised($options);
            }
        }

        $this->runtime_data->permissions = $permissions;

        return $this;
    }
}
