<?php
/**
 * @package     Molajo
 * @subpackage  Application
 * @copyright   Copyright (C) 2012 Amy Stephen. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */
defined('MOLAJO') or die;

/**
 * MolajoMessage
 *
 * @package     Molajo
 * @subpackage  Application
 * @since       1.0
 */
class MolajoMessage
{
    /**
     *
     */
    $bum int;

    /**
     * Application Message Queue
     *
     * @var    array
     * @since  1.0
     */
    protected $_messageQueue = array();

    /**
     * _load
     *
     * Load published Messages
     *
     * @return  array
     */
    protected function load()
    {
        static $Messages;

        if (isset($Messages)) {
            return $Messages;
        }

        $Messages = MolajoExtension::getExtensions(MOLAJO_ASSET_TYPE_EXTENSION_Message);

        return $Messages;
    }

    /**
     * getMessage
     *
     * Get Message by name (real, eg 'Breadcrumbs' or folder, eg 'breadcrumbs')
     *
     * @param   string  The name of the Message
     * @param   string  The title of the Message, optional
     *
     * @return  object  The Message object
     */
    public static function getMessage($name, $title = null)
    {

    }


        /**
         * Get the system message queue.
         *
         * @return  array  The system message queue.
         *
         * @since  1.0
         */
        public function getMessageQueue()
        {
            /** initialize */
            $tmpmsg = array();
            $tmpobj = new JObject();
            $count = 0;

            /** are there messages? */
            foreach ($this->_messageQueue as $msg) {
                if ($msg['message'] == '') {
                } else {
                    $count++;
                }
            }

            /** pull in application session messages */
            if ($count == 0) {
                if (count(MolajoFactory::getSession()->get('application.queue'))) {
                    $this->_messageQueue = MolajoFactory::getSession()->get('application.queue');
                    MolajoFactory::getSession()->set('application.queue', null);
                }
                foreach ($this->_messageQueue as $msg) {
                    if ($msg['message'] == '') {
                    } else {
                        $count++;
                    }
                }
            }

            /** exit if no messages */
            if ($count == 0) {
                $_messageQueue = array();
                return $_messageQueue;
            }

            /** edit message queue */
            foreach ($this->_messageQueue as $msg) {

                if ($msg['message'] == '') {
                } else {
                    $tmpobj->set('message', $msg['message']);

                    if ($msg['type'] == 'message'
                        || $msg['type'] == 'notice'
                        || $msg['type'] == 'warning'
                        || $msg['type'] == 'error'
                    ) {

                    } else {
                        $msg['type'] == 'message';
                    }
                    $tmpobj->set('type', $msg['type']);
                    $tmpmsg[] = $tmpobj;
                    $count++;
                }
            }
            $_messageQueue = $tmpmsg;

            return $_messageQueue;
        }
        /**
         * Enqueue a system message.
         *
         * @param   string   $msg   The message to enqueue.
         * @param   string   $type  The message type. Default is message.
         *
         * @return  void
         *
         * @since  1.0
         */
        public function enqueueMessage($msg, $type = 'message')
        {
            if (count($this->_messageQueue)) {
            } else {

                $session = MolajoFactory::getSession();
                $sessionQueue = $session->get('application.queue');

                if (count($sessionQueue)) {
                    $this->_messageQueue = $sessionQueue;
                    $session->set('application.queue', null);
                }
            }

            $this->_messageQueue[] = array('message' => $msg, 'type' => strtolower($type));
        }

    /**
     * isEnabled
     *
     * Checks if a Message is enabled
     *
     * @param   string  The Message name
     *
     * @return  boolean
     */
    public static function isEnabled($Message)
    {
        $result = self::getMessage($Message);
        return (!is_null($result));
    }

    /**
     * getMessages
     *
     * Get Messages by position
     *
     * @param   string  $position    The position of the Message
     *
     * @return  array  An array of Message objects
     */
    public static function getMessages($position)
    {
    }


