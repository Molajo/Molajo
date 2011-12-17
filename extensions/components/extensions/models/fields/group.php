<?php
/**
 * @version        $Id: group.php 20196 2011-01-09 02:40:25Z ian $
 * @package        Joomla.Administrator
 * @subpackage    installer
 * @copyright    Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license        GNU General Public License, see LICENSE.php
 */

// No direct access.
defined('_JEXEC') or die;

jimport('joomla.form.formfield');

/**
 * Form Field Place class.
 *
 * @package        Joomla.Administrator
 * @subpackage    installer
 * * * @since        1.0
 */
class JFormFieldGroup extends JFormField
{
    /**
     * The field type.
     *
     * @var        string
     */
    protected $type = 'Group';

    /**
     * Method to get the field input.
     *
     * @return    string        The field input.
     * @since    1.0
     */
    protected function getInput()
    {
        $onchange = $this->element['onchange'] ? ' onchange="' . (string)$this->element['onchange'] . '"' : '';
        $options = array();

        foreach ($this->element->children() as $option) {
            $options[] = MolajoHTML::_('select.option', (string)$option->attributes()->value, MolajoTextHelper::_(trim($option->data())));
        }

        $dbo = MolajoFactory::getDbo();
        $query = $dbo->getQuery(true);
        $query->select('DISTINCT `folder`');
        $query->from('#__extensions');
        $query->where('`folder` != ' . $dbo->quote(''));
        $query->order('`folder`');
        $dbo->setQuery((string)$query);
        $folders = $dbo->loadResultArray();

        foreach ($folders as $folder) {
            $options[] = MolajoHTML::_('select.option', $folder, $folder);
        }

        $return = MolajoHTML::_('select.genericlist', $options, $this->name, $onchange, 'value', 'text', $this->value, $this->id);

        return $return;
    }
}