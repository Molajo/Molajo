<?php
/**
 * @version     $id: mgrid.php
 * @package     Molajo
 * @subpackage  Override Grid
 * @copyright   Copyright (C) 2011 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die;

/*** NEED PARAMETER TO SHOW OR HIDE PUBLISHED OPTION FOR SPAM ***/

/**
 * Molajo Override Class for creating HTML Grids
 *
 * @package        Joomla.Framework
 * @subpackage    HTML
 * @since        1.6
 */
abstract class MolajoHtmlGrid
{
    /**
     * state
     *
     * Returns a state value on a grid
     *
     * @param    int        $value        The state value.
     * @param    int        $i        The row index
     * @param    string|array    $prefix        An optional task prefix or an array of options
     * @param    boolean        $enabled    An optional setting for access control on the action.
     * @param    string        $checkbox    An optional prefix for checkboxes.
     *
     * @return The Html code
     *
     * @see MolajoHTMLJGrid::state
     *
     * @since    1.6
     */

    public function state($value, $i, $canChange = true)
    {
        if (is_array($prefix)) {
            $options = $prefix;
            $enabled = array_key_exists('enabled', $options) ? $options['enabled'] : $enabled;
            $checkbox = array_key_exists('checkbox', $options) ? $options['checkbox'] : $checkbox;
            $prefix = array_key_exists('prefix', $options) ? $options['prefix'] : '';
        }

        $arraySelected = array();
        if ($value == MOLAJO_STATUS_ARCHIVED) {
            $arraySelected = array(MOLAJO_STATUS_ARCHIVED => array('archived.png', strtolower(JRequest::getCmd('DefaultView')) . '.trash', 'Archived', 'Toggle to change state to Trashed', false, 'archive', 'archive'));
        } else if ($value == MOLAJO_STATUS_PUBLISHED) {
            $arraySelected = array(MOLAJO_STATUS_PUBLISHED => array('tick.png', strtolower(JRequest::getCmd('DefaultView')) . '.archive', 'Published', 'Toggle to change state to Archived', false, 'publish', 'publish'));
        } else if ($value == MOLAJO_STATUS_UNPUBLISHED) {
            $arraySelected = array(MOLAJO_STATUS_UNPUBLISHED => array('disabled.png', strtolower(JRequest::getCmd('DefaultView')) . '.publish', 'Unpublished', 'Toggle to change state to Published', false, 'unpublish', 'unpublish'),);
        } else if ($value == MOLAJO_STATUS_TRASHED) {
            $arraySelected = array(MOLAJO_STATUS_TRASHED => array('spam.png', strtolower(JRequest::getCmd('DefaultView')) . '.unpublish', 'Spam', 'Toggle to change state to Unpublish', false, 'spam', 'spam'));
        } else if ($value == MOLAJO_STATUS_SPAMMED) {
            $arraySelected = array(MOLAJO_STATUS_SPAMMED => array('trash.png', strtolower(JRequest::getCmd('DefaultView')) . '.spam', 'Trash', 'Toggle to change state to Spammed', false, 'trash', 'trash'));
        } else if ($value == MOLAJO_STATUS_VERSION) {
            $arraySelected = array(MOLAJO_STATUS_VERSION => array('restore.png', strtolower(JRequest::getCmd('DefaultView')) . '.restore', 'Trash', 'Toggle to change state to Restored', false, 'restore', 'restore'));
        }

        $state = JArrayHelper::getValue($arraySelected, (int)$value, $states[1]);

        $html = MolajoHTML::_('image', 'admin/' . $state[0], MolajoText::_($state[2]), NULL, true);

        if ($canChange) {
            $html = '<a href="javascript:void(0);" onclick="return listItemTask(\'cb' . $i . '\',\'' . $state[1] . '\')" title="' . MolajoText::_($state[3]) . '">'
                    . $html . '</a>';
        }
        return $html;
    }

    /**
     * featured
     *
     * @param    int $value
     * @param    int $i
     * @param   int $canChange - edit.state for ACL
     *
     */
    public function featured($value, $i, $canChange = true)
    {

        $states = array(
            0 => array('disabled.png', JRequest::getCmd('DefaultView') . '.featured', 'MOLAJO_OPTION_UNFEATURED', 'MOLAJO_TOGGLE_TO_FEATURE'),
            1 => array('featured.png', JRequest::getCmd('DefaultView') . '.unfeatured', 'MOLAJO_OPTION_FEATURED', 'MOLAJO_TOGGLE_TO_UNFEATURE'),
        );

        $state = JArrayHelper::getValue($states, (int)$value, $states[1]);

        $html = MolajoHTML::_('image', 'admin/' . $state[0], MolajoText::_($state[2]), NULL, true);

        if ($canChange) {
            $html = '<a href="javascript:void(0);" onclick="return listItemTask(\'cb' . $i . '\',\'' . $state[1] . '\')" title="' . MolajoText::_($state[3]) . '">'
                    . $html . '</a>';
        }
        return $html;
    }

    /**
     * stickied
     *
     * @param    int $value
     * @param    int $i
     * @param   int $canChange - edit.state for ACL
     *
     */
    function stickied($value, $i, $canChange = true)
    {
        /** request object **/
        $option = JRequest::getCmd('option');
        $defaultView = JRequest::getCmd('DefaultView');

        /** grid **/
        $states = array(
            0 => array('disabled.png', $defaultView . '.stickied', 'MOLAJO_OPTION_UNSTICKIED', 'MOLAJO_TOGGLE_TO_STICKY'),
            1 => array('stickied.png', $defaultView . '.unstickied', 'MOLAJO_OPTION_STICKIED', 'MOLAJO_TOGGLE_TO_UNSTICKY'),
        );

        $state = JArrayHelper::getValue($states, (int)$value, $states[1]);

        $html = MolajoHTML::_('image', 'admin/' . $state[0], MolajoText::_($state[2]), NULL, true);

        if ($canChange) {
            $html = '<a href="javascript:void(0);" onclick="return listItemTask(\'cb' . $i . '\',\'' . $state[1] . '\')" title="' . MolajoText::_($state[3]) . '">'
                    . $html . '</a>';
        }

        return $html;
    }
}