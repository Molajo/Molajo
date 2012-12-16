<?php
/**
 * @package    Niambie
 * @copyright  2012 Amy Stephen. All rights reserved.
 * @license    MIT, see License folder
 */
namespace Molajo\Plugin\Itemuserpermissions;

use Molajo\Plugin\Plugin\Plugin;

defined('NIAMBIE') or die;

/**
 * Item Snippet
 *
 * @package     Niambie
 * @license     MIT
 * @since       1.0
 */
class ItemuserpermissionsPlugin extends Plugin
{

    /**
     * After-read processing
     *
     * Use with Grid to determine permissions for buttons and items
     * Validate action-level user permissions on each row - relies upon catalog_id
     *
     * @return  boolean
     * @since   1.0
     */
    public function onAfterRead()
    {
        if (isset($this->row->catalog_id)) {
        } else {
            return false;
        }

        $actions = $this->get('toolbar_buttons', '', 'parameters');

        $actionsArray = explode(',', $actions);

        $permissions = Services::Permissions()
            ->verifyTaskList($actionsArray, $this->row->catalog_id);

        foreach ($actionsArray as $action) {
            if ($permissions[$action] === true) {
                $field = $action . 'Permission';
                $this->row->$field = $permissions[$action];
            }
        }

        return true;
    }
}
