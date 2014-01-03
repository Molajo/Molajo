<?php
/**
 * Messages Plugin
 *
 * @package    Molajo
 * @copyright  2014 Amy Stephen. All rights reserved.
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 */
namespace Molajo\Plugin\Messages;

use stdClass;
use CommonApi\Event\DisplayInterface;
use Molajo\Plugin\DisplayEventPlugin;

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
     * Prepares system messages for display
     *
     * @return  $this
     * @since   1.0
     */
    public function onBeforeParseHead()
    {
        return $this;
        $this->runtime_data->page->messages = new stdClass();
        $this->runtime_data->page->messages = $temp_row;

        $messages = $controller->getData();

        $temp_row = array();

        foreach ($messages as $message) {

            $temp_row = new stdClass();

            $temp_row->message = $message->message;
            $temp_row->type    = $message->type;
            $temp_row->code    = $message->code;
            $temp_row->action  = $this->runtime_data->page->urls['page'];

            $temp_row->class = 'alert-box';
            if ($message->type == 'Success') {
                $temp_row->heading = $this->language_controller->translate('Success');
                $temp_row->class .= ' success';
            } elseif ($message->type == 'Warning') {
                $temp_row->heading = $this->language_controller->translate('Warning');
                $temp_row->class .= ' warning';
            } elseif ($message->type == 'Error') {
                $temp_row->heading = $this->language_controller->translate('Error');
                $temp_row->class .= ' alert';
            } else {
                $temp_row->heading = $this->language_controller->translate('Information');
                $temp_row->class .= ' secondary';
            }

            $temp_row[] = $temp_row;
        }

        return $this;
    }
}
