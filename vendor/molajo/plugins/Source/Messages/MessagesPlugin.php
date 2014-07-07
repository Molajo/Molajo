<?php
/**
 * Messages Plugin
 *
 * @package    Molajo
 * @copyright  2014 Amy Stephen. All rights reserved.
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 */
namespace Molajo\Plugins\Messages;

use CommonApi\Event\DisplayInterface;
use Molajo\Plugins\DisplayEventPlugin;
use stdClass;

/**
 * Messages Plugin
 *
 * @package     Molajo
 * @license     http://www.opensource.org/licenses/mit-license.html MIT License
 * @since       1.0
 */
class MessagesPlugin extends DisplayEventPlugin implements DisplayInterface
{
    /**
     * Messages
     *
     * @var    array
     * @since  1.0.0
     */
    protected $messages = array();

    /**
     * Alerts
     *
     * @var    array
     * @since  1.0.0
     */
    protected $alerts = array(
        'Success'     => 'success',
        'Warning'     => 'warning',
        'Error'       => 'alert',
        'Information' => 'secondary',
    );

    /**
     * Before Parse Head
     *
     * @return  $this
     * @since   1.0.0
     */
    public function onBeforeParseHead()
    {
        if ($this->processMessagesPlugin() === false) {
            return $this;
        }

        return $this->setMessages();
    }

    /**
     * Should plugin be executed?
     *
     * @return  boolean
     * @since   1.0.0
     */
    protected function processMessagesPlugin()
    {
        $this->messages = $this->user->getFlashmessage();

        if (count($this->messages) === 0) {
            $this->plugin_data->page->messages = array();
            return false;
        }

        return true;
    }

    /**
     * Prepares system messages for display
     *
     * @return  $this
     * @since   1.0.0
     */
    protected function setMessages()
    {
        $temp_row = array();

        foreach ($this->messages as $message) {
            $temp_row[] = $this->setMessageRow($message);
        }

        $this->plugin_data->page->messages = $temp_row;

        return $this;
    }

    /**
     * Set Message Row
     *
     * @param   object  $message
     *
     * @return  stdClass
     * @since   1.0
     */
    protected function setMessageRow($message)
    {
        $temp_row = new stdClass();

        $temp_row->message = $message->message;
        $temp_row->type    = $message->type;
        $temp_row->code    = $message->code;
        $temp_row->action  = $this->plugin_data->page->urls['page'];

        $temp_row->class = 'alert-box';

        return $this->setMessageRowHeading($temp_row, $message->type, $this->alerts[$message->type]);
    }

    /**
     * Set Message Type
     *
     * @param   stdClass $temp_row
     * @param   string   $type
     * @param   string   $type2
     *
     * @return  stdClass
     * @since   1.0.0
     */
    protected function setMessageRowHeading($temp_row, $type, $type2)
    {
        $temp_row->heading = $this->language_controller->translateString($type);
        $temp_row->class .= ' ' . strtolower($type2);

        return $temp_row;
    }
}
