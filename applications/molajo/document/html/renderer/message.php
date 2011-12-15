<?php
/**
 * @package     Molajo
 * @subpackage  Document
 * @copyright   Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @copyright   Copyright (C) 2012 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die;

/**
 * MolajoDocument system message renderer
 *
 * @package    Molajo
 * @subpackage  Document
 * @since       1.0
 */
class MolajoDocumentRendererMessage extends MolajoDocumentRenderer
{
    /**
     * Renders the error stack and returns the results as a string
     *
     * @param   string  $name    (unused)
     * @param   array   $parameters  Associative array of values
     * @param   string  $content
     *
     * @return  string  The output of the script
     *
     * @since   11.1
     */
    public function render($name, $parameters = array(), $content = null)
    {
        // Initialise variables.
        $buffer = null;
        $lists = null;

        // Get the message queue
        $messages = MolajoFactory::getApplication()->getMessageQueue();

        // Record the scope.
        $scope = MolajoFactory::getApplication()->scope;

        // Set scope to module name
        MolajoFactory::getApplication()->scope = 'message';

        // Build the sorted message list
        if ($messages == null) {

        } else {

            /** view */
            $view = new MolajoView ();

            /** defaults */
            $request = array();
            $state = array();
            $parameters = array();
            $rowset = array();
            $pagination = array();
            $layout_type = 'document';
            $layout = 'messages';
            $wrap = 'div';

            $application = MolajoFactory::getApplication();
            $document = MolajoFactory::getDocument();
            $user = MolajoFactory::getUser();

            $parameters = new JRegistry;

            $request['wrap_title'] = '';
            $request['wrap_id'] = 'system-message-container';
            $request['wrap_class'] = '';
            $request['wrap_subtitle'] = '';
            $request['wrap_date'] = '';
            $request['wrap_author'] = '';
            $request['position'] = '';
            $request['wrap_more_array'] = array();

            /** 1. Application */
            $view->app = $application;

            /** 2. Document */
            $view->document = $document;

            /** 3. User */
            $view->user = $user;

            /** 4. Request */
            $view->request = $request;

            /** 5. State */
            $view->state = array();

            /** 6. Parameters */
            $view->parameters = $parameters;

            /** 7. Query */
            $view->rowset = $messages;

            /** 8. Pagination */
            $view->pagination = $pagination;

            /** 9. Layout Type */
            $view->layout_type = $layout_type;

            /** 10. Layout */
            $view->layout = $layout;

            /** 11. Wrap */
            $view->wrap = $wrap;

            /** display view */
            ob_start();
            $view->display();
            $buffer = ob_get_contents();
            ob_end_clean();
        }

        MolajoFactory::getApplication()->scope = $scope;

        return $buffer;
    }
}
