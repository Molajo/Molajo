<?php
/**
 * @package   Molajo
 * @copyright 2012 Amy Stephen. All rights reserved.
 * @license   GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
namespace Molajo\Service;

use HTMLPurifier\HTMLPurifier;
use HTMLPurifier\HTMLPurifier_Config;
use Molajo\Service;

defined('MOLAJO') or die;

/**
 * Filter
 *
 * @package     Molajo
 * @subpackage  Service
 * @since       1.0
 *
 * http://docs.joomla.org/Secure_coding_guidelines
 */
Class FilterService
{
	/**
	 * Instance
	 *
	 * @var    object
	 * @since  1.0
	 */
	protected static $instance;

	/**
	 * Filter
	 *
	 * @var    array
	 * @since  1.0
	 */
	protected $filter;

	/**
	 * HTML Purifier
	 *
	 * @var    object
	 * @since  1.0
	 */
	protected $purifier;

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
			self::$instance = new FilterService ();
		}
		return self::$instance;
	}

	/**
	 * Class constructor.
	 *
	 * @return  null
	 * @since   1.0
	 */
	public function __construct()
	{
		$this->initialise_filtering();
	}

	/**
	 * initialise_filtering
	 *
	 * HTMLPurifier can be configured by:
	 *
	 * 1. defining options in applications/Configuration/htmlpurifier.xml
	 * 2. creating custom filters in applications/filters
	 * 3. setting html_display_filter parameter false (default = true)
	 *
	 * HTML 5 is not supported by HTMLPurifier although they are
	 *  working on it. http://htmlpurifier.org/doxygen/html/classHTML5.html
	 *
	 */
	protected function initialise_filtering()
	{
		return;

		$config = HTMLPurifier\HTMLPurifier_Config::createDefault();
		var_dump($config);

		if ((int)Service::Registry()->get('Configuration', 'html5', 1) == 1) {
			$config->set('HTML.Doctype', 'HTML 4.01 Transitional');
			//not supported $config->set('HTML.Doctype', 'HTML5');
		} else {
			$config->set('HTML.Doctype', 'HTML 4.01 Transitional');
		}
		$config->set('URI.Host', BASE_URL);

		/** Custom Filters */
		$files = Service::Filesystem()->folderFiles(HTMPURIFIER_FILTERS, '\.php$', false, false);
		foreach ($files as $file) {
			$class = 'HTMLPurifier\\filters\\';
			$class .= substr($file, 0, strpos($file, '.'));
			$config->set('Filter.Custom', array(new $class()));
		}

		/** Configured Options */
		$options = Service::Configuration()->loadXML('htmlpurifier');
		$options = array();
		if (count($options) > 0) {
			foreach ($options->option as $o) {
				$key = (string)$o['key'];
				$value = (string)$o['value'];
				$config->set($key, $value);
			}
		}
		$this->purifier = new HTMLPurifier($config);
	}

	/**
	 * filter
	 *
	 * Filter input, default value, edit
	 *
	 * @param   string  $field_value  Value of input field
	 * @param   string  $dataType     Datatype of input field
	 * @param   int     $null         0 or 1 - is null allowed
	 * @param   string  $default      Default value, optional
	 * @param   array   $values       Set of values of which the field must contain
	 *
	 * @return  mixed
	 * @since   1.0
	 */
	public function filter($field_value,
						   $dataType = 'char',
						   $null = 1,
						   $default = null,
						   $values = array())
	{

		switch (strtolower($dataType)) {
			case 'int':
			case 'boolean':
			case 'float':
				return $this->filter_numeric(
					$field_value, $dataType, $null, $default
				);
				break;

			case 'date':
				return $this->filter_date(
					$field_value, $null, $default
				);
				break;

			case 'text':
				return $field_value;
				return $this->filter_html(
					$field_value, $null, $default
				);
				break;

			case 'email':
				return $this->filter_email(
					$field_value, $null, $default
				);
				break;

			case 'url':
				return $this->filter_url(
					$field_value, $null, $default
				);
				break;

			case 'word':
				return (string)preg_replace('/[^A-Z_]/i', '', $field_value);
				break;

			case 'alnum':
				return (string)preg_replace('/[^A-Z0-9]/i', '', $field_value);
				break;

			case 'cmd':
				$result = (string)preg_replace('/[^A-Z0-9_\.-]/i', '', $field_value);
				return ltrim($result, '.');
				break;

			case 'base64':
				return (string)preg_replace('/[^A-Z0-9\/+=]/i', '', $field_value);
				break;

			case 'filename':
				return $this->filter_filename($field_value);
				break;

			case 'path':
				return $this->filter_foldername($field_value);
				break;

			case 'username':
				return (string)preg_replace('/[\x00-\x1F\x7F<>"\'%&]/', '', $field_value);
				break;

			case 'header_injection_test':
				return $this->filter_header_injection_test($field_value);
				break;

			default:
				return $this->filter_char(
					$field_value, $null, $default
				);
				break;
		}
	}

	/**
	 * filter_numeric
	 *
	 * @param   string  $field_value  Value of input field
	 * @param   string  $dataType     Datatype of input field
	 * @param   int     $null         0 or 1 - is null allowed
	 * @param   string  $default      Default value, optional
	 *
	 * @return  string
	 * @since   1.0
	 */
	public function filter_numeric($field_value,
								   $dataType = 'int',
								   $null = 1,
								   $default = null)
	{
		if ($default == null) {
		} else if ($field_value == null) {
			$field_value = $default;
		}

		if ($field_value == null) {
		} else {
			switch ($dataType) {

				case 'boolean':
					$test = filter_var(
						$field_value,
						FILTER_SANITIZE_NUMBER_INT
					);
					if ($test == 1) {
					} else {
						$test = 0;
					}
					break;

				case 'float':
					$test = filter_var(
						$field_value,
						FILTER_SANITIZE_NUMBER_FLOAT,
						FILTER_FLAG_ALLOW_FRACTION
					);
					break;

				default:
					$test = filter_var(
						$field_value,
						FILTER_SANITIZE_NUMBER_INT
					);
					break;

			}
			if ($test == $field_value) {
				return $test;
			} else {
				throw new \Exception('FILTER_INVALID_VALUE');
			}
		}

		if ($field_value == null
			&& $null == 0
		) {
			throw new \Exception('FILTER_VALUE_REQUIRED');
		}

		return $field_value;
	}

	/**
	 * filter_date
	 *
	 * @param   string  $field_value  Value of input field
	 * @param   int     $null         0 or 1 - is null allowed
	 * @param   string  $default      Default value, optional
	 *
	 * @return  string
	 * @since   1.0
	 */
	public function filter_date($field_value = null,
								$null = 1,
								$default = null)
	{
		if ($default == null) {
		} else if ($field_value == null
			|| $field_value == ''
			|| $field_value == 0
		) {
			$field_value = $default;
		}

		if ($field_value == null
			|| $field_value == '0000-00-00 00:00:00'
		) {

		} else {
			$dd = substr($field_value, 8, 2);
			$mm = substr($field_value, 5, 2);
			$ccyy = substr($field_value, 0, 4);

			if (checkdate((int)$mm, (int)$dd, (int)$ccyy)) {
			} else {
				throw new \Exception('FILTER_INVALID_VALUE');
			}
			$test = $ccyy . '-' . $mm . '-' . $dd;

			if ($test == substr($field_value, 0, 10)) {
				return $field_value;
			} else {
				throw new \Exception('FILTER_INVALID_VALUE');
			}
		}

		if ($field_value == null
			&& $null == 0
		) {
			throw new \Exception('FILTER_VALUE_REQUIRED');
		}

		return $field_value;
	}

	/**
	 * filter_char
	 *
	 * @param   string  $field_value  Value of input field
	 * @param   int     $null         0 or 1 - is null allowed
	 * @param   string  $default      Default value, optional
	 *
	 * @return  mixed
	 * @since   1.0
	 */
	public function filter_char($field_value = null,
								$null = 1,
								$default = null)
	{
		if ($default == null) {
		} else {
			$field_value = $default;
		}

		if ($field_value == null) {
		} else {
			$test = filter_var($field_value, FILTER_SANITIZE_STRING);
			if ($test == $field_value) {
				return $test;
			} else {
				throw new \Exception('FILTER_INVALID_VALUE');
			}
		}

		if ($field_value == null
			&& $null == 0
		) {
			throw new \Exception('FILTER_VALUE_REQUIRED');
		}

		return $field_value;
	}

	/**
	 * filter_email
	 *
	 * @param   string  $field_value  Value of input field
	 * @param   int     $null         0 or 1 - is null allowed
	 * @param   string  $default      Default value, optional
	 *
	 * @return  mixed
	 * @since   1.0
	 */
	public function filter_email($field_value = null,
								 $null = 1,
								 $default = null)
	{
		if ($default == null) {
		} else {
			$field_value = $default;
		}

		if ($field_value == null) {
		} else {
			$test = filter_var($field_value, FILTER_SANITIZE_EMAIL);
			if (filter_var($test, FILTER_VALIDATE_EMAIL)) {
				return $test;
			} else {
				throw new \Exception('FILTER_INVALID_VALUE');
			}
		}

		if ($field_value == null
			&& $null == 0
		) {
			throw new \Exception('FILTER_VALUE_REQUIRED');
		}

		return $field_value;
	}

	/**
	 * filter_url
	 *
	 * @param   string  $field_value  Value of input field
	 * @param   int     $null         0 or 1 - is null allowed
	 * @param   string  $default      Default value, optional
	 *
	 * @return  mixed
	 * @since   1.0
	 */
	public function filter_url($field_value = null,
							   $null = 1,
							   $default = null)
	{
		if ($default == null) {
		} else {
			$field_value = $default;
		}

		if ($field_value == null) {
		} else {
			$test = filter_var($field_value, FILTER_SANITIZE_URL);
			if (filter_var($test, FILTER_VALIDATE_URL)) {
				return $test;
			} else {
				throw new \Exception('FILTER_INVALID_VALUE');
			}
		}

		if ($field_value == null
			&& $null == 0
		) {
			throw new \Exception('FILTER_VALUE_REQUIRED');
		}

		return $field_value;
	}

	/**
	 * filter_html
	 *
	 * @param   string  $field_value  Value of input field
	 * @param   int     $null         0 or 1 - is null allowed
	 * @param   string  $default      Default value, optional
	 *
	 * @return  mixed
	 * @since   1.0
	 */
	public function filter_html($field_value = null,
								$null = 0,
								$default = null)
	{
		if ($default == null) {
		} else if ($field_value == null) {
			$field_value = $default;
		}

		if ($field_value == null) {
		} else {
			$field_value = $this->purifier->purify($field_value);
		}

		if ($field_value == null
			&& $null == 0
		) {
			throw new \Exception('FILTER_VALUE_REQUIRED');
		}

		return $field_value;
	}

	/**
	 * filter_filename
	 *
	 * Filters the filename so that it is safe to use
	 *
	 * @param   string  $file  The name of the file [not full path]
	 *
	 * @return  string  The sanitised string
	 * @since   1.0
	 */
	public function filter_filename($file)
	{
		$regex = array('#(\.){2,}#', '#[^A-Za-z0-9\.\_\- ]#', '#^\.#');

		return preg_replace($regex, '', $file);
	}

	/**
	 * filter_foldername
	 *
	 * Filters the foldername so that it is safe to use
	 *
	 * @param   string  $path  The full path to sanitise.
	 *
	 * @return  string  The sanitised string.
	 * @since   1.0
	 */
	public function filter_foldername($path)
	{
		$regex = array('#[^A-Za-z0-9:_\\\/-]#');

		return preg_replace($regex, '', $path);
	}

	/**
	 * filter_header_injection_test
	 *
	 * Looks for unauthorized header information
	 *
	 * @param   string  $content  The content to test.
	 *
	 * @return mixed
	 * @throws \Exception
	 */
	public function filter_header_injection_test($content)
	{
		$headers = array('Content-Type:',
			'MIME-Version:',
			'Content-Transfer-Encoding:',
			'bcc:',
			'cc:'
		);

		foreach ($headers as $header) {
			if (strpos($content, $header) !== false) {
				throw new \Exception('FILTER_INVALID_VALUE');
			}
		}

		return $content;
	}

	/**
	 * encode_link
	 *
	 * @param object $option_Link
	 * $url = ConfigurationServiceURL::encode_link ($option_Link);
	 */
	public function encode_link($option_Link)
	{
		return urlencode($option_Link);
	}

	/**
	 * encode_link_text
	 *
	 * @param object $option_Text
	 * $url = ConfigurationServiceURL::encode_link_text ($option_Text);
	 */
	public function encode_link_text($option_Text)
	{
		return htmlentities($option_Text, ENT_QUOTES, 'UTF-8');
	}

	/**
	 * escape_html
	 *
	 * @param string $text
	 *
	 * @return  string
	 * @since   1.0
	 */
	public function escape_html($htmlText)
	{

	}

	/**
	 * escape_integer
	 *
	 * @param string $integer
	 *
	 * @return  string
	 * @since   1.0
	 */
	public function escape_integer($integer)
	{
		return (int)$integer;
	}

	/**
	 * escape_text
	 *
	 * @param string $text
	 *
	 * @return  string
	 * @since   1.0
	 */
	public function escape_text($text)
	{
		return htmlspecialchars($text, ENT_COMPAT, 'utf-8');
	}

	/**
	 * escape_url
	 *
	 * @param   string  $url
	 *
	 * @return  string
	 * @since  1.0
	 */
	public function escape_url($url)
	{
		if (Service::Registry()->get('Configuration', 'unicode_slugs') == 1) {
//            return FilterOutput::stringURLUnicodeSlug($url);
		} else {
//            return FilterOutput::stringURLSafe($url);
		}
	}
}
