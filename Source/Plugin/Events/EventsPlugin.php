<?php
/**
 * Events Plugin
 *
 * @package    Molajo
 * @copyright  2014 Amy Stephen. All rights reserved.
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
        $events = array(
            'onAfterInitialise',
            'onBeforeRoute',
            'onAfterRoute',
            'onBeforeAuthorise',
            'onAfterAuthorise',
            'onBeforeExecute',
            'onAfterExecute',
            'onBeforeResponse',
            'onAfterResponse',
            'onBeforeRender',
            'onBeforeParse',
            'onAfterParse',
            'onBeforeRenderView',
            'onBeforeRenderViewHead',
            'onBeforeRenderViewItem',
            'onBeforeRenderViewFooter',
            'onAfterRenderView',
            'onAfterRender',
            'onBeforeRead',
            'onAfterRead',
            'onAfterReadall',
            'onBeforeCreate',
            'onAfterCreate',
            'onBeforeDelete',
            'onAfterDelete',
            'onBeforeUpdate',
            'onAfterUpdate',
            'onBeforeAuthenticate',
            'onAfterAuthenticate',
            'onBeforeAuthorise',
            'onAfterAuthorise',
            'onBeforeLogout',
            'onAfterLogout'
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

        $this->plugin_data->datalists->events = $eventArray;

        return $this;
    }
}
