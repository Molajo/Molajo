<?php
/**
 * IP Address Plugin
 *
 * @package    Molajo
 * @copyright  2014 Amy Stephen. All rights reserved.
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 */
namespace Molajo\Plugins\Ipaddress;

use CommonApi\Event\CreateInterface;
use CommonApi\Event\UpdateInterface;
use Molajo\Plugins\CreateEventPlugin;

/**
 * IP Address Plugin
 *
 * @package     Molajo
 * @license     http://www.opensource.org/licenses/mit-license.html MIT License
 * @since       1.0
 */
class IpaddressPlugin extends CreateEventPlugin implements CreateInterface, UpdateInterface
{
    /**
     * Pre-create processing
     *
     * @return  $this
     * @since   1.0
     */
    public function onBeforeCreate()
    {
        $fields = $this->getFieldsByType('ip_address');

        $ip_address = Services::Client()->get('ip_address');

        if (is_array($fields) && count($fields) > 0) {
            foreach ($fields as $field) {
                $this->setField($field, $field['name'], $ip_address);
            }
        }

        return $this;
    }

    /**
     * Pre-update processing
     *
     * @return  $this
     * @since   1.0
     */
    public function onBeforeUpdate()
    {
        // No updates allowed for activity
        return $this;
    }
}
