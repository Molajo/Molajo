<?php
/**
 * @package     Molajo
 * @subpackage  Extension
 * @copyright   Copyright (C) 2012 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die;

/**
 * Molajo Extension Class
 *
 * Base class
 */
class MolajoExtension
{
    /**
     * Configuration
     *
     * @var    integer
     * @since  1.0
     */
    protected $config = null;

    /**
     *  User
     *
     * @var string
     * @since 1.0
     */
    protected $user = null;

    /**
     * Template
     *
     * @var object
     * @since 1.0
     */
    protected $template = null;

    /**
     *  Page
     *
     * @var string
     * @since 1.0
     */
    protected $page = null;

    /**
     *  Buffered output
     *
     * @var string
     * @since 1.0
     */
    protected $buffered_output = array();

    /**
     *  Render Type
     *
     *  1: HTML Webpage
     *      a. parse template for doc statements
     *      b. process doc statements (render and buffer output) for:
     *          html-head
     *          message
     *          component
     *          module
     *          modules
     *
     *  2: feed
     *
     *  3: JSON Webpage
     *      a. process (render and buffer output) for:
     *          json-head
     *          component
     *
     *  4: raw
     *      a. process (render and buffer output) for:
     *          text-head
     *          component
     *
     *  5: xml
     *      a. process:
     *          xml-head
     *          component
     */

    /**
     * __construct
     *
     * Class constructor.
     *
     * @param   array  $config  A configuration array
     *
     * @since  1.0
     */
    public function __construct($request = null, $asset_id = null)
    {
        /** Asset for Request */
        $this->asset = new MolajoAsset ($request = null, $asset_id = null);

        /** Already Redirected */
        if ($this->asset->redirect_to_id == 0) {
        } else {
            return;
        }

        /** User */
        $this->loadUser();

        /** Authorise */
        $this->authorise();

        /** Document */
        $this->renderDocumentType();

        /** Event */
        //   MolajoPlugin::importPlugin('system');
        //   MolajoFactory::getApplication()->triggerEvent('onAfterInitialise');
    }

    /**
     * route
     *
     * Route the application.
     *
     * Routing is the process of examining the request environment to determine which
     * component should receive the request. The component optional parameters
     * are then set in the request object to be processed when the application is being
     * dispatched.
     *
     * @return  void;
     * @since  1.0
     */
    public function route()
    {
        /** trigger onAfterRoute Event */
        MolajoPlugin::importPlugin('system');
        MolajoFactory::getApplication()->triggerEvent('onAfterRoute');
    }

    /**
     * Load User
     *
     * @since   1.0
     */
    private function loadUser()
    {
        $this->user = MolajoFactory::getUser();
    }

    /**
     * Execute Extension
     *
     * @return  boolean
     *
     * @since   1.0
     */
    public function authorise()
    {
        if (in_array($this->asset->view_group_id, $this->user->view_groups)) {
            return true;
        } else {
            //            $this->redirect($url, MolajoTextHelper::_('JGLOBAL_YOU_MUST_LOGIN_FIRST'));
            return false;
        }
        //        MolajoError::raiseError(403, MolajoTextHelper::_('ERROR_NOT_AUTHORIZED'));
    }

    /**
     * Get Header information for Page
     *
     * @return  void
     *
     * @since   1.0
     */
    public function renderDocumentType()
    {
        $documentTypeClass = 'Molajo'.$this->asset->format;
        $results = new $documentTypeClass ();
    }
}