<?php
/**
 * @package    Niambie
 * @copyright  2013 Amy Stephen. All rights reserved.
 * @license    MIT
 */
namespace Molajo\Plugin\Ipaddress;

use Molajo\Plugin\Plugin\Plugin;
use Molajo\Service\Services;

defined('NIAMBIE') or die;

/**
 * IP Address
 *
 * @package     Niambie
 * @license     MIT
 * @since       1.0
 */
class IpaddressPlugin extends Plugin
{
    /**
     * Pre-create processing
     *
     * @return boolean
     * @since   1.0
     */
    public function onBeforeCreate()
    {
        $fields = $this->retrieveFieldsByType('ip_address');

        $ip_address = Services::Client()->get('ip_address');

        if (is_array($fields) && count($fields) > 0) {
            foreach ($fields as $field) {
                $this->saveField($field, $field['name'], $ip_address);
            }
        }

        return true;
    }

    /**
     * Pre-update processing
     *
     * @return boolean
     * @since   1.0
     */
    public function onBeforeUpdate()
    {
        // No updates allowed for activity
        return true;
    }
}
