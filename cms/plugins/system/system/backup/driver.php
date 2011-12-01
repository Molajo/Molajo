<?php
/**
 * @package     Molajo
 * @subpackage  Molajo System Plugin
 * @copyright   Copyright (C) 2012 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die;

class MolajoSystemBackup
{

    /**
     * Minutes
     *
     * @var    string
     * @access    public
     */
    protected $days;

    /**
     * Driver
     *
     * Method called by plgMolajoSystem::MolajoOnAfterInitialise to schedule Backup
     *
     * @param    none
     * @return    boolean
     * @since    1.6
     */
    function driver()
    {
        /** system plugin **/
        $molajoSystemPlugin =& MolajoPlugin::getPlugin('system', 'molajo');
        $systemParameters = new JParameter($molajoSystemPlugin->parameters);

        /** backup **/
        if ($systemParameters->def('enable_backup', 0) == '0') {
            return false;
        }
        if ($systemParameters->def('backup_days', 7) == '0') {
            return false;
        }

        $this->days = $systemParameters->def('backup_days', 7);

        return true;
    }
}