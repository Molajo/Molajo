<?php
/**
 * @package     Molajo
 * @subpackage  Form
 * @copyright   Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @copyright   Copyright (C) 2012 Cristina Solano. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die;

/**
 * Supports an HTML select list of menu
 *
 * @package    Molajo
 * @subpackage  Form
 * @since       1.0
 */
class MolajoFormFieldMenu extends MolajoFormFieldList
{
    /**
     * The form field type.
     *
     * @var    string
     * @since  1.0
     */
    public $type = 'Menu';

    /**
     * Method to get the field options.
     *
     * @return  array  The field option objects.
     * @since   1.0
     */
    protected function getOptions()
    {
        // Merge any additional options in the XML definition.
        $options = array_merge(parent::getOptions(), MolajoHTML::_('menu.menus'));

        return $options;
    }
}