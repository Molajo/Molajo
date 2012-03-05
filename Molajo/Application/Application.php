<?php
/**
 * @package     Molajo
 * @copyright   Copyright (C) 2012 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
namespace Molajo\Application;

defined('MOLAJO') or die;

use Molajo\Application\Request;
use Molajo\Application\Parse;
use Molajo\Application\Service;

/**
 * Application
 *
 * @package     Molajo
 * @subpackage  Base
 * @since       1.0
 */
class Application
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
            self::$instance = new Application();
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
         * 1. Parse: recursively parses theme and then rendered output
         *      for <include:type statements
         *
         * 2. Includer: each include statement is processed by the
         *      associated extension includer in order, collecting
         *      rendering data needed by the MVC
         *
         * 3. MVC: executes controller task, invoking model processing and
         *    rendering of template and wrap views
         *
         * Steps 1-3 continue until no more <include:type statements are
         *    found in the Theme and rendered output
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
