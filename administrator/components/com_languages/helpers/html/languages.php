<?php
/**
 * @version		$Id: languages.php 21020 2011-03-27 06:52:01Z infograf768 $
 * @copyright	Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;

/**
 * Utility class working with languages
 *
 * @package		Joomla.Administrator
 * @subpackage	com_languages
 * * * @since		1.0
 */
abstract class JHtmlLanguages {
	/**
	 * method to generate an information about the default language
	 *
	 * @param	boolean	$published is the language the default?
	 *
	 * @return	string	html code
	 */
	public static function published($published)
	{
		if ($published) {
			return JHtml::_('image','menu/icon-16-default.png', MolajoText::_('COM_LANGUAGES_HEADING_DEFAULT'), NULL, true);
		}
		else {
			return '&#160;';
		}
	}

	/**
	 * method to generate an input radio button
	 *
	 * @param	int		$rowNum the row number
	 * @param	string	language tag
	 *
	 * @return	string	html code
	 */
	public static function id($rowNum,$language)
	{
		return '<input type="radio" id="cb'.$rowNum.'" name="cid" value="'.htmlspecialchars($language).'" onclick="isChecked(this.checked);" title="'.($rowNum+1).'"/>';
	}

	public static function applications()
	{
		return array(
			JHtml::_('select.option', 0, MolajoText::_('JSITE')),
			JHtml::_('select.option', 1, MolajoText::_('JADMINISTRATOR'))
		);
	}

	/**
	 * Returns an array of published state filter options.
	 *
	 * @return	string			The HTML code for the select tag
	 * @since	1.0
	 */
	public static function publishedOptions()
	{
		// Build the active state filter options.
		$options	= array();
		$options[]	= JHtml::_('select.option', '1', 'JPUBLISHED');
		$options[]	= JHtml::_('select.option', '0', 'JUNPUBLISHED');
		$options[]	= JHtml::_('select.option', '-2', 'JTRASHED');
		$options[]	= JHtml::_('select.option', '*', 'JALL');

		return $options;
	}

}

