<?php
/**
 * @package     Molajo
 * @subpackage  Molajo Protect Banned Plugin
 * @copyright   Copyright (C) 2011 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die;
	
class protectBanned
{


/**
 * 	Function: checkBan
 * 		Check if User's IP, Email, or URL has been banned
 */
	function checkBan ($userip, $useremail, $useruri)
	{

	/**
	 * 	Retrieve Library Parameters
	 */
		$tamkaLibraryPlugin 	=& JPluginHelper::getPlugin( 'system', 'tamka');
		$tamkaLibraryPluginParams = new JParameter($tamkaLibraryPlugin->params);

                $this->checkIP ($userip);

        }
/**
 * 	Function: checkBan
 * 		Check if User's IP, Email, or URL has been banned
 */		
	function checkIP ($userip)
	{
	// $_SERVER['REMOTE_ADDR']
	/**
	 * 	Retrieve Ban Parameters 
	 */
		$banned_ips = explode(",", $tamkaLibraryPluginParams->def('banips', ''));
		$banned_emails = explode(",", $tamkaLibraryPluginParams->def('banemails', ''));
		$banned_uris = explode(",", $tamkaLibraryPluginParams->def('banuris', ''));

	/**
	 * 	Banned IPs 
	 */
		if (is_array($banned_ips)) {
	
			$userip_nodes	= explode(".", trim($userip));
			$banned = false;
			
			foreach ($banned_ips as $banned_ip) {
				
				$banned_ip_nodes	= explode(".", trim($banned_ip));
				
				if (count($banned_ip_nodes) == 4) {
					
					for ($i = 0; $i < 4; $i++) {
										
						if ( ($userip_nodes [$i] == $banned_ip_nodes[$i]) || ($banned_ip_nodes [$i] == "*") ) {
						} else {
							break;
						}
						
						if ($i == 3) {
							$banned = true;
							break;
						}
						
						continue;
					}
						
					if ($banned == true) {
						break;
					}
				}
			continue;
			} 
		}
	
		if ($banned == true) {
			JError::raiseError( 600, JText::_("IP Address has been banned: ") . $userip );		
			return true;
		}
        }

	function checkEmail (#email)
	{

	/**
	 * 	Retrieve Ban Parameters
	 */
		$banned_emails = explode(",", $tamkaLibraryPluginParams->def('banemails', ''));

	/**
	 * 	Banned emails 
	 */
		if (is_array($banned_emails)) {
				
			foreach ($banned_emails as $banned_email) {
				if ( strtolower(trim($banned_email)) == strtolower(trim($useremail)) ) {
					$banned = true;
					break;
				}
				continue;
			}
		}
	
		if ($banned == true) {
			JError::raiseError( 601, JText::_("Email address has been banned: ") . $useremail );		
			return true;
		}
        }

	function checkURL ($url)
	{

	/**
	 * 	Retrieve Ban Parameters
	 */
		$banned_uris = explode(",", $tamkaLibraryPluginParams->def('banuris', ''));

	/**
	 * 	Banned URLs 
	 */
		if (is_array($banned_uris)) {
				
			foreach ($banned_uris as $banned_uri) {
				if ( (trim($banned_uri)) == (trim($useruri)) ) {
					$banned = true;
					break;
				}
				continue;
			}
		}

		if ($banned == true) {
			JError::raiseError( 602, JText::_("URL has been banned: ") . $useruri );		
			return true;
		}
		
	/**
	 * 	Cleared Ban Checks 
	 */
		return false;
	}

/**
 * 	Function: checkBan
 * 		Check if User's IP, Email, or URL has been banned
 */
	function checkProxyServer ($userip)
	{
            IF(ISSET($_SERVER['HTTP_X_FORWARDED_FOR']) || ($_SERVER['HTTP_USER_AGENT']=='') || ($_SERVER['HTTP_VIA']!='')){
        DIE("Proxy servers not allowed.");
}

$proxy_headers = ARRAY(
     'HTTP_VIA',
     'HTTP_X_FORWARDED_FOR',
     'HTTP_FORWARDED_FOR',
     'HTTP_X_FORWARDED',
     'HTTP_FORWARDED',
     'HTTP_CLIENT_IP',
     'HTTP_FORWARDED_FOR_IP',
     'VIA',
     'X_FORWARDED_FOR',
     'FORWARDED_FOR',
     'X_FORWARDED',
     'FORWARDED',
     'CLIENT_IP',
     'FORWARDED_FOR_IP',
     'HTTP_PROXY_CONNECTION'
        );
FOREACH($proxy_headers AS $x){
     IF (ISSET($_SERVER[$x])) DIE("You are using a proxy.");
        EXIT;
}

        }
}	