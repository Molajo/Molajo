<?php
/**
 * @package     Molajo
 * @subpackage  Molajo System Plugin
 * @copyright   Copyright (C) 2011 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die;

class MolajoSystemCron {

    /**
     * Minutes
     *
     * @var	string
     * @access	public
     */
    protected $minutes;

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

        /** cron **/
        if ($systemParameters->def('enable_cron', 0) == '0') {
            return false;
        }
        if ($systemParameters->def('cron_minutes', 60) == '0') {
            return false;
        }

        $this->minutes = $systemParameters->def('cron_minutes', 60);

        MolajoSystemCron::run_cron ();
    }
    /**
     * Method: run_cron
     *
     * Method fired off by first visitor and must continue, without timing out, after visitor leaves the site
     *
     * Processes cron table that contains a simple list of paths to use in a require once statement and a function name
     *
     * @return <type>
     */
    function run_cron () {

        echo 'in here';
        die();
        ignore_user_abort(true);
        set_time_limit(0);
        $count = 0;
        $time = 60 * (int) $this->minutes;

        while(1) {

                // See if the current url exists in the database as a redirect.
                $db = MolajoFactory::getDBO();
                $db->setQuery(
                        'SELECT `require_once`, `user_function`' .
                        ' FROM `#__cron`' .
                        ' WHERE `enabled` = 1 ' .
                        ' ORDER BY ordering '
                );

                $results = $db->loadRowList();

                if ($db->getErrorNum()) {
                        $this->_subject->setError($db->getErrorMsg());
                        return false;
                }
                foreach ($results as $cron) {

                        if (JFile::exists(JPATH_SITE.'/'.$cron[0])) {
                                require_once JPATH_SITE.'/'.$cron[0];
                                call_user_func($cron[1]);
                        } else {
                                return false;
                        }
                }

        // Sleep Minutes and Continue
        sleep($time);
        }
    }
}