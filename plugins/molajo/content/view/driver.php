<?php
/**
 * @package     Molajo
 * @subpackage  Content Plugin
 * @copyright   Copyright (C) 2011 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die;

class MolajoContentView {

    /**
     * MolajoContentView::driver
     *
     * Conditionally displays content based on view level access
     *
     * Groups 1,2 and 4 are able to see
     *
     * {view:1,2,4}Here is the note, and so on.{/view}
     *
     * @param	string		The context for the content passed to the plugin.
     * @param	object		The content object.
     * @param	object		The content params
     * @param	string		The 'page' number
     * @param   string          Then name of the text field in the content object
     * @return	string
     * @since	1.6
     */
    function driver ($content)
    {
        $firsttime = true;

        /** $matches[0][i] = "{view:1}You must be in group 1 to see this top secret information.{/view}"**/
        /** $matches[1][i] = "1" **/
        /** $matches[2][i] = You must be in group 1 to see this top secret information.{/view} **/
        preg_match_all( '~{view:([^}]+)}(.*?)}~is', $content, $matches);

        for ( $i=0; $i < count($matches[0]); $i++ ) {

            if ($firsttime) {
                $acl = new MolajoACL();
                $userAccess = array_unique($acl->getList('Viewaccess'));
                $firsttime = false;
            }

            $contentAccess = explode(',', $matches[1][$i]);

            for ( $a=0; $a < count($contentAccess); $a++ ) {

                if (in_array($contentAccess [$a], $userAccess)) {
                    $display = substr($matches[2][$i], 0, strlen(trim($matches[2][$i])) - 6);
                    break;
                }
            }

            $content = trim(str_replace($matches[0][$i], trim($display), $content));
        }

        return $content;
    }
}