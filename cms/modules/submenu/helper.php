<?php
/**
 * @package     Molajo
 * @subpackage  Module
 * @copyright   Copyright (C) 2012 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die;

/**
 * @package        Molajo
 * @subpackage    header
 * @since        1.0
 */
abstract class MolajoSubmenuHelper
{
    /**
     * $data
     *
     * @since    1.0
     */
    protected static $data = array();

    /**
     * Helper method to generate data
     *
     * @param    array    A named array with keys link, image, text, access and imagePath
     *
     * @return    string    HTML for button
     * @since    1.0
     */
    public static function getList($parameters)
    {
        $tmpobj = new JObject();
        $tmpobj->set('site_title', MolajoFactory::getApplication()->getConfig('site_title', 'Molajo'));
        $data[] = $tmpobj;
        return $data;
    }

    /**
     * add
     *
     * @since    1.0
     */
    public static function add()
    {
        /** component parameters **/
        $parameters = MolajoComponent::getParameters(JRequest::getCmd('option'));

        /** Toolbar title and buttons **/
        for ($i = 1; $i < 1000; $i++) {
            $value = $parameters->get('config_manager_submenu' . $i);
            if ($value == null) {
                break;
            }
        }
        $max = $i;

        /** toolbar title and buttons not desired **/
        if ($max == 1) {
            return;
        }

        /** loop thru config options **/
        for ($i = 1; $i < $max; $i++) {

            $SubmenuValue = $parameters->def('config_manager_submenu' . $i, 0);

            if (!$SubmenuValue == '0') {
                $functionName = 'add' . ucfirst($SubmenuValue) . 'Submenu';
                if (method_exists('MolajoSubmenuHelper', $functionName)) {
                    $Submenu = new MolajoSubmenuHelper ();
                    $Submenu->$functionName (JRequest::getCmd('option'), JRequest::getCmd('DefaultView'));
                }
            }
        }

        return;
    }

    /**
     * addDefaultSubmenu
     *
     * @param    array $permissions
     * @since    1.0
     */
    public function addDefaultSubmenu()
    {
        MolajoSubmenuHelper::addEntry(
            MolajoTextHelper::_('MOLAJO_SUBMENU_' . strtoupper(JRequest::getCmd('DefaultView'))),
            'index.php?option=' . JRequest::getCmd('option') . '&view=' . JRequest::getCmd('DefaultView'),
            JRequest::getCmd('DefaultView')
        );
    }

    /**
     * addFeaturedSubmenu
     *
     * @param    array $permissions
     * @since    1.0
     */
    public function addFeaturedSubmenu()
    {
        MolajoSubmenuHelper::addEntry(
            MolajoTextHelper::_('MOLAJO_SUBMENU_FEATURED'),
            'index.php?option=' . JRequest::getCmd('option') . '&view=' . JRequest::getCmd('DefaultView') . '&feature=1',
            JRequest::getCmd('DefaultView')
        );
    }

    /**
     * addStickiedSubmenu
     *
     * @param    array $permissions
     * @since    1.0
     */
    public function addStickiedSubmenu()
    {
        MolajoSubmenuHelper::addEntry(
            MolajoTextHelper::_('MOLAJO_SUBMENU_STICKIED'),
            'index.php?option=' . JRequest::getCmd('option') . '&view=' . JRequest::getCmd('DefaultView') . '&sticky=1',
            JRequest::getCmd('DefaultView')
        );
    }

    /**
     * addUnpublishedSubmenu
     *
     * @param    array $permissions
     * @since    1.0
     */
    public function addUnpublishedSubmenu()
    {
        MolajoSubmenuHelper::addEntry(
            MolajoTextHelper::_('MOLAJO_SUBMENU_UNPUBLISHED'),
            'index.php?option=' . JRequest::getCmd('option') . '&view=' . JRequest::getCmd('DefaultView') . '&publish=0',
            JRequest::getCmd('DefaultView')
        );
    }
}