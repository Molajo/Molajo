<?php 
/**
* @id $Id$
* @author  Joomla Bamboo
* @package  JB Library
* @copyright Copyright (C) 2006 - 2010 Joomla Bamboo. http://www.joomlabamboo.com  All rights reserved.
* @license  GNU/GPL license: http://www.gnu.org/copyleft/gpl.html
*/
// no direct access
defined('_JEXEC') or die('Restricted access');
class JElementPanel extends JElement
{
	   var   $_name = 'Panel';
	   function fetchElement($name, $value, &$node, $control_name)
	   {
		   	//when our code starts the second td in a tr are open
		   	//we close the second td in tr
			$panel = '</td></tr>';
			//we close the current table and divs
			$panel .= '</tbody></table></div></div>';
			//we open the new table and divs
			//we retrieve the panel id and title attributes and add them to the toggle div
			$panel .= '<div class="pane-sliders">
			<div class="panel">
			<h3 class="jpane-toggler title"id="'.JText::_($node->attributes('panel')).'">
			<span>'.JText::_($node->attributes('title')).'</span>
			</h3><div class="jpane-slider content">
			<table width="100%" class="paramlist admintable" cellspacing="1"><tbody>';
			//we open and close the first td and open the second td
			$panel .= '<tr><td></td><td>';
			//we allow the normal element function to close the td and tr
			return $panel;
	   }
}
?>