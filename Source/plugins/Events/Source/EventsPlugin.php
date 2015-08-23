<?php
/**
 * Events Plugin
 *
 * @package    Molajo
 * @copyright  2014-2015 Amy Stephen. All rights reserved.
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 */
namespace Molajo\Plugins\Events;

use CommonApi\Event\SystemEventInterface;
use Molajo\Plugins\SystemEvent;
use stdClass;

/**
 * Events Plugin
 *
 * @package  Molajo
 * @license  http://www.opensource.org/licenses/mit-license.html MIT License
 * @since    1.0
 */
final class EventsPlugin extends SystemEvent implements SystemEventInterface
{
    /**
     * Events Array
     *
     * @var    array
     * @since  1.0.0
     */
    protected $events
        = array(
            'onAfterStart',
            'onBeforeRoute',
            'onAfterRoute',
            'onBeforeDispatcher',
            'onAfterDispatcher',
            'onBeforeExecute',
            'onAfterExecute',
            'onBeforeResponse',
            'onAfterResponse',

            'onBeforeRender',
            'onBeforeParse',
            'onBeforeParseHead',
            'onAfterParse',
            'onBeforeRenderTheme',
            'onAfterRenderTheme',
            'onBeforeRenderPage',
            'onAfterRenderPage',
            'onBeforeRenderPosition',
            'onAfterRenderPosition',
            'onBeforeRenderTemplate',
            'onBeforeRenderTemplateHead',
            'onBeforeRenderTemplateItem',
            'onBeforeRenderTemplateFooter',
            'onAfterRenderTemplate',
            'onBeforeRenderWrap',
            'onAfterRenderWrap',
            'onAfterRender',

            'onBeforeAuthenticate',
            'onAfterAuthenticate',
            'onBeforeAuthorisation',
            'onAfterAuthorisation',
            'onBeforeLogout',
            'onAfterLogout',

            'onBeforeInitialise',
            'onAfterInitialise',
            'onBeforeCreate',
            'onAfterCreate',
            'onBeforeRead',
            'onAfterReadRow',
            'onAfterRead',
            'onBeforeUpdate',
            'onAfterUpdate',
            'onBeforeDelete',
            'onAfterDelete'
        );

    /**
     * Prepares formatted copyright statement with year span
     *
     * @return  $this
     * @since   1.0.0
     */
    public function onAfterStart()
    {
        if ($this->checkProcessPlugin() === false) {
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
    protected function checkProcessPlugin()
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

            $temp_row = new stdClass();

            $temp_row->id    = $key;
            $temp_row->value = trim($key);

            $eventArray[] = $temp_row;
        }

        $this->plugin_data->datalist_events
            = $this->getDataList('Events', array('value_list' => $eventArray));

        return $this;
    }
}
