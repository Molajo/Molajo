<?php
/**
 * @package    Molajo
 * @copyright  2012 Amy Stephen. All rights reserved.
 * @license    GNU GPL v 2, or later and MIT, see License folder
 */
namespace Molajo\Service\Services\Error;

use Molajo\Service\Services;

defined('MOLAJO') or die;

/**
 * Error
 *
 * @package     Molajo
 * @subpackage  Service
 * @since       1.0
 */
Class ErrorService
{

    /**
     * 500 Set routing for an error condition
     *
     * @param   integer  $code
     * @param   null|string  $message
     *
     * @return  null
     * @since   1.0
     */
    public function set($code, $message = 'Internal server error')
    {
        Services::Registry()->set(PARAMETERS_LITERAL, ERROR_STATUS_LITERAL, true);

        Services::Registry()->set(PARAMETERS_LITERAL, 'request_action', ACTION_READ);
        Services::Registry()->set(PARAMETERS_LITERAL, 'request_task_permission', 'read'); //for now
        Services::Registry()->set(PARAMETERS_LITERAL, 'request_task_controller', 'read');

        /** default error theme and page */
        Services::Registry()->set(PARAMETERS_LITERAL, 'theme_id',
            Services::Registry()->get(CONFIGURATION_LITERAL, 'error_theme_id')
        );
        Services::Registry()->set(PARAMETERS_LITERAL, 'page_view_id',
            Services::Registry()->get(CONFIGURATION_LITERAL, 'error_page_view_id')
        );

        if ($code == 503) {
            $this->error503();

        } elseif ($code == 403) {
            $this->error403();

        } elseif ($code == 404) {
            $this->error404();

        } else {

            Services::Response()
                ->setStatusCode(500)
                ->send('500 Error');

            Services::Message()
                ->set($message, MESSAGE_TYPE_ERROR, 500);
        }

        Services::Registry()->merge(CONFIGURATION_LITERAL, PARAMETERS_LITERAL, true);

        $this->extensionHelper->setThemePageView();

        return true;
    }

    /**
     * 503 Offline
     *
     * @return  null
     * @since   1.0
     */
    protected function error503()
    {
        Services::Response()
            ->setStatusCode(503);

        Services::Message()
            ->set(
            Services::Registry()->get(CONFIGURATION_LITERAL, 'offline_message',
                'This site is not available.<br /> Please check back again soon.'
            ),
            MESSAGE_TYPE_WARNING,
            503
        );

        Services::Registry()->set(PARAMETERS_LITERAL, 'theme_id',
            Services::Registry()->get(CONFIGURATION_LITERAL, 'offline_theme_id', 0)
        );

        Services::Registry()->set(PARAMETERS_LITERAL, 'page_view_id',
            Services::Registry()->get(CONFIGURATION_LITERAL, 'offline_page_view_id', 0)
        );
    }

    /**
     * 403 Not Authorised
     *
     * @return  null
     * @since   1.0
     */
    protected function error403()
    {
        Services::Response()
            ->setStatusCode(403);

        Services::Message()
            ->set(
            Services::Registry()->get(CONFIGURATION_LITERAL, 'error_403_message', 'Not Authorised.'),
            MESSAGE_TYPE_ERROR,
            403
        );
    }

    /**
     * 404 Page Not Found
     *
     * @return  null
     * @since   1.0
     */
    protected function error404()
    {
        Services::Response()
            ->setStatusCode(404);

        Services::Message()
            ->set(
            Services::Registry()->get(CONFIGURATION_LITERAL, 'error_404_message', 'Page not found.'),
            MESSAGE_TYPE_ERROR,
            404
        );

        return;
    }
}
