<?php
/**
 * @package     Molajo
 * @subpackage  Media
 * @copyright   Copyright (C) 2011 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die;

class MolajoMediaAudio {

    /**
     * id
     *
     * @var	string
     * @access	public
     */
    protected $id;
    
    /**
     * audio_file
     *
     * @var	string
     * @access	public
     */
    protected $audio_file;

    /**
     * audio_folder
     *
     * @var	string
     * @access	public
     */
    protected $audio_folder;

    /**
     * audio_folder
     *
     * @var	string
     * @access	public
     */
    protected $audio_file_loader;

    /**
     * $systemParams
     *
     * @var	string
     * @access	public
     */
    protected $systemParams;

    /**
     * MolajoMediaAudio::driver
     *
     * Implements Wordpress Audio Player Standalone Version
     *
     * From http://wpaudioplayer.com/standalone/
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
        /** search for pullquotes **/
        preg_match_all( "#{audio}(.*?){/audio}#s", $content->$location, $matches );
        if (count($matches[1])== 0) { return; }

        /** initialization **/
        $workText = $content->$location;

        $molajoSystemPlugin =& MolajoPluginHelper::getPlugin('system', 'molajo');
        $this->systemParams = new JParameter($molajoSystemPlugin->params);
        $this->audio_folder = 'images/'.$this->systemParams->def('audio_folder', 'audio');

        /** layout: loads JS for head **/
        $layoutPath = MolajoPluginHelper::getLayoutPath(array('type' =>'molajo', 'name' =>'media'), $layout = 'audio_head');
        MolajoPluginHelper::generateLayout ($layoutPath);

        for ( $i=0; $i < count($matches[0]); $i++ ) {

            /** model **/
            $this->audio_file = substr($matches[0][$i], 7, strlen($matches[0][$i]) - 15);
            $this->id = $i;

            /** layout **/
            $layoutPath = MolajoPluginHelper::getLayoutPath(array('type' =>'molajo', 'name' =>'media'), $layout = 'audio');
            $renderedLayout = MolajoPluginHelper::generateLayout ($layoutPath);

            /** replace **/
            $workText = str_replace( $matches[0][$i], $renderedLayout, $workText ) ;
        }

        /** layout: loads JS for footer **/
        $layoutPath = MolajoPluginHelper::getLayoutPath(array('type' =>'molajo', 'name' =>'media'), $layout = 'audio');
        MolajoPluginHelper::generateLayout ($layoutPath);

        /** update source **/
        $content->$location = $workText;

        return;
    }
}