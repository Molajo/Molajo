<?php
/**
 * @version		$Id: article.php 20196 2011-01-09 02:40:25Z ian $
 * @package		Joomla.Administrator
 * @copyright	Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access
defined('JPATH_PLATFORM') or die();

class MolajoElementArticle extends JElement
{
	/**
	 * Element name
	 *
	 * @var		string
	 */
	var	$_name = 'Article';

	function fetchElement($name, $value, $node, $control_name)
	{
		$fieldName	= $control_name.'['.$name.']';
		$article = JTable::getInstance('content');
		if ($value) {
			$article->load($value);
		} else {
			$article->title = JText::_('COM_CONTENT_SELECT_AN_ARTICLE');
		}

		$js = "
		function jSelectItem_".$name."(id, title, catid, object) {
			document.getElementById(object + '_id').value = id;
			document.getElementById(object + '_name').value = title;
			SqueezeBox.close();
		}";
		JFactory::getDocument()->addScriptDeclaration($js);

		$link = 'index.php?option=com_articles&amp;task=element&amp;tmpl=component&amp;function=jSelectItem_'.$name;

		JHtml::_('behavior.modal', 'a.modal');
		$html = "\n".'<div class="fltlft"><input type="text" id="'.$name.'_name" value="'.htmlspecialchars($article->title, ENT_QUOTES, 'UTF-8').'" disabled="disabled" /></div>';
		$html .= '<div class="button2-left"><div class="blank"><a class="modal" title="'.JText::_('COM_CONTENT_SELECT_AN_ARTICLE').'"  href="'.$link.'" rel="{handler: \'iframe\', size: {x: 650, y: 375}}">'.JText::_('JSELECT').'</a></div></div>'."\n";
		$html .= "\n".'<input type="hidden" id="'.$name.'_id" name="'.$fieldName.'" value="'.(int)$value.'" />';

		return $html;
	}
}