<?php
/**
 * @package     Molajo
 * @subpackage  Helper
 * @copyright   Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @copyright   Copyright (C) 2012 Amy Stephen. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */
defined('MOLAJO') or die;

/**
 * Application Helper
 *
 * @package     Molajo
 * @subpackage  Filter Helper
 * @since       1.0
 */
class MolajoFilterHelper
{
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
     * Applies the content text filters as per settings for current user group
     *
     * @param text The string to filter
     * @return string The filtered string
     */
    public function text($text)
    {
        return true;

        /** filter parameters **/
        $molajoSystemPlugin =& MolajoPluginHelper::getPlugin('system', 'molajo');
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
                $filter = JFilterInput::getInstance(
                    array_diff($blackListTags, $whiteListTags), // blacklisted tags
                    array_diff($blackListAttributes, $whiteListAttributes), // blacklisted attributes
                    1, // blacklist tags
                    1 // blacklist attributes
                );
            }
                // White lists take third precedence.
            else if ($whiteList) {
                $filter = JFilterInput::getInstance($whiteListTags, $whiteListAttributes, 0, 0, 0); // turn off xss auto clean
            }
                // No HTML takes last place.
            else {
                $filter = JFilterInput::getInstance();
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

}