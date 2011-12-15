<?php
/**
 * @package    Molajo
 * @subpackage  HTML
 *
 * @copyright   Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('MOLAJO') or die;

/**
 * Renders a standard button
 *
 * @package    Molajo
 * @subpackage  HTML
 * @since       1.0
 */
class MolajoButtonStandard extends MolajoButton
{
    /**
     * Button type
     *
     * @var    string
     */
    protected $_name = 'Standard';

    public function fetchButton($type = 'Standard', $name = '', $text = '', $task = '', $list = true)
    {
        $i18n_text = MolajoTextHelper::_($text);
        $class = $this->fetchIconClass($name);
        $doTask = $this->_getCommand($text, $task, $list);

        $html = "<a href=\"#\" onclick=\"$doTask\" class=\"toolbar\">\n";
        $html .= "<span class=\"$class\">\n";
        $html .= "</span>\n";
        $html .= "$i18n_text\n";
        $html .= "</a>\n";

        return $html;
    }

    /**
     * Get the button CSS Id
     *
     * @return  string  Button CSS Id
     * @since   1.0
     */
    public function fetchId($type = 'Standard', $name = '', $text = '', $task = '', $list = true, $hideMenu = false)
    {
        return $this->_parent->getName() . '-' . $name;
    }

    /**
     * Get the JavaScript command for the button
     *
     * @param   string   $name    The task name as seen by the user
     * @param   string   $task    The task used by the application
     * @param   ???        $list
     *
     * @return  string   JavaScript command string
     * @since   1.0
     */
    protected function _getCommand($name, $task, $list)
    {
        MolajoHTML::_('behavior.framework');
        $message = MolajoTextHelper::_('MOLAJO_HTML_PLEASE_MAKE_A_SELECTION_FROM_THE_LIST');
        $message = addslashes($message);

        if ($list) {
            $cmd = "javascript:if (document.adminForm.boxchecked.value==0){alert('$message');}else{ Joomla.submitbutton('$task')}";
        }
        else {
            $cmd = "javascript:Joomla.submitbutton('$task')";
        }

        return $cmd;
    }
}