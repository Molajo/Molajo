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
     * Applies the content text filters as per settings for current user group
     *
     * @param text The string to filter
     * @return string The filtered string
     */
    public function filterText($text)
    {
        return true;

        /** filter parameters **/
        $molajoSystemPlugin =& MolajoPluginService::getPlugin('system', 'molajo');
        $systemParameters = new JParameter($molajoSystemPlugin->parameters);

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

    public static function filterURL($text)
    {
    }

    public static function filterEmail($text)
    {
    }

    public static function filterFile($text)
    {
    }

    public static function filterIPAddress($text)
    {
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
        return (int) $integer;
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
