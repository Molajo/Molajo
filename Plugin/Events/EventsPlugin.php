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

        $events = array('onAfterInitialise',
            'onAfterRoute',
            'onAfterAuthorise',
            'onBeforeParse',
            'onBeforeInclude',
            'onBeforeRead',
            'onAfterRead',
            'onAfterReadall',
            'onBeforeviewRender',
            'onAfterviewRender',
            'onAfterInclude',
            'onAfterParsebody',
            'onBeforeDocumenthead',
            'onAfterDocumenthead',
            'onAfterParse',
            'onAfterExecute',
            'onAfterResponse',
            'onBeforecreate',
            'onAftercreate',
            'onBeforeupdate',
            'onAfterupdate',
            'onBeforedelete',
            'onAfterdelete',
            'onBeforelogon',
            'onBeforelogout'
        );

        $eventArray = array();
        foreach ($events as $key) {

            $row = new \stdClass();

            $row->id = $key;
            $row->value = trim($key);

            $eventArray[] = $row;
        }

        Services::Registry()->set(DATALIST_LITERAL, EVENTS_LITERAL, $eventArray);

        Services::Registry()->set(EVENTS_LITERAL, 'Plugins', $this->pluginArray);
        Services::Registry()->set(EVENTS_LITERAL, 'PluginEvents', $this->plugin_eventArray);
        Services::Registry()->set(EVENTS_LITERAL, 'Events', $this->eventArray);
        Services::Registry()->set(EVENTS_LITERAL, 'EventPlugins', $this->event_pluginArray);

        return true;
    }
}
