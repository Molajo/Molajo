<?php
/** 
 * @package     Minima
 * @subpackage  mod_myshortcuts
 * @author      Marco Barbosa
 * @copyright   Copyright (C) 2010 Marco Barbosa. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

// import JFile
//jimport( 'joomla.filesystem.file' );

class Mod_MyshortcutsInstallerScript {

    /**
     * Download a file with curl
     * inspired by Stian Didriksen
     * based on http://www.php-mysql-tutorial.com/wikis/php-tutorial/reading-a-remote-file-using-php.aspx
     */
    function downloadFile($url) {
        $content = "";
        // if we have curl enabled
        if (!function_exists('curl_init')) {
            // initialize a new curl resource
            $ch = curl_init();
            // set the url to fetch
            curl_setopt($ch, CURLOPT_URL, $url);
            // don't give me the headers just the content
            curl_setopt($ch, CURLOPT_HEADER, 0);
            // return the value instead of printing the response to browser
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            // use a user agent to mimic a browser
            curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.7.5) Gecko/20041107 Firefox/1.0');
            // store file content in $content
            $content = curl_exec($ch);
            // remember to always close the session and free all resources
            curl_close($ch);
        } else { // curl_init fallback
            $content = file_get_contents($url);
        }
        return $content;
    }

    function postflight($type, $parent) {

        $db = JFactory::getDBO();
        
        $db->setQuery("SELECT `home` FROM `#__template_styles` WHERE `#__template_styles`.`template` = 'minima'");                

        $alreadyInstalled = $db->loadResult();

        if (!$alreadyInstalled) {

            // myshortcuts
            $db->setQuery("UPDATE `#__modules`".
                " SET `position` = 'shortcuts', `published` = '1', `access` = '3'".
                " WHERE `#__modules`.`module` = 'mod_myshortcuts'; ");

            if (!$db->query() && ($db->getErrorNum() != 1060)) {
                echo $db->getErrorMsg(true);
            }

            // mypanel
            $db->setQuery("UPDATE `#__modules`".
                " SET `position` = 'panel', `published` = '1', `access` = '3'".
                " WHERE `#__modules`.`module` = 'mod_mypanel'; ");

            if (!$db->query() && ($db->getErrorNum() != 1060)) {
                echo $db->getErrorMsg(true);
            }

            // copy modules
            $db->setQuery("INSERT INTO `#__modules` (`title`, `note`, `content`, `ordering`, `position`, `checked_out`, `checked_out_time`, `publish_up`, `publish_down`, `published`, `module`, `access`, `showtitle`, `params`, `client_id`, `language`) VALUES ".
                " ('Popular Articles', '', '', 3, 'widgets-last', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 1, 'mod_popular', 3, 1, '{\"count\":\"5\",\"catid\":\"\",\"user_id\":\"0\",\"layout\":\"_:default\",\"moduleclass_sfx\":\"\",\"cache\":\"0\",\"automatic_title\":\"1\"}', 1, '*'),".
                " ('Recently Added Articles', '', '', 4, 'widgets-first', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 1, 'mod_latest', 3, 1, '{\"count\":\"5\",\"ordering\":\"c_dsc\",\"catid\":\"\",\"user_id\":\"0\",\"layout\":\"_:default\",\"moduleclass_sfx\":\"\",\"cache\":\"0\",\"automatic_title\":\"1\"}', 1, '*'),".
                " ('Logged-in Users', '', '', 2, 'widgets-first', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 1, 'mod_logged', 3, 1, '{\"count\":\"5\",\"name\":\"1\",\"layout\":\"_:default\",\"moduleclass_sfx\":\"\",\"cache\":\"0\",\"automatic_title\":\"1\"}', 1, '*')");

            if (!$db->query() && ($db->getErrorNum() != 1060)) {
                die($db->getErrorMsg(true));
            }

            // add values to modules_menu
            $db->setQuery("INSERT INTO `#__modules_menu` (`moduleid`,`menuid`)".
                " SELECT `id`,0 FROM `#__modules`".
                " WHERE `#__modules`.`module` = 'mod_myshortcuts' OR `#__modules`.`module` = 'mod_mypanel' LIMIT 2");

            if (!$db->query() && ($db->getErrorNum() != 1060)) {
                echo $db->getErrorMsg(true);
            }
    	
    	   // add values to modules_menu
            $db->setQuery("INSERT INTO `#__modules_menu` (`moduleid`,`menuid`)".
                " SELECT `id`,0 FROM `#__modules`".
                " WHERE `#__modules`.`position` = 'widgets-last' OR `#__modules`.`position` = 'widgets-first'");

            if (!$db->query() && ($db->getErrorNum() != 1060)) {
                echo $db->getErrorMsg(true);
            }

            // set minima style default
            $db->setQuery("UPDATE `#__template_styles`".
                " SET `home` = '0'".
                " WHERE `#__template_styles`.`client_id` =1;");

            if (!$db->query() && ($db->getErrorNum() != 1060)) {
                die($db->getErrorMsg(true));
            }

            $db->setQuery("UPDATE `#__template_styles`".
                " SET `home` = '1' WHERE `#__template_styles`.`template` = 'minima';");

            if (!$db->query() && ($db->getErrorNum() != 1060)) {
                die($db->getErrorMsg(true));
            }

            // language that is being used
            /*$currentLang = JFactory::getLanguage()->getTag();
            // for testing purposes, say the language is de-DE
            //$currentLang = "de-DE";

            // available translations
            $languages = array("de-DE", "nb-NO", "pt-BR", "sv-SE", "ru-RU");

            // files to download (without the language prefix)
            $files = array("mod_myshortcuts.ini","tpl_minima.ini");

            // if we have that translation available
            if (in_array($currentLang, $languages)) {
                foreach($files as $toDownload) {
                    // fix the filename with the language prefix: 'en-GB.tpl_minima.ini'
                    $file = $currentLang.".".$toDownload;
                    // url to download the language
                    $url = "http://minimatemplate.com/get/language/".$currentLang."/".$file;
                    // path to save the file
                    $path = JPATH_ADMINISTRATOR.'/language/'.$currentLang.'/'.$file;
                    // content of the file
                    $content = $this->downloadFile($url);
                    // done, now proccess the $content
                    if ($content && ($content !== false)) {
                        // save the file in the language folder
                        JFile::write($path, $content);
                    }
                } // end of foreach
            } //end of if in_array
            */
        } // end of alreadyInstalled
    } // end of postflight

}
