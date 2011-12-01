<?php
/**
 * @version     $id: articles.php
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
 * @subpackage	articles
 * @since	1.6
 */
class ArticlesHelper
{
    public static $extension = 'articles';

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
                MolajoText::_('ARTICLES_ARTICLES'),
                'index.php?option=articles&view=articles',
                $vName == 'articles'
        );
        MolajoSubMenuHelper::addEntry(
                MolajoText::_('ARTICLES_SUBMENU_CATEGORIES'),
                'index.php?option=categories&extension=articles',
                $vName == 'categories'
        );
    }
}