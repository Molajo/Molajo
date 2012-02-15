<?php
/**
 * @package     Molajo
 * @subpackage  Helper
 * @copyright   Copyright (C) 2012 Amy Stephen. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */
defined('MOLAJO') or die;

/**
 * Language
 *
 * @package     Molajo
 * @subpackage  Helper
 * @since       1.0
 */
class MolajoLanguageHelper
{
    /**
     * getLanguage
     *
     * Tries to detect the language.
     *
     * @return  string  locale or null if not found
     * @since   1.0
     */
    public function getDefault()
    {
        /** Installed Languages */
        $languages = LanguageHelper::getLanguages(
            MOLAJO_EXTENSIONS_LANGUAGES
        );

        $installed = array();
        foreach ($languages as $language) {
            $installed[] = $language->subtitle;
        }

        $language = false;

        /** 1. if there is just one, take it */
        if (count($installed) == 1) {
            return $installed[0];
        }

        /** 2. user  */
        $language = Services::User()->get('language', '');
        if ($language === false) {
        } elseif (in_array($language, $installed)) {
            return $language;
        }

        /** 3. language of browser */
        if (isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {
            $browserLanguages = explode(',', $_SERVER['HTTP_ACCEPT_LANGUAGE']);
        } else {
            return null;
        }
        foreach ($browserLanguages as $language) {
            if (in_array(strtolower($language), $installed)) {
                return $language;
            }
        }

        /** 4. Application configuration */
        $language = Services::Language()->get('tag', 'en-GB');
        if (in_array($language, $installed)) {
            return $language;
        }

        /** 5. default */
        return 'en-GB';
    }

    /**
     * createLanguageList
     *
     * Builds a list of the languages installed for core or an extension
     *
     * @return  array
     * @since   1.0
     */
    public static function createLanguageList($path = null)
    {
        if (MOLAJO_APPLICATION_ID == 0) {
            $path = MOLAJO_EXTENSIONS_COMPONENTS . '/' . 'installer';

        } else {
            if ($path == null) {
                $path = MOLAJO_EXTENSIONS_LANGUAGES;
            }
        }

        /** for selected item determination */
        $currentLanguage = Services::Language()->get('tag');
        if ($currentLanguage === false || $currentLanguage == null) {
            $currentLanguage = 'en-GB';
        }

        /** retrieve language list */
        $languages = LanguageHelper::getLanguages($path);

        $list = array();
        foreach ($languages as $language)
        {
            $listItem = array();

            $listItem['value'] = $language->title;
            $listItem['key'] = $language->subtitle;

            $list[] = $listItem;
        }

        return $list;
    }

    /**
     * getLanguages
     *
     * Returns languages for core or a specific extension
     *
     * @param   string  $path
     *
     * @return  object
     * @since   1.0
     */
    public function getLanguages($path = MOLAJO_EXTENSIONS_LANGUAGES)
    {
        if ($path == MOLAJO_EXTENSIONS_LANGUAGES) {
            return LanguageHelper::getLanguagesCore();
        }

        $languages = array();

        $files = JFolder::files($path . '/language', '\.ini', false, false);
        if (count($files) == 0) {
            return false;
        }

        foreach ($files as $file) {
            $language = new stdClass();

            $language->value = substr($file, 0, strlen($file) - 4);
            $language->key = substr($file, 0, strlen($file) - 4);

            $languages[] = $language;
        }

        return $languages;
    }

    /**
     * getLanguagesCore
     *
     * During Service Initiation, the language service is started before
     * the Date Service. This routine is used at that time in lieu of
     * ability to query where date comparisons are needed.
     *
     * @return array
     * @since  1.0
     */
    public function getLanguagesCore()
    {
        $subfolders = JFolder::folders(MOLAJO_EXTENSIONS_LANGUAGES);
        $languages = array();

        foreach ($subfolders as $path) {
            $language = new stdClass();

            $language->title = $path;
            $language->subtitle = $path;

            $languages[] = $language;
        }
        return $languages;
    }

    /**
     * getMetadata
     *
     * Read Language Manifest XML file for metadata
     *
     * @param   string  $path
     *
     * @return  array  array
     * @since   1.0
     */
    public function getMetadata($file)
    {
        $xml = simplexml_load_file($file);
        if ($xml) {
        } else {
            return true;
        }

        $metadata = array();
        foreach ($xml->metadata->children() as $child) {
            $metadata[$child->getName()] = (string)$child;
        }

        return $metadata;
    }
}
