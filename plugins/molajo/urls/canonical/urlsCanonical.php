<?php
/**
 * @package     Molajo
 * @subpackage  Molajo URLs Canonical
 * @copyright   Copyright (C) 2011 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die;

jimport( 'joomla.plugin.plugin' );

class URLSCanonical extends JPlugin
{
	
	function onPrepareContent( &$article, &$params, $limitstart )
	{
			
	/**
	 * 	Article View, only
	 */
		$view = JRequest::getVar('view','article');
		if ($view !== 'article') {
			return;
		}
				
	/**
	 * rel="canonical" http://googlewebmastercentral.blogspot.com/2009/02/specify-your-canonical.html
	 */		
		$document =& JFactory::getDocument();

		$uri =& JFactory::getURI();
		$query = $uri->getQuery(true);
		$urlhost = $uri->toString(array('scheme', 'user', 'pass', 'host', 'port'));
		$ArticleURL = $urlhost . $article->readmore_link;
						
		$document =& JFactory::getDocument();
		$document->addHeadLink($ArticleURL, 'canonical', 'rel', '');
    }
}