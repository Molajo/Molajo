<?php
/**
 * @package    Molajo
 * @copyright  2012 Individual Molajo Contributors. All rights reserved.
 * @license    GNU GPL v 2, or later and MIT, see License folder
 */
namespace Molajo\Service\Services\Error;

use Molajo\Helpers;
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
     * @static
     * @var    object
     * @since  1.0
     */
    protected static $instance;

    /**
     * @static
     * @return bool|object
     * @since   1.0
     */
    public static function getInstance()
    {
        if (empty(self::$instance)) {
            self::$instance = new ErrorService();
        }

        return self::$instance;
    }

    /**
     * 500 Set routing for an error condition
     *
     * @param             $code
     * @param null|string $message
     *
     * @return null
     *
     * @since   1.0
     */
    public function set($code, $message = 'Internal server error')
    {
        Services::Registry()->set('Parameters', 'error_status', true);

        Services::Registry()->set('Parameters', 'request_action', 'Display');
        Services::Registry()->set('Parameters', 'request_action_authorisation', 'read'); //for now
        Services::Registry()->set('Parameters', 'request_controller', 'read');

        /** default error theme and page */
        Services::Registry()->set('Parameters', 'theme_id',
            Services::Registry()->get('Configuration', 'error_theme_id')
        );
        Services::Registry()->set('Parameters', 'page_view_id',
            Services::Registry()->get('Configuration', 'error_page_view_id')
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

        Services::Registry()->merge('Configuration', 'Parameters', true);

        Helpers::Extension()->setThemePageView();

        return true;
    }

    /**
     * 503 Offline
     *
     * @return null
     *
     * @since   1.0
     */
    protected function error503()
    {
        Services::Response()
            ->setStatusCode(503);

        Services::Message()
            ->set(
            Services::Registry()->get('Configuration', 'offline_message',
                'This site is not available.<br /> Please check back again soon.'
            ),
            MESSAGE_TYPE_WARNING,
            503
        );

        Services::Registry()->set('Parameters', 'theme_id',
            Services::Registry()->get('Configuration', 'offline_theme_id', 0)
        );

        Services::Registry()->set('Parameters', 'page_view_id',
            Services::Registry()->get('Configuration', 'offline_page_view_id', 0)
        );
    }

    /**
     * 403 Not Authorised
     *
     * @return null
     *
     * @since   1.0
     */
    protected function error403()
    {
        Services::Response()
            ->setStatusCode(403);

        Services::Message()
            ->set(
            Services::Registry()->get('Configuration', 'error_403_message', 'Not Authorised.'),
            MESSAGE_TYPE_ERROR,
            403
        );
    }

    /**
     * 404 Page Not Found
     *
     * @return null
     *
     * @since   1.0
     */
    protected function error404()
    {
        Services::Response()
            ->setStatusCode(404);

        Services::Message()
            ->set(
            Services::Registry()->get('Configuration', 'error_404_message', 'Page not found.'),
            MESSAGE_TYPE_ERROR,
            404
        );

        return;
    }
}
