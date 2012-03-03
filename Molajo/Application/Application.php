<?php
/**
 * @package     Molajo
 * @subpackage  Base
 * @copyright   Copyright (C) 2012 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
namespace Molajo\Application;
namespace Molajo\Application\Service;

defined('MOLAJO') or die;

use Molajo\Application\Service;

/**
 * Application
 *
 * @package     Molajo
 * @subpackage  Base
 * @since       1.0
 */
Class Application
{
    /**
     * Application instance
     *
     * @var    object
     * @since  1.0
     */
    protected static $instance;

    /**
     * getInstance
     *
     * @static
     * @return bool|object
     * @since  1.0
     */
    public static function getInstance()
    {
        if (empty(self::$instance)) {
            self::$instance = new Application ();
        }
        return self::$instance;
    }

    /**
     * initialize
     *
     * Load application services and verify required settings
     *
     * @return  mixed
     * @since   1.0
     */
    public function initialize()
    {
        /** initiate application services */
        $sv = Molajo::Service()->startServices();
        if (Service::Configuration()->get('debug', 0) == 1) {
            debug('Application::initialize Start Services');
        }

        /** offline */
        if (Service::Configuration()->get('offline', 0) == 1) {
            $this->_error(503);
        }

        /** verify application secure access configuration */
        if (Service::Configuration()->get('force_ssl') >= 1) {
            if ((Service::Request()->isSecure() === true)) {
            } else {

                $redirectTo = (string)'https' .
                    substr(MOLAJO_BASE_URL, 4, strlen(MOLAJO_BASE_URL) - 4) .
                    MOLAJO_APPLICATION_URL_PATH .
                    '/' . MOLAJO_PAGE_REQUEST;

                Service::Response()
                    ->setStatusCode(301)
                    ->isRedirect($redirectTo);
            }
        }

        /** establish the session */
        //Service::Session()->create(
        //        Service::Session()->getHash(get_class($this))
        //  );
        if (Service::Configuration()->get('debug', 0) == 1) {
            debug('Application::initialize Service::Session()');
        }

        /** return to Molajo::Site */
        return;
    }

    /**
     * process
     *
     * Primary Application Logic Flow activated by Molajo::Site
     *
     * @return  mixed
     * @since   1.0
     */
    public function process()
    {
        Molajo::Request()->process();
        if (Service::Configuration()->get('debug', 0) == 1) {
            debug('Application::process Molajo::Request()->process()');
        }

        /**
         * Display Task
         *
         * Input Statement Loop until no more <input statements found
         *
         * 1. Parse: parses theme and rendered output for <input:renderer statements
         *
         * 2. Renderer: each input statement processed by extension renderer in order
         *    to collect task object for use by the MVC
         *
         * 3. MVC: executes task/controller which handles model processing and
         *    renders template and wrap views
         */

        if (Molajo::Request()->get('mvc_controller') == 'display') {
            $content = Molajo::Parse();
            if (Service::Configuration()->get('debug', 0) == 1) {
                debug('Application::process Molajo::Parse() completed');
            }

            /** response */
            Service::Response()->setContent($content);
            Service::Response()->setStatusCode(200);
            Service::Response()->prepare(Service::Request()->request);
            Service::Response()->send();

        } else {

            /**
             * Action Task
             */
            //$this->_processTask();
        }

        if (Service::Configuration()->get('debug', 0) == 1) {
            debug('Application::process Service::Response()->respond() completed');
        }
        return;
    }
}
