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
 * Language Helper
 *
 * @package     Molajo
 * @subpackage  Language Helper
 * @since       1.0
 */
class MolajoLanguageHelper
{
    /**
     * Builds a list of the system languages which can be used in a select option
     *
     * @param   string   $actualLanguage  Client key for the area
     * @param   string   $basepath        Base path to use
     * @param   boolean  $caching         True if caching is used
     * @param   array    $installed       An array of arrays (text, value, selected)
     *
     * @return  array  List of system languages
     *
     * @since   1.0
     */
    public static function createLanguageList($actualLanguage, $basePath = MOLAJO_BASE_FOLDER, $caching = false, $installed = false)
    {
        $list = array();
        $langs = MolajoLanguage::getKnownLanguages($basePath);

        if (MOLAJO_APPLICATION_ID == 0) {
            $installed == false;

        } elseif ($installed === true) {
            $installed_languages = MolajoExtension::getExtensions(2);
        }

        foreach ($langs as $lang => $metadata)
        {
            $option = array();

            $option['text'] = $metadata['name'];
            $option['value'] = $lang;
            if ($lang == $actualLanguage) {
                $option['selected'] = 'selected="selected"';
            }
            $list[] = $option;
        }

        return $list;
    }

    /**
     * Tries to detect the language.
     *
     * @return  string  locale or null if not found
     * @since   1.0
     */
    public static function detectLanguage()
    {

        if (isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {
            $browserLangs = explode(',', $_SERVER['HTTP_ACCEPT_LANGUAGE']);
            $systemLangs = self::getLanguages();
            foreach ($browserLangs as $browserLang)
            {
                // Slice out the part before ; on first step, the part before - on second, place into array
                $browserLang = substr($browserLang, 0, strcspn($browserLang, ';'));
                $primary_browserLang = substr($browserLang, 0, 2);
                foreach ($systemLangs as $systemLang)
                {
                    // Take off 3 letters iso code languages as they can't match browsers' languages and default them to en
                    $Jinstall_lang = $systemLang->lang_code;

                    if (strlen($Jinstall_lang) < 6) {
                        if (strtolower($browserLang) == strtolower(substr($systemLang->lang_code, 0, strlen($browserLang)))) {
                            return $systemLang->lang_code;
                        }
                        else if ($primary_browserLang == substr($systemLang->lang_code, 0, 2)) {
                            $primaryDetectedLang = $systemLang->lang_code;
                        }
                    }
                }

                if (isset($primaryDetectedLang)) {
                    return $primaryDetectedLang;
                }
            }
        }

        return null;
    }

    /**
     * Get available languages
     *
     * @param   string  $key  Array key
     *
     * @return  array  An array of published languages
     *
     * @since   1.0
     */
    public static function getLanguages($key = 'default')
    {
        static $languages;

        if (empty($languages)) {

            // Installation uses available languages
            if (MOLAJO_APPLICATION_ID == 0) {
                $languages[$key] = array();
                $knownLangs = MolajoLanguage::getKnownLanguages(MOLAJO_BASE_FOLDER);
                foreach ($knownLangs as $metadata)
                {
                    // take off 3 letters iso code languages as they can't match browsers' languages and default them to en
                    $languages[$key][] = new JObject(array('lang_code' => $metadata['tag']));
                }
            } else {
                $languages['default'] = MolajoExtension::getExtensions(2);
                $languages['sef'] = array();
                $languages['lang_code'] = array();

                if (isset($languages['default'][0])) {
                    foreach ($languages['default'] as $lang) {
                        $languages['sef'][$lang->sef] = $lang;
                        $languages['lang_code'][$lang->lang_code] = $lang;
                    }
                }
            }
        }

        return $languages[$key];
    }
}
