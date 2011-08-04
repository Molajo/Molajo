<?php
/**
 * @package		Tamka
 * @subpackage	Router
 * @copyright	Copyright (C) 2009 Tämkä Teäm and individual contributors. All rights reserved. See http://tamka.org/copyright
 * @license		GNU General Public License Version 2, or later
 */
defined( '_JEXEC' ) or die( 'Restricted access' );



class plgSystemTamka_Router extends JPlugin	{

/**
 * OnAfterInitialise
 *
 * Parse: for incoming requests - Molajo has already parsed the incoming URL, looked up the menu item,
 * 	populated variables, but has not yet routed the request. attachBuildRule allows one to override these
 * 	settings and to compensate with information that the core Router does not have. Runs one time per page load.
 *
 * Build: runs one time for each internal website URL presented on the Web page. In the same sense,
 * 	Molajo has already populated variables needed to write the URL to output. In attachBuildRule,
 * 	one can impact these Web links.
 *
 */
	function onAfterInitialise() {

		//	Only for Frontend, not Administrator
		$application =& JFactory::getApplication('JSite');
		$router =& $application->getRouter();

		if ($router->getMode() == JROUTER_MODE_SEF) {
			$router->attachBuildRule(array(&$this, 'TamkaBuildRoute'));
			$router->attachParseRule(array(&$this, 'TamkaParseURL'));
		}
	}
/**
 * Build URL for com_articles given SEF URL options
 */
	function TamkaBuildRoute(&$router, &$uri)	{

		$query = $uri->getQuery(true);
		if(isset($query['task'])) {
			return;
		}
		$option = '';
		if(isset($query['option'])) {
			$option = $query['option'];			
		}
		
		if($option == 'com_articles') {
			$option = $query['option'];
			$component	= preg_replace('/[^A-Z0-9_\.-]/i', '', $option);
			if (file_exists(JPATH_BASE . DS . 'plugins' . DS . 'system' . DS . 'tamka_router' . DS . $component . 'router.php')) {
				require_once(JPATH_BASE . DS . 'plugins' . DS . 'system' . DS. 'tamka_router' . DS . $component . 'router.php');
				$function = substr($component, 4) . 'TamkaBuildRoute'; 
				$function ($router, $uri);
			}
		}
		return;
	}

/**
 * 	Modify Query Variables, if necessary, for the Router
 * 		Molajo's Route function, which is the next event in J's processing. This parse function runs after
 * 		Molajo's Parse and makes adjustments needed to implement Tamka URLs. The process also provides
 * 		additional URL-related functions (No WWW, 301 handling, Custom 404 handling).
 *
 * 	This is the basic logic and flow covered by this function:
 *
 * 	1.	Initialization
 *
 * 	2.	No WWW logic  
 *
 * 	3.	Format and print parameters (don't process)
 *
 *  4.	Home Page - Enforce http://example.com/ (not index.php or /home, etc.)
 *  
 * 	5.	Form Actions - Forms do not use SEF URLs
 *
 * 	6.	Parameterized URLs - try to find good URL, then 301, else 404
 *
 *  7.	Normal Molajo Menu Item URLs - Tamka uses these and relies on Molajo's normal processing
 * 		When normal Menu Item URLs are found, this process lets them pass unchanged
 *
 *  8.	Component/object-name URLs - find good URL, then 301
 *  
 * 	9. 	Legacy Article SEF URLs and Internal Tiny URL
 *
 *  10.	Tamka URLs
 *
 *  11.	Last Resort - Check if there is a 301 table entry for a changed Alias 
 * 		Or if there is a numeric value that matches an Article (should help automatically migrate Molajo URLs)
 *  		Or if the last slug matches the Article Alias, then 301 - or 404
 *
 */
	function TamkaParseURL (&$router, &$uri)	{

	 /**
	 * 	1.	Initialization
	 */
		//	No SEF URL Routing when in Backend Administrator
		global $mainframe;
		if ($mainframe->isAdmin()) {
			return $vars;
		}
		
		require_once(JPATH_BASE . DS . 'plugins' . DS . 'system' . DS. 'tamka_router' . DS . 'tamka_router_functions.php');

		$route = $uri->getPath();
		$query = $uri->getQuery(true);

		$vars   			= array();
		$vars 				= $uri->getQuery(true);

	 	$redirectTo			= '';

		$task = JRequest::getVar('task', '');
		if ($task !== "") {
			return $vars;
		}
		$option = JRequest::getVar('option', '');
		if ($option !== "") {
			if ($option !== "com_articles") {
				return $vars;
			}
		}

	 	// Load Tamka Library is ready to load
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

	 	/*	Get Path and Query */
		$uri				= &JFactory::getURI();
		$uriString			= strtolower($uri->toString(array('path', 'query')));

	 	/*	Remove base and left forward slash '/' */
 		$base = JURI::base(true) . '/';
		if (trim($base) == '/') {
 			$uriString = substr($uriString, 1, (strlen($uriString) - 1));
		} else {
			$uriString = str_replace ( $base, '', $uriString );
		}

	 	/*	Remove the file extension, if exists */
	 	$parameterExtension 	= $this->params->def('extension', '');
  		$uriString = str_replace ( $parameterExtension, '', $uriString );

	 	/*	Remove the right forward slash '/'  */
 		if (substr($uriString, (strlen($uriString) - 1), 1) == '/') {
 			$uriString = substr($uriString, 0, (strlen($uriString) - 1));
 		}

	 	/*	Preserve original path for possible 301/404 processing */
 		$uriPathPreserved = $uriString;
		JRequest::setVar('currentURL', $uriPathPreserved );

	 	/*	Place path segments into an array for processing */
		$uriArray				= Array();
		if (trim($uriString) == '') {
			$uriArrayCount 		= 0;
		} else {
			$uriArray 			= explode('/', $uriString);
			$uriArrayCount 		= count($uriArray);
		}

		if(isset($query['option'])) {
			$option = $query['option'];
			if ( $option == 'com_articles') {
			} else {
				return $vars;		
			}			
		}

	 /**
	 * 	2.	No WWW logic
	 */
		$hostname = $uri->toString(array('host'));
		$parameterNoWWW	= $this->params->def('noWWW', 0);

		if (($parameterNoWWW == 1) && (strtolower(substr($hostname, 0, 4)) == 'www.')) {

			$hostname = substr($hostname, 4, (strlen($hostname) - 3));

			/*	Rebuild the URL with No WWW and 301 Redirect */
			$redirectTo = $uri->toString(array('scheme')) . $hostname;
			
			if (($uri->toString(array('path')) == '//') || ($uri->toString(array('path')) == '/') ) {
			} else {
				$redirectTo .= $uri->toString(array('path'));
				if ($uri->toString(array('query')) == '') {
				} else {
					$redirectTo .= '/' . $uri->toString(array('query'));
					if ($uri->toString(array('fragment')) == '') {
					} else {
						$redirectTo .= '/' . $uri->toString(array('fragment'));
					}
				}
			}
			global $mainframe;
			header('Location: ' . htmlspecialchars( $redirectTo ), true, '301');
			$mainframe->redirect($redirectTo);
			$app = & JFactory::getApplication();
			$app->close();
			return;	
    	}
    	
	 /**
	 *  3.	Format and print parameters (don't process)
	 */
		if ( (strpos($uriString, 'format=')) || (strpos($uriString, 'print=')) ) {
			 if (substr($uriString, 0, 9) == 'index.php') {
			 	// default menu -- http://amystephen.us/amystephen?format=feed&type=rss
			 } else {
			 	// find menu
			 }
			return $vars;
		}
		  	
		if ( (strpos($uriString, '=')) || (strpos($uriString, '&')) ) {
			return $vars;
		}
		    	
//		amy rss if ( (strpos($uriString, 'format=')) || (strpos($uriString, 'print=')) || strpos($uriString, '&searchphrase=')) ) {
//		if (strpos($uriString, '&searchphrase=')) {

	 /**
	 *  4.	Home Page - Enforce http://example.com/ (not index.php or /home, etc.)
	 */		
		/*	http://example.com/index.php -> http://example.com */
   		if ((trim($uriString) == 'index.php') || (trim($uriString) == 'index.htm') || (trim($uriString) == 'index.html')) {
			plgSystemTamka_Router::process301s ('', $parameterExtension);
		}
		/*	Default Home menu item -> http://example.com */
   		if ($uriString == getDefaultItemidAlias ()) {
			plgSystemTamka_Router::process301s ('', $parameterExtension);
		}
		/*	Home	*/
   		if ($uriString == '') {
   			return $vars;
		}

	/**
	* 	5.	Form Actions - Forms do not use SEF URLs
	*/
		if ((JRequest::getCmd('method') == 'post') || (JRequest::getCmd('task') !== '')) {
			return $vars;
		}
		
	 /**
	 * 	6.	Parameterized URLs
	 * 		a.  If it's an Article request, build a good SEF URL and 301 redirect 
	 * 			index.php?option=com_articles&view=article&id=5:joomla-license-guidelines&catid=25:the-project&Itemid=2
	 * 		b.	If there is a Menu Item match, build a good SEF URL and 301 redirect
	 * 		c.  If there is a 301 table entry, build a good SEF URL and redirect
	 * 		d.  Or, 404
	 */
		if ( (strpos($uriString, '?') ) || ( substr($uriString, 0, 9) == 'index.php' )) {

			if(isset($query['option'])) {
				//	Article
				if ($query['option'] == 'com_articles') {
					$id = $query['id'];
					if (stripos($id, ':')) {
						$id = substr($id, 0, stripos($id, ':'));
					}
					$redirectTo = TamkaContentHelperRoute::getArticleURL ($id);
					if ($redirectTo !== '') {
						plgSystemTamka_Router::process301s ($redirectTo, $parameterExtension);
					}
				}
			}
			
			/*	Menu Item Match */
			if(isset($query['Itemid'])) {
				$findItemID = $query['Itemid'];
			} else {
				$findItemID = parameterizedURI ($uriString);
			}
		
			if ($findItemID !== 0) {
				JRequest::setVar('Itemid', $findItemID);
				JURI::setVar('Itemid', $findItemID);
				$Itemid = JRequest::getInt('Itemid');
	
				/*	See what the default (home) Menu ID is						*/
				$redirectTo = TamkaContentHelperRoute::getMenuItemAliasURI ($findItemID);
				if ($redirectTo !== '') {
					plgSystemTamka_Router::process301s ($redirectTo, $parameterExtension);
				}
			}

			/*	301 table entry - or - 404 error  */
			plgSystemTamka_Router::process_301_404_errors ($uriPathPreserved, $parameterExtension);
		}

	 /**
	 * 	7.	Normal Molajo Menu Item URLs - these are identified and allowed to pass
	 * 		Function: cascadeMenuItems - matches URL segments to Menu Item Alias Tree
	 */
		$results = cascadeMenuItems ($uriArray, $uriArrayCount);
		if ($results) {
			return $vars;
		}

	 /**
	 * 	8.	Component/object-name URLs - find good URL, then 301
	 */	
		if ($uriArray[0] == 'component') {
			if (($uriArray[1] == 'content') && ($uriArray[2] == 'article')) {
				if (stripos($uriArray[3], ':')) {
					$id = substr($uriArray[3], 0, stripos($uriArray[3], ':'));
				}
				$redirectTo = TamkaContentHelperRoute::getArticleURL ($id);
				if ($redirectTo !== '') {
					plgSystemTamka_Router::process301s ($redirectTo, $parameterExtension);
				}
			}
		}

	 /**
	 * 	9. Legacy Article SEF URLs and Internal Tiny URL
	 */	
		if (stripos($uriArray[$uriArrayCount - 1], ':')) {
			$id = substr($uriArray[$uriArrayCount - 1], 0, stripos($uriArray[$uriArrayCount - 1], ':'));
			$redirectTo = TamkaContentHelperRoute::getArticleURL ($id);
			if ($redirectTo !== '') {
				plgSystemTamka_Router::process301s ($redirectTo, $parameterExtension);
			}
		}
				
	/**
	 * 	10.	Tamka URLs - Initialization
	 * 
	 * 	At this point, we are looking for real Tamka URLs. 
	 * 		These could be "summary pages" or detail level URLs
	 *  
	 *  Summary page URLs can get very complex with tagging and paging
	 *  
	 *  	http://example.com/index.php/xxxxxx/tag/tag-value/page/1
	 *  
	 *  	xxxxx - could be menu item or base of Tamka URL (ex. category or section/category, etc)
	 *  	tag - could also be tag menu - or calendar date - or nothing
	 * 		page - will be the final piece 
	 * 
	*/
		//	Initialization
		$sectionValue = "";
		$sectionAlias = "";
		$secid = 0;

		$categoryValue = "";
		$categoryAlias = "";
		$catid = 0;

		$ccyy = "";
		$mm = "";
		$dd = "";

		$detailValue = "";
		$detailAlias = "";
		$id = 0;

		$tag = "";
		$tagmenu = "";
		$page = "";
		$calccyy = "";
		$calmm = "";
		$caldd = "";		
		$format = "";
		$type = "";
		
		$plugin 		=& JPluginHelper::getPlugin( 'system', 'tamka_router');
		$pluginParams 	= new JParameter($plugin->params);

		$tagbaseValue = trim(strtolower($pluginParams->def('tagbase', 'tag')));
		$tagmenubaseValue = trim(strtolower($pluginParams->def('tagmenubase', 'menu')));
		$datebaseValue = trim(strtolower($pluginParams->def('datebase', 'date')));
		
		$formatbaseValue = 'format';
		$typebaseValue = 'type';		
		
		$pagebaseValue = trim(strtolower($pluginParams->def('page', 'page')));		

		/* 	Needed in order to check for /index.php/ */
		$app =& JFactory::getApplication();
				
	/**
	 *	10A. Menu item portion of URL will be next (possibly) 
	 * 		Look for keyword literals (Tags or Menu or Date)
	 * 			ex. http://example.com/xxxxx/tag/tag-alias/  
	 * 		Hold preceding URL segments (/xxxxx/) for later processing 
	 */	
		$i = 0;		
		
		/*	For Core SEF Pathinfo, index.php will be first */
		$pathinfo = '';

		if ($app->getCfg('sef_rewrite') == 1) {
		} else {
			if ($uriArray[$i] == 'index.php') {
				$pathinfo = 'index.php';
				$i++;
			}
		}

		$menuOrTamkaURLSegments = array();
		$iMenu = 0;
		$foundIt = false;
		
		while ($i < $uriArrayCount) {

			if ($uriArray[$i] == $tagbaseValue) {
				$i++;
				if ($i <= $uriArrayCount) {
					$tag = $uriArray[$i];
					$foundIt = true;
					$i++;
				}
				break;	
								
			} else if ($uriArray[$i] == $tagmenubaseValue) {
				$i++;
				if ($i < $uriArrayCount) {
					$tagmenu = $uriArray[$i];
					$foundIt = true;
					$i++;								
				}
				break;			

			} else if ($uriArray[$i] == $datebaseValue) {
				$i++;
				if (($i < $uriArrayCount) && ($uriArray[$i] !== $pagebaseValue) ) {
					$calccyy = $uriArray[$i];
					$foundIt = true;
					$i++;
				}
				if (($i < $uriArrayCount) && ($uriArray[$i] !== $pagebaseValue) ) {
					$calmm = $uriArray[$i];
					$i++;
				}
				if (($i < $uriArrayCount) && ($uriArray[$i] !== $pagebaseValue) ) {
					$caldd = $uriArray[$i];
					$i++;
				}					
				break;	
				
			} else if ($uriArray[$i] == $formatbaseValue) {
				$i++;
				if ($i < $uriArrayCount) {
					$format = $uriArray[$i];
					$foundIt = true;
					$i++;								
				}
				break;
				
			} else if ($uriArray[$i] == $typebaseValue) {
				$i++;
				if ($i < $uriArrayCount) {
					$type = $uriArray[$i];
					$foundIt = true;
					$i++;								
				}
				break;								
			}
								
			/* Could be a menu item if one of above was found */
			if ($uriArray[$i] == $pagebaseValue) {
				break;	
			} else {
				$menuOrTamkaURLSegments[$iMenu] = $uriArray[$i];
				$iMenu++;
			}
			$i++;
		}

		JRequest::setVar('tag', $tag );
		JRequest::setVar('tagmenu', $tagmenu );	
		JRequest::setVar('calccyy', $calccyy );
		JRequest::setVar('calmm', $calmm );
		JRequest::setVar('caldd', $caldd );
		if (($format == "") || ($type == "")) {
		} else {
			JRequest::setVar('format', $format );		
			JRequest::setVar('type', $type );
		}		
		
	/**
	 *	10B. Now, check if there is paging:	 
	 * 	Restart? (ex. http://example.com/xxxxx/page/1)
	 * 	Or, keep going (ex. http://example.com/xxxxx/tag/tag-alias/page/1) 
	 * 	Again, hold preceding URL segments (/xxxxx/) for later processing   
	 */
		if ($foundIt == true) {
		} else {
			$menuOrTamkaURLSegments = array();
			$iMenu = 0;
			if ($pathinfo == '') {
				$i = 0;
			} else {
				$i = 1;
			} 
		}

		while ($i < $uriArrayCount) {
																	
			if ($uriArray[$i] == $pagebaseValue) {
				$i++;
				if ($i < $uriArrayCount) {
					$page = $uriArray[$i];
					$i++;					
				}
				break;	
			}
								
			/* Could be a menu item if one of above was found */
					/* Could be a menu item if one of above was found */
			if ($uriArray[$i] == $pagebaseValue) {
				break;	
			} else {
				$menuOrTamkaURLSegments[$iMenu] = $uriArray[$i];
				$iMenu++;
			}
			$i++;
		}
		
		JRequest::setVar('limitstart', $page );
		
	/**
	 *	10C. See if /xxxxx/ is a menu item string
	 */
		$countmenuOrTamkaURLSegments = $iMenu;

		if ($countmenuOrTamkaURLSegments == 0) {
			$results = getDefaultItemid();
		} else {
			$results = cascadeMenuItems ($menuOrTamkaURLSegments, $countmenuOrTamkaURLSegments);
		}
		if ($results) {

			//Set query again in the URI
			$Itemid 			= $results;
			$vars['Itemid'] 	= $Itemid;

		 	/*	Set the Active Itemid (Menu Item) */
			$menu  				=& JSite::getMenu(true);
			$menu->setActive($Itemid);

			setItemIDOptions ($Itemid);
			
			/*	Update the path */
			$uri->setQuery('');
			$uri = &JFactory::getURI();
			$uri->setQuery($vars);
			return $vars;
		}

	/**
	 *	10D. Process /xxxxx/ as a Tamka URL - either "summary" level or "detail"
	 */		
		/*	Parameter 1: Tamka URL Option	*/
		$customSegments = array();
		$i = 0;

		/*	1. http://example.com/category/detail */
		if ($pluginParams->def('option', 1) == 1) {
			$customSegments[$i] = 'category';
			$i++;
			$customSegments[$i] = 'detail';

		/*	2. http://example.com/section/category/detail */
		} else if ($pluginParams->def('option', 1) == 2) {
			$customSegments[$i] = 'section';
			$i++;
			$customSegments[$i] = 'category';
			$i++;
			$customSegments[$i] = 'detail';

		/*	3. http://example.com/section/detail (use category when no section)	*/
		} else if ($pluginParams->def('option', 1) == 3) {
			$customSegments[$i] = 'section';
			$i++;
			$customSegments[$i] = 'detail';

		/*	4. http://example.com/ccyy/mm/dd/detail	*/
		} else if ($pluginParams->def('option', 1) == 4) {
			$customSegments[$i] = 'ccyy';
			$i++;
			$customSegments[$i] = 'mm';
			$i++;
			$customSegments[$i] = 'dd';
			$i++;
			$customSegments[$i] = 'detail';

		/*	5. http://example.com/ccyy/mm/detail  */
		} else if ($pluginParams->def('option', 1) == 5) {
			$customSegments[$i] = 'ccyy';
			$i++;
			$customSegments[$i] = 'mm';
			$i++;
			$customSegments[$i] = 'detail';

		/*	6. Custom URL pattern: section, category, ccyy, mm, dd, detail		*/
		} else if ($pluginParams->def('option', 1) == 6) {
			$customSegments = explode ('/', strtolower($pluginParams->get('customurl')));
		}

		/*	Given parameter options selected, process URL segments, without tags and page values  */
		$i = 0;
		
		foreach ($customSegments as $customSegment)	{

			/*	Exit if there are no more URL segments */
			if ($i < $countmenuOrTamkaURLSegments) {
			} else {
				break;
			}

			/*	Is there a value for the next segment? If so, what is it?*/
			if (strtolower($customSegment) == 'section') {
				$sectionValue = $uriArray[$i];
			} elseif (strtolower($customSegment) == 'category') {
				$categoryValue = $uriArray[$i];
			} elseif (strtolower($customSegment) == 'ccyy') {
				$ccyy = $uriArray[$i];
			} elseif (strtolower($customSegment) == 'mm') {
				$mm = $uriArray[$i];
			} elseif (strtolower($customSegment) == 'dd') {
				$dd = $uriArray[$i];
			} elseif (strtolower($customSegment) == 'detail') {
				$detailValue = $uriArray[$i];
			} else {
				break;
			}
			$i++;
		}

		/*	Parameter 3: Use ID (2) or Alias (1) */
		if ($pluginParams->def('idoralias', 1) == 1) {
			$sectionAlias 	= $sectionValue;
			$categoryAlias 	= $categoryValue;
			$detailAlias 	= $detailValue;
		} else {
			$secid 			= $sectionValue;
			$catid 			= $categoryValue;
			$id 			= $detailValue;
		}
		
		$found = componentTamkaParseRoute
			($secid, $sectionAlias, $catid, $categoryAlias, $ccyy, $mm, $dd, $detailAlias, $id);

		if ($found) {
			return $vars;
		}
				
	/**
	 * 	11.	Check for possible 301 table entries, Article ID or Alias,
	 * 		Otherwise these will 404 - page not found
	*/
		plgSystemTamka_Router::process_301_404_errors ($uriPathPreserved, $parameterExtension, $vars);
		// amy
		return $vars;		

		/*	Logic should not reach this point - either a 301 or a 404 will have happened */
		return;
	}

