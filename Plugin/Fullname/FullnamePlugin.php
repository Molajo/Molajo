<?php
/**
 * @package    Molajo
 * @copyright  2012 Amy Stephen. All rights reserved.
 * @license    GNU GPL v 2, or later and MIT, see License folder
 */
namespace Molajo\Plugin\Fullname;

use Molajo\Plugin\Plugin\Plugin;

defined('MOLAJO') or die;

/**
 * Full name
 *
 * @package     Molajo
 * @subpackage  Plugin
 * @since       1.0
 */
class FullnamePlugin extends Plugin
{

    /**
     * Adds full_name to recordset containing first_name and last_name
     *
     * @return boolean
     * @since   1.0
     */
    public function onAfterRead()
    {
        $first_name_field = $this->getField('first_name');
        if ($first_name_field === false) {
            return false;
        }
        $first_name = $this->getFieldValue($first_name_field);

        $last_name_field = $this->getField('last_name');
        if ($last_name_field === false) {
            return false;
        }
        $last_name = $this->getFieldValue($last_name_field);

        if ($first_name === false && $last_name === false) {
            return false;

        } else {

            $newFieldValue = $first_name . ' ' . $last_name;

            if ($newFieldValue === false) {
            } else {

                /** Creates the new 'normal' or special field and populates the value */
                $this->saveField(null, 'full_name', $newFieldValue);
            }
        }

        return true;
    }
}
