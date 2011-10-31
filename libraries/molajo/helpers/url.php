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
 * @subpackage  URL Helper
 * @since       1.0
 */
class MolajoURLHelper {

    /**
     * Get either a Gravatar URL or complete image tag for a specified email address.
     *
     * @param string $option_Email The email address
     * @param string $s Size in pixels, defaults to 80px [ 1 - 512 ]
     * @param string $d Default imageset to use [ 404 | mm | identicon | monsterid | wavatar ]
     * @param string $r Maximum rating (inclusive) [ g | pg | r | x ]
     * @param boole $img True to return a complete IMG tag False for just the URL
     * @param array $atts Optional, additional key/value attributes to include in the IMG tag
     * @return String containing either just a URL or a complete image tag
     * @source http://gravatar.com/site/implement/images/php/
     */
    function getGravatar( $option_Email, $s = 80, $d = 'mm', $r = 'g', $img = false, $atts = array() )
    {
        $url = 'http://www.gravatar.com/avatar/';
        $url .= md5( strtolower( trim( $option_Email ) ) );
        $url .= "?s=$s&d=$d&r=$r";
        if ( $img ) {
                $url = '<img src="'.$url.'"';
                if (count($atts) > 0 ) {
                    foreach ( $atts as $key => $val )   {
                        $url .= ' '.$key.'="'.$val.'"';
                    }
                }
                $url .= ' />';
        }
        return $url;
    }

    /**
     * addLinks
     * @param string $option_Text
     * @return string
     */
    function addLinks ($option_Text)
    {
        $pattern = "/(((http[s]?:\/\/)|(www\.))?(([a-z][-a-z0-9]+\.)?[a-z][-a-z0-9]+\.[a-z]+(\.[a-z]{2,2})?)\/?[a-z0-9._\/~#&=;%+?-]+[a-z0-9\/#=?]{1,1})/is";
        $option_Text = preg_replace($pattern, " <a href='$1'>$1</a>", $option_Text);
        // fix URLs without protocols
        $option_Text = preg_replace("/href=\"www/", "href=\"http://www", $option_Text);
        return $option_Text;
    }

    /**
     * checkURLExternal - determines if it is a local site or external link
     * @param string $option_URL
     * @return boolean
     */
    function checkURLExternal ($option_URL)
    {
        if (substr($option_URL, 0, strlen(MOLAJO_BASE_FOLDER)) == MOLAJO_BASE_FOLDER) {
            return false;
        } elseif ( (strtolower(substr($option_URL, 0, 3)) == 'www') && (substr($option_URL, 3, strlen(MOLAJO_BASE_FOLDER)) == MOLAJO_BASE_FOLDER)) {
            return false;
        } else {
            return true;
        }
    }

    /**
     * checkURLValidity - determines if the URL is properly formed
     * @param string $option_URL
     * @return boolean
     */
    function checkURLValidity ($option_URL)
    {
        return preg_match('|^http(s)?://[a-z0-9-]+(.[a-z0-9-]+)*(:[0-9]+)?(/.*)?$|i', $option_URL);
    }

    /**
     * createWebLinks - marks up a link into an <a href link
     * @param string $option_URL
     * @return linked value
     */
    function createWebLinks ($option_URL)
    {
        return preg_replace ('#(?<=[\s>])(\()?([\w]+?://(?:[\w\\x80-\\xff\#$%&~/=?@\[\](+-]|[.,;:](?![\s<]|(\))?([\s]|$))|(?(1)\)(?![\s<.,;:]|$)|\)))+)#is', '\\1<a href="\\2">\\2</a>', $option_URL);
    }

    /**
     * getHost - retrieves host from the URL
     * @param string $option_URL
     * @return boolean
     */
    function getHost ($option_URL)
    {
        $hostArray = parse_url($option_URL);
        return $hostArray['scheme'].'://'.$hostArray['host'];
    }

    /**
     * retrieveURLContents - issues request with link via curl
     * @param string $option_URL
     * @return boolean
     */
    function retrieveURLContents ($option_URL)
    {
        return curl::processCurl ($option_URL);
    }

    /**
    * encodeLink
    * @param object $option_Link
    * $url = MolajoHelperURL::encodeLink ($option_Link);
    */
    function encodeLink ($option_Link) {
        return urlencode($option_Link);
    }

    /**
    * encodeLinkText
    * @param object $option_Text
    * $url = MolajoHelperURL::encodeLinkText ($option_Text);
    */
    function encodeLinkText ($option_Text) {
        return htmlentities($option_Text, ENT_QUOTES, 'UTF-8');
    }

    /**
    * addTrailingSlash
    * @param object $option_Text
    * $url = MolajoHelperURL::encodeLinkText ($option_InputText);
    */
    function addTrailingSlash ($option_InputText) {
            return untrailingslashit($option_InputText).'/';
    }

    /**
    * removeTrailingSlash
    * @param object $option_Text
    * $url = MolajoHelperURL::removeTrailingSlash ($option_InputText);
    */
    function removeTrailingSlash ($option_InputText) {
            return rtrim($option_InputText, '/');
    }

    /**
    * urlShortener
    * $longurl
    * @param object $longurl
    * 1 Local Shortened
    * 2 TinyURL
    * 3 is.gd
    * 4 bit.ly
    * 5 tr.im
    * @return
    */
    function urlShortener ( $longurl, $username, $apikey, $username, $apikey )
    {
        if ($shortener == '1') {
            return $longurl;	// todo: create local short url

        } else if ($shortener == '2') {
            return(implode('', file('http://tinyurl.com/api-create.php?url='.urlencode($longurl))));

        } else if ($shortener == '3') {
            return(implode('', file('http://is.gd/api.php?longurl='.urlencode($longurl))));

        } else if ($shortener == '4') {

            $bitlyURL = file_get_contents("http://api.bit.ly/v3/shorten"."&login=".$username."&apiKey=".$apikey."&longUrl=".urlencode($longurl)."&format=json");
            $bitlyContent = json_decode($bitlyURL, true);
            $bitlyError = $bitlyContent["errorCode"];
            if ($bitlyError == 0){
                return $bitlyContent["results"][$longurl]["shortUrl"];
            } else {
                return $bitlyError;
            }

        } else if ($shortener == '5') {
            return(implode('', file('http://api.tr.im/api/trim_simple?url='.urlencode($longurl))));

        } else {
            return $longurl;
        }
    }
}