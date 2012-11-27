<?php
/**
 * @package    Molajo
 * @copyright  2012 Individual Molajo Contributors. All rights reserved.
 * @license    GNU GPL v 2, or later and MIT, see License folder
 */
namespace Molajo\Plugin\Events;

use Molajo\Plugin\Plugin\Plugin;
use Molajo\Service\Services;

defined('MOLAJO') or die;

/**
 * @package     Molajo
 * @subpackage  Plugin
 * @since       1.0
 */
class EventsPlugin extends Plugin
{
    /**
     * Generates list of Events for use in Datalists
     *
     * @return  boolean
     * @since   1.0
     */
    public function onAfterRoute()
    {
        if (APPLICATION_ID == 2) {
        } else {
            return true;
        }

        Services::Registry()->sort('Events');
        $events = Services::Registry()->get('Events');

        $eventArray = array();

        foreach ($events as $key => $value) {

            $row = new \stdClass();

            if (strtolower(substr($key, 0, strlen('onbefore'))) == 'onbefore') {
                $eventName = substr($key, strlen('onbefore'), strlen($key));
                $formatted = 'onBefore' . ucfirst(strtolower($eventName));

            } elseif (strtolower(substr($key, 0, strlen('onafter'))) == 'onafter') {
                $eventName = substr($key, strlen('onafter'), strlen($key));
                $formatted = 'onAfter' . ucfirst(strtolower($eventName));

            } else {
                $eventName = substr($key, strlen('on'), strlen($key));
                $formatted = 'on' . ucfirst(strtolower($eventName));
            }

            $row->id = $key;
            $row->value = trim($formatted);

            $eventArray[] = $row;
        }

        Services::Registry()->set(DATA_OBJECT_DATALIST, 'Events', $eventArray);

        return true;
    }
}
