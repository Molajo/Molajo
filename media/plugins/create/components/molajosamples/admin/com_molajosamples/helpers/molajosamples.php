<?php
/**
 * @version     $id: com_molajosamples
 * @package     Molajo
 * @subpackage  Molajosamples Component
 * @copyright   Copyright (C) 2011 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('JPATH_PLATFORM') or die;

/**
 * MolajosamplesHelper
 *
 * Category Submenu Helper
 *
 * @package	Molajo
 * @subpackage	com_molajosamples
 * @since	1.6
 */
class MolajosamplesHelper
{
    public static $extension = 'com_molajosamples';

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
        JSubMenuHelper::addEntry(
                JText::_('MOLAJOSAMPLES_MOLAJOSAMPLES'),
                'index.php?option=com_molajosamples&view=molajosamples',
                $vName == 'molajosamples'
        );
        JSubMenuHelper::addEntry(
                JText::_('MOLAJOSAMPLES_SUBMENU_CATEGORIES'),
                'index.php?option=com_categories&extension=com_molajosamples',
                $vName == 'categories'
        );
    }
}