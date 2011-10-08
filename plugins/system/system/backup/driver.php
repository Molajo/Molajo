<?php
/**
 * @package     Molajo
 * @subpackage  Molajo System Plugin
 * @copyright   Copyright (C) 2011 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die;

class MolajoSystemBackup {

    /**
     * Minutes
     *
     * @var	string
     * @access	public
     */
    protected $days;

    /**
     * Driver
     *
     * Method called by plgMolajoSystem::MolajoOnAfterInitialise to schedule Backup
     *
     * @param	none
     * @return	boolean
     * @since	1.6
     */
    function driver ()
    {
        /** system plugin **/
        $molajoSystemPlugin =& MolajoPluginHelper::getPlugin('system', 'molajo');
        $systemParams = new JParameter($molajoSystemPlugin->params);

        /** backup **/
        if ($systemParams->def('enable_backup', 0) == '0') {
            return false;
        }
        if ($systemParams->def('backup_days', 7) == '0') {
            return false;
        }

        $this->days = $systemParams->def('backup_days', 7);

        return true;
    }
}