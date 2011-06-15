<?php
/** 
 * @package     Minima
 * @subpackage  mod_myshortcuts
 * @author      Marco Barbosa
 * @copyright   Copyright (C) 2010 Marco Barbosa. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

// Check to ensure this file is included in Joomla!
defined('JPATH_PLATFORM') or die;

abstract class ModMyshortcutsHelper
{
    /**
     * Stack to hold default buttons
     */
    protected static $buttons = array();

    // module parameters
    protected static $params = "";

    /**
     * Setter just to set the parameters
     *
     * @return  boolean true to show, false to not
     */
     public static function setParams($_params)
     {
         self::$params = $_params;
     }

    /**
     * Helper method to generate a button in administrator panel
     *
     * @param   array   A named array with keys link, image, text, access and imagePath
     * @return  string  HTML for button
     */
    public static function button($button)
    {
        if ( !empty($button['access']) )
        {
            if ( !JFactory::getUser()->authorize($button['access']) ) {
                return '';
            }
        }

        ob_start();
        require JModuleHelper::getLayoutPath('mod_myshortcuts', 'button');
        $html = ob_get_clean();
        return $html;
    }

    /**
     * Helper method if it's to show the add link or not
     *
     * @return  boolean true to show, false to not
     */
     public static function showLink()
     {
         return self::$params->get('show_add_link') == 0 ? false : true;
     }

     /**
     * Helper method to return button list.
     *
     * This method returns the array by reference so it can be
     * used to add custom buttons or remove default ones.
     *
     * @return  array   An array of buttons
     */
    public static function &getButtons2()
    {
        if ( !empty(self::$params) )
        {
            /*foreach($params as $param):

            endforeach;*/
            self::$buttons = array(
                array(
                    'link' => JRoute::_('index.php?option=com_config'),
                    'text' => 'Global Config',
                    'access' => array('manage','com_config')
                ),
                array(
                    'link' => JRoute::_('index.php?option=com_articles'),
                    'text' => 'Content',
                    'access' => array('manage','com_articles')
                )
            );
        }

        return self::$buttons;
    }


    /**
     * Helper method to return button list.
     *
     * This method returns the array by reference so it can be
     * used to add custom buttons or remove default ones.
     *
     * @return  array   An array of buttons
     */
    public static function &getButtons()
    {
        if ( empty(self::$buttons) )
        {
            self::$buttons = array(
                array(
                    'link' => JRoute::_('index.php?option=com_config'),
                    'text' => JText::_('MOD_MYSHORTCUTS_CONFIGURATION'),
                    'access' => array('manage','com_config')
                ),
                array(
                    'link' => JRoute::_('index.php?option=com_articles'),
                    'text' => JText::_('MOD_MYSHORTCUTS_ARTICLES'),
                    'access' => array('manage','com_articles')
                ),
                array(
                    'link' => JRoute::_('index.php?option=com_articles&task=article.add'),
                    'text' => JText::_('MOD_MYSHORTCUTS_ADD_ARTICLE'),
                    'access' => array('manage','com_articles')
                ),
                array(
                    'link' => JRoute::_('index.php?option=com_media'),
                    'text' => JText::_('MOD_MYSHORTCUTS_MEDIA'),
                    'access' => array('manage','com_media')
                ),
                array(
                    'link' => JRoute::_('index.php?option=com_menus&view=menus'),
                    'text' => JText::_('MOD_MYSHORTCUTS_MENUS'),
                    'access' => array('manage','com_menus')
                ),
                array(
                    'link' => JRoute::_('index.php?option=com_users'),
                    'text' => JText::_('MOD_MYSHORTCUTS_USERS'),
                    'access' => array('manage','com_users')
                ),
                /*array(
                    'link' => JRoute::_('index.php?option=com_modules'),
                    'text' => JText::_('MOD_SHORTCUTS_MODULES'),
                    'access' => array('manage','com_modules')
                ),*/
                array(
                    'link' => JRoute::_('index.php?option=com_installer'),
                    'text' => JText::_('MOD_MYSHORTCUTS_EXTEND'),
                    'access' => array('manage','com_installer')
                ),
                array(
                    'link' => JRoute::_('index.php?option=com_admin&view=help'),
                    'text' => JText::_('MOD_MYSHORTCUTS_HELP'),
                    'access' => array('manage','com_admin')
                )
            );
        }
        return self::$buttons;
    }
}
