<?php
/**
 * @package     Molajo
 * @subpackage  Molajo Protect Akismet Plugin
 * @copyright   Copyright (C) 2011 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die;
	
class protectAkismet
{

        function invokeAkismet ($comment_captcha, $referer)
	{	

	/**
	 * 	Retrieve User Group Parameter for Auto Publish 
	 */
		$tamkaLibraryPlugin 	=& MolajoPluginHelper::getPlugin( 'system', 'tamka');
		$tamkaLibraryPluginParameters = new JParameter($tamkaLibraryPlugin->parameters);
		$spamProtectionOption = $tamkaLibraryPluginParameters->def('spamprevention', '1');

		if ($spamProtectionOption == '0') {
			return 0;
		}
		
	/**
	 * 	2 - Akismet
	 */
					
		require_once Akismet.class.php;
			
    		$akismetkey = $tamkaLibraryPluginParameters->get( 'akismetkey' );

                $uri	= &MolajoFactory::getURI();
                $url	= $uri->toString(array('scheme', 'user', 'pass', 'host', 'port', 'path'));
                $akismet = new Akismet($url, $akismetkey);
			
                $session =& MolajoFactory::getSession();

                $akismet->setCommentAuthor($session->get('comment_author_name', null, 'com_responses'));
                $akismet->setCommentAuthorEmail($session->get('comment_author_email', null, 'com_responses'));
                $akismet->setCommentAuthorURL($session->get('comment_author_url', null, 'com_responses'));
                $akismet->setCommentContent($session->get('comment_body', null, 'com_responses'));
                $akismet->setPermalink($session->get('component_url', null, 'com_responses'));
			
                if ($akismet->isCommentSpam()) {
                        global $mainframe;
                        $mainframe->enqueueMessage(MolajoText::_('Comment identified as Spam by Akismet.'));
                        $published = 2;
                }
 *
 *  <b>Usage:</b>
 *  <code>
 *    $akismet = new Akismet('http://www.example.com/blog/', 'aoeu1aoue');
 *    $akismet->setCommentAuthor($name);
 *    $akismet->setCommentAuthorEmail($email);
 *    $akismet->setCommentAuthorURL($url);
 *    $akismet->setCommentContent($comment);
 *    $akismet->setPermalink('http://www.example.com/blog/alex/someurl/');
 *    if($akismet->isCommentSpam())
 *      // store the comment but mark it as spam (in case of a mis-diagnosis)
 *    else
 *      // store the comment normally
 *  </code>
 *
 *  Optionally you may wish to check if your WordPress API key is valid as in the example below.
 *
 * <code>
 *   $akismet = new Akismet('http://www.example.com/blog/', 'aoeu1aoue');
 *
 *   if($akismet->isKeyValid()) {
 *     // api key is okay
 *   } else {
 *     // api key is invalid
 *   }
 * </code>
 *

	}
}	
?>