<?php
/**
 * Events Plugin
 *
 * @package    Molajo
 * @copyright  2014 Amy Stephen. All rights reserved.
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 */
namespace Molajo\Plugins\Events;

use CommonApi\Event\SystemInterface;
use Molajo\Plugins\SystemEventPlugin;
use stdClass;

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
     * Plugin Name
     *
     * @var    array
     * @since  1.0.0
     */
    protected $events = array(
        'onAfterInitialise',
        'onBeforeRoute',
        'onAfterRoute',
        'onBeforeResourcecontroller',
        'onAfterResourcecontroller',
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

    /**
     * Prepares formatted copyright statement with year span
     *
     * @return  $this
     * @since   1.0.0
     */
    public function onAfterInitialise()
    {
        if ($this->processEventsPlugin() === false) {
            return $this;
        }

        return $this->setEvents();
    }

    /**
     * Should plugin be executed?
     *
     * @return  boolean
     * @since   1.0.0
     */
    protected function processEventsPlugin()
    {
        if (count($this->events) > 0) {
            return true;
        }

        return false;
    }

    /**
     * Generates list of Events for use in Datalists
     *
     * @return  $this
     * @since   1.0.0
     */
    protected function setEvents()
    {
        $eventArray = array();

        foreach ($this->events as $key) {

            $temp_row = new \stdClass();

            $temp_row->id    = $key;
            $temp_row->value = trim($key);

            $eventArray[] = $temp_row;
        }

        $this->plugin_data->datalists->events = $eventArray;

        return $this;
    }
}
