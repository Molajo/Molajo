<?php
/**
 * @package     Molajo
 * @subpackage  Links Plugin
 * @copyright   Copyright (C) 2011 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die();

class MolajoLinksExternalLinks {

    /**
     * MolajoLinksExternalLinks::driver
     *
     * Adds descriptive text to all external links and then replace this text with a small icon for CSS
     *
     * Addresses Web Content Accessibility Guidelines 1.0:
     *
     * “Clearly identify the target of each link. [Priority 2] Link text (The rendered text content of a link)
     * should be meaningful enough to make sense when read out of context — either on its own or as part of
     * a sequence of links. Link text should also be terse.”
     *
     * @param	string		The context for the content passed to the plugin.
     * @param	object		The content object.
     * @param	object		The content params
     * @param	string		The 'page' number
     * @param   string          Then name of the text field in the content object
     * @return	string
     * @since	1.6
     */
    function driver ($context, &$content, &$params, $page = 0, $location)
    {
        /** initialization **/
        $firsttime = true;

        /** search **/
        preg_match_all('#<\s*a.*?href\s*=\s*(?:"|\')((?=[a-z0-9]+:).*?)(?:"|\').*?>(.*?)<\s*/\s*a\s*>#i', $content->$location, $matches );

        /** replace **/
        for ( $i=0; $i < count($matches[0]); $i++ ) {
            if ($firsttime) {
                $firsttime = false;
                $molajoSystemPlugin =& JPluginHelper::getPlugin('system', 'molajo');
                $systemParams = new JParameter($molajoSystemPlugin->params);

                $rel = $systemParams->def('index_external_links', 'noindex');
                $rel .= ', '.$systemParams->def('follow_external_links', 'nofollow');
                
		$document =& JFactory::getDocument();
		$document->addScript(JURI::base().'media/molajo/js/mooexternal.js' );
		$document->addStyleSheet(JURI::base().'media/molajo/css/external.css' );

            }

            $verify = MolajoHelperURLs::checkURLExternal ($matches[1][$i]);
            if (verify == true) {
                    $content->$location = str_replace( $matches[0][$i], '<a class="external" href="'.$matches[1][$i].'" rel="'.$rel.'" title="'.$matches[2][$i].'">'.$matches[2][$i].'</a>', $content->$location);
            }
        }
    }
}