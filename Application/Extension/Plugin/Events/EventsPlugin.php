<?php
/**
 * Events Plugin
 *
 * @package    Molajo
 * @copyright  2013 Amy Stephen. All rights reserved.
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 */
namespace Molajo\Plugin\Events;

use stdClass;
use Molajo\Plugin\SystemEventPlugin;
use CommonApi\Event\SystemInterface;

/**
 * Events Plugin
 *
 * @package  Molajo
 * @license  http://www.opensource.org/licenses/mit-license.html MIT License
 * @since    1.0
 */
class EventsPlugin extends SystemEventPlugin implements SystemInterface
{
    /**
     * Generates list of Events for use in Datalists
     *
     * @return  $this
     * @since   1.0
     */
    public function onAfterInitialise()
    {
        if ($this->runtime_data->application->id == 2) {
        } else {
            return $this;
        }

        $events = array(
            'onAfterInitialise',
            'onAfterRoute',
            'onAfterAuthorise',
            'onAfterResource',
            'onAfterExecute',
            'onBeforeRender',
            'onBeforeParse',
            'onBeforeParseHead',
            'onBeforeRenderView',
            'onAfterRenderView',
            'onAfterParse',
            'onAfterRender',
            'onBeforeRenderView',
            'onAfterInclude',
            'onBeforeResponse',
            'onAfterResponse',
            'onBeforeLogin',
            'onAfterLogin',
            'onBeforeLogout',
            'onAfterLogout',
            'onBeforeRead',
            'onAfterRead',
            'onAfterReadall',
            'onBeforeCreate',
            'onAfterCreate',
            'onBeforeDelete',
            'onAfterDelete',
            'onBeforeUpdate',
            'onAfterUpdate'
        );

        foreach ($events as $e) {
            if (in_array(strtolower($e), array_map('strtolower', $events))) {
            } else {
                $events[] = $e;
            }
        }

        $eventArray = array();
        foreach ($events as $key) {

            $temp_row = new \stdClass();

            $temp_row->id    = $key;
            $temp_row->value = trim($key);

            $eventArray[] = $temp_row;
        }

        $this->runtime_data->plugin_data->datalists->eventslist = $eventArray;

        return $this;
    }
}
