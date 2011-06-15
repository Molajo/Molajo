<?php
/**
 * @version     $id: plugintype
 * @package     Molajo
 * @subpackage  HTML Class
 * @copyright   Copyright (C) 2011 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die();

/**
 * Select list of Plugin Types
 *
 * @static
 * @package		Joomla.Framework
 * @subpackage	HTML
 * @since		1.5
 */
abstract class JHtmlPluginType
{
    /**
     * Returns an array of plugin types
     *
     * @param	string	The extension option.
     * @param	array	An array of configuration options.
     *
     * @return	array
     */
    public function options()
    {

        /** option list **/
        $options	= array();
//        $options[]	= JHtml::_('select.option', 'authentication', JText::_('PLG_SYSTEM_CREATE_PLUGIN_TYPE_AUTHENTICATION'));
        $options[]	= JHtml::_('select.option', 'content', JText::_('PLG_SYSTEM_CREATE_PLUGIN_TYPE_CONTENT'));
//        $options[]	= JHtml::_('select.option', 'editors', JText::_('PLG_SYSTEM_CREATE_PLUGIN_TYPE_EDITORS'));
//        $options[]	= JHtml::_('select.option', 'editors-xtd', JText::_('PLG_SYSTEM_CREATE_PLUGIN_TYPE_EDITOR_BUTTONS'));
        $options[]	= JHtml::_('select.option', 'extension', JText::_('PLG_SYSTEM_CREATE_PLUGIN_TYPE_EXTENSION'));
        $options[]	= JHtml::_('select.option', 'search', JText::_('PLG_SYSTEM_CREATE_PLUGIN_TYPE_SEARCH'));
        $options[]	= JHtml::_('select.option', 'system', JText::_('PLG_SYSTEM_CREATE_PLUGIN_TYPE_SYSTEM'));
        $options[]	= JHtml::_('select.option', 'user', JText::_('PLG_SYSTEM_CREATE_PLUGIN_TYPE_USER'));

        return $options;

    }
}