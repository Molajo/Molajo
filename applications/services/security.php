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
     * @var        array    A list of the default whitelist tags.
     * @since    1.5
     */
    var $tagWhitelist = array('a', 'abbr', 'acronym', 'address', 'area', 'b', 'big', 'blockquote', 'br', 'button', 'caption', 'center', 'cite', 'code', 'col', 'colgroup', 'dd', 'del', 'dfn', 'dir', 'div', 'dl', 'dt', 'em', 'fieldset', 'font', 'form', 'h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'hr', 'i', 'img', 'input', 'ins', 'kbd', 'label', 'legend', 'li', 'map', 'menu', 'ol', 'optgroup', 'option', 'p', 'pre', 'q', 's', 'samp', 'select', 'small', 'span', 'strike', 'strong', 'sub', 'sup', 'table', 'tbody', 'td', 'textarea', 'tfoot', 'th', 'thead', 'tr', 'tt', 'u', 'ul', 'var');

    /**
     * @var        array    A list of the default whitelist tag attributes.
     * @since    1.5
     */
    var $attrWhitelist = array('abbr', 'accept', 'accept-charset', 'accesskey', 'action', 'align', 'alt', 'axis', 'border', 'cellpadding', 'cellspacing', 'char', 'charoff', 'charset', 'checked', 'cite', 'class', 'clear', 'cols', 'colspan', 'color', 'compact', 'coords', 'datetime', 'dir', 'disabled', 'enctype', 'for', 'frame', 'headers', 'height', 'href', 'hreflang', 'hspace', 'id', 'ismap', 'label', 'lang', 'longdesc', 'maxlength', 'media', 'method', 'multiple', 'name', 'nohref', 'noshade', 'nowrap', 'prompt', 'readonly', 'rel', 'rev', 'rows', 'rowspan', 'rules', 'scope', 'selected', 'shape', 'size', 'span', 'src', 'start', 'summary', 'tabindex', 'target', 'title', 'type', 'usemap', 'valign', 'value', 'vspace', 'width');

    /**
     * @var        array    A list of the default blacklisted tags.
     * @since    1.5
     */
    var $tagBlacklist = array('applet', 'body', 'bgsound', 'base', 'basefont', 'embed', 'frame', 'frameset', 'head', 'html', 'id', 'iframe', 'ilayer', 'layer', 'link', 'meta', 'name', 'object', 'script', 'style', 'title', 'xml');

    /**
     * @var        array    A list of the default blacklisted tag attributes.
     * @since    1.5
     */
    var $attrBlacklist = array('action', 'background', 'codebase', 'dynsrc', 'lowsrc');

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
        $this->filter = FilterInput::getInstance();
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
     * @param   string  $field_name   Value of input field
     * @param   string  $field_value  Value of input field
     * @param   string  $datatype     Datatype of input field
     * @param   int     $null         0 or 1 - is null allowed
     * @param   string  $default      Default value, optional
     *
     * @return  mixed
     * @since   1.0
     */
    public function filter($field_name,
                           $field_value,
                           $datatype = 'char',
                           $null = 1,
                           $default = null)
    {

        switch ($datatype) {
            case 'int':
            case 'boolean':
            case 'float':
                return $this->filter_numeric(
                    $field_name, $field_value, $datatype, $null, $default
                );
                break;

            case 'date':
                return $this->filter_date(
                    $field_name, $field_value, $null, $default
                );
                break;

            case 'text':
                return $this->filter_html(
                    $field_name, $field_value, $null, $default
                );
                break;

            case 'email':
                return $this->filter_email(
                    $field_name, $field_value, $null, $default
                );
                break;

            case 'url':
                return $this->filter_url(
                    $field_name, $field_value, $null, $default
                );
                break;

            case 'char':
                return $this->filter_char(
                    $field_name, $field_value, $null, $default
                );
                break;
        }
    }

    /**
     * filter_numeric
     *
     * @param   string  $field_name   Value of input field
     * @param   string  $field_value  Value of input field
     * @param   string  $datatype     Datatype of input field
     * @param   int     $null         0 or 1 - is null allowed
     * @param   string  $default      Default value, optional
     *
     * @return  string
     * @since   1.0
     */
    public function filter_numeric($field_name,
                                   $field_value = null,
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
     * @param   string  $field_name   Value of input field
     * @param   string  $field_value  Value of input field
     * @param   int     $null         0 or 1 - is null allowed
     * @param   string  $default      Default value, optional
     *
     * @return  string
     * @since   1.0
     */
    public function filter_date($field_name,
                                $field_value = null,
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
     * @param   string  $field_name   Value of input field
     * @param   string  $field_value  Value of input field
     * @param   int     $null         0 or 1 - is null allowed
     * @param   string  $default      Default value, optional
     *
     * @return  mixed
     * @since   1.0
     */
    public function filter_char($field_name,
                                $field_value = null,
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
     * @param   string  $field_name   Value of input field
     * @param   string  $field_value  Value of input field
     * @param   int     $null         0 or 1 - is null allowed
     * @param   string  $default      Default value, optional
     *
     * @return  mixed
     * @since   1.0
     */
    public function filter_email($field_name,
                                 $field_value = null,
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
     * @param   string  $field_name   Value of input field
     * @param   string  $field_value  Value of input field
     * @param   int     $null         0 or 1 - is null allowed
     * @param   string  $default      Default value, optional
     *
     * @return  mixed
     * @since   1.0
     */
    public function filter_url($field_name,
                               $field_value = null,
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
     * @param   string  $field_name   Value of input field
     * @param   string  $field_value  Value of input field
     * @param   int     $null         0 or 1 - is null allowed
     * @param   string  $default      Default value, optional
     *
     * @return  mixed
     * @since   1.0
     */
    public function filter_html($field_name,
                                $field_value = null,
                                $null = 1,
                                $default = null)
    {
        if ($default == null) {
        } else if ($field_value == null) {
            $field_value = $default;
        }

        if ($field_value == null) {
        } else {
            $field_value = $this->filter->clean($field_value, 'HTML');
        }

        if ($field_value == null
            && $null == 0
        ) {
            throw new Exception('FILTER_VALUE_REQUIRED');
        }

        return $field_value;
    }

    /**
     * Method to be called by another php script. Processes for XSS and
     * specified bad code.
     *
     * @param   mixed   $source  Input string/array-of-string to be 'cleaned'
     * @param   string  $type    Return type for the variable (INT, UINT, FLOAT, BOOLEAN, WORD, ALNUM, CMD, BASE64, STRING, ARRAY, PATH, NONE)
     *
     * @return  mixed  'Cleaned' version of input parameter
     *
     * @since   1.0
     */
    public function clean($source, $type = 'string')
    {
        // Handle the type constraint
        switch (strtoupper($type))
        {
            case 'INT':
            case 'INTEGER':
                // Only use the first integer value
                preg_match('/-?[0-9]+/', (string)$source, $matches);
                $result = @ (int)$matches[0];
                break;

            case 'UINT':
                // Only use the first integer value
                preg_match('/-?[0-9]+/', (string)$source, $matches);
                $result = @ abs((int)$matches[0]);
                break;

            case 'FLOAT':
            case 'DOUBLE':
                // Only use the first floating point value
                preg_match('/-?[0-9]+(\.[0-9]+)?/', (string)$source, $matches);
                $result = @ (float)$matches[0];
                break;

            case 'BOOL':
            case 'BOOLEAN':
                $result = (bool)$source;
                break;

            case 'WORD':
                $result = (string)preg_replace('/[^A-Z_]/i', '', $source);
                break;

            case 'ALNUM':
                $result = (string)preg_replace('/[^A-Z0-9]/i', '', $source);
                break;

            case 'CMD':
                $result = (string)preg_replace('/[^A-Z0-9_\.-]/i', '', $source);
                $result = ltrim($result, '.');
                break;

            case 'BASE64':
                $result = (string)preg_replace('/[^A-Z0-9\/+=]/i', '', $source);
                break;

            case 'STRING':
                $result = (string)$this->_remove($this->_decode((string)$source));
                break;

            case 'HTML':
                $result = (string)$this->_remove((string)$source);
                break;

            case 'ARRAY':
                $result = (array)$source;
                break;

            case 'PATH':
                $pattern = '/^[A-Za-z0-9_-]+[A-Za-z0-9_\.-]*([\\\\\/][A-Za-z0-9_-]+[A-Za-z0-9_\.-]*)*$/';
                preg_match($pattern, (string)$source, $matches);
                $result = @ (string)$matches[0];
                break;

            case 'USERNAME':
                $result = (string)preg_replace('/[\x00-\x1F\x7F<>"\'%&]/', '', $source);
                break;

            default:
                // Are we dealing with an array?
                if (is_array($source)) {
                    foreach ($source as $key => $value)
                    {
                        // filter element for XSS and other 'bad' code etc.
                        if (is_string($value)) {
                            $source[$key] = $this->_remove($this->_decode($value));
                        }
                    }
                    $result = $source;
                }
                else
                {
                    // Or a string?
                    if (is_string($source) && !empty($source)) {
                        // filter source for XSS and other 'bad' code etc.
                        $result = $this->_remove($this->_decode($source));
                    }
                    else
                    {
                        // Not an array or string.. return the passed parameter
                        $result = $source;
                    }
                }
                break;
        }

        return $result;
    }


    /**
     * Applies the content text filters as per settings for current user group
     *
     * @param text The string to filter
     * @return string The filtered string
     */
    public function filterText($text)
    {
        return true;

        $acl = new MolajoACL ();
        $userGroups = $acl->getList('usergroups');

        /** retrieve defined filters by group **/
        $filters = $systemParameters->get('filters');

        /** initialise with default black and white list values **/
        $blackListTags = array();
        $blackListAttributes = array();
        $blackListTags = explode(',', $tagBlacklist);
        $blackListAttributes = explode(',', $attrBlacklist);

        $whiteListTags = array();
        $whiteListAttributes = array();
        $whiteListTags = explode(',', $tagWhitelist);
        $whiteListAttributes = explode(',', $attrWhitelist);

        $noHtml = false;
        $whiteList = false;
        $blackList = false;
        $unfiltered = false;

        // Cycle through each of the user groups the user is in.
        // Remember they are include in the Public group as well.
        foreach ($userGroups AS $groupId)
        {
            // May have added a group by not saved the filters.
            if (!isset($filters->$groupId)) {
                continue;
            }

            // Each group the user is in could have different filtering properties.
            $filterData = $filters->$groupId;
            $filterType = strtoupper($filterData->filter_type);

            if ($filterType == 'NH') {
                // Maximum HTML filtering.
                $noHtml = true;
            }
            else if ($filterType == 'NONE') {
                // No HTML filtering.
                $unfiltered = true;
            }
            else {
                // Black or white list.
                // Preprocess the tags and attributes.
                $tags = explode(',', $filterData->filter_tags);
                $attributes = explode(',', $filterData->filter_attributes);
                $tempTags = array();
                $tempAttributes = array();

                foreach ($tags AS $tag) {
                    $tag = trim($tag);
                    if ($tag) {
                        $tempTags[] = $tag;
                    }
                }

                foreach ($attributes AS $attribute) {
                    $attribute = trim($attribute);
                    if ($attribute) {
                        $tempAttributes[] = $attribute;
                    }
                }

                // Collect the black or white list tags and attributes.
                // Each list is cummulative.
                if ($filterType == 'BL') {
                    $blackList = true;
                    $blackListTags = array_merge($blackListTags, $tempTags);
                    $blackListAttributes = array_merge($blackListAttributes, $tempAttributes);
                }
                else if ($filterType == 'WL') {
                    $whiteList = true;
                    $whiteListTags = array_merge($whiteListTags, $tempTags);
                    $whiteListAttributes = array_merge($whiteListAttributes, $tempAttributes);
                }
            }
        }

        // Remove duplicates before processing (because the black list uses both sets of arrays).
        $blackListTags = array_unique($blackListTags);
        $blackListAttributes = array_unique($blackListAttributes);
        $whiteListTags = array_unique($whiteListTags);
        $whiteListAttributes = array_unique($whiteListAttributes);

        // Unfiltered assumes first priority.
        if ($unfiltered) {
            // Dont apply filtering.
        }
        else {
            // Black lists take second precedence.
            if ($blackList) {
                // Remove the white-listed attributes from the black-list.
                $filter = FilterInput::getInstance(
                    array_diff($blackListTags, $whiteListTags), // blacklisted tags
                    array_diff($blackListAttributes, $whiteListAttributes), // blacklisted attributes
                    1, // blacklist tags
                    1 // blacklist attributes
                );
            }
            // White lists take third precedence.
            else if ($whiteList) {
                $filter = FilterInput::getInstance($whiteListTags, $whiteListAttributes, 0, 0, 0); // turn off xss auto clean
            }
            // No HTML takes last place.
            else {
                $filter = FilterInput::getInstance();
            }

            $text = $filter->clean($text, 'html');
        }

        return $text;
    }


    /**
     * encodeLink
     * @param object $option_Link
     * $url = MolajoConfigurationServiceURL::encodeLink ($option_Link);
     */
    function encodeLink($option_Link)
    {
        return urlencode($option_Link);
    }

    /**
     * encodeLinkText
     * @param object $option_Text
     * $url = MolajoConfigurationServiceURL::encodeLinkText ($option_Text);
     */
    function encodeLinkText($option_Text)
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
    static public function escapeHTML($htmlText)
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
    static public function escapeInteger($integer)
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
    static public function escapeText($text)
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
    static public function escapeURL($url)
    {
        if (Services::Configuration()->get('unicode_slugs') == 1) {
            return FilterOutput::stringURLUnicodeSlug($url);
        } else {
            return FilterOutput::stringURLSafe($url);
        }
    }
}
