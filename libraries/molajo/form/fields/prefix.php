<?php
/**
 * @package     Molajo
 * @subpackage  Form
 * @copyright   Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @copyright   Copyright (C) 2011 Cristina Solana, Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die();

/**
 * Database Prefix for Installer
 *
 * @package		Molajo
 * @subpackage	Form
 * @since		1.6
 */
class MolajoFormFieldPrefix extends MolajoFormField
{
	/**
	 * The form field type.
	 *
	 * @var		string
	 * @since	1.6
	 */
	protected $type = 'Prefix';

	/**
	 * Method to get the field input markup.
	 *
	 * @return	string	The field input markup.
	 * @since	1.6
	 */
	protected function getInput()
	{

        $this->rowset = array();

        if (isset($this->id)) {
            $this->rowset[0]['id'] = $this->id;
        } else {
            $this->rowset[0]['id'] = '';
        }
        if (isset($this->name)) {
            $this->rowset[0]['name'] = $this->name;
        } else {
            $this->rowset[0]['name'] = '';
        }
        if (isset($this->description)) {
            $this->rowset[0]['description'] = $this->description;
        } else {
            $this->rowset[0]['description'] = '';
        }

        if (isset($this->fieldname)) {
            $this->rowset[0]['fieldname'] = $this->fieldname;
        } else {
            $this->rowset[0]['fieldname'] = '';
        }
        if (isset($this->group)) {
            $this->rowset[0]['group'] = $this->group;
        } else {
            $this->rowset[0]['group'] = '';
        }
        if (isset($this->input)) {
            $this->rowset[0]['input'] = $this->input;
        } else {
            $this->rowset[0]['input'] = '';
        }
        if (isset($this->label)) {
            $this->rowset[0]['label'] = $this->label;
        } else {
            $this->rowset[0]['label'] = '';
        }
        if (isset($this->multiple)) {
            $this->rowset[0]['multiple'] = $this->multiple;
        } else {
            $this->rowset[0]['multiple'] = '';
        }
        if (isset($this->required)) {
            $this->rowset[0]['required'] = $this->required;
        } else {
            $this->rowset[0]['required'] = '';
        }
        if (isset($this->title)) {
            $this->rowset[0]['title'] = $this->title;
        } else {
            $this->rowset[0]['title'] = '';
        }
        if (isset($this->translateLabel)) {
            $this->rowset[0]['translateLabel'] = $this->translateLabel;
        } else {
            $this->rowset[0]['translateLabel'] = '';
        }
        if (isset($this->translate_description)) {
            $this->rowset[0]['translate_description'] = $this->translate_description;
        } else {
            $this->rowset[0]['translate_description'] = '';
        }
        if (isset($this->type)) {
            $this->rowset[0]['type'] = $this->type;
        } else {
            $this->rowset[0]['type'] = '';
        }

        if ($this->element->class) {
            $this->rowset[0]['class'] = (string) $this->element->class;
        } else {
            $this->rowset[0]['class'] = '';
        }

        if ($this->element->readonly == 'true' || $this->element->readonly === true) {
            $this->rowset[0]['readonly'] = 'readonly="readonly"';
        } else {
            $this->rowset[0]['readonly'] = '';
        }

        $maxLength	= (int) $this->element->maxlength;

        if ($this->element->enabled == 'false' || $this->element->enabled === false) {
            $this->rowset[0]['disabled'] = 'disabled="disabled"';
        } else {
            $this->rowset[0]['disabled'] = '';
        }

        if ($this->element->onchange) {
            $this->rowset[0]['onchange'] = ' onchange="'.(string) $this->element->onchange.'"';
        } else {
            $this->rowset[0]['onchange'] = '';
        }

    /**
     * Prefix
     */
        $prefix = false;

		$size		= $this->element->size ? abs((int) $this->element->size) : 5;
		if ($size > 10) {
			$size = 10;
		}

		$session = JFactory::getSession()->get('setup.options', array());
		if (empty($session->db_prefix)) {
        } else {
            $prefix = $session->db_prefix;
        }

        if ($prefix) {
        } else {
            $prefix = JFactory::getApplication()->getCfg('prefix');
        }

        if ($prefix) {
        } else {
            $prefix = $this->getPrefix ($size);
        }

        if ($prefix) {
        } else {
            $prefix = 'molajo';
        }

        $this->rowset[0]->prefix = htmlspecialchars($prefix, ENT_COMPAT, 'UTF-8');
        
        if ($prefix) {
            $this->rowset[0]['prefix'] = htmlspecialchars($prefix, ENT_COMPAT, 'UTF-8');
        } else {
            $this->rowset[0]['prefix'] = 'molajo_';
        }

        var_dump($this->rowset);
	}

    /**
     * get_prefix
     *
     * @param $size
     * @param $count
     * @return void
     */
    protected function getPrefix ($size=10, $count=100)
    {
        // For an existing table, retrieve all table names
        $db = JFactory::getApplication()->getCfg('db');
        if ($db) {
            $tables = JFactory::getDbo()->getTableList();
        } else {
            $tables = array();
        }

        // Loop until an non used prefix is found or until $count is reached
        $found = false;
        $k = 0;
        for ($k=0; ($k < $count || $found === true); $k++)
        {
            // Create the random prefix:
            $prefix = '';
            $chars = range('a', 'z');
            $numbers = range(0, 9);

            // first character is random letter
            shuffle($chars);
            $prefix .= $chars[0];

            // combine numbers and characters into pool and retrieve random set
            $symbols = array_merge($numbers, $chars);
            shuffle($symbols);

            for($i = 0, $j = $size - 1; $i < $j; ++$i) {
                $prefix .= $symbols[$i];
            }

            // Add in the underscore:
            $prefix .= '_';

            // Search for conflict
            if ($tables) {
                foreach ($tables as $table) {
                    if (strpos($table, $prefix) === 0) {
                        $found = true;
                        break;
                    }
                }
            }
        }

        return $prefix;
    }
}

