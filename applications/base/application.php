<?php
/**
 * @package     Molajo
 * @subpackage  Base
 * @copyright   Copyright (C) 2012 Amy Stephen. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */
defined('MOLAJO') or die;
use Symfony\Component\HttpFoundation\Request;
/**
 * Application
 *
 * @package     Molajo
 * @subpackage  Base
 * @since       1.0
 */
class MolajoApplication
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
            self::$instance = new MolajoApplication ();
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
        $sv = Molajo::Services()->startServices();

        if (Services::Configuration()->get('debug', 0) == 1) {
            debug('MolajoApplication::initialize Start Services');
        }

        /** Server */
        $server = Services::Request()->request->server->get('HTTP_HOST');
        echo 'Server: '.$server.'<br />';

        /** Request */
        // retrieve an HTTP request header, with normalized, lowercase keys
        $host = Services::Request()->request->headers->get('host');
        echo 'Host: '.$host.'<br />';
        $content_type = Services::Request()->request->headers->get('content_type');
        echo 'Content Type:  '.$content_type.'<br />';
        $task = Services::Request()->request->get('task');
        echo 'Task: '.$task.'<br />';

        echo 'Ajax? ' . Services::Request()->request->isXmlHttpRequest().'<br />';

        /** URL */
        if (Services::Request()->isSecure() === true) {
            $https = true;
            echo 'https=true';
        } else {
            $https = false;
            echo 'https=false';
        }
        echo 'PathInfo: '. Services::Request()->request->getPathInfo() .'<br />';
        echo 'Querystring: '. Services::Request()->request->getQueryString() .'<br />';

        /** Cookie */
        $cookie = Services::Request()->cookies->get('PHPSESSID');
        echo 'PHPSESSID Cookie: '.'<br />';
         echo '<pre>';
         var_dump($cookie);
         echo '</pre>';



        $all = Services::Request()->request->all();
        echo 'All '.'<br />';
        echo '<pre>';
        var_dump($all);
        echo '</pre>';
  die;


        /** User and Session */
        $this->model = new MolajoUsersModel (0);
        $columns = $this->model->getFields();
        echo '<pre>';
        var_dump($columns);
        echo '</pre>';
        die;
        /** offline */
        if (Services::Configuration()->get('offline', 0) == 1) {
            $this->_error(503);
        }

        /** verify application secure access configuration */
        if (Services::Configuration()->get('force_ssl') >= 1) {
            if (isset($_SERVER['HTTPS'])) {
            } else {
                $redirectTo = (string)'https' .
                    substr(MOLAJO_BASE_URL, 4, strlen(MOLAJO_BASE_URL) - 4) .
                    MOLAJO_APPLICATION_URL_PATH .
                    '/' . MOLAJO_PAGE_REQUEST;
                Services::Response()
                    ->setStatusCode(301)
                    ->isRedirect($redirectTo);
            }
        }

        /** establish the session */
        //Services::Session()->create(
        //        Services::Session()->getHash(get_class($this))
        //  );
        if (Services::Configuration()->get('debug', 0) == 1) {
            debug('MolajoApplication::initialize Services::Session()');
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
        if (Services::Configuration()->get('debug', 0) == 1) {
            debug('MolajoApplication::process Molajo::Request()->process()');
        }

        die;

        /**
         * Display Task
         *
         * Input Statement Loop until no more <input statements found
         *
         * 1. Parser: parses theme and rendered output for <input:renderer statements
         *
         * 2. Renderer: each input statement processed by extension renderer in order
         *    to collect task object for use by the MVC
         *
         * 3. MVC: executes task/controller which handles model processing and
         *    renders template and wrap views
         */

        if (Molajo::Request()->get('mvc_controller') == 'display'
        ) {
            $content = Molajo::Parser();
            if (Services::Configuration()->get('debug', 0) == 1) {
                debug('MolajoApplication::process Molajo::Parser() completed');
            }

            /** response */
            Services::Response()->setContent($content);
            Services::Response()->setStatusCode(200);
            Services::Response()->prepare(Services::Request()->request);
            Services::Response()->send();

        } else {

            /**
             * Action Task
             */
            //$this->_processTask();
        }

        if (Services::Configuration()->get('debug', 0) == 1) {
            debug('MolajoApplication::process Services::Response()->respond() completed');
        }
        return;
    }
}
