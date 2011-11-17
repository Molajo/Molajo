<?php
/**
 * @package     Molajo
 * @subpackage  Molajo Media Plugin
 * @copyright   Copyright (C) 2011 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die;

/**
 *
 * Thanks to Martin Laine from http://www.1pixelout.net/
 * Thanks to Mindy McAdams for the tutorial http://www.macloo.com/examples/audio_player/
 *
*/

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport( 'joomla.plugin.plugin' );

class MediaAudio extends MolajoPlugin
{
	function onPrepareContent( &$article, &$parameters, $limitstart )
	{
		//	Get Parameters
		$plugin =& MolajoPluginHelper::getPlugin('content', 'tamka_media_audio');
		$pluginParameters = new JParameter( $plugin->parameters );

		//	Document
		$document =& MolajoFactory::getDocument();

		//	Add Javascript
		$document->addScript( JURI::base().'plugins/content/tamka_media_audio/audio-player.js' );

	    // Expression to search for {audio}Toggle|Text to toggle.{/audio}
		$regex = "#{audio}(.*?){/audio}#s";

		// find all instances of plugin and put in $matches
		preg_match_all( $regex, $article->text, $matches );

		// Number of plugins
 		$count = count( $matches[0] );

        // Process each Quote
 		for ( $i=0; $i < $count; $i++ ) {

	       	// the whole text to replace: {audio}http://example.com/example.mp3{/audio}
   	       	$replacethis = $matches[0][$i];

			// Extract mp3 file and remove {audio} and {/audio}
			$audioFile = "";
			$audioFile = substr($replacethis, strlen("{audio}"), (strlen($replacethis)-strlen("{audio}{/audio}")) );

		   	// Create Flash Player
			$replacewith = '';
			$replacewith .= '<span class="audioplayer">';
			$replacewith .= '<object type="application/x-shockwave-flash" ';
			$replacewith .= 'data="'.JURI::base().'plugins/content/tamka_media_audio/player.swf" ';
			$replacewith .= 'width="290" height="24" id="audioplayer'.($i + 1).'">';
			$replacewith .= '<param name="movie" value="'.JURI::base().'plugins/content/tamka_media_audio/player.swf"'.' />';
			$replacewith .= '<param name="FlashVars" value="playerID='.($i + 1);
			$replacewith .= '&amp;bg=0x'.$pluginParameters->def('bgColor', 'E5E5E5');
			$replacewith .= '&amp;leftbg=0x'.$pluginParameters->def('leftbgColor', 'CCCCCC');
			$replacewith .= '&amp;lefticon=0x'.$pluginParameters->def('lefticonColor', '333333');
			$replacewith .= '&amp;rightbg=0x'.$pluginParameters->def('rightbgColor', 'B4B4B4');
			$replacewith .= '&amp;rightbghover=0x'.$pluginParameters->def('rightbghoverColor', '999999');
			$replacewith .= '&amp;righticon=0x'.$pluginParameters->def('righticonColor', '333333');
			$replacewith .= '&amp;righticonhover=0x'.$pluginParameters->def('rightbghoverColor', '999999');
			$replacewith .= '&amp;text=0x'.$pluginParameters->def('textColor', '333333');
			$replacewith .= '&amp;slider=0x'.$pluginParameters->def('volsliderColor', '666666');
			$replacewith .= '&amp;track=0x'.$pluginParameters->def('trackColor', 'FFFFFF');
			$replacewith .= '&amp;border=0x'.$pluginParameters->def('borderColor', 'CCCCCC');
			$replacewith .= '&amp;loader=0x'.$pluginParameters->def('loaderColor', '009900');
			$replacewith .= '&amp;soundFile=';
			$replacewith .= urlencode($audioFile);
			$replacewith .= '" />';
			$replacewith .= '<param name="quality" value="high" />';
			$replacewith .= '<param name="menu" value="false" />';
			$replacewith .= '<param name="bgcolor" value="#'.$pluginParameters->def('pagebgColor', 'FFFFFF').'" /></object>';
			$replacewith .= '</span>';

			// Replace the Plugin+Text
	        $article->text = str_replace( $replacethis, $replacewith, $article->text ) ;
	    }
        return true;
	}
}