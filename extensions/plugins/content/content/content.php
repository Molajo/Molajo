<?php
/**
 * @package     Molajo
 * @subpackage  Molajo Content
 * @copyright   Copyright (C) 2011 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die;

class plgMolajoContent extends MolajoPlugin	{

    /**
     * @var string	Stores name of data element containing text for content object
     * @since	1.6
     */
    protected $location;
    
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

        /** init **/
//        if (!plgMolajoContent::initialization ($context, $content)) {
//            return;
//        }
        $this->location = 'introtext';
        /** parameters **/
        $molajoSystemPlugin =& MolajoPluginHelper::getPlugin('system', 'molajo');
        $systemParams = new JParameter($molajoSystemPlugin->parameters);
        $loc = $this->location;

        /** view access **/
        if ($systemParams->def('enable_view_access', 0) == 1) {
            require_once dirname(__FILE__) . '/view/driver.php';
            $results = MolajoContentView::driver ($content->introtext);
            $content->introtext = $results;
        }

        /** hide author notes **/
        if ($systemParams->def('enable_hidden_notes', 0) == 1) {
            require_once dirname(__FILE__) . '/notes/driver.php';
            MolajoContentNotes::driver ($context, &$content, &$parameters, $page = 0, $this->location);
        }
        /** add line breaks **/
        if ($systemParams->def('enable_add_line_breaks', 0) == 1) {
            $content->$loc = MolajoTextHelper::addLineBreaks ($content->$loc);
        }
        /** pullquotes and blockquotes **/
        if (($systemParams->def('enable_blockquotes', 0) == 1) || ($systemParams->def('enable_pullquotes', 0) == 1)) {
            require_once dirname(__FILE__) . '/quotes/driver.php';
            MolajoContentQuotes::driver ($context, &$content, &$parameters, $page = 0, $this->location);
        }
        /** syntax highlighter **/
        if ($systemParams->def('enable_syntax_highlighter', 0) == 1) {
            require_once dirname(__FILE__) . '/syntaxhighlighter/driver.php';
            MolajoContentSyntaxHighlighter::driver ($context, &$content, &$parameters, $page = 0, $this->location);
        }
        /** smilies functions **/
        if ($systemParams->def('enable_smilies', 0) == 1) {
            $content->$loc = MolajoTextHelper::smilies ($content->$loc);
        }
        return;
    }

    /**
     * MolajoOnAfterRender
     *
     * System Component Plugin that adds the Google Analytics Tracking to a Web page
     *
     * @param	none
     * @return	none
     * @since	1.6
     */
    function MolajoOnAfterRender()
    {
        /** admin check **/
        $app =& MolajoFactory::getApplication();
        if ($app->getName() == 'administrator') { return; }

        /** retrieve parameters for system plugin molajo library **/
        $molajoSystemPlugin =& MolajoPluginHelper::getPlugin('system', 'molajo');
        $systemParams = new JParameter($molajoSystemPlugin->parameters);

        /** talk like a pirate day **/
        if (($systemParams->def('enable_pirate_day', 0) == 1) && (date("m.d") == '09/19')) {
            require_once dirname(__FILE__) . '/pirate/driver.php';
            MolajoContentPirate::driver ();
        }
    }

    /**
     * initialization
     * @param string $context
     * @param object $content
     * @return binary
     */
    function initialization ($context, $content) {


        /** text location **/
        $this->location = '';
        if ($multiple == true) {
            if (isset($content->introtext)) {
                $this->location = 'introtext';
            } elseif (isset($content->text)) {
                $this->location = 'text';
            }
        } else {
            if (isset($content->fulltext)) {
                $this->location = 'fulltext';
            } else if (isset($content->text)) {
                $this->location = 'text';
            }
        }

        if ($this->location == '') {
            return false;
        }
        return true;
    }
}