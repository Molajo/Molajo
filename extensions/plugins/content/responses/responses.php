<?php
/**
 * @package     Molajo
 * @subpackage  Molajo Responses 
 * @copyright   Copyright (C) 2011 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die;


class plgMolajoResponses extends MolajoPlugin	{

    /**
     * @var string	Stores name of data element containing text for content object
     * @since	1.6
     */
    protected $location;

    /**
     * @var binary	Used to remember {closed} content items identified in Content Prepare
     * @since	1.6
     */
    protected $closed;

    /**
     * plgMolajoContent::MolajoOnContentPrepare
     *
     * Content Component Plugin that applies text and URL functions to content object
     *
     * @param	string		The context for the content passed to the plugin.
     * @param	object		The content object.
     * @param	object		The content parameters
     * @param	stromg		The 'page' number
     * @return	string
     * @since	1.6
     */
    function MolajoOnContentPrepare ($context, &$content, &$parameters, $page = 0)
    {
        return;
        /** init **/
        if (!plgMolajoResponses::initialization ($context, $content)) {
            return;
        }

        /** closed in content **/
        $temp = $this->location;
        $content->$temp = str_replace('{closed}', '', $content->$temp, $results);
        if ($results > 0) {
            $this->closed = true;
        }
    }

    /**
     * plgMolajoResponses::MolajoOnContentAfterDisplay
     *
     * Responses Component Plugin that injects response elements following the component output
     *
     * 1: Comments
     * 2: Ratings
     * 3: Bookmarks
     * 4: Subscriptions
     * 5: Logs
     *
     * Future:
     * Polls
     * Trackback
     *
     * @param	string		The context for the content passed to the plugin.
     * @param	object		The content object.
     * @param	object		The content parameters
     * @param	int		The 'page' number
     * @return	string
     * @since	1.6
     */
    function MolajoOnContentAfterDisplay ($context, &$content, &$parameters, $page = 0)
    {
        /** init **/
        if (!plgMolajoResponses::initialization ($context, $content)) {
            return;
        }

        /** com_responses parameters **/
        $responsesParameters = MolajoComponentHelper::getParameters('com_responses', true);

        /** response type 1: comments **/
        if (($responsesParameters->def('enable_comments', 0) == '1') &&
            (in_array($content->catid, $responsesParameters->def('enable_comments_categories', array())))) {
            require_once dirname(__FILE__).'/comments/driver.php';
            $commentResults = MolajoResponsesComments::driver ($context, &$content, &$parameters, $page = 0, $this->location, $this->closed);
        } else {
            $commentResults = false;
        }

        /** response type 2: ratings **/
        if (($responsesParameters->def('enable_ratings', 0) == '1') &&
            (in_array($content->catid, $responsesParameters->def('enable_ratings_categories', array())))) {
            require_once dirname(__FILE__).'/ratings/driver.php';
            $ratingResults = false;
            //$ratingResults = MolajoResponsesRatings::driver ($context, &$content, &$parameters, $page = 0);
        } else {
            $ratingResults = false;
        }

        /** response type 3: bookmarks **/
        if (($responsesParameters->def('enable_bookmarks', 0) == '1') &&
            (in_array($content->catid, $responsesParameters->def('enable_bookmarks_categories', array())))) {
            require_once dirname(__FILE__).'/bookmarks/driver.php';
            $bookmarkResults = false;
            //$ratingResults = MolajoResponsesRatings::driver ($context, &$content, &$parameters, $page = 0);
        } else {
            $bookmarkResults = false;
        }

        /** response type 4: broadcasting (subscriptions, feeds) **/
        if (($responsesParameters->def('enable_broadcast', 0) == '1') &&
            (in_array($content->catid, $responsesParameters->def('enable_broadcast_categories', array())))) {

            if ($responsesParameters->def('enable_feeds', 0) == '1') {
                require_once dirname(__FILE__).'/subscriptions/driver.php';
                $rssResults = false;
                //$ratingResults = MolajoResponsesFeeds::driver ($context, &$content, &$parameters, $page = 0);
            } else {
                $subscriptionResults = false;
                $rssResults = false;
            }

            if ($responsesParameters->def('enable_subscriptions', 0) == '1') {
                require_once dirname(__FILE__).'/subscriptions/driver.php';
                $subscriptionResults = false;
                //$ratingResults = MolajoResponsesSubscriptions::driver ($context, &$content, &$parameters, $page = 0);
            } else {
                $subscriptionResults = false;
            }
        }

        /** response type 5: log **/
        if ($responsesParameters->def('enable_logs', 0) == '1') {

            if (in_array($content->catid, $responsesParameters->def('enable_bookmarks_categories', array()))) {
                 echo 'Category enabled for bookmarks: '.$content->catid.'<br />';
            }

        }

        /** append layout results **/
        $renderedLayouts = '';

        /** append results **/
        if ($commentResults) {
            $renderedLayouts .= $commentResults;
        } else {
            $commentResults = false;
        }

        /** dumb, but what can you do? **/
        $temp = $this->location;
        $content->$temp .= $renderedLayouts;
        return;
    }

    /**
     * initialization
     * @param string $context
     * @param object $content
     * @return binary
     */
    function initialization ($context, $content) {

        /** no responses for responses **/
        if ($context == 'com_responses.response') {
            return false;
        }

        /** com_responses enabled? **/
        if (MolajoComponentHelper::isEnabled('com_responses')) {
        } else {
            return false;
        }

        /** content has necessary attributes? **/
        if (!isset($content->catid)) {
            return false;
        }
        if (!isset($content->id)) {
            return false;
        }

        /** request **/
        $option = JRequest::getVar('option');
        if ($option == 'com_responses') {
            return false;
        }
        $view = JRequest::getVar('view');
        if ($view == 'form') {
            return false;
        }

        /** request values **/
        $option = JRequest::getVar('option');
        $view = JRequest::getVar('view');
        if (($view == 'archive') || ($view == 'featured') || ($view == 'category') || ($view == 'categories')) {
            $multiple = true;
        } else {
            $multiple = false;
        }

        /** append location **/
        if ($multiple == true) {
            if (isset($content->introtext)) {
                $this->location = 'introtext';
            } elseif (isset($content->text)) {
                $this->location = 'text';
            } else {
                return false;
            }
        } else {
            if (isset($content->text)) {
                $this->location = 'text';
            } else {
                return false;
            }
        }
        return true;
    }
}