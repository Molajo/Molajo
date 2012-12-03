<?php
/**
 * @package    Molajo
 * @copyright  2012 Amy Stephen. All rights reserved.
 * @license    GNU GPL v 2, or later and MIT, see License folder
 */
namespace Molajo\Plugin\Messages;

use Molajo\Plugin\Plugin\Plugin;
use Molajo\Service\Services;
use Molajo\Application;

defined('MOLAJO') or die;

/**
 * @package     Molajo
 * @subpackage  Plugin
 * @since       1.0
 */
class MessagesPlugin extends Plugin
{
    /**
     * Prepares system messages for display
     *
     * @return  boolean
     * @since   1.0
     */
    public function onAfterParsebody()
    {
        $controllerClass = CONTROLLER_CLASS;
        $controller = new $controllerClass();
        $controller->getModelRegistry(DATA_OBJECT_LITERAL, 'Messages');
        $controller->setDataobject();
        $messages = $controller->getData(QUERY_OBJECT_LIST);

        if (count($messages) == 0 || $messages === false) {
            Services::Registry()->set(MESSAGES_LITERAL, $this->get('template_view_path_node', '', 'parameters'), array());
            return true;
        }

        $query_results = array();
        foreach ($messages as $message) {

            $row = new \stdClass();

            $row->message = $message->message;
            $row->type = $message->type;
            $row->code = $message->code;
            $row->action = Services::Registry()->get('parameters', 'request_base_url_path') .
                Services::Registry()->get('parameters', 'request_url');

            $row->class = 'alert-box';
            if ($message->type == MESSAGE_TYPE_SUCCESS) {
                $row->heading = Services::Language()->translate('Success');
                $row->class .= ' success';

            } elseif ($message->type == MESSAGE_TYPE_WARNING) {
                $row->heading = Services::Language()->translate('Warning');
                $row->class .= ' warning';

            } elseif ($message->type == MESSAGE_TYPE_ERROR) {
                $row->heading = Services::Language()->translate('Error');
                $row->class .= ' alert';

            } else {
                $row->heading = Services::Language()->translate('Information');
                $row->class .= ' secondary';
            }
            $query_results[] = $row;
        }

        Services::Registry()->set(TEMPLATE_LITERAL, $this->get('template_view_path_node', '', 'parameters'), $query_results);

        return true;
    }
}
