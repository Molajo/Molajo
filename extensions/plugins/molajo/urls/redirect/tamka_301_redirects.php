<?php
/**
 * @package		Tamka
 * @subpackage	301 Redirects
 * @copyright	Copyright (C) 2009 Tämkä Teäm and individual contributors. All rights reserved. See http://tamka.org/copyright
 * @license		GNU General Public License Version 2, or later
 */
defined( '_JEXEC' ) or die( 'Restricted access' );



class plgSystemTamka_301_redirects extends MolajoPlugin
{

	function onAfterInitialise() {

		$application =& MolajoFactory::getApplication('JSite');
		$router =& $application->getRouter();

		if ($router->getMode() == JROUTER_MODE_SEF) {
			$router->attachParseRule(array(&$this, 'Tamka301Redirect'));
		}
	
	}
	
	function Tamka301Redirect (&$router, &$uri)	{

	 /**
	 * 	1.	Initialization
	 */

				
		$route = $uri->getPath();
		$query = $uri->getQuery(true);

		$vars   			= array();
		$vars 				= $uri->getQuery(true);

	 	/*	Get Path and Query */
		$uri				= &MolajoFactory::getURI();
		$uriString			= strtolower($uri->toString(array('path', 'query')));

		$ret = $uri->toString();

	 	/*	Remove base and left forward slash '/' */
 		$base = JURI::base(true) . '/';
		if (trim($base) == '/') {
 			$uriString = substr($uriString, 1, (strlen($uriString) - 1));
		} else {
			$uriString = str_replace ( $base, '', $uriString );
		}
	 	if (trim($uriString) == '') {
	 		return $vars;
	 	}
	
	 /**
	  * See if URL should be redirected
	  */
		$db	=& MolajoFactory::getDBO();
		$redirectTo = '';

		$query = 'SELECT new_path ' .
			' FROM `#__tamka_301_redirects` ' .
			' WHERE old_path = "' . Trim($uriString) . '"';

		$db->setQuery($query);
		$redirectTo = $db->loadResult();

		/*	End of the road - handle 404						*/
		if ($redirectTo == null) {
			return $vars;
		}

		$redirectTo = JURI::base() . $redirectTo;

		header('Location: ' . htmlspecialchars( $redirectTo ), true, '301');
		$mainframe->redirect($redirectTo);
		$app = & MolajoFactory::getApplication();
		$app->close();
		return;

	}
}
?>