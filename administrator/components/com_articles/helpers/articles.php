<?php
/**
 * @version     $id: articles.php
 * @package     Molajo
 * @subpackage  Articles Component
 * @copyright   Copyright (C) 2011 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('JPATH_PLATFORM') or die;

/**
 * ArticlesHelper
 *
 * Category Submenu Helper
 *
 * @package	Molajo
 * @subpackage	com_articles
 * @since	1.6
 */
class ArticlesHelper
{
    public static $extension = 'com_articles';

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
                JText::_('COM_ARTICLES_ARTICLES'),
                'index.php?option=com_articles&view=articles',
                $vName == 'articles'
        );
        JSubMenuHelper::addEntry(
                JText::_('COM_ARTICLES_SUBMENU_CATEGORIES'),
                'index.php?option=com_categories&extension=com_articles',
                $vName == 'categories'
        );
    }
}