    /**
     * renderMessage
     *
     * Render the Message.
     *
     * @param   object  A Message object.
     * @param   array   An array of attributes for the Message (probably from the XML).
     *
     * @return  string  The HTML content of the Message output.
     */
    public static function renderMessage($MessageObject, $attribs = array())
    {
        $output = '';
        $Message = $MessageObject->Message;

        //echo '<pre>';var_dump($Message);'</pre>';
        // Record the scope.
        $scope = MolajoFactory::getApplication()->scope;

        // Set scope to Message name
        MolajoFactory::getApplication()->scope = $Message->title;

        // Get Message path
        $Message->title = preg_replace('/[^A-Z0-9_\.-]/i', '', $Message->title);
        $path = MOLAJO_CMS_MessageS . '/' . $Message->extension_name . '/' . $Message->extension_name . '.php';

        // Load the Message
        if (file_exists($path)) {

            $lang = MolajoFactory::getLanguage();
            $lang->load($Message->extension_name, MOLAJO_CMS_MessageS . '/' . $Message->extension_name, $lang->getDefault(), false, false);

            /** view */
            $view = new MolajoView ();

            /** defaults */
            $request = array();
            $state = array();
            $rowset = array();
            $pagination = array();
            $layout = 'default';
            if (isset($attribs->wrap)) {
                $wrap = $attribs->wrap;
            } else {
                $wrap = 'none';
            }

            $application = MolajoFactory::getApplication();
            $document = MolajoFactory::getDocument();
            $user = MolajoFactory::getUser();

            $parameters = new JRegistry;
            $parameters->loadString($Message->parameters);

            $request = self::getRequest($Message, $parameters, $wrap);

            $request['wrap_title'] = $Message->title;
            $request['wrap_subtitle'] = $Message->subtitle;
            $request['wrap_id'] = '';
            $request['wrap_class'] = '';
            $request['wrap_date'] = '';
            $request['wrap_author'] = '';
            $request['position'] = $Message->position;
            $request['wrap_more_array'] = array();

            /** execute the Message */
            include $path;

            /** 1. Application */
            $view->app = $application;

            /** 2. Document */
            $view->document = $document;

            /** 3. User */
            $view->user = $user;

            /** 4. Request */
            $view->request = $request;

            /** 5. State */
            $view->state = $Message;

            /** 6. Parameters */
            $view->parameters = $parameters;

            /** 7. Query */
            $view->rowset = $rowset;

            /** 8. Pagination */
            $view->pagination = $pagination;

            /** 9. Layout Type */
            $view->layout_type = 'extensions';

            /** 10. Layout */
            $view->layout = $layout;

            /** 11. Wrap */
            $view->wrap = $wrap;

            /** display view */
            ob_start();
            $view->display();
            $output = ob_get_contents();
            ob_end_clean();
        }

        MolajoFactory::getApplication()->scope = $scope;

        return $output;
    }

    /**
     * getRequest
     *
     * Gets the Request Object and populates Page Session Variables for Component
     *
     * @return bool
     */
    protected function getRequest($Message, $parameters, $wrap)
    {
        $session = MolajoFactory::getSession();

        /** 1. Request */
        $request = array();
        $request['application_id'] = $session->get('page.application_id');
        $request['current_url'] = $session->get('page.current_url');
        $request['component_path'] = $session->get('page.component_path');
        $request['base_url'] = $session->get('page.base_url');
        $request['item_id'] = $session->get('page.item_id');

        $request['controller'] = 'Message';
        $request['extension_type'] = 'Message';
        $request['option'] = $session->get('page.option');
        $request['view'] = 'Message';
        $request['model'] = 'Message';
        $request['task'] = 'display';
        $request['format'] = 'html';
        $request['plugin_type'] = 'content';

        $request['id'] = $session->get('page.id');
        $request['cid'] = $session->get('page.cid');
        $request['catid'] = $session->get('page.catid');
        $request['parameters'] = $parameters;

        $request['acl_implementation'] = $session->get('page.acl_implementation');
        $request['component_table'] = $session->get('page.component_table');
        $request['filter_name'] = $session->get('page.filter_name');
        $request['select_name'] = $session->get('page.select_name');
        $request['title'] = $Message->title;
        $request['subtitle'] = $Message->subtitle;
        $request['metakey'] = $session->get('page.metakey');
        $request['metadesc'] = $session->get('page.metadesc');
        $request['metadata'] = $session->get('page.metadata');
        $request['wrap'] = $wrap;
        $request['position'] = $Message->position;

        return $request;
    }
}
