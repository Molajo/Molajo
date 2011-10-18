<?php
/**
 * @package     Molajo
 * @subpackage  Molajo Broadcast Email Plugin
 * @copyright   Copyright (C) 2011 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die;

jimport( 'joomla.plugin.plugin' );

class MolajoBroadcastEmail extends MolajoPlugin
{

	function OnBeforeContentSave ( &$article, $isNew )	{

			
	/**
	 * 	Initialization
	 */
$plugin =& MolajoPluginHelper::getPlugin('content', 'tamka_post_email');
$pluginParams = new JParameter( $plugin->params );


	/**
	 * 	Prepare Email Content - Author
	 */
		$email_author = '';
		if ($pluginParams->def('author', 1) !== 0) {
			$email_author = TamkaContentHelperRoute::getAuthorInfo ($article->id, $pluginParams->get('author'));
		}


	/**
	 * 	Prepare Email Content - Article
	 */
		$email_title	= '';
		if ($pluginParams->def('title', 1) == 1) {
			$email_title = $article->title;
		}

		if ($isNew) {
			$neworupdatedArticle = 	JText::_( ' a new post' );
			$neworupdatedQuery = ' AND params LIKE "%emailnotificationposts=1%" ';
		} else {
			$neworupdatedArticle = 	JText::_( ' an updated post' );
			$neworupdatedQuery = ' AND params LIKE "%emailnotificationposts=1%"';
		}

	/**
	 * 	Prepare content - Site name, Article title, URL
	 */

		global $mainframe;		
	 	$SiteName 		= $mainframe->getCfg('sitename');
		$articleURL = TamkaContentHelperRoute::getSiteURL () . TamkaContentHelperRoute::getArticleURL ($article->id);
		$ArticleTitle = $article->title;
		
		$mailfrom 		= $mainframe->getCfg('mailfrom');
		$fromname 		= $mainframe->getCfg('fromname');

	/**
	 * 	Format Email - Subject and Message
	*/
		$emailSubject	= '[' . $SiteName . '] ' .   $email_title;;

		$emailMessage = JText::_( 'At your request, ' );
		$emailMessage .= $SiteName . JText::_( ' is notifying you of a post' );
		if ($email_title) {
			$emailMessage .= JText::_( ' entitled "' ) . $email_title;
		}
		if ($email_author) {
			$emailMessage .= JText::_( '" written by ' ) . $email_author;
		} else {
			$emailMessage .= JText::_( '." ' );
		}

		if ($articleURL) {
			$emailMessage .= JText::_( ' To read more of this post, visit: ' ) . $articleURL;
		}
		$emailMessage .= '. ';

	/**
	 * 	Format Email - How to update User Settings
	*/
		$emailMessage .= JText::_( ' To discontinue these messages, please visit: ' ) . $articleURL;
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
				'   AND activation = "" ';
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