<?php
/**
 * @package     Molajo
 * @subpackage  Service
 * @copyright   Copyright (C) 2012 Amy Stephen. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */
defined('MOLAJO') or die;

/**
 * URL
 *
 * @package     Molajo
 * @subpackage  Service
 * @since       1.0
 */
class MolajoUrlService
{
    /**
     * Static instance
     *
     * @var    object
     * @since  1.0
     */
    protected static $instance;

    /**
     * getInstance
     *
     * @static
     * @return bool|object
     * @since  1.0
     */
    public static function getInstance()
    {
        if (empty(self::$instance)) {
            self::$instance = new MolajoUrlService();
        }
        return self::$instance;
    }

    /**
     * __construct
     *
     * Class constructor.
     *
     * @return boolean
     * @since  1.0
     */
    public function __construct()
    {

    }

    /**
     * get
     *
     * Returns a property of the Input object
     * or the default value if the property is not set.
     *
     * @param   string  $key      The name of the property.
     * @param   mixed   $default  The default value (optional) if none is set.
     *
     * @return  mixed   The value of the configuration.
     *
     * @since   1.0
     */
    public function get($key, $default = null)
    {
        return $this->input->get($key, $default);
    }

    /**
     * set
     *
     * Modifies a property of the Input object, creating it if it does not already exist.
     *
     * @param   string  $key    The name of the property.
     * @param   mixed   $value  The value of the property to set (optional).
     *
     * @return  mixed   Previous value of the property
     *
     * @since   1.0
     */
    public function set($key, $value = null)
    {
        return $this->input->set($key, $value);
    }

    /**
     * Get either a Gravatar URL or complete image tag for a specified email address.
     *
     * @param string $option_Email The email address
     * @param string $s Size in pixels, defaults to 80px [ 1 - 512 ]
     * @param string $d Default imageset to use [ 404 | mm | identicon | monsterid | wavatar ]
     * @param string $r Maximum rating (inclusive) [ g | pg | r | x ]
     * @param boolean $img True to return a complete IMG tag False for just the URL
     * @param array $atts Optional, additional key/value attributes to include in the IMG tag
     * @return String containing either just a URL or a complete image tag
     * @source http://gravatar.com/site/implement/images/php/
     */
    public function getGravatar($option_Email, $s = 80, $d = 'mm', $r = 'g', $img = false, $atts = array())
    {
        $url = 'http://www.gravatar.com/avatar/';
        $url .= md5(strtolower(trim($option_Email)));
        $url .= "?s=$s&d=$d&r=$r";
        if ($img) {
            $url = '<img src="' . $url . '"';
            if (count($atts) > 0) {
                foreach ($atts as $key => $val) {
                    $url .= ' ' . $key . '="' . $val . '"';
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
    function addLinks($option_Text)
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
    function checkURLExternal($option_URL)
    {
        if (substr($option_URL, 0, strlen(MOLAJO_BASE_FOLDER)) == MOLAJO_BASE_FOLDER) {
            return false;
        } elseif ((strtolower(substr($option_URL, 0, 3)) == 'www')
            && (substr($option_URL, 3, strlen(MOLAJO_BASE_FOLDER)) == MOLAJO_BASE_FOLDER)
        ) {
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
    function checkURLValidity($option_URL)
    {
        return preg_match('|^http(s)?://[a-z0-9-]+(.[a-z0-9-]+)*(:[0-9]+)?(/.*)?$|i', $option_URL);
    }

    /**
     * createWebLinks - marks up a link into an <a href link
     * @param string $option_URL
     * @return linked value
     */
    function createWebLinks($option_URL)
    {
        return preg_replace('#(?<=[\s>])(\()?([\w]+?://(?:[\w\\x80-\\xff\#$%&~/=?@\[\](+-]|[.,;:](?![\s<]|(\))?([\s]|$))|(?(1)\)(?![\s<.,;:]|$)|\)))+)#is', '\\1<a href="\\2">\\2</a>', $option_URL);
    }

    /**
     * getHost - retrieves host from the URL
     * @param string $option_URL
     * @return boolean
     */
    function getHost($option_URL)
    {
        $hostArray = parse_url($option_URL);
        return $hostArray['scheme'] . '://' . $hostArray['host'];
    }

    /**
     * retrieveURLContents - issues request with link via curl
     * @param string $option_URL
     * @return boolean
     */
    function retrieveURLContents($option_URL)
    {
        return curl::processCurl($option_URL);
    }

    /**
     * addTrailingSlash
     * @param object $option_Text
     * $url = MolajoConfigurationServiceURL::encodeLinkText ($option_InputText);
     */
    function addTrailingSlash($option_InputText)
    {
        return untrailingslashit($option_InputText) . '/';
    }

    /**
     * removeTrailingSlash
     * @param object $option_Text
     * $url = MolajoConfigurationServiceURL::removeTrailingSlash ($option_InputText);
     */
    function removeTrailingSlash($option_InputText)
    {
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
    function urlShortener($longurl, $username, $apikey, $username, $apikey)
    {
        $shortener = 1;

        if ($shortener == '1') {
            return $longurl; // todo: create local short url

        } else if ($shortener == '2') {
            return (implode('', file('http://tinyurl.com/api-create.php?url=' . urlencode($longurl))));

        } else if ($shortener == '3') {
            return (implode('', file('http://is.gd/api.php?longurl=' . urlencode($longurl))));

        } else if ($shortener == '4') {

            $bitlyURL = file_get_contents("http://api.bit.ly/v3/shorten" . "&login=" . $username . "&apiKey=" . $apikey . "&longUrl=" . urlencode($longurl) . "&format=json");
            $bitlyContent = json_decode($bitlyURL, true);
            $bitlyError = $bitlyContent["errorCode"];
            if ($bitlyError == 0) {
                return $bitlyContent["results"][$longurl]["shortUrl"];
            } else {
                return $bitlyError;
            }

        } else if ($shortener == '5') {
            return (implode('', file('http://api.tr.im/api/trim_simple?url=' . urlencode($longurl))));

        } else {
            return $longurl;
        }
    }
}
