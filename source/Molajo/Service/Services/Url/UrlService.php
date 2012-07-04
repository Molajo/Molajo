<?php
/**
 * @package   Molajo
 * @copyright 2012 Amy Stephen. All rights reserved.
 * @license    GNU GPL v 2, or later and MIT, see License folder
 */
namespace Molajo\Service\Services\Url;

use Molajo\Service\Services;
use Molajo\Extension\Helpers;

defined('MOLAJO') or die;

/**
 * URL
 *
 * @package     Molajo
 * @subpackage  Service
 * @since       1.0
 */
Class UrlService
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
			self::$instance = new UrlService();
		}

		return self::$instance;
	}

	/**
	 * Get either a Gravatar URL or complete image tag for a specified email address.
	 *
	 * @param string  $email
	 * @param string  $size       Size in pixels, defaults to 80px [ 1 - 512 ]
	 * @param string  $type       Default imageset to use [ 404 | mm | identicon | monsterid | wavatar ]
	 * @param string  $rating     Maximum rating (inclusive) [ g | pg | r | x ]
	 * @param boolean $image      True to return a complete IMG tag False for just the URL
	 * @param array   $attributes Optional, additional key/value attributes to include in the IMG tag
	 *
	 * @return Linked image or URL
	 *
	 * @source http://gravatar.com/site/implement/images/php/
	 */
	public function getGravatar($email, $size = 0, $type = 'mm', $rating = 'g',
								$image = false, $attributes = array(), $align='left')
	{

		if ((int)$size == 0) {
			$size = Services::Registry()->get('Configuration', 'gravatar_size', 80);
			$type = Services::Registry()->get('Configuration', 'gravatar_type', 'mm');
			$rating = Services::Registry()->get('Configuration', 'gravatar_rating', 'pg');
			$image = Services::Registry()->get('Configuration', 'gravatar_image', 0);
		}

		if ($align == 'right') {
			$css = '.gravatar { float:right; margin: 0 0 15px 15px; }';
		} else {
			$css = '.gravatar { float:left; margin: 0 15px 15px 0; }';
		}
		Services::Asset()->addCssDeclaration($css, 'text/css');

		$url = 'http://www.gravatar.com/avatar/';
		$url .= md5(strtolower(trim($email)));
		$url .= '?s=' . $size . '&d=' . $type . '&r=' . $rating;
		if ($image) {
			$url = '<img class="gravatar" src="' . $url . '"';
			if (count($attributes) > 0) {
				foreach ($attributes as $key => $val) {
					$url .= ' ' . $key . '="' . $val . '"';
				}
			}
			$url .= ' />';
		}

		return $url;
	}

	/**
	 * getURL Retrieves URL based on Catalog ID
	 *
	 * @param integer $catalog_id
	 *
	 * @return string
	 * @since  1.0
	 */
	public function getURL($catalog_id)
	{
		return $this->getApplicationURL(Helpers::Catalog()->getURL($catalog_id));
	}

	/**
	 * getCatalogID Retrieves Catalog ID for the SEF URL
	 *
	 * @param integer $catalog_id
	 *
	 * @return string
	 * @since  1.0
	 */
	public function getCatalogID($url)
	{
		return Helpers::Catalog()->getIDUsingSEFURL($url);
	}

	/**
	 * getApplicationURL - pass in non-application, non-base URL portion, returns full URL
	 *
	 * @param   string  $path
	 *
	 * @return  string
	 * @since   1.0
	 */
	public function getApplicationURL($path = '')
	{
		$url = BASE_URL . APPLICATION_URL_PATH;

		if ((int)Services::Registry()->get('Configuration', 'url_sef_rewrite', 0) == 1) {
		} else {
			$url .= 'index.php/';
		}

		if (Services::Registry()->get('Configuration', 'url_sef', 1) == 1) {

			$url .= $path;

			if ((int)Services::Registry()->get('Configuration', 'url_sef_suffix', 0) == 1) {
				$url .= '.html';
			}

		} else {
			$url .= $path;
		}

		return $url;
	}

	/**
	 * obfuscate Email
	 *
	 * @param   $email_address
	 *
	 * @return  string
	 * @since   1.0
	 */
	public function obfuscateEmail($email_address)
	{
		$obfuscate_email = "";

		for ($i = 0; $i < strlen($email_address); $i++) {
			$obfuscate_email .= "&#" . ord($email_address[$i]) . ";";
		}

		return $obfuscate_email;
	}

	/**
	 * Add links to a generic text field when URLs are found
	 *
	 * @param string $text_field
	 *
	 * @return string
	 */
	public function addLinks($text_field)
	{
		$pattern = "/(((http[s]?:\/\/)|(www\.))?(([a-z][-a-z0-9]+\.)?[a-z][-a-z0-9]+\.[a-z]+(\.[a-z]{2,2})?)\/?[a-z0-9._\/~#&=;%+?-]+[a-z0-9\/#=?]{1,1})/is";

		$text_field = preg_replace($pattern, " <a href='$1'>$1</a>", $text_field);

		// fix URLs without protocols
		$text_field = preg_replace("/href=\"www/", "href=\"http://www", $text_field);

		return $text_field;
	}

	/**
	 * createWebLinks - marks up a link into an <a href link
	 *
	 * todo: pick one of these two (previous and this one)
	 *
	 * @param string $url_field
	 *
	 * @return linked value
	 */
	public function createWebLinks($url_field)
	{
		return preg_replace('#(?<=[\s>])(\()?([\w]+?://(?:[\w\\x80-\\xff\#$%&~/=?@\[\](+-]|[.,;:](?![\s<]|(\))?([\s]|$))|(?(1)\)(?![\s<.,;:]|$)|\)))+)#is', '\\1<a href="\\2">\\2</a>', $url_field);
	}

	/**
	 * checkURLExternal - determines if it is a local site or external link
	 *
	 * @param string $url_field
	 *
	 * @return boolean
	 */
	public function checkURLExternal($url_field)
	{
		if (substr($url_field, 0, strlen(BASE_FOLDER)) == BASE_FOLDER) {
			return false;

		} elseif ((strtolower(substr($url_field, 0, 3)) == 'www')
			&& (substr($url_field, 3, strlen(BASE_FOLDER)) == BASE_FOLDER)
		) {
			return false;

		} else {
			return true;
		}
	}

	/**
	 * getHost - retrieves host from the URL
	 *
	 * @param string $url_field
	 *
	 * @return boolean
	 */
	public function getHost($url_field)
	{
		$hostArray = parse_url($url_field);

		return $hostArray['scheme'] . '://' . $hostArray['host'];
	}

	/**
	 * retrieveURLContents - issues request with link via curl
	 *
	 * @param string $url_field
	 *
	 * @return boolean
	 */
	public function retrieveURLContents($url_field)
	{
		return curl::processCurl($url_field);
	}

	/**
	 * addTrailingSlash
	 *
	 * @param object $url_field
	 *
	 * $url = Services::Url()->addTrailingSlash ($url_field);
	 */
	public function addTrailingSlash($url_field)
	{
		return untrailingslashit($url_field) . '/';
	}

	/**
	 * removeTrailingSlash
	 *
	 * @param object $url_field
	 *
	 * $url = Services::Url()->removeTrailingSlash ($url_field);
	 */
	public function removeTrailingSlash($url_field)
	{
		return rtrim($url_field, '/');
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
	public function urlShortener($longurl, $username, $apikey, $username, $apikey)
	{
		$shortener = 1;

		if ($shortener == '1') {
			return $longurl; // todo: create local short url

		} elseif ($shortener == '2') {
			return (implode('', file('http://tinyurl.com/api-create.php?url=' . urlencode($longurl))));

		} elseif ($shortener == '3') {
			return (implode('', file('http://is.gd/api.php?longurl=' . urlencode($longurl))));

		} elseif ($shortener == '4') {

			$bitlyURL = file_get_contents("http://api.bit.ly/v3/shorten" . "&login=" . $username . "&apiKey=" . $apikey . "&longUrl=" . urlencode($longurl) . "&format=json");
			$bitlyContent = json_decode($bitlyURL, true);
			$bitlyError = $bitlyContent["errorCode"];
			if ($bitlyError == 0) {
				return $bitlyContent["results"][$longurl]["shortUrl"];
			} else {
				return $bitlyError;
			}

		} elseif ($shortener == '5') {
			return (implode('', file('http://api.tr.im/api/trim_simple?url=' . urlencode($longurl))));

		} else {
			return $longurl;
		}
	}
}
