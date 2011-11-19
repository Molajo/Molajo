<?php
/**
 * @package     Molajo
 * @subpackage  Molajo System Plugin
 * @copyright   Copyright (C) 2011 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die;

class MolajoSystemOffline {

    /**
     * Minutes
     *
     * @var	string
     * @access	protected
     */
    protected $bypass;

    /**
     * Driver
     *
     * Method called by plgMolajoSystem::MolajoOnAfterInitialise to schedule Cron
     *
     * @param	none
     * @return	boolean
     * @since	1.6
     */
    function driver ()
    {
        /** system plugin **/
        $molajoSystemPlugin =& MolajoPluginHelper::getPlugin('system', 'molajo');
        $systemParameters = new JParameter($molajoSystemPlugin->parameters);

        /** config **/
        $config =& MolajoFactory::getConfig();

        /** verify function needed **/
        if ($config->setValue('config.offline', 0) == 0) {
            return false;
        }

        if ($systemParameters->def('enable_offline_bypass', '') == '') {
            return false;
        }

        if (JRequest::getString('bypass', '') == '') {
            return false;
        }

        /** verify bypass **/
        $this->bypass = $JRequest::getString('bypass', '');
        if ($this->bypass == $systemParameters->def('bypass', '')) {
            $config->setValue('config.offline', 0);
            setcookie('bypass', $this->bypass);
            return true;
        }

        return false;
    }
}