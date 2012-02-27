<?php
/**
 * @package     Molajo
 * @subpackage  Service
 * @copyright   Copyright (C) 2012 Amy Stephen. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */
defined('MOLAJO') or die;

/**
 * Security
 *
 * @package     Molajo
 * @subpackage  Service
 * @since       1.0
 *
 * http://docs.joomla.org/Secure_coding_guidelines
 */
class MolajoSecurityService
{
    /**
     * Instance
     *
     * @var    object
     * @since  1.0
     */
    protected static $instance;

    /**
     * Session
     *
     * @var    object
     * @since  1.0
     */
    protected $session;

    /**
     * Hash
     *
     * @var    array
     * @since  1.0
     */
    protected $hash;

    /**
     * Token
     *
     * @var    array
     * @since  1.0
     */
    protected $token;

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
            self::$instance = new MolajoSecurityService ();
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
        $this->session = Services::Session();
        $this->initialiseFiltering();
    }

    /**
     * initialiseFiltering
     *
     * HTMLPurifier can be configured by:
     *
     * 1. defining options in applications/options/htmlpurifier.xml
     * 2. creating custom filters in applications/filters
     * 3. setting html_display_filter parameter false (default = true)
     *
     * HTML 5 is not supported by HTMLPurifier although they are
     *  working on it. http://htmlpurifier.org/doxygen/html/classHTML5.html
     *
     */
    protected function initialiseFiltering()
    {
        $config = HTMLPurifier_Config::createDefault();

        if ((int)Services::Configuration()->get('html5', 1) == 1) {
            $config->set('HTML.Doctype', 'HTML 4.01 Transitional');
            //not supported $config->set('HTML.Doctype', 'HTML5');
        } else {
            $config->set('HTML.Doctype', 'HTML 4.01 Transitional');
        }
        $config->set('URI.Host', MOLAJO_BASE_URL);

        /** Custom Filters */
        $files = Services::Folder()->files(MOLAJO_APPLICATIONS . '/filters', '\.php$', false, false);
        foreach ($files as $file) {
            $class = 'Molajo' . ucfirst(substr($file, 0, strpos($file, '.'))) . 'Filter';
            $config->set('Filter.Custom', array(new $class()));
        }

        /** Configured Options */
        $options = simplexml_load_file(MOLAJO_APPLICATIONS . '/options/htmlpurifier.xml');
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
     * getHash
     *
     * Provides a secure hash based on a seed
     *
     * @param   string   $seed  Seed string.
     *
     * @return  string   A secure hash
     * @since  1.0
     */
    public function getHash($seed)
    {
        return md5(Services::Configuration()->get('secret') . $seed);
    }

    /**
     * getToken
     *
     * Tokens are used to secure forms from spamming attacks.
     *
     * @param   boolean  If true, force a new token to be created
     * @return  string   Session token
     */
    public function getToken($forceNew = false)
    {
        $token = $this->session->get('session.token');

        if ($token === null
            || $forceNew
        ) {
            $token = $this->session->_createToken(12);
            $this->session->set('session.token', $token);
        }

        return $token;
    }

    /**
     * hasToken
     *
     * Method to determine if a token exists in the session. If not the
     * session will be set to expired
     *
     * @param   string   Hashed token to be verified
     * @param   boolean  If true, expires the session
     *
     * @return  boolean
     * @since   1.0
     */
    public function hasToken($tCheck, $forceExpire = true)
    {
        $tStored = $this->session->get('session.token');

        if (($tStored !== $tCheck)) {
            if ($forceExpire) {
                $this->session->_state = 'expired';
            }
            return false;
        }

        return true;
    }

    /**
     * getFormToken
     *
     * Method to determine a hash for anti-spoofing variable names
     *
     * @return  string  Hashed variable name
     * @since   1.0
     */
    public function getFormToken($forceNew = false)
    {
        return $this->getHash(Services::User()->get('id', 0));
        //                . $this->getToken($forceNew)
    }

    /**
     * _createToken
     *
     * Create a token-string
     *
     * @param   integer  length of string
     *
     * @return  string  generated token
     * @since  1.0
     */
    protected function _createToken($length = 32)
    {
        static $chars = '0123456789abcdef';
        $max = strlen($chars) - 1;
        $token = '';
        $name = session_name();
        for ($i = 0; $i < $length; ++$i) {
            $token .= $chars[(rand(0, $max))];
        }
        return md5($token . $name);
    }

    /**
     * filter
     *
     * Filter input, default value, edit
     *
     * @param   string  $field_value  Value of input field
     * @param   string  $datatype     Datatype of input field
     * @param   int     $null         0 or 1 - is null allowed
     * @param   string  $default      Default value, optional
     *
     * @return  mixed
     * @since   1.0
     */
    public function filter($field_value,
                           $datatype = 'char',
                           $null = 1,
                           $default = null)
    {

        switch (strtolower($datatype)) {
            case 'int':
            case 'boolean':
            case 'float':
                return $this->filter_numeric(
                    $field_value, $datatype, $null, $default
                );
                break;

            case 'date':
                return $this->filter_date(
                    $field_value, $null, $default
                );
                break;

            case 'text':
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
     * @param   string  $datatype     Datatype of input field
     * @param   int     $null         0 or 1 - is null allowed
     * @param   string  $default      Default value, optional
     *
     * @return  string
     * @since   1.0
     */
    public function filter_numeric($field_value,
                                   $datatype = 'int',
                                   $null = 1,
                                   $default = null)
    {
        if ($default == null) {
        } else if ($field_value == null) {
            $field_value = $default;
        }

        if ($field_value == null) {
        } else {
            switch ($datatype) {

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
                throw new Exception('FILTER_INVALID_VALUE');
            }
        }

        if ($field_value == null
            && $null == 0
        ) {
            throw new Exception('FILTER_VALUE_REQUIRED');
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
                throw new Exception('FILTER_INVALID_VALUE');
            }
            $test = $ccyy . '-' . $mm . '-' . $dd;

            if ($test == substr($field_value, 0, 10)) {
                return $field_value;
            } else {
                throw new Exception('FILTER_INVALID_VALUE');
            }
        }

        if ($field_value == null
            && $null == 0
        ) {
            throw new Exception('FILTER_VALUE_REQUIRED');
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
                throw new Exception('FILTER_INVALID_VALUE');
            }
        }

        if ($field_value == null
            && $null == 0
        ) {
            throw new Exception('FILTER_VALUE_REQUIRED');
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
                throw new Exception('FILTER_INVALID_VALUE');
            }
        }

        if ($field_value == null
            && $null == 0
        ) {
            throw new Exception('FILTER_VALUE_REQUIRED');
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
                throw new Exception('FILTER_INVALID_VALUE');
            }
        }

        if ($field_value == null
            && $null == 0
        ) {
            throw new Exception('FILTER_VALUE_REQUIRED');
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
            throw new Exception('FILTER_VALUE_REQUIRED');
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
     * encodeLink
     *
     * @param object $option_Link
     * $url = MolajoConfigurationServiceURL::encodeLink ($option_Link);
     */
    public function encodeLink($option_Link)
    {
        return urlencode($option_Link);
    }

    /**
     * encodeLinkText
     *
     * @param object $option_Text
     * $url = MolajoConfigurationServiceURL::encodeLinkText ($option_Text);
     */
    public function encodeLinkText($option_Text)
    {
        return htmlentities($option_Text, ENT_QUOTES, 'UTF-8');
    }

    /**
     * escapeHTML
     *
     * @param string $text
     *
     * @return  string
     * @since   1.0
     */
    public function escapeHTML($htmlText)
    {

    }

    /**
     * escapeInteger
     *
     * @param string $integer
     *
     * @return  string
     * @since   1.0
     */
    public function escapeInteger($integer)
    {
        return (int)$integer;
    }

    /**
     * escapeText
     *
     * @param string $text
     *
     * @return  string
     * @since   1.0
     */
    public function escapeText($text)
    {
        return htmlspecialchars($text, ENT_COMPAT, 'utf-8');
    }

    /**
     * escapeURL
     *
     * @param   string  $url
     *
     * @return  string
     * @since  1.0
     */
    public function escapeURL($url)
    {
        if (Services::Configuration()->get('unicode_slugs') == 1) {
            return FilterOutput::stringURLUnicodeSlug($url);
        } else {
            return FilterOutput::stringURLSafe($url);
        }
    }
}
