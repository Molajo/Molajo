<?php
/**
 * @package     Molajo
 * @subpackage  Molajo Protect Flood Control Plugin
 * @copyright   Copyright (C) 2011 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die();
	
class protectFloodControl
{

        function invokeAkismet ($comment_captcha, $referer)
	{	
IF (!ISSET($_SESSION)) {
    SESSION_START();
}
// anti flood protection
IF($_SESSION['last_session_request'] > TIME() - 2){
    // users will be redirected to this page if it makes requests faster than 2 seconds
    HEADER("location: /flood.html");
    EXIT;
}
$_SESSION['last_session_request'] = TIME();

        }
}	