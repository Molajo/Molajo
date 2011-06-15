<?php
/**
 * @package     Molajo
 * @subpackage  Molajo Protect Bad Words Plugin
 * @copyright   Copyright (C) 2011 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die();
	
class protectBadWords
{
	function checkWords ($cleanString)
	{		
	/**
	 * 	Retrieve User Group Parameter for Auto Publish 
	 */
		$tamkaLibraryPlugin 	=& JPluginHelper::getPlugin( 'system', 'tamka');
		$tamkaLibraryPluginParams = new JParameter($tamkaLibraryPlugin->params);	
		
	/**
	 * 	Filter content through array of Bad Words
	 */
		$badWords = explode(",", $tamkaLibraryPluginParams->def('badword', ''));
		return str_replace($badWords, '', $cleanString);	
			
	}
}