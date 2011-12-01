<?php
/**
 * @package     Molajo
 * @subpackage  Media
 * @copyright   Copyright (C) 2012 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die;

class MolajoMediaAudio
{

    /**
     * id
     *
     * @var    string
     * @access    public
     */
    protected $id;

    /**
     * audio_file
     *
     * @var    string
     * @access    public
     */
    protected $audio_file;

    /**
     * audio_folder
     *
     * @var    string
     * @access    public
     */
    protected $audio_folder;

    /**
     * audio_folder
     *
     * @var    string
     * @access    public
     */
    protected $audio_file_loader;

    /**
     * $systemParameters
     *
     * @var    string
     * @access    public
     */
    protected $systemParameters;

    /**
     * MolajoMediaAudio::driver
     *
     * Implements Wordpress Audio Player Standalone Version
     *
     * From http://wpaudioplayer.com/standalone/
     *
     * @param    string        The context for the content passed to the plugin.
     * @param    object        The content object.
     * @param    object        The content parameters
     * @param    string        The 'page' number
     * @param   string          Then name of the text field in the content object
     * @return    string
     * @since    1.6
     */
    function driver($context, &$content, &$parameters, $page, $location)
    {
        /** search for pullquotes **/
        preg_match_all("#{audio}(.*?){/audio}#s", $content->$location, $matches);
        if (count($matches[1]) == 0) {
            return;
        }

        /** initialization **/
        $workText = $content->$location;

        $molajoSystemPlugin =& MolajoPlugin::getPlugin('system', 'molajo');
        $this->systemParameters = new JParameter($molajoSystemPlugin->parameters);
        $this->audio_folder = 'images/' . $this->systemParameters->def('audio_folder', 'audio');

        /** layout: loads JS for head **/
        $layoutPath = MolajoPlugin::getLayoutPath(array('type' => 'molajo', 'name' => 'media'), $layout = 'audio_head');
        MolajoPlugin::generateLayout($layoutPath);

        for ($i = 0; $i < count($matches[0]); $i++) {

            /** model **/
            $this->audio_file = substr($matches[0][$i], 7, strlen($matches[0][$i]) - 15);
            $this->id = $i;

            /** layout **/
            $layoutPath = MolajoPlugin::getLayoutPath(array('type' => 'molajo', 'name' => 'media'), $layout = 'audio');
            $renderedLayout = MolajoPlugin::generateLayout($layoutPath);

            /** replace **/
            $workText = str_replace($matches[0][$i], $renderedLayout, $workText);
        }

        /** layout: loads JS for footer **/
        $layoutPath = MolajoPlugin::getLayoutPath(array('type' => 'molajo', 'name' => 'media'), $layout = 'audio');
        MolajoPlugin::generateLayout($layoutPath);

        /** update source **/
        $content->$location = $workText;

        return;
    }
}