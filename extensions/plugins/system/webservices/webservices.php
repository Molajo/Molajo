<?php
/**
 * @package     Molajo
 * @subpackage  Molajo Web Services
 * @copyright   Copyright (C) 2011 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die;

class plgMolajoWebServices extends MolajoApplicationPlugin	{

    /**
     * @var string	Stores name of data element containing text for WebServices object
     * @since	1.6
     */
    protected $location;

    /**
     * plgMolajoWebServices::MolajoOnContentPrepare
     *
     * WebServices Component Plugin that interacts with external data
     *
     * @param	string		The context for the WebServices passed to the plugin.
     * @param	object		The WebServices object.
     * @param	object		The WebServices parameters
     * @param	stromg		The 'page' number
     * @return	string
     * @since	1.6
     */
    function MolajoOnContentPrepare ($context, &$content, &$parameters, $page = 0)
    {
        return;
        /** init **/
        if (!plgMolajoWebServices::initialization ($context, $content)) {
            return;
        }

        /** parameters **/
        $molajoSystemPlugin =& MolajoApplicationPlugin::getPlugin('system', 'molajo');
        $systemParameters = new JParameter($molajoSystemPlugin->parameters);
        $loc = $this->location;

        /** syntax highlighter **/
        if (($systemParameters->def('enable_google_maps', 0) == 1) && (!$systemParameters->def('google_maps_api_key', '') == '')) {
            require_once dirname(__FILE__).'/googlemaps/driver.php';
            MolajoWebServicesGoogleMaps::driver ($context, &$content, &$parameters, $page = 0, $this->location);
        }

        return;
    }
    
    /**
     * MolajoOnAfterRender
     *
     * Webservices Plugin that fires on OnAfterRender
     *
     * @param	none
     * @return	none
     * @since	1.6
     */
    function MolajoOnAfterRender()
    {
        /** admin check **/
        $app =& MolajoFactory::getApplication();
        if ($app->getName() == 'administrator') { return; }

        /** retrieve parameters for system plugin molajo library **/
        $molajoSystemPlugin =& MolajoApplicationPlugin::getPlugin('system', 'molajo');
        $systemParameters = new JParameter($molajoSystemPlugin->parameters);

        /** test for google analytics **/
        if (($systemParameters->def('enable_google_analytics', 0) == '1') && ($systemParameters->get('google_analytics_tracking_code'))) {
            require_once dirname(__FILE__).'/googleanalytics/driver.php';
            MolajoWebservicesGoogleAnalytics::driver ();
        }
    }

    /**
     * initialization
     * @param string $context
     * @param object $content
     * @return binary
     */
    function initialization ($context, $content) {

        /** request values **/
        $option = JRequest::getVar('option');
        $view = JRequest::getVar('view');
        if (($view == 'archive') || ($view == 'featured') || ($view == 'category') || ($view == 'categories')) {
            $multiple = true;
        } else {
            $multiple = false;
        }

        /** text location **/
        $this->location = '';
        if ($multiple == true) {
            if (isset($content->introtext)) {
                $this->location = 'introtext';
            } elseif (isset($content->text)) {
                $this->location = 'text';
            }
        } else {
            if (isset($content->text)) {
                $this->location = 'text';
            }
        }

        if ($this->location == '') {
            return false;
        }
        return true;
    }
}