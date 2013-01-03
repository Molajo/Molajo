<?php
/**
 * @package    Niambie
 * @copyright  2013 Amy Stephen. All rights reserved.
 * @license    MIT
 */
namespace Molajo\Plugin\Messages;

use Molajo\Plugin\Plugin\Plugin;
use Molajo\Service\Services;
use Molajo\Frontcontroller;

defined('NIAMBIE') or die;

/**
 * @package     Niambie
 * @license     MIT
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
    public function onBeforeParseHead()
    {
        $controllerClass = CONTROLLER_CLASS_NAMESPACE;
        $controller      = new $controllerClass();
        $controller->getModelRegistry('Dataobject', 'Messages', 1);
        $messages = $controller->getData(QUERY_OBJECT_LIST);

        if (count($messages) == 0 || $messages === false) {
            Services::Registry()->set(
                'Messages',
                $this->get('template_view_path_node', '', 'parameters'),
                array()
            );

            return true;
        }

        $temp_query_results = array();

        foreach ($messages as $message) {

            $temp_row = new \stdClass();

            $temp_row->message = $message->message;
            $temp_row->type    = $message->type;
            $temp_row->code    = $message->code;
            $temp_row->action  = Services::Registry()->get('parameters', 'request_base_url_path') .
                Services::Registry()->get('parameters', 'request_url');

            $temp_row->class = 'alert-box';
            if ($message->type == MESSAGE_TYPE_SUCCESS) {
                $temp_row->heading = Services::Language()->translate('Success');
                $temp_row->class .= ' success';

            } elseif ($message->type == MESSAGE_TYPE_WARNING) {
                $temp_row->heading = Services::Language()->translate('Warning');
                $temp_row->class .= ' warning';

            } elseif ($message->type == MESSAGE_TYPE_ERROR) {
                $temp_row->heading = Services::Language()->translate('Error');
                $temp_row->class .= ' alert';

            } else {
                $temp_row->heading = Services::Language()->translate('Information');
                $temp_row->class .= ' secondary';
            }
            $temp_query_results[] = $temp_row;
        }

        Services::Registry()->set(
            'Template',
            $this->get('template_view_path_node', '', 'parameters'),
            $temp_query_results
        );

        return true;
    }
}