	/**
	 * 	Function: process_301_404_errors
	 *
	 * 		If a URL cannot be identified with a Molajo Web page, this process is used to:
	 * 		a) See if a permanent 301 redirect has been defined in the #__301_redirect (if so, use it.)
	 * 		b) If not, send to the 404 function to log the error and redirect to a custom 404 page
	 *
	 * 		Note: the #__301_redirects table can be used for URL Migrations from older implementations,
	 * 			moving from a different website deployment, or minor navigation changes on the existing site
	*/
	function process_301_404_errors ($uriPathPreserved, $parameterExtension )	{

		$db	=& JFactory::getDBO();
		$redirectTo = '';

		$query = 'SELECT new_path ' .
			' FROM #__tamka_301_redirects ' .
			' WHERE old_path = "' . Trim($uriPathPreserved) . '"';

		$db->setQuery($query);
		$redirectTo = $db->loadResult();

		/*	End of the road - handle 404						*/
		if ($redirectTo) {
		} else {

			/*	Extract last segment to see if Article ID can be determined */
			$lastSegment = substr($uriPathPreserved, strrpos($uriPathPreserved, '/') + 1, 999);

			/*	Article ID	*/
			$id = 0;
			if (stripos($lastSegment, '-')) {
				$id = substr($lastSegment, 0, stripos($lastSegment, '-'));
			}

			/*	Get Article ID using Article Alias */
			if ($id == 0) {
			}  else {
				$id = getArticleIDforAlias ($lastSegment);
			}

			/*	Get new URL for Article ID */
			if (is_numeric($id)) {
				$redirectTo = TamkaContentHelperRoute::getArticleURL ($id);
			}
		}

		/*	End of the road - handle 404						*/
		if ($redirectTo) {
		} else {
			// amy
			return;
			plgSystemTamka_Router::process404s ($uriPathPreserved, $parameterExtension);
		}

		plgSystemTamka_Router::process301s ($redirectTo, $parameterExtension);
	}

