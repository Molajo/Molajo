<?php
/**
 * @package     Molajo
 * @subpackage  Molajo Responses Subscriptions
 * @copyright   Copyright (C) 2011 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die;

jimport( 'joomla.plugin.plugin' );

class ResponsesSubscriptions extends MolajoPlugin
{

	function OnBeforeContentSave ( &$article, $isNew )	{
	
	/**
	 * Make certain Tamka Library is ready to load
	 */
		 if (!file_exists(JPATH_PLUGINS.DS.'system'.DS.'tamka.php')) {
		  		JError::raiseWarning( '700', JText::_('The Tamka Library is required for this extension.' ));
		 		return NULL;
		 }
		 if (!function_exists('tamkaimport')) {
		 		JError::raiseWarning( '725', JText::_('The Tamka Library must be enabled for this extension.' ));
		 		return NULL;
		 }
		 if (!version_compare('0.1', 'TAMKA')) {
		 		JError::raiseWarning( '750', JText::_('The Tamka Library Version is outdated.' ));
		 		return NULL;
		 }
		tamkaimport('tamka.routehelper.content');
				
	/**
	 * 	Determine if Article was Published prior to save
	 */		
		$currentlyPublished = TamkaContentHelperRoute::checkArticleforPublished ($article->id);
		JRequest::setVar('onBeforePublished', $currentlyPublished);

	}
		
	function onAfterContentSave( &$article, $isNew )	{
	
	/**
	 * Make certain Tamka Library is ready to load
	 */
		 if (!file_exists(JPATH_PLUGINS.DS.'system'.DS.'tamka.php')) {
		  		JError::raiseWarning( '700', JText::_('The Tamka Library is required for this extension.' ));
		 		return NULL;
		 }
		 if (!function_exists('tamkaimport')) {
		 		JError::raiseWarning( '725', JText::_('The Tamka Library must be enabled for this extension.' ));
		 		return NULL;
		 }
		 if (!version_compare('0.1', 'TAMKA')) {
		 		JError::raiseWarning( '750', JText::_('The Tamka Library Version is outdated.' ));
		 		return NULL;
		 }
		tamkaimport('tamka.routehelper.content');

	/**
	 * 	Article must be Published as of this moment with public access
	 */		
		$results = TamkaContentHelperRoute::checkArticleforBroadcast ($article->id);
		if ($results == false) {
			return;
		}
			
	/**
	 * 	Initialization
	 */
		$plugin =& MolajoPluginHelper::getPlugin('content', 'tamka_post_email');
		$pluginParameters = new JParameter( $plugin->parameters );

	/**
	 * 	Should Tamka email?
	 */	
		/* 	What Categories should be included or excluded?		*/
		$showCategoriesAll = false;
		$showCategories = explode(',', $pluginParameters->get('categories'));
		if ($pluginParameters->get('categories')) {
		} else {
			$showCategoriesAll = true;
		}
		$includeorexclude = $pluginParameters->def('include_or_exclude', 'Include');

		// 	Is this the right Category?
		$show = false;
		if ($article->sectionid == 0 && $article->catid == 0) {
			$show = false;
			return;
		}
		if ($includeorexclude == 'Include' && (in_array($article->catid, $showCategories) || $showCategoriesAll)) {
			$show = true;
		}
		if ($includeorexclude == 'Exclude' && (in_array($article->catid, $showCategories) == false) && ($showCategoriesAll == false)) {
			$show = true;
		}
		if ($show == false) {
			return;
		}

	/**
	 * 	Determine if Article is moving from Unpublished to Published state
	 */		
		$currentlyPublished = TamkaContentHelperRoute::checkArticleforPublished ($article->id);	
				
		//	If published state was 0 in before update - and is now 1 - it's a new publish		
		$onBeforePublished = JRequest::getVar('onBeforePublished');
		if ($onBeforePublished == 1)  {
			return;
		}

	/**
	 * 	Prepare Email Content - Author
	 */
		$email_author = '';
		if ($pluginParameters->def('author', 1) !== 0) {
			$email_author = TamkaContentHelperRoute::getAuthorInfo ($article->id, $pluginParameters->get('author'));
		}


	/**
	 * 	Prepare Email Content - Article
	 */
		$email_title	= '';
		if ($pluginParameters->def('title', 1) == 1) {
			$email_title = $article->title;
		}

		if ($isNew) {
			$neworupdatedArticle = 	JText::_( ' a new post' );
			$neworupdatedQuery = ' AND parameters LIKE "%emailnotificationposts=1%" ';
		} else {
			$neworupdatedArticle = 	JText::_( ' an updated post' );
			$neworupdatedQuery = ' AND parameters LIKE "%emailnotificationposts=1%"';
		}

	/**
	 * 	Prepare content - Site name, Article title, URL
	 */

		global $mainframe;		
	 	$SiteName 		= $mainframe->getSiteConfig('sitename');
		$articleURL = TamkaContentHelperRoute::getSiteURL ().TamkaContentHelperRoute::getArticleURL ($article->id);
		$ArticleTitle = $article->title;
		
		$mailfrom 		= $mainframe->getSiteConfig('mailfrom');
		$fromname 		= $mainframe->getSiteConfig('fromname');

	/**
	 * 	Format Email - Subject and Message
	*/
		$emailSubject	= '['.$SiteName.'] '.  $email_title;;

		$emailMessage = JText::_( 'At your request, ' );
		$emailMessage .= $SiteName.JText::_( ' is notifying you of a post' );
		if ($email_title) {
			$emailMessage .= JText::_( ' entitled "' ).$email_title;
		}
		if ($email_author) {
			$emailMessage .= JText::_( '" written by ' ).$email_author;
		} else {
			$emailMessage .= JText::_( '." ' );
		}

		if ($articleURL) {
			$emailMessage .= JText::_( ' To read more of this post, visit: ' ).$articleURL;
		}
		$emailMessage .= '. ';

	/**
	 * 	Format Email - How to update User Settings
	*/
		$emailMessage .= JText::_( ' To discontinue these messages, please visit: ' ).$articleURL;
		$emailMessage .= JText::_( ' and update your User Settings. Thanks! ' ) ;
		
	/**
	 * 	Format Email - encoding
	*/
		$emailSubject	= html_entity_decode ($emailSubject, ENT_QUOTES);
		$emailMessage 	= html_entity_decode ($emailMessage, ENT_QUOTES);

	/**
	 * 	Send Email - retrieve list and send individually
	*/
		$db	=& MolajoFactory::getDBO();

		$query = 'SELECT name, email ' .
				' FROM #__users ' .
				' WHERE block = 0 ' .
				'   AND activated = "" ';
		$query .= $neworupdatedQuery;

		$db->setQuery( $query );
		$rows = $db->loadObjectList();

		foreach ( $rows as $row ) {
			$name 			= $row->name;
			$email 			= $row->email;
			JUtility::sendMail($mailfrom, $fromname, $email, $emailSubject, $emailMessage);
		}
		return;
	}
}