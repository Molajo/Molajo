<?php
/**
 * @package     Molajo
 * @subpackage  Molajo Responses Social Bookmarks
 * @copyright   Copyright (C) 2011 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die;

jimport( 'joomla.plugin.plugin' );

class ResponsesSocialBookmarks extends MolajoPlugin
{
	function onAfterDisplayContent ( &$article, &$params, $limitstart )
	{

	/**
	 * 		Get Print Parameter
	*/
		$uri =& MolajoFactory::getURI();
		$query = $uri->getQuery(true);
		if(isset($query['print'])) {
			$print = $query['print'];
		} else {
			$print = false;
		}
		if ($print == true) {
			return;
		}
		$document =& MolajoFactory::getDocument();

		$option = JRequest::getVar('option','com_articles');
		if ($option !== 'com_articles') {
				return;
		}

		//	Load Style tags
		$document->addStyleSheet( JURI::base() . 'plugins/content/tamka_article_social_bookmark/socialbookmark.css' );

		//	Get Parameters
		$plugin =& MolajoPluginHelper::getPlugin('content', 'tamka_article_social_bookmark');
		$pluginParams = new JParameter( $plugin->params );

		$parameterblogandarticle = $pluginParams->def('blogandarticle', 0);
		$parametersquareorcircle = $pluginParams->def('squareorcircle', 0);
		$parameterposition = $pluginParams->def('position', 0);

		$includeBlinklist 	= $pluginParams->def('blinklist', 1);
		$includeDelicious 	= $pluginParams->def('delicious', 1);
		$includeDigg 		= $pluginParams->def('digg', 1);
		$includeFlickr 		= $pluginParams->def('flickr', 1);
		$includeFurl 		= $pluginParams->def('furl', 1);
		$includeMagnolia 	= $pluginParams->def('magnolia', 1);
		$includeNewsvine 	= $pluginParams->def('newsvine', 1);
		$includeReddit 		= $pluginParams->def('reddit', 1);
		$includeStumbleupon = $pluginParams->def('stumbleupon', 1);
		$includeTechnorati 	= $pluginParams->def('technorati', 1);
		$includeTwitter 	= $pluginParams->def('twitter', 1);		
		$includeTweetmeme 	= $pluginParams->def('tweetmeme', 1);		

		/* 	What Categories should be included or excluded?		*/
		$showCategoriesAll = false;
		$showCategories = explode(',', $pluginParams->get('categories'));	
		if ($pluginParams->get('categories')) {
		} else {
			$showCategoriesAll = true;
		}
		$includeorexclude = $pluginParams->def('include_or_exclude', 'Include');

		// 	Is it the right Category?
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
		$view = JRequest::getVar('view','article');
		// Display bookmarks on full article page, only
		if (($parameterblogandarticle == 0) && ($view !== 'article')) {
			return;
		}

		// Display bookmarks on blogs and article pages? Or, article page, only
		if ($parametersquareorcircle == 0) {
			$sizeshape = 'circle24-';
		} else {
			$sizeshape = 'square24-';
		}

		// Retrieve and Encode the Article URL and Title
		$uri	 = &MolajoFactory::getURI();
		$urlhost = $uri->toString(array('scheme', 'user', 'pass', 'host', 'port'));

		$ArticleTitle = $article->title;
		$ArticleURL = $urlhost . $article->readmore_link;
		
	/**
	 *	Retrieve Tiny URL (Make certain Tamka Library is ready to load)
	 */
		if (!file_exists(JPATH_PLUGINS . DS . 'system' . DS . 'tamka.php')) {
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
		$tinyURL = TamkaContentHelperRoute::urlShortener ( $ArticleURL, $article->id );
		
	/**
	 * Encode
	 */	
		$encodedURL = urlencode('url=' . $ArticleURL);
		$encodedURL = ('url=' . $ArticleURL);
		$encodedTitle = urlencode('&title=' . $ArticleTitle );
		$encodedshortTitle = urlencode(trim(substr($ArticleTitle, 0, 80)));		
		$encoded = $encodedURL . $encodedTitle;

		// Add the xHTML to the body of the page
		if ($parameterposition == 1) {
			$bookmarks = '<ul class="socialbookmark-top">';
		} else {
			$bookmarks = '<ul class="socialbookmark-bottom">';
		}

		// Add Tags
		if ($includeBlinklist == 1) {
			$bookmarks .= '<li class="blinklist"><a href="http://blinklist.com/index.php?Action=Blink/addblink.php&amp;' . $encoded . '"><img src="' . JURI::base() . 'plugins/content/tamka_article_social_bookmark/' . $sizeshape . 'blinklist.png" alt="Blinklist" /></a></li>';
		}
		if ($includeDelicious == 1) {
			$bookmarks .= '<li class="delicious"><a href="http://del.icio.us/post?' . $encoded . '"><img src="' . JURI::base() . 'plugins/content/tamka_article_social_bookmark/' . $sizeshape . 'delicious.png" alt="del.icio.us" /></a></li>';
		}
		if ($includeDigg == 1) {
			$bookmarks .= '<li class="digg"><a href="http://digg.com/submit?phase=2&amp;' . $encoded . '"><img src="' . JURI::base() . 'plugins/content/tamka_article_social_bookmark/' . $sizeshape . 'digg.png" alt="digg" /></a></li>';
		}
		if ($includeFurl == 1) {
			$bookmarks .= '<li class="furl"><a href="http://furl.net/storeIt.jsp?u=' . urlencode($ArticleURL) . '&amp;t=' . urlencode($ArticleTitle) . '"><img src="' . JURI::base() . 'plugins/content/tamka_article_social_bookmark/' . $sizeshape . 'furl.png" alt="furl" /></a></li>';
		}
		if ($includeMagnolia == 1) {
			$bookmarks .= '<li class="magnolia"><a href="http://ma.gnolia.com/bookmarklet/add?' . $encoded . '"><img src="' . JURI::base() . 'plugins/content/tamka_article_social_bookmark/' . $sizeshape . 'magnolia.png" alt="magnolia" /></a></li>';
		}
		if ($includeNewsvine == 1) {
			$bookmarks .= '<li class="newsvine"><a href="http://www.newsvine.com/_tools/seed&amp;save?popoff=0&amp;u=' . urlencode($ArticleURL) . '&amp;h=' . urlencode($ArticleTitle) . '"><img src="' . JURI::base() . 'plugins/content/tamka_article_social_bookmark/' . $sizeshape . 'newsvine.png" alt="newsvine" /></a></li>';
		}
		if ($includeReddit == 1) {
			$bookmarks .= '<li class="reddit"><a href="http://reddit.com/submit?' . $encoded . '"><img src="' . JURI::base() . 'plugins/content/tamka_article_social_bookmark/' . $sizeshape . 'reddit.png" alt="reddit" /></a></li>';
		}
		if ($includeStumbleupon == 1) {
			$bookmarks .= '<li class="stumbleupon"><a href="http://www.stumbleupon.com/submit?' . $encoded . '"><img src="' . JURI::base() . 'plugins/content/tamka_article_social_bookmark/' . $sizeshape . 'stumbleupon.png" alt="stumbleupon" /></a></li>';
		}
		if ($includeTechnorati == 1) {
			$bookmarks .= '<li class="technorati"><a href="http://www.technorati.com/faves?add=' . urlencode($ArticleURL) . '"><img src="' . JURI::base() . 'plugins/content/tamka_article_social_bookmark/' . $sizeshape . 'technorati.png" alt="technorati" /></a></li>';
		}
		if ($includeTwitter == 1) {
			$bookmarks .= '<li class="twitter"><a href="http://twitter.com/home/?status=' . $encodedshortTitle . '+' . urlencode($tinyURL) . '"><img src="' . JURI::base() . 'plugins/content/tamka_article_social_bookmark/Twitter_24x24.png" alt="tweet this" /></a></li>';
		}
		if (($includeTweetmeme == 1) && ($view == 'article')) {		
			$bookmarks .= '<li class="tweetmeme"><script type="text/javascript" src="http://tweetmeme.com/i/scripts/button.js"></script></li>';
		}			
		$bookmarks .= '</ul>';

		// Add the xHTML to the body of the page
		if ($parameterposition == 1) {
			$article->text = '<div class="socialbookmark-top">' . $bookmarks . '</div><div class="socialbookmark-top2"></div>' . $article->text;
		} else {
			$article->text.= '<div class="socialbookmark-bottom">' . $bookmarks . '</div>';
		}

		// Exit successfully
		return;
	}
}