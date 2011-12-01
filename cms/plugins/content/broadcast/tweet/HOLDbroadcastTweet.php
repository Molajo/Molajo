<?php
/**
 * @package     Molajo
 * @subpackage  Molajo Broadcast Tweet Plugin
 * @copyright   Copyright (C) 2012 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die;

jimport('joomla.plugin.plugin');

class plgContenttamka_post_twitter extends MolajoPlugin
{

    function OnBeforeContentSave(&$article, $isNew)
    {

        /**
         * Make certain Tamka Library is ready to load
         */
        if (!file_exists(JPATH_PLUGINS . DS . 'system' . DS . 'tamka.php')) {
            JError::raiseWarning('700', MolajoTextHelper::_('The Tamka Library is required for this extension.'));
            return NULL;
        }
        if (!function_exists('tamkaimport')) {
            JError::raiseWarning('725', MolajoTextHelper::_('The Tamka Library must be enabled for this extension.'));
            return NULL;
        }
        if (!version_compare('0.1', 'TAMKA')) {
            JError::raiseWarning('750', MolajoTextHelper::_('The Tamka Library Version is outdated.'));
            return NULL;
        }
        tamkaimport('tamka.routehelper.content');

        /**
         *     Determine if Article was Published prior to save
         */
        $currentlyPublished = TamkaContentHelperRoute::checkArticleforPublished($article->id);
        JRequest::setVar('onBeforePublished', $currentlyPublished);

    }

    /**
     * After a Molajo Article has been saved - Twitter the Title with a Tiny URL
     */
    function onAfterContentSave(&$article, $isNew)
    {
        /**
         * Make certain curl is loaded
         */
        $loadedExtensionsArray = Array();
        $loadedExtensionsArray = get_loaded_extensions();
        $isLoaded = extension_loaded('curl');
        if (!$isLoaded) {
            JError::raiseWarning('800', MolajoTextHelper::_('The PHP curl extension must be loaded before the Tamka Twitter Post Plugin can function. Ask your system administrator to activate curl in PHP.'));
            return NULL;
        }

        /**
         *     Article must be Published as of this moment with public access
         */
        $results = TamkaContentHelperRoute::checkArticleforBroadcast($article->id);
        if ($results == false) {
            return;
        }

        /**
         *     Initialization
         */
        $plugin =& MolajoPlugin::getPlugin('content', 'tamka_post_ping');
        $pluginParameters = new JParameter($plugin->parameters);

        /**
         *     Should Tamka Ping?
         */
        /* 	What Categories should be included or excluded?		*/
        $showCategoriesAll = false;
        $showCategories = explode(',', $pluginParameters->get('categories'));
        if ($pluginParameters->get('categories')) {
        } else {
            $showCategoriesAll = true;
        }
        $includeorexclude = $pluginParameters->def('include_or_exclude', 'Include');

        // 	Is this the right Category?
        $show = false;
        if ($article->sectionid == 0 && $article->catid == 0) {
            $show = false;
            return;
        }
        if ($includeorexclude == 'Include' && (in_array($article->catid, $showCategories) || $showCategoriesAll)) {
            $show = true;
        }
        if ($includeorexclude == 'Exclude' && (in_array($article->catid, $showCategories) == false) && ($showCategoriesAll == false)) {
            $show = true;
        }
        if ($show == false) {
            return;
        }

        /**
         *     Determine if Article is moving from Unpublished to Published state
         */
        $currentlyPublished = TamkaContentHelperRoute::checkArticleforPublished($article->id);

        //	If published state was 0 in before update - and is now 1 - it's a new publish
        $onBeforePublished = JRequest::getVar('onBeforePublished');
        if ($onBeforePublished == 1) {
            return;
        }

        /**
         *     Prepare content for Tweet - Site name, Article title, URL
         */
        global $mainframe;
        $SiteName = $mainframe->getConfig('sitename');
        $articleURL = TamkaContentHelperRoute::getSiteURL() . TamkaContentHelperRoute::getArticleURL($article->id);
        $ArticleTitle = $article->title;

        /**
         *    Retrieve Tiny URL
         */
        $tinyURL = TamkaContentHelperRoute::urlShortener($articleURL, $article->id);

        /**
         * Set Twitter Status to Post Title and Tiny URL (less than 140 characters recommended)
         */
        $twitterStatus = substr(trim($ArticleTitle), 0, (140 - strlen($tinyURL))) . ' ' . $tinyURL;

        /**
         *     Login to Twitter Account and set the new Twitter Status
         */

        require "Twitter.class.php";

        // Instantiate a Twitter object with a given username and pasword
        // If a third argument is set to true, Twitter will be in debug mode which will
        // output incoming and outgoing data.
        $tweet = new Twitter("username", "password");
        // Set a new status. This can be called multiple times on the same object.
        // The update() function returns true if the status update was successful or
        // false if an error occured. In case of an error, a string describing the error
        // is stored in the error variable of the object (in our case $tweet->error)
        $success = $tweet->update("PHP rocks my socks!");
        if ($success) echo "Tweet successful!";
        else echo $tweet->error;
        $results = TamkaActivation::twitterProcess($twitterStatus);
        return;
    }
}

?>