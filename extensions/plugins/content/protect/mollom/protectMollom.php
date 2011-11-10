<?php
/**
 * @package     Molajo
 * @subpackage  Molajo Protect Mollom Plugin
 * @copyright   Copyright (C) 2011 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die;
	
class ProtectMollom
{

        function invokeAkismet ($comment_captcha, $referer)
	{	

	/**
	 * 	Retrieve User Group Parameter for Auto Publish 
	 */
		$tamkaLibraryPlugin 	=& MolajoPluginHelper::getPlugin( 'system', 'tamka');
		$tamkaLibraryPluginParams = new JParameter($tamkaLibraryPlugin->parameters);
		$spamProtectionOption = $tamkaLibraryPluginParams->def('spamprevention', '1');		

		if ($spamProtectionOption == '0') {
			return 0;
		}
		
if ($spamProtectionOption == '3') {
			tamkaimport('tamka.spam.mollom.mollom');
			$session =& MolajoFactory::getSession();
			
			$mollompublickey = $tamkaLibraryPluginParams->get( 'mollompublickey' );
			$mollomprivatekey = $tamkaLibraryPluginParams->get( 'mollomprivatekey' );			

			Mollom::setPublicKey($mollompublickey);
			Mollom::setPrivateKey($mollomprivatekey);
			$servers = Mollom::getServerList();
			Mollom::setServerList($servers);
			
//			$mollom_challenge_response = $session->get('mollom_challenge_response', false, 'com_responses');
			$session->set('mollom_challenge_response', false, 'com_responses');

//			if ($mollom_challenge_response) {
//				if (Mollom::checkCaptcha(null, $mollomresponse) == true) {
					// echo 'the answer is correct, you may proceed!';
//					global $mainframe;
//					$mainframe->enqueueMessage(JText::_('Good answer'));
//				} else {
//					global $mainframe;
//					$mainframe->enqueueMessage(JText::_('Incorrect Captcha value. Please try, again.'));
//					$session->set('mollom_challenge_response', true, 'com_responses');					
//					$published = 3;					
//				}
//			} else {
				
				$feedback = Mollom::checkContent(null, null, 
					$session->get('comment_body', null, 'com_responses'), 
					$session->get('comment_author_name', null, 'com_responses'), 
					$session->get('comment_author_email', null, 'com_responses'), 
					$session->get('component_url', null, 'com_responses'));
				
					if (in_array($feedback['spam'], array('unsure', 'unknown'))) {
//						$session->set('mollom_challenge_response', true, 'com_responses');
						
					} else if ($feedback['spam'] == 'spam') {
						global $mainframe;
						$mainframe->enqueueMessage(JText::_('Comment identified as Spam by Mollom'));
						$published = 3;
					} else {
		//				echo 'must be ham ' . $feedback['spam'];
					}
			}
		}
	}

}	
?>