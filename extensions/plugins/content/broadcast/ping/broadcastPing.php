<?php
/**
 * @package     Molajo
 * @subpackage  Molajo Broadcast Ping Plugin
 * @copyright   Copyright (C) 2011 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die;
/**
 *  Adapted from Rogers Cadenhead Weblog_Pinger PHP Class Library Version 1.4
 * 	Web: http://www.cadenhead.org/workbench/weblog-pinger
 * 	Copyright (C) 2007 Rogers Cadenhead
 */

class BroadcastPing {
	
	function pingProcess ($sourceTitle, $sourceURL, $sourceSiteName) { 
		
	/**
	 * 	Tamka Library Parameters
	 */
		$tamkaLibraryPlugin 	=& MolajoApplicationPlugin::getPlugin( 'system', 'tamka');
		$tamkaLibraryPluginParameters = new JParameter($tamkaLibraryPlugin->parameters);
		global $mainframe;
		$tamkaPingCounter = 0;	
		tamkaimport('tamka.broadcast.ping.xmlrpc');
		
	/**
	 * 	Ping each selected service
	 */		

		if ($tamkaLibraryPluginParameters->def( 'ping-o-matic', 1 )) {
			$pingServer		= "rpc.pingomatic.com";
		    $pingPort 		= 80;
		    $pingPath 		= "/RPC2";
		    $pingMethod 	= "weblogUpdates.ping";
		    $pingExtended 	= "weblogUpdates.extendedPing";
		    $results = TamkaPing::ping ($sourceTitle, $sourceURL, $sourceSiteName, $pingServer, $pingPort, $pingPath, $pingMethod, $pingExtended);
		    if ($results == true) { 
		    	$tamkaPingCounter++;
		    }
		}
		
		if ($tamkaLibraryPluginParameters->def( 'google', 1 )) {
			$pingServer		= "blogsearch.google.com";
		    $pingPort 		= 80;
		    $pingPath 		= "/ping/RPC2";
		    $pingMethod 	= "weblogUpdates.ping";
		    $pingExtended 	= "weblogUpdates.extendedPing";
			$results = TamkaPing::ping ($sourceTitle, $sourceURL, $sourceSiteName, $pingServer, $pingPort, $pingPath, $pingMethod, $pingExtended);
		    if ($results == true) { 
		    	$tamkaPingCounter++;
		    }		    						
		}		
		
		if ($tamkaLibraryPluginParameters->def( 'audioweblogs', 0 )) {
			$pingServer		= "audiorpc.weblogs.com";
		    $pingPort 		= 80;
		    $pingPath 		= "/RPC2";
		    $pingMethod 	= "weblogUpdates.ping";
		    $pingExtended 	= "";
			$results = TamkaPing::ping ($sourceTitle, $sourceURL, $sourceSiteName, $pingServer, $pingPort, $pingPath, $pingMethod, $pingExtended);
		    if ($results == true) { 
		    	$tamkaPingCounter++;
		    }		    											
		}
				
		if ($tamkaLibraryPluginParameters->def( 'blogs', 0 )) {
		   	$pingServer		= "ping.blo.gs";
		    $pingPort 		= 80;
		    $pingPath 		= "/";
		    $pingMethod 	= "weblogUpdates.ping";
		    $pingExtended 	= "";		    
		    $results = TamkaPing::ping ($sourceTitle, $sourceURL, $sourceSiteName, $pingServer, $pingPort, $pingPath, $pingMethod, $pingExtended);
		    if ($results == true) { 
		    	$tamkaPingCounter++;
		    }		    
		}
		
		if ($tamkaLibraryPluginParameters->def( 'feedburner', 0 )) {
		   	$pingServer		= "ping.feedburner.com";
		    $pingPort 		= 80;
		    $pingPath 		= "/RPC2";
		    $pingMethod 	= "weblogUpdates.ping";
		    $pingExtended 	= "";
			$results = TamkaPing::ping ($sourceTitle, $sourceURL, $sourceSiteName, $pingServer, $pingPort, $pingPath, $pingMethod, $pingExtended);
		    if ($results == true) { 
		    	$tamkaPingCounter++;
		    }		    
		}
		
		if ($tamkaLibraryPluginParameters->def( 'Technorati', 0 )) {
		   	$pingServer		= "rpc.technorati.com";
		    $pingPort 		= 80;
		    $pingPath 		= "/rpc/ping";
		    $pingMethod 	= "weblogUpdates.ping";
		    $pingExtended 	= "";
			$results = TamkaPing::ping ($sourceTitle, $sourceURL, $sourceSiteName, $pingServer, $pingPort, $pingPath, $pingMethod, $pingExtended);
		    if ($results == true) { 
		    	$tamkaPingCounter++;
		    }		    
		}
		
		if ($tamkaLibraryPluginParameters->def( 'weblogscom', 0 )) {
		   	$pingServer		= "rpc.weblogs.com";
		    $pingPort 		= 80;
		    $pingPath 		= "/RPC2";
		    $pingMethod 	= "weblogUpdates.ping";
		    $pingExtended 	= "weblogUpdates.extendedPing";
			$results = TamkaPing::ping ($sourceTitle, $sourceURL, $sourceSiteName, $pingServer, $pingPort, $pingPath, $pingMethod, $pingExtended);
		    if ($results == true) { 
		    	$tamkaPingCounter++;
		    }		    
		}
		
		/**
		 * 	If No Ping Services were selected, provide warning.
		 */
		if ($tamkaPingCounter == 0) {
			$message = MolajoText::_('No Ping Services were activated in the Tamka Library System Plugin.');
           	$mainframe->enqueueMessage(MolajoText::_('Warning: ').$message);
        	return false;
		}
	}
		
    function ping ($sourceTitle, $sourceURL, $sourceSiteName, $pingServer, $pingPort, $pingPath, $pingMethod, $pingExtended) 
    {
    /**
     * Prepare call to xml-rpc
     */
		global $mainframe;
		
        $name_param	 			= new xmlrpcval($sourceTitle, 'string');
        $url_param 				= new xmlrpcval($sourceURL, 'string');
        $changes_param 			= new xmlrpcval($sourceSiteName, 'string');
        
        $cat_or_rss_param 		= new xmlrpcval(' ');
        $method_name 			= "weblogUpdates.ping";

        $parameters 				= array($name_param, $url_param, $changes_param);
        
    /**
     * 	Execute Call
     */        
        $call_text 	= "$method_name(\"$sourceTitle\", \"$sourceURL\", \"$sourceSiteName\")";

        $message 	= new xmlrpcmsg ($pingMethod, $parameters);
        $client 	= new xmlrpc_client($pingPath, $pingServer, $pingPort);
        $response 	= $client->send($message);
        
    /**
     * 	Process results
     */        
		if ($response->faultCode() == 0)  {
        	/* Table */
			$message = MolajoText::_('Ping to: ').$pingServer." for ".$sourceTitle." URL: ".$sourceURL." Site name: ".$sourceSiteName;
           	$mainframe->enqueueMessage(MolajoText::_('Success: ').$message);
        	return true;
		}  else {
			$message = MolajoText::_('Error: ').$pingServer.": ".$response->faultCode()." ".$response->faultString();
           	$mainframe->enqueueMessage(MolajoText::_('Ping error: ').$message);
           	return false;
        }

	}
}	
?>