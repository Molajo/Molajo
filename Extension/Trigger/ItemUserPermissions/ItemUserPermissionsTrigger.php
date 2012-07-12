<?php
/**
 * @package    Molajo
 * @copyright  2012 Amy Stephen. All rights reserved.
 * @license    GNU GPL v 2, or later and MIT, see License folder
 */
namespace Molajo\Extension\Trigger\Itemuserpermissions;

use Molajo\Extension\Trigger\Content\ContentTrigger;

defined('MOLAJO') or die;

/**
 * Item Snippet
 *
 * @package     Molajo
 * @subpackage  Trigger
 * @since       1.0
 */
class ItemuserpermissionsTrigger extends ContentTrigger
{

    /**
     * After-read processing
     *
     * Use with Grid to determine permissions for buttons and items
     * Validate action-level user permissions on each row - relies upon catalog_id
     *
     * @param   $this->data
     * @param   $model
     *
     * @return boolean
     * @since   1.0
     */
    public function onAfterRead()
    {
        if (isset($this->data->catalog_id)) {
        } else {
            return false;
        }

        /** Resource Buttons */
        $actions = $this->get('toolbar_buttons');

        $actionsArray = explode(',', $actions);

        /** User Permissions */
        $permissions = Services::Authorisation()
            ->verifyTaskList($actionsArray, $this->data->catalog_id);

        /** Append onto row */
        foreach ($actionsArray as $action) {
            if ($permissions[$action] === true) {
                $field = $action . 'Permission';
                $this->data->$field = $permissions[$action];
            }
        }

        return true;
    }
}
