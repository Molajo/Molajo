<?php
/**
 * @package     Molajo
 * @subpackage  Helper
 * @copyright   Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @copyright   Copyright (C) 2011 Amy Stephen. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */
defined('MOLAJO') or die;

/**
 * Image Helper
 *
 * @package     Molajo
 * @subpackage  Oembed Helper
 * @since       1.0
 */

/**
 * Usage:
 * MolajoOembedHelper::getProvider
 * http://api.embed.ly/tools/generator
 *
 * http://planetozh.com/blog/2010/05/how-to-embed-a-tweet-in-wordpress-a-complete-oembed-tutorial/
 */
defined('MOLAJO') or die;

class MolajoOembedHelper {

	var $output_Class = '';
	var $option_height = '';
	var $option_width = '';
	var $prepared_URL = '';
	var $providersArray = array();

	function __construct () {}

	function getProvider ($option_URL, $option_MediaClass = '')
	{
		/* See if Link is for Embedded Media */
		$read = JFile::readfile (MOLAJO_PATH_SITE.'/media/molajo/MediaEmbedConfiguration.json');
		$jsonResults = json_decode($read);

		/* See if Link is for Embedded Media */
		$done = false;
		$providerName = false;

		$i = 1;
		foreach ($jsonResults as $result) {

			preg_match( '#'.$result->{'url_scheme'}.'#', $option_URL, $matches );

			if (count( $matches ) == 0) {
			}	else {
				$providerName = $result->{'name'};
				$providerURL = $result->{'url'};
				$providerURLScheme = $result->{'url_scheme'};
				$providerEndPoint = $result->{'endpoint'};
				$providerHandling = $result->{'handling'};
				$providerType = $result->{'type'};
				break;
			}
		}

		if ($providerName) {
		} else {
			return '';
		}

//		$this->prepared_URL = 'http://api.embed.ly/v1/api/oembed?'.
//			'url='.rawurlencode($option_URL).
//			'&maxwidth=200'.
//			'&maxheight=151'.
//			'&format=json';

		$curl = new curl ();
		$curl->curl($this->prepared_URL, false);
		$curlResults = $curl->exec();

		$jsonResults = json_decode($curlResults);
 		$embedResults = $jsonResults->{'html'};

		$embedOutput = '<div class="'.$output_Class.'">';
		$embedOutput .= $embedResults;
		$embedOutput .= '</div >';

		return $embedOutput;


	/**
	 * Media Embed URL found
	 * Note:
	 * 	Extremely disappointing level of implementation
	 * 	Youtube - to use oembed, use /watch/ - resize doesn't work
	 * 			To resize, use /v/ and not oembed, and create your own embed
	 *  Blip TV - must switch over to pycon.blip.tv/file/nnnn
	 *  College Humor - http://www.collegehumor.com/video:1772239
	 *  Daily Show - no idea.
	 *  http://oohembed.com/
	 *  http://liqd.org/wiki/oembed
	 */

		$this->option_width = 200;
		$this->option_height = 151;
		$this->output_Class= trim($primarysecondaryClass.$listitemClass.$option_MediaClass.$providerClass.$mediaClass);
		$this->prepared_URL = trim($providerEndPoint).'?url='.rawurlencode($option_URL).'&width='.$this->option_width.'&height='.$this->option_height;

		if ($providerHandling == 'embed') {
			return MolajoFunctionsOembed::createEmbed ();
		}

		$curl = new MolajoCurl ();
		$curl->curl($this->prepared_URL, false);
		$curlResults = $curl->exec();

		if ($providerHandling == 'JSON') {
			$jsonResults = json_decode($curlResults);
	 		$embedResults = $jsonResults->{'html'};
		}	else {
			$xmlResults = simplexml_load_string ($curlResults);
			$embedResults = html_entity_decode ($xmlResults->html);
		}

		$embedOutput = '<div class="'.$output_Class.'">';
		$embedOutput .= $embedResults;
		$embedOutput .= '</div >';

		return $embedOutput;
	}

	function createEmbed ()
	{
		return '
		<div class="'.$this->output_Class.'">
		<object width="'.$this->option_width.'" height="'.$this->option_height.'">
			<param name="movie" value="'.$this->prepared_URL.'"></param>
			<param name="allowFullScreen" value="true"></param>
			<param name="allowscriptaccess" value="always"></param>
			<embed src="'.$this->prepared_URL.'" type="application/x-shockwave-flash"
				allowscriptaccess="always" allowfullscreen="true"
				width="'.$this->option_width.'" height="'.$this->option_height.'">
			</embed>
		</object>
		</div>';
	}

