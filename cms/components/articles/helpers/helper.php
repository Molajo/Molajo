<?php
/**
 * @package     Molajo
 * @subpackage  Helper
 * @copyright   Copyright (C) 2012 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die;

/**
 * ArticlesHelper
 *
 * @package      Molajo
 * @subpackage   Helpers
 * @since        1.0
 */
class ArticlesHelper
{
    public static $extension = 'articles';

    /**
     * Configure the Linkbar.
     *
     * @param    string    $vName    The name of the active view.
     *
     * @return    void
     * @since    1.6
     */
    public static function addSubmenu($vName)
    {
        MolajoSubMenuHelper::addEntry(
            MolajoTextHelper::_('ARTICLES_ARTICLES'),
            'index.php?option=articles&view=articles',
            $vName == 'articles'
        );
        MolajoSubMenuHelper::addEntry(
            MolajoTextHelper::_('ARTICLES_SUBMENU_CATEGORIES'),
            'index.php?option=categories&extension=articles',
            $vName == 'categories'
        );
    }
}