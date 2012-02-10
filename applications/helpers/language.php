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
    public function get()
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
        $language = Molajo::Services()->connect('User')->get('language', '');
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
        $language = Molajo::Application()->get('language', 'en-GB');
        if (in_array($language, $installed)) {
            return $language;
        }

        /** 5. default */
        return 'en-GB';
    }

    /**
     * createLanguageList
     *
     * Builds a list of the system languages
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
        $currentLanguage = Molajo::Application()->get('language');
        if ($currentLanguage === false || $currentLanguage == null) {
            $currentLanguage = 'en-GB';
        }

        /** retrieve language list */
        $languages = LanguageHelper::getLanguages($path);

        $list = array();
        foreach ($languages as $language)
        {
            $listItem = array();

            $listItem['text'] = $language->title;
            $listItem['value'] = $language->subtitle;
            if ($language->subtitle == $currentLanguage) {
                $listItem['selected'] = 'selected="selected"';
            }

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
            if (Molajo::Application()->get('Date', false, 'services') === false) {
                return LanguageHelper::getLanguagesCore();
            } else {
                return ExtensionHelper::get(
                    MOLAJO_ASSET_TYPE_EXTENSION_LANGUAGE
                );
            }
        }

        $languages = array();

        $files = JFolder::files($path . '/language', '\.ini', false, false);
        if (count($files) == 0) {
            return false;
        }

        foreach ($files as $file) {
            $language = new stdClass();

            $language->title = substr($file, 0, strlen($file) - 4);
            $language->subtitle = substr($file, 0, strlen($file) - 4);

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
        foreach ($subfolders as $path) {
            $language = new stdClass();

            $language->title = $path;
            $language->subtitle = $path;

            $languages[] = $language;
        }

        return $languages;
    }

    /**
     * getPath
     *
     * Get the path to a specific language
     *
     * @param   string  $path
     * @param   string  $language
     *
     * @return  string  Path
     *
     * @since   1.0
     */
    public function getPath($path = MOLAJO_EXTENSIONS_LANGUAGES,
                            $language = null)
    {
        if ($path == MOLAJO_EXTENSIONS_LANGUAGES) {
            $dir = $path . '/' . $language;
        } else {
            $dir = $path . '/language';
        }
        return $dir;
    }
}
