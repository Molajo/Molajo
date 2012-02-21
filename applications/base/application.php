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
   	 * The input object.
   	 *
   	 * @var    JInput
   	 * @since  11.2
   	 */
   	public $input = null;

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

        /** Test */
        $id = 50;

        /** Instantiate Model */
        $m = new MolajoContentModel($id);

        /** Create or Update */
        $m->query->where($m->db->qn('id')
            . ' = ' . $m->db->q($id));

        $results = $m->loadResult();
        if (empty($results)) {
            $action = 'create';
        } else {
            $action = 'update';
        }

        /** Prepare the data */
        $data = array();
        $data['id'] = 0;
        $data['title'] = 'One long summer';
        $data['protected'] = 0;
        $data['asset_type_id'] = 10000;
        $data['checked_out_by'] = 0;
        $data['created_by'] = 42;
        $data['extension_instance_id'] = 2;
        $data['modified_by'] = 42;

        $results = $m->bind($data);

        if ($results === true) {
        } else {
            echo 'Bind failed';
            die;
        }

        $results = $m->validate();
        if ($results === true) {
        } else {
            echo 'Validation failed';
            die;
        }

        $results = $m->store();
        if ($results === true) {
        } else {
            echo 'Store failed';
            die;
        }
echo 'success';
die;
        $results = $this->store();


        if (isset($this->table->asset_type_id)) {
            $this->_storeRelated();
        }

        echo '<pre>';
        var_dump($data);
        die;
        /**
        echo $action;
        die;


        $hash = Services::Security()->getHash(MOLAJO_APPLICATION.get_class($this));

        $session = Services::Session()->create($hash);
var_dump($session);
echo 'back in app';
die;
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

        /** request: define processing instructions in page_request object */
        Molajo::Request()->process();

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

        if (Molajo::Request()->get('mvc_task') == 'add'
            || Molajo::Request()->get('mvc_task') == 'edit'
            || Molajo::Request()->get('mvc_task') == 'display'
        ) {
            Molajo::Parser();

        } else {

            /**
             * Action Task
             */
            //$this->_processTask();
        }

        /** responder: process rendered output */
        Molajo::Responder()->respond();

        return;
    }
}
