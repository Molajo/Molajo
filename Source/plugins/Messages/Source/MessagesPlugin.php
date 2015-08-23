<?php
/**
 * Messages Plugin
 *
 * @package    Molajo
 * @copyright  2014-2015 Amy Stephen. All rights reserved.
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 */
namespace Molajo\Plugins\Messages;

use CommonApi\Event\SystemEventInterface;
use Molajo\Plugins\SystemEvent;
use stdClass;

/**
 * Messages Plugin
 *
 * @package     Molajo
 * @license     http://www.opensource.org/licenses/mit-license.html MIT License
 * @since       1.0
 */
final class MessagesPlugin extends SystemEvent implements SystemEventInterface
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
    protected $alerts
        = array(
            'Success'     => 'success',
            'Warning'     => 'warning',
            'Error'       => 'alert',
            'Information' => 'secondary',
        );

    /**
     * Prepares formatted copyright statement with year span
     *
     * @return  $this
     * @since   1.0.0
     */
    public function onAfterStart()
    {
        $this->plugin_data->messages = array();

        return $this;
    }

    /**
     * On After Execute
     *
     * @return  $this
     * @since   1.0.0
     */
    public function onAfterExecute()
    {
        $this->messages = $this->user->getFlashmessage();

        if (count($this->messages) === 0) {
            $this->plugin_data->messages = array();
            return false;
        }



//        if ($this->checkProcessPlugin() === false) {
//            $this->plugin_data->messages = array();
//            return $this;
//        }
//todo: remove below
        $temp_row = new stdClass();

        $temp_row->message = 'I am a message';
        $temp_row->type    = 'Success';
        $temp_row->code    = 100;
        $temp_row->action  = $this->runtime_data->request->data->url;

        $this->messages[] = $temp_row;
//todo: remove above

        return $this->setMessages();
    }

    /**
     * Prepares system messages for display
     *
     * @return  $this
     * @since   1.0.0
     */
    protected function setMessages()
    {
        $messages = array();
        foreach ($this->messages as $message) {
            $messages[] = $this->setMessageRow($message);
        }

        $this->plugin_data->messages = $messages;

        return $this;
    }

    /**
     * Set Message Row
     *
     * @param   object $message
     *
     * @return  stdClass
     * @since   1.0.0
     */
    protected function setMessageRow($message)
    {
        $temp_row = new stdClass();

        $temp_row->message = $message->message;
        $temp_row->type    = $message->type;
        $temp_row->code    = $message->code;
        $temp_row->action  = $this->runtime_data->request->data->url;
        $temp_row->class   = $this->alerts[$message->type];

        return $this->setMessageRowHeading($temp_row, $message->type);
    }

    /**
     * Set Message Type
     *
     * @param   stdClass $temp_row
     * @param   string   $type
     *
     * @return  stdClass
     * @since   1.0.0
     */
    protected function setMessageRowHeading($temp_row, $type)
    {
        $temp_row->heading = $this->language->translateString($type);

        return $temp_row;
    }
}
