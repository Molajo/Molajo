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
        if (isset($this->query_results->catalog_id)) {
        } else {
            return $this;
        }

        $actions = $this->get('toolbar_buttons', '', 'runtime_data');

        $actionsArray = explode(',', $actions);

        $permissions = $this->authorisation_controller
            ->verifyTaskListPermissions($actionsArray, $this->query_results->catalog_id);

        foreach ($actionsArray as $action) {
            if ($permissions[$action] === true) {
                $field                       = $action . 'Permission';
                $this->query_results->$field = $permissions[$action];
            }
        }

        return $this;
    }
}