	/**
	 * 	9 - process301s
	 *
	 * 		1.	In some cases, this function is used for intentional 301's
	 * 			An example would be rerouting an incoming parameterized URL to an SEF URL
	 *
	 * 		2. 	This is also used for permanent 301 redirects defined in the #__301_redirect table
	*/
	 function process301s ($redirectTo, $parameterExtension)	{

		global $mainframe;

		if ($redirectTo == '') {
			$redirectTo = JURI::base();
		} else {
			$redirectTo = JURI::base() . $redirectTo . $parameterExtension;
		}

		header('Location: ' . htmlspecialchars( $redirectTo ), true, '301');
		$mainframe->redirect($redirectTo);
		$app = & JFactory::getApplication();
		$app->close();
		return;
	}

	/**
	 * 	10: process404s
	 *
	 * 		Log 404 errors and redirect to 404 page
	*/
	function process404s ($uriPathPreserved, $parameterExtension)	{

		global $mainframe;
		$db	=& JFactory::getDBO();

		try {
				$referer = ' ';
				if (isset($_SERVER['HTTP_REFERER'])) {
						$referer = $_SERVER['HTTP_REFERER'];
				}

				$query = 'INSERT INTO #__tamka_404_log (`id`,`path`,`referer`,`visit_timedate`) ' .
					'	VALUES (NULL,\''.$uriPathPreserved.$parameterExtension.'\',\''.$referer.'\',CURRENT_TIMESTAMP)';
				$db->setQuery($query);
				$db->query();
			}
			catch (Exception $e) {
			header('HTTP/1.0 404 Not Found');
		}

		//	Retrieve URL for Custom Error Page		//
		$query = 'SELECT alias 													' .
			' FROM #__menu	 													' .
			' WHERE link = "index.php?option=com_tamka_error&view=error" 		' .
			'   AND published = 1 												';

		$db->setQuery($query);
		$results = $db->loadObjectList();

		$TamkaErrorUrl = 'index.php?option=com_tamkaerror&view=error';
		foreach ($results as $row)		{
			$TamkaErrorUrl = $row->alias;
		}

		/*	to do - figure out how to actually get a 404 header without a core hack		*/
		/*	the page redirected to exists and will therefore send a 200 header			*/
		/*	this means Google Webmaster Services, etc. will show this as a valid page 	*/
		JResponse::setHeader('HTTP/1.0', '404 Not Found');
		$redirectTo = JURI::base() . $TamkaErrorUrl . $parameterExtension;
		$mainframe->redirect($redirectTo);
		$app = & JFactory::getApplication();
		$app->close();
		return;
	}
}
?>