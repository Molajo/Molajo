<?php
/**
 * @package     Molajo
 * @subpackage  Base
 * @copyright   Copyright (C) 2012 Amy Stephen. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */
defined('MOLAJO') or die;

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
     * Application static instance
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
        /** Services: initiate */
        $sv = Molajo::Services()->initiateServices();

        /** SSL: check requirement */
        if (Services::Configuration()->get('force_ssl') >= 1) {
            if (isset($_SERVER['HTTPS'])) {
            } else {
                Molajo::Responder()
                    ->redirect((string)'https' .
                        substr(MOLAJO_BASE_URL, 4, strlen(MOLAJO_BASE_URL) - 4) .
                        MOLAJO_APPLICATION_URL_PATH .
                        '/' .
                        MOLAJO_PAGE_REQUEST
                );
            }
        }

        /** Session */
        Services::Session()->create(
            Services::Session()->getHash(get_class($this))
        );

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
        /** responder: prepare for output */
        Molajo::Responder();
        if (Services::Configuration()->get('debug', 0) == 1) {
            debug('MolajoApplication::process Molajo::Responder() completed');
        }

        /** request: define processing instructions in page_request object */
        Molajo::Request()->process();
        if (Services::Configuration()->get('debug', 0) == 1) {
            debug('MolajoApplication::process Molajo::Request()->process() completed');
        }

//        $results = Molajo::Request()->getAll('array');
//        foreach ($results as $key=>$value) {
//            echo 'Key'.$key.'<br/>';
//        }

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
            Molajo::Parser();
            if (Services::Configuration()->get('debug', 0) == 1) {
                debug('MolajoApplication::process Molajo::Parser() completed');
            }
        } else {

            /**
             * Action Task
             */
            //$this->_processTask();
        }

        /** responder: process rendered output */
        Molajo::Responder()->respond();
        if (Services::Configuration()->get('debug', 0) == 1) {
            debug('MolajoApplication::process Molajo::Responder()->respond() completed');
        }
        return;
    }
}
