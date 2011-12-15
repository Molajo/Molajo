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
 * Renders a link button
 *
 * @package    Molajo
 * @subpackage  HTML
 * @since       1.0
 */
class MolajoButtonLink extends MolajoButton
{
    /**
     * Button type
     * @var    string
     */
    protected $_name = 'Link';

    public function fetchButton($type = 'Link', $name = 'back', $text = '', $url = null)
    {
        $text = MolajoTextHelper::_($text);
        $class = $this->fetchIconClass($name);
        $doTask = $this->_getCommand($url);

        $html = "<a href=\"$doTask\">\n";
        $html .= "<span class=\"$class\">\n";
        $html .= "</span>\n";
        $html .= "$text\n";
        $html .= "</a>\n";

        return $html;
    }

    /**
     * Get the button CSS Id
     *
     * @param   string  $type    The button type.
     * @param   string  $name    The name of the button.
     *
     * @return  string  Button CSS Id
     * @since   1.0
     */
    public function fetchId($type = 'Link', $name = '')
    {
        return $this->_parent->getName() . '-' . $name;
    }

    /**
     * Get the JavaScript command for the button
     *
     * @param   object  $definition    Button definition
     *
     * @return  string  JavaScript command string
     * @since   1.0
     */
    protected function _getCommand($url)
    {
        return $url;
    }
}