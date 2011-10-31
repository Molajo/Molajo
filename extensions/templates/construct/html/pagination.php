<?php defined('_JEXEC') or die;
/**
* @package		Template Framework for Molajo 1.6
* @author		Joomla Engineering http://joomlaengineering.com
* @copyright	Copyright (C) 2010 Matt Thomas | Joomla Engineering. All rights reserved.
* @license		GNU/GPL v2 or later http://www.gnu.org/licenses/gpl-2.0.html
*/

/**
 * This is a file to add template specific chrome to pagination rendering.
 *
 * pagination_list_footer
 *	 Input variable $list is an array with offsets:
 *		 $list[limit]		: int
 *		 $list[limitstart]	: int
 *		 $list[total]		: int
 *		 $list[limitfield]	: string
 *		 $list[pagescounter]	: string
 *		 $list[pageslinks]	: string
 *
 * pagination_list_render
 *	 Input variable $list is an array with offsets:
 *		 $list[all]
 *			 [data]		: string
 *			 [active]	: boolean
 *		 $list[start]
 *			 [data]		: string
 *			 [active]	: boolean
 *		 $list[previous]
 *			 [data]		: string
 *			 [active]	: boolean
 *		 $list[next]
 *			 [data]		: string
 *			 [active]	: boolean
 *		 $list[end]
 *			 [data]		: string
 *			 [active]	: boolean
 *		 $list[pages]
 *			 [{PAGE}][data]		: string
 *			 [{PAGE}][active]	: boolean
 *
 * pagination_item_active
 *	 Input variable $item is an object with fields:
 *		 $item->base	: integer
 *		 $item->link	: string
 *		 $item->text	: string
 *
 * pagination_item_inactive
 *	 Input variable $item is an object with fields:
 *		 $item->base	: integer
 *		 $item->link	: string
 *		 $item->text	: string
 *
 * This gives template designers ultimate control over how pagination is rendered.
 *
 * NOTE: If you override pagination_item_active OR pagination_item_inactive you MUST override them both
 */

function pagination_list_render($list)
{
	// Initialize variables
	$lang =& MolajoFactory::getLanguage();
	$html = "<ol class=\"pagination\">";

	$html .= $list['start']['data'];
	$html .= $list['previous']['data'];

	foreach( $list['pages'] as $page )
	{
		if($page['data']['active']) {
			// $html .= '<strong>';
		}

		$html .= $page['data'];

		if($page['data']['active']) {
			//  $html .= '</strong>';
		}
	}

	$html .= $list['next']['data'];
	$html .= $list['end']['data'];
	// $html .= '&#171;';

	$html .= "</ol>";
	return $html;
}

function pagination_item_active(&$item) {
	return "<li><a href=\"".$item->link."\" title=\"".$item->text."\">".$item->text."</a></li>";
}

function pagination_item_inactive(&$item) {
	return "<li>".$item->text."</li>";
}
?>