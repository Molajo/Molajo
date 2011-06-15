<?php
/**
 * @package     Molajo
 * @subpackage  Links Plugin
 * @copyright   Copyright (C) 2011 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die();

class MolajoLinksFootnotes {

    /**
     * id
     *
     * @var	int
     * @access	public
     */
    protected $id;

   /**
     * title
     *
     * @var	string
     * @access	public
     */
    protected $title;

    /**
     * worktext
     *
     * @var	string
     * @access	public
     */
    protected $worktext;

    /**
     * fulllink
     *
     * @var	string
     * @access	public
     */
    protected $fulllink;

    /**
     * link
     *
     * @var	string
     * @access	public
     */
    protected $link;

    /**
     * linktext
     *
     * @var	string
     * @access	public
     */
    protected $linktext;

    /**
     * MolajoLinksFootnotes::driver

     * Thanks to From http://www.brandspankingnew.net/specials/footnote_5.html#
     * CSS and JS CC licensed: http://creativecommons.org/licenses/by-sa/2.5/
     *
     * @param	string		The context for the content passed to the plugin.
     * @param	object		The content object.
     * @param	object		The content params
     * @param	string		The 'page' number
     * @param   string          Then name of the text field in the content object
     * @return	string
     * @since	1.6
     */
    function driver ($context, &$content, &$params, $page, $location)
    {
        /** initialization **/
        $this->id = $content->id;
        $this->title = $content->title;
        $this->worktext = $content->$location;

        /** search **/
        preg_match_all('#<\s*a.*?href\s*=\s*(?:"|\')(.*?)(?:"|\').*?>(.*?)<\s*/a\s*>#', $this->worktext, $matches );
        if (count($matches[1]) == 0) { return; }

        for ( $i=0; $i < count($matches); $i++ ) {

            /** model **/
            $this->fulllink = trim($matches[0][$i]);
            $this->link = trim($matches[1][$i]);
            $this->linktext = trim($matches[2][$i]);

            /** layout **/
            $layoutPath = MolajoPluginHelper::getLayoutPath(array('type' =>'molajo', 'name' =>'links'), $layout = 'footnote');
            $renderedLayout = MolajoPluginHelper::generateLayout ($layoutPath);

            /** replace **/
            $this->worktext = str_replace( $matches[0][$i], $renderedLayout, $this->worktext ) ;
        }

        /** layout **/
        $layoutPath = MolajoPluginHelper::getLayoutPath(array('type' =>'molajo', 'name' =>'links'), $layout = 'footnote_footer');
        $renderedLayout = MolajoPluginHelper::generateLayout ($layoutPath);

        /** replace article text **/
        $content->$location = $renderedLayout;

        return;
    }
}