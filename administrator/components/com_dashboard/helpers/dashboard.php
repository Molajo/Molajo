<?php
/**
 * @version     $id: dashboard.php
 * @package     Molajo
 * @subpackage  Articles Component
 * @copyright   Copyright (C) 2011 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die;

/**
 * ArticlesHelper
 *
 * Category Submenu Helper
 *
 * @package	Molajo
 * @subpackage	com_dashboard
 * @since	1.6
 */
class ArticlesHelper
{
    public static $extension = 'com_dashboard';

    /**
     * Configure the Linkbar.
     *
     * @param	string	$vName	The name of the active view.
     *
     * @return	void
     * @since	1.6
     */
    public static function addSubmenu($vName)
    {
        MolajoSubMenuHelper::addEntry(
                MolajoText::_('COM_DASHBOARDS_DASHBOARDS'),
                'index.php?option=com_dashboard&view=dashboard',
                $vName == 'dashboard'
        );
        MolajoSubMenuHelper::addEntry(
                MolajoText::_('COM_DASHBOARDS_SUBMENU_CATEGORIES'),
                'index.php?option=com_categories&extension=com_dashboard',
                $vName == 'categories'
        );
    }
}