<?php
/**
 * @package     Molajo
 * @subpackage  Display Model
 * @copyright   Copyright (C) 2011 Babs Gösgens. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die;

/**
 * Display Model
 *
 * InstallerModelDisplay extends MolajoModelDisplay extends JModel extends JObject
 *
 * @package	    Molajo
 * @subpackage	Model
 * @since       1.0
 */
class InstallerModelDisplay extends MolajoModelDummy
{

    /**
     * display
     *
     * @return void
     */
    public function SystemChecks()
    {
        $system_checks = array();
        return $system_checks;
    }

    /**
     * getLanguageList
     *
     * Retrieves the list of installable languages
     *
     * @return void
     */
    public function getLanguageList()
    {
        //MolajoLanguageHelper::createLanguageList
        $language_list = array();
        return $language_list;
    }

    /**
     * getUserLanguage
     *
     * Retrieves the User's autodetected Language from their Browser
     *
     * @return void
     */
    public function getUserLanguage()
    {
        $user_language = 'en-GB';
        return $user_language;
    }

    /**
     * getFormFields
     *
     * @return void
     */
    public function getFormFields()
    {

        //retrieve ALL of the form fields off of the post
        //doesn't matter which page you are on -- get them all with the defaults
        //some will be hidden
        $form_fields = array();
        return $form_fields;
    }


    /**
     * getFormEdits
     *
     * @return void
     */
    public function getFormEdits()
    {

        //retrieve ALL of the form fields off of the post
        //doesn't matter which page you are on -- get them all with the defaults
        //some will be hidden
        $form_edits = array();
        return $form_edits;
    }


}