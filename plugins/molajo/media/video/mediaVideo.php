<?php
/**
 * @package     Molajo
 * @subpackage  Molajo Media Video Plugin
 * @copyright   Copyright (C) 2011 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die;
jimport( 'joomla.plugin.plugin' );

class plgContenttamka_media_video extends JPlugin
{
	function onPrepareContent( &$article, &$params, $limitstart )
	{
		//	Get Parameters
		$plugin =& JPluginHelper::getPlugin('content', 'tamka_media_video');
		$pluginParams = new JParameter( $plugin->params );

		//	Document
		$document =& JFactory::getDocument();

	    // Expression to search for {video}Toggle|Text to toggle.{/video}
		$regex = "#{video}(.*?){/video}#s";

		// find all instances of plugin and put in $matches
		preg_match_all( $regex, $article->text, $matches );

		// Number of plugins
 		$count = count( $matches[0] );

        // Process each Quote
 		for ( $i=0; $i < $count; $i++ ) {

	       	// the whole text to replace: {video}http://www.youtube.com/v/VIDEO_ID{/video}
   	       	$replacethis = $matches[0][$i];

			// Extract mp3 file and remove {video} and {/video}
			$videoFile = "";
			$videoFile = substr($replacethis, strlen("{video}"), (strlen($replacethis)-strlen("{video}{/video}")) );

			// Find the video number and build the proper URL
			if (stripos($videoFile, 'v/') == true) {
				$videoID = substr($videoFile, (stripos($videoFile, 'v/') + 2), 999);

			} else 	if (stripos($videoFile, 'v=') == true) {
				$videoID = substr($videoFile, (stripos($videoFile, 'v=') + 2), 999);

			} else {
				$videoID = $videoFile;
			}

			$videoFile = 'http://www.youtube.com/v/' . $videoID;

			if ($pluginParams->def('fs', 0) == 1) {
				$allowFullScreen1 = '<param name="allowFullScreen" value="true"></param>';
				$allowFullScreen2 = 'allowfullscreen="true"';
			} else {
				$allowFullScreen1 = '';
				$allowFullScreen2 = '';
			}

			$videoSource = '<object type="application/x-shockwave-flash" ';
			$videoSource .= 'width="'. $pluginParams->def('width', 400);
			$videoSource .= '" height="'. $pluginParams->def('height', 330);
			$videoSource .= '" data="' . $videoFile;
			$videoSource .= '&amp;rel=' . $pluginParams->def('rel', 1);
			$videoSource .= '&amp;autoplay=' . $pluginParams->def('autoplay', 0);
			$videoSource .= '&amp;loop=' . $pluginParams->def('loop', 0);
			$videoSource .= '&amp;enablejsapi=' . $pluginParams->def('enablejsapi', 0);
			$videoSource .= '&amp;disablekb=' . $pluginParams->def('disablekb', 0);
			$videoSource .= '&amp;egm=' . $pluginParams->def('egm', 1);
			$videoSource .= '&amp;border=' . $pluginParams->def('border', 1);
			$videoSource .= '&amp;color1=0x' . $pluginParams->def('color1', '0E0906');
			$videoSource .= '&amp;color2=0x' . $pluginParams->def('color2', 'D9D9D9');
			$videoSource .= '&amp;start=' . $pluginParams->def('start', 0);
			$videoSource .= '&amp;fs=' . $pluginParams->def('fs', 1);

		   	// Create Flash Player
			$replacewith = '';
			$replacewith .= '<span class="videoplayer">';
 			$replacewith .= $videoSource . '">';
			$replacewith .= '<param name="movie" value="' . $videoFile . '" />';
			$replacewith .= '<param name="wmode" value="transparent" />';
			$replacewith .= '</object>';
			$replacewith .= '</span>';

			// Replace the Plugin+Text
	        $article->text = str_replace( $replacethis, $replacewith, $article->text );
	    }
        return true;
	}
}