<?php
/**
 * @package     Molajo
 * @subpackage  Helper
 * @copyright   Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @copyright   Copyright (C) 2011 Amy Stephen. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */
defined('MOLAJO') or die;

/**
 * Image Helper
 *
 * @package     Molajo
 * @subpackage  Submenu Helper
 * @since       1.0
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
                    $submenu->$functionName (JRequest::getCmd('option'), JRequest::getCmd('DefaultView'));
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
            JText::_('MOLAJO_SUBMENU_'.strtoupper(JRequest::getCmd('DefaultView'))),
            'index.php?option='.JRequest::getCmd('option').'&view='.JRequest::getCmd('DefaultView'),
            JRequest::getCmd('DefaultView')
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
            'index.php?option='.JRequest::getCmd('option').'&view='.JRequest::getCmd('DefaultView').'&feature=1',
            JRequest::getCmd('DefaultView')
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
            'index.php?option='.JRequest::getCmd('option').'&view='.JRequest::getCmd('DefaultView').'&sticky=1',
            JRequest::getCmd('DefaultView')
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
            'index.php?option='.JRequest::getCmd('option').'&view='.JRequest::getCmd('DefaultView').'&publish=0',
            JRequest::getCmd('DefaultView')
        );
    }
}