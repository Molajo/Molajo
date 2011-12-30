<?php
/**
 * @package     Molajo
 * @subpackage  Renderer
 * @copyright   Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
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
class MolajoMessageRenderer
{
    /**
     *  Message Output
     *
     * @var array
     * @since 1.0
     */
    protected $message_output = null;

    /**
     *  Parameters
     *
     * @var array
     * @since 1.0
     */
    protected $parameters = null;

    /**
     *  Config
     *
     * @var array
     * @since 1.0
     */
    protected $config = null;

    public function __construct($parameters = array(), $config = null)
    {
        $this->parameters = $parameters;
        $this->config = $config;
    }

    /**
     * getMessage
     *
     * Get message by name (real, eg 'Breadcrumbs' or folder, eg 'breadcrumbs')
     *
     * @param   string  The name of the message
     * @param   string  The title of the message, optional
     *
     * @return  object  The Message object
     */
    public static function getMessage($name, $title = null)
    {
        $result = null;
        $messages = self::_load();

        $total = count($messages);
        for ($i = 0; $i < $total; $i++)
        {
            // Match the name of the message
            if ($messages[$i]->title == $name) {
                // Match the title if we're looking for a specific instance of the message
                if (!$title || $messages[$i]->title == $title) {
                    $result = &$messages[$i];
                    break; // Found it
                }
            }
        }

        // If we didn't find it, and the name is something, create a dummy object
        if (is_null($result)) {
            $result = new stdClass;
            $result->id = 0;
            $result->title = '';
            $result->subtitle = '';
            $result->message = $name;
            $result->position = '';
            $result->content = '';
            $result->showtitle = 0;
            $result->showsubtitle = 0;
            $result->control = '';
            $result->parameters = '';
            $result->user = 0;
        }

        return $result;
    }

    /**
     * getMessages
     *
     * Get messages by position
     *
     * @param   string  $position    The position of the message
     *
     * @return  array  An array of message objects
     */
    public static function getMessages($position)
    {
        $position = strtolower($position);
        $result = array();

        $messages = self::_load();

        $total = count($messages);
        for ($i = 0; $i < $total; $i++)
        {
            if ($messages[$i]->position == $position) {
                $result[] = &$messages[$i];
            }
        }
        if (count($result) == 0) {

            if (JRequest::getBool('tp')
                && MolajoComponent::getParameters('templates')->get('template_positions_display')
            ) {
                $result[0] = self::getMessage('' . $position);
                $result[0]->title = $position;
                $result[0]->content = $position;
                $result[0]->position = $position;
            }
        }

        return $result;
    }

    /**
     * isEnabled
     *
     * Checks if a message is enabled
     *
     * @param   string  The message name
     *
     * @return  boolean
     */
    public static function isEnabled($message)
    {
        $result = self::getMessage($message);
        return (!is_null($result));
    }

    /**
     * renderMessage
     *
     * Render the message.
     *
     * @param   object  A message object.
     * @param   array   An array of attributes for the message (probably from the XML).
     *
     * @return  string  The HTML content of the message output.
     */
    public function render($message_object)
    {
        $output = '';
        $this->message_object = $message_object;
        $message = $message_object->message;

        //echo '<pre>';var_dump($message);'</pre>';

        // Get message path
        $message->title = preg_replace('/[^A-Z0-9_\.-]/i', '', $message->title);
        $path = MOLAJO_EXTENSIONS_MODULES . '/' . $message->extension_name . '/' . $message->extension_name . '.php';

        // Load the message
        if (file_exists($path)) {

            $lang = MolajoFactory::getLanguage();
            $lang->load($message->extension_name, MOLAJO_EXTENSIONS_MODULES . '/' . $message->extension_name, $lang->getDefault(), false, false);

            /** view */
            $view = new MolajoView ();

            /** defaults */
            $request = array();
            $state = array();
            $rowset = array();
            $pagination = array();
            $view = 'default';
            if (isset($this->parameters->wrap)) {
                $wrap = $this->parameters->wrap;
            } else {
                $wrap = 'none';
            }

            $application = MolajoFactory::getApplication();
            $user = MolajoFactory::getUser();

            $this->parameters = new JRegistry;
            $this->parameters->loadString($message->parameters);

///            $request = self::getRequest($message, $this->parameters, $wrap);

            $request['wrap_title'] = $message->title;
            $request['wrap_subtitle'] = $message->subtitle;
            $request['wrap_id'] = '';
            $request['wrap_class'] = '';
            $request['wrap_date'] = '';
            $request['wrap_author'] = '';
            $request['position'] = $message->position;
            $request['wrap_more_array'] = array();

            /** execute the message */
            include $path;

            /** 1. Application */
            $view->app = $application;

            /** 3. User */
            $view->user = $user;

            /** 4. Request */
            $view->request = $request;

            /** 5. State */
            $view->state = $message;

            /** 6. Parameters */
            $view->parameters = $this->parameters;

            /** 7. Query */
            $view->rowset = $rowset;

            /** 8. Pagination */
            $view->pagination = $pagination;

            /** 9. View Type */
            $view->view_type = 'extensions';

            /** 10. View */
            $view->view = $view;

            /** 11. Wrap */
            $view->wrap = $wrap;

            /** display view */
            ob_start();
            $view->display();
            $output = ob_get_contents();
            ob_end_clean();
        }

        return $output;
    }
}
