<?php
/**
 * @version     $id: submenu.php
 * @package     Molajo
 * @subpackage  Submenu Helper
 * @copyright   Copyright (C) 2011 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die();

/**
 * Molajo SubMenu Helper
 *
 * @package	Molajo
 * @subpackage	Helper
 * @since	1.0
 */
class MolajoSubmenuHelper
{
    /**
     * add
     *
     * @since	1.0
     */
    public static function add ()
    {
        /** component parameters **/
        $params = MolajoComponentHelper::getParams(JRequest::getCmd('option'));

        /** Toolbar title and buttons **/
        for ($i=1; $i < 1000; $i++) {
            $value = $params->get('config_manager_sub_menu'.$i);
            if ($value == null) {
               break;
            }
        }
        $max = $i;

        /** toolbar title and buttons not desired **/
        if ($max == 1) {
            return;
        }

        /** loop thru config options **/
        for ($i=1; $i < $max; $i++) {

            $subMenuValue = $params->def('config_manager_sub_menu'.$i, 0);

            if (!$subMenuValue == '0') {
                $functionName = 'add'.ucfirst($subMenuValue).'Submenu';
                if (method_exists('MolajoSubmenuHelper',$functionName)) {
                    $submenu = new MolajoSubmenuHelper ();
                    $submenu->$functionName (JRequest::getCmd('option'), JRequest::getCmd('default_view'));
                }
            }
        }        

        return;
    }

    /**
     * addCategorySubmenu
     *
     * @param	array $permissions
     * @since	1.0
     */
    public function addCategorySubmenu ()
    {
        JSubMenuHelper::addEntry (
            JText::_('MOLAJO_SUBMENU_CATEGORIES'),
            'index.php?option=com_categories&extension='.JRequest::getCmd('option'),
            'categories'
        );
    }

    /**
     * addDefaultSubmenu
     *
     * @param	array $permissions
     * @since	1.0
     */
    public function addDefaultSubmenu ()
    {
        JSubMenuHelper::addEntry(
            JText::_('MOLAJO_SUBMENU_'.strtoupper(JRequest::getCmd('default_view'))),
            'index.php?option='.JRequest::getCmd('option').'&view='.JRequest::getCmd('default_view'),
            JRequest::getCmd('default_view')
        );
    }

    /**
     * addFeaturedSubmenu
     *
     * @param	array $permissions
     * @since	1.0
     */
    public function addFeaturedSubmenu ()
    {
        JSubMenuHelper::addEntry(
            JText::_('MOLAJO_SUBMENU_FEATURED'),
            'index.php?option='.JRequest::getCmd('option').'&view='.JRequest::getCmd('default_view').'&feature=1',
            JRequest::getCmd('default_view')
        );
    }

    /**
     * addStickiedSubmenu
     *
     * @param	array $permissions
     * @since	1.0
     */
    public function addStickiedSubmenu ()
    {
        JSubMenuHelper::addEntry(
            JText::_('MOLAJO_SUBMENU_STICKIED'),
            'index.php?option='.JRequest::getCmd('option').'&view='.JRequest::getCmd('default_view').'&sticky=1',
            JRequest::getCmd('default_view')
        );
    }

    /**
     * addUnpublishedSubmenu
     *
     * @param	array $permissions
     * @since	1.0
     */
    public function addUnpublishedSubmenu ()
    {
        JSubMenuHelper::addEntry(
            JText::_('MOLAJO_SUBMENU_UNPUBLISHED'),
            'index.php?option='.JRequest::getCmd('option').'&view='.JRequest::getCmd('default_view').'&publish=0',
            JRequest::getCmd('default_view')
        );
    }
}