	function loadProvidersfromFile () {

		$read = MolajoFile::readfile (TAMKA_CONFIGURATION_APPLICATIONS.'FunctionsOembedConfiguration.json');
		$jsonResults = json_decode($read);
		$i = 1;
		foreach ($jsonResults as $result) {

			$this->providersArray[$i]['name'] = $result->{'name'};
			$this->providersArray[$i]['url'] = $result->{'url'};
			$this->providersArray[$i]['url_scheme'] = $result->{'url_scheme'};
			$this->providersArray[$i]['endpoint'] = $result->{'endpoint'};
			$this->providersArray[$i]['handling'] = $result->{'handling'};
			$this->providersArray[$i]['type'] = $result->{'type'};

			$i++;
		}
		$jsonResults = json_encode($jsonResults);
	}

	function initialLoadProviders () {

		$providers = array ();

		$i = 1;
		$providers[$i]['name'] = "5min.com";
		$providers[$i]['url'] = "http://5min.com";
		$providers[$i]['url_scheme'] = "/(http:\/\/www\.5min\.com\/Video\/.*)/i";
		$providers[$i]['endpoint'] = "http://oohembed.com/oohembed/";
		$providers[$i]['handling'] = "JSON";
		$providers[$i]['type'] = "video";
		$i++;

		$providers[$i]['name'] = "Amazon Product Image";
		$providers[$i]['url'] = "http://amazon.com";
		$providers[$i]['url_scheme'] = "/(http:\/\/.*amazon\..*\/gp\/product\/.*|http:\/\/.*amazon\..*\/.*\/dp\/.*|http:\/\/.*amazon\..*\/dp\/.*|http:\/\/.*amazon\..*\/o\/ASIN\/.*|http:\/\/.*amazon\..*\/gp\/offer-listing\/.*)/i";
		$providers[$i]['endpoint'] = "http://oohembed.com/oohembed/";
		$providers[$i]['handling'] = "JSON";
		$providers[$i]['type'] = "video";
		$i++;

		$providers[$i]['name'] = "blip.tv";
		$providers[$i]['url'] = "http://blip.tv";
		$providers[$i]['url_scheme'] = "/(http:\/\/blip\.tv\/file\/.*)/i";
		$providers[$i]['endpoint'] = "http://oohembed.com/oohembed/";
		$providers[$i]['handling'] = "JSON";
		$providers[$i]['type'] = "video";
		$i++;

		$providers[$i]['name'] = "CollegeHumor Video";
		$providers[$i]['url'] = "http://collegehumor.com";
		$providers[$i]['url_scheme'] = "/(http:\/\/www\.collegehumor\.com\/video:.*)/i";
		$providers[$i]['endpoint'] = "http://oohembed.com/oohembed/";
		$providers[$i]['handling'] = "XML";
		$providers[$i]['type'] = "images";
		$i++;

		$providers[$i]['name'] = "Daily Show with Jon Stewart";
		$providers[$i]['url'] = "http://thedailyshow.com";
		$providers[$i]['url_scheme'] = "/(http:\/\/www\.thedailyshow\.com\/watch\/.*|http:\/\/www\.thedailyshow\.com\/full-episodes\/.*|http:\/\/www\.thedailyshow\.com\/collection\/.*\/.*\/.*)/i";
		$providers[$i]['endpoint'] = "http://oohembed.com/oohembed/";
		$providers[$i]['handling'] = "XML";
		$providers[$i]['type'] = "images";
		$i++;

		$providers[$i]['name'] = "Dailymotion";
		$providers[$i]['url'] = "http://dailymotion.com";
		$providers[$i]['url_scheme'] = "/(http:\/\/.*\.dailymotion\.com\/video\/.*)/i";
		$providers[$i]['endpoint'] = "http://oohembed.com/oohembed/";
		$providers[$i]['handling'] = "XML";
		$providers[$i]['type'] = "images";
		$i++;

		$providers[$i]['name'] = "Flickr";
		$providers[$i]['url'] = "http://flickr.com";
		$providers[$i]['url_scheme'] = "/(http:\/\/www\.flickr\.com\/photos\/.*)/i";
		$providers[$i]['endpoint'] = "http://www.flickr.com/services/oembed/";
		$providers[$i]['handling'] = "XML";
		$providers[$i]['type'] = "images";
		$i++;

		$providers[$i]['name'] = "Funny or Die";
		$providers[$i]['url'] = "http://funnyordie.com";
		$providers[$i]['url_scheme'] = "/(http:\/\/www\.funnyordie\.com\/videos\/.*)/i";
		$providers[$i]['endpoint'] = "http://oohembed.com/oohembed/";
		$providers[$i]['handling'] = "XML";
		$providers[$i]['type'] = "images";
		$i++;

		$providers[$i]['name'] = "Google Video";
		$providers[$i]['url'] = "http://video.google.com";
		$providers[$i]['url_scheme'] = "/(http:\/\/video\.google\.com\/videoplay\?.*)/i";
		$providers[$i]['endpoint'] = "http://oohembed.com/oohembed/";
		$providers[$i]['handling'] = "XML";
		$providers[$i]['type'] = "images";
		$i++;

		$providers[$i]['name'] = "Hulu";
		$providers[$i]['url'] = "http://hulu.com";
		$providers[$i]['url_scheme'] = "/(http:\/\/www\.hulu\.com\/watch\/.*)/i";
		$providers[$i]['endpoint'] = "http://oohembed.com/oohembed/";
		$providers[$i]['handling'] = "XML";
		$providers[$i]['type'] = "images";
		$i++;

		$providers[$i]['name'] = "Metacafe";
		$providers[$i]['url'] = "http://metacafe.com";
		$providers[$i]['url_scheme'] = "/(http:\/\/www\.metacafe\.com\/watch\/.*)/i";
		$providers[$i]['endpoint'] = "http://oohembed.com/oohembed/";
		$providers[$i]['handling'] = "XML";
		$providers[$i]['type'] = "images";
		$i++;

		$providers[$i]['name'] = "National Film Board of Canada";
		$providers[$i]['url'] = "http://nfb.ca";
		$providers[$i]['url_scheme'] = "/(http:\/\/nfb\.ca\/film\/.*/i";
		$providers[$i]['endpoint'] = "http://oohembed.com/oohembed/";
		$providers[$i]['handling'] = "JSON";
		$providers[$i]['type'] = "video";
		$i++;

		$providers[$i]['name'] = "Phodroid Photos";
		$providers[$i]['url'] = "http://phodroid.com";
		$providers[$i]['url_scheme'] = "/(http:\/\/phodroid\.com\/.*\/.*\/.*)/i";
		$providers[$i]['endpoint'] = "http://oohembed.com/oohembed/";
		$providers[$i]['handling'] = "JSON";
		$providers[$i]['type'] = "video";
		$i++;

		$providers[$i]['name'] = "Pownce";
		$providers[$i]['url'] = "http://pownce.com";
		$providers[$i]['url_scheme'] = "http://(.*?\.)?pownce\.com/.*?";
		$providers[$i]['endpoint'] = "http://api.pownce.com/2.1/oembed.{format}";
		$providers[$i]['handling'] = "JSON";
		$providers[$i]['type'] = "video";
		$i++;

		$providers[$i]['name'] = "qik";
		$providers[$i]['url'] = "http://qik.com";
		$providers[$i]['url_scheme'] = "/(http:\/\/qik\.com\/video\/.*|http:\/\/qik\.com\/.*)/i";
		$providers[$i]['endpoint'] = "http://oohembed.com/oohembed/";
		$providers[$i]['handling'] = "JSON";
		$providers[$i]['type'] = "video";
		$i++;

		$providers[$i]['name'] = "Revision3";
		$providers[$i]['url'] = "http://www.revision3.com";
		$providers[$i]['url_scheme'] = "/(http:\/\/.*revision3\.com\/.*)/i";
		$providers[$i]['endpoint'] = "http://revision3.com/api/oembed/";
		$providers[$i]['handling'] = "JSON";
		$providers[$i]['type'] = "video";
		$i++;

		$providers[$i]['name'] = "Scribd";
		$providers[$i]['url'] = "http://scribd.com";
		$providers[$i]['url_scheme'] = "/(http:\/\/.*\.scribd\.com\/doc\/.*)/i";
		$providers[$i]['endpoint'] = "http://oohembed.com/oohembed/";
		$providers[$i]['handling'] = "JSON";
		$providers[$i]['type'] = "documents";
		$i++;

		$providers[$i]['name'] = "Slideshare";
		$providers[$i]['url'] = "http://slideshare.net";
		$providers[$i]['url_scheme'] = "/(http:\/\/www\.slideshare\.net\/.*\/.*)/i";
		$providers[$i]['endpoint'] = "http://oohembed.com/oohembed/";
		$providers[$i]['handling'] = "JSON";
		$providers[$i]['type'] = "documents";
		$i++;

		$providers[$i]['name'] = "TwitPic";
		$providers[$i]['url'] = "http://twitpic.com";
		$providers[$i]['url_scheme'] = "/(http:\/\/twitpic\.com\/.*)/i";
		$providers[$i]['endpoint'] = "http://oohembed.com/oohembed/";
		$providers[$i]['handling'] = "JSON";
		$providers[$i]['type'] = "documents";
		$i++;

		$providers[$i]['name'] = "Twitter Status";
		$providers[$i]['url'] = "http://twitter.com";
		$providers[$i]['url_scheme'] = "http://twitter\.com/.*?/statuses/.*?";
		$providers[$i]['endpoint'] = "http://oohembed.com/oohembed/";
		$providers[$i]['handling'] = "JSON";
		$providers[$i]['type'] = "text";
		$i++;

		$providers[$i]['name'] = "Viddler Video";
		$providers[$i]['url'] = "http://viddler.com";
		$providers[$i]['url_scheme'] = "/(http:\/\/.*viddler\.com\/explore\/.*\/videos\/.*)/i";
		$providers[$i]['endpoint'] = "http://lab.viddler.com/services/oembed/";
		$providers[$i]['handling'] = "JSON";
		$providers[$i]['type'] = "text";
		$i++;

		$providers[$i]['name'] = "Vimeo";
		$providers[$i]['url'] = "http://vimeo.com";
		$providers[$i]['url_scheme'] = "http://www\.vimeo\.com/.*?,http://www\.vimeo\.com/groups/.*?/.*?";
		$providers[$i]['endpoint'] = "http://oohembed.com/oohembed/";
		$providers[$i]['handling'] = "JSON";
		$providers[$i]['type'] = "text";
		$i++;

		$providers[$i]['name'] = "Wikipedia";
		$providers[$i]['url'] = "http://wikipedia.org";
		$providers[$i]['url_scheme'] = "http://.*?\.wikipedia\.org/wiki/.*?";
		$providers[$i]['endpoint'] = "http://oohembed.com/oohembed/";
		$providers[$i]['handling'] = "JSON";
		$providers[$i]['type'] = "text";
		$i++;

		$providers[$i]['name'] = "Wordpress.com";
		$providers[$i]['url'] = "http://*.wordpress.com/yyyy/mm/dd/*";
		$providers[$i]['url_scheme'] = "/(http:\/\/wordpress\.tv\/.*\/.*\/.*\/.*\/)/i";
		$providers[$i]['endpoint'] = "http://oohembed.com/oohembed/";
		$providers[$i]['handling'] = "JSON";
		$providers[$i]['type'] = "text";
		$i++;

		$providers[$i]['name'] = "XKCD Comic";
		$providers[$i]['url'] = "http://xkcd.com/";
		$providers[$i]['url_scheme'] = "/(http:\/\/xkcd\.com\/.*)/i";
		$providers[$i]['endpoint'] = "http://oohembed.com/oohembed/";
		$providers[$i]['handling'] = "JSON";
		$providers[$i]['type'] = "text";
		$i++;

		$providers[$i]['name'] = "Yfrog photo";
		$providers[$i]['url'] = "http://yfrog.(com|ru|com.tr|it|fr|co.il|co.uk|com.pl|pl|eu|us)/*";
		$providers[$i]['url_scheme'] = "/(http:\/\/yfrog\..*\/.*)/i";
		$providers[$i]['endpoint'] = "http://oohembed.com/oohembed/";
		$providers[$i]['handling'] = "JSON";
		$providers[$i]['type'] = "text";
		$i++;

		$providers[$i]['name'] = "Youtube";
		$providers[$i]['url'] = "http://youtube.com";
		$providers[$i]['url_scheme'] = "/(http:\/\/.*\.youtube\.com\/watch.*|http:\/\/youtu\.be\/.*)/i";
		$providers[$i]['endpoint'] = "http://oohembed.com/oohembed/";
		$providers[$i]['handling'] = "JSON";
		$providers[$i]['type'] = "video";

		return $providers;
		}
}