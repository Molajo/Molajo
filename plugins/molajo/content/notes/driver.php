<?php
/**
 * @package     Molajo
 * @subpackage  Content Plugin
 * @copyright   Copyright (C) 2011 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die();

class MolajoContentNotes {

    /**
     * MolajoContentNotes::driver
     *
     * Removes hidden author and editor notes delinated by {note}Here is the note, and so on.{/note}
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
        preg_match_all( "#{note}(.*?){/note}#s", $content->$location, $matches );
        for ( $i=0; $i < count($matches); $i++ ) {
            $content->$location = trim(str_replace( $matches[0][$i], '', $content->$location )) ;
        }
    }
}