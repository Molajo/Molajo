<?php
/**
 * @package    Molajo
 * @copyright  2012 Amy Stephen. All rights reserved.
 * @license    GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
namespace Molajo\Extension\Trigger\ItemUserPermissions;

use Molajo\Extension\Trigger\Content\ContentTrigger;

defined('MOLAJO') or die;

/**
 * Item Snippet
 *
 * @package     Molajo
 * @subpackage  Trigger
 * @since       1.0
 */
class ItemUserPermissionsTrigger extends ContentTrigger
{
    /**
     * Static instance
     *
     * @var    object
     * @since  1.0
     */
    protected static $instance;

    /**
     * getInstance
     *
     * @static
     * @return bool|object
     * @since  1.0
     */
    public static function getInstance()
    {
        if (empty(self::$instance)) {
            self::$instance = new ItemUserPermissionsTrigger();
        }

        return self::$instance;
    }

    /**
     * After-read processing
     *
     * Use with Grid to determine permissions for buttons and items
     * Validate action-level user permissions on each row - relies upon catalog_id
     *
     * @param   $this->query_results
     * @param   $model
     *
     * @return boolean
     * @since   1.0
     */
    public function onAfterRead()
    {
        if (isset($this->query_results->catalog_id)) {
        } else {
            return false;
        }

        /** Component Buttons */
        $actions = Services::Registry() - get('ExtensionParameters', 'toolbar_buttons');

        $actionsArray = explode(',', $actions);

        /** User Permissions */
        $permissions = Services::Authorisation()
            ->authoriseTaskList($actionsArray, $this->query_results->catalog_id);

        /** Append onto row */
        foreach ($actionsArray as $action) {
            if ($permissions[$action] === true) {
                $field = $action . 'Permission';
                $this->query_results->$field = $permissions[$action];
            }
        }

        return true;
    }
}
