<?php
/**
 * @package     Molajo
 * @subpackage  Form
 * @copyright   Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @copyright   Copyright (C) 2011 Cristina Solana, Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die;

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
	 * Method to get the field calendar markup.
	 *
	 * @return	string	The field calendar markup.
	 * @since	1.6
	 */
	protected function getInput()
	{
        /** pre-processing: create $this->rowset and populate shared elements  */
        parent::getInput();

        /** processing for field type */
        $prefix = false;

		$size		= $this->element->size ? abs((int) $this->element->size) : 5;
		if ($size > 10) {
			$size = 10;
		}

		$session = MolajoFactory::getSession()->get('setup.options', array());
		if (empty($session->db_prefix)) {
        } else {
            $prefix = $session->db_prefix;
        }

        if ($prefix) {
        } else {
            $prefix = MolajoFactory::getApplication()->getCfg('prefix');
        }

        if ($prefix) {
        } else {
            $prefix = $this->getPrefix ($size);
        }

        if ($prefix) {
            $this->rowset[0]['prefix'] = htmlspecialchars($prefix, ENT_COMPAT, 'UTF-8');
        } else {
            $this->rowset[0]['prefix'] = strtolower(MOLAJO).'_';
        }

        /** post-processing: outputs HTML into layout  **/
        parent::getInput();

        return;
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
        $db = MolajoFactory::getApplication()->getCfg('db');
        if ($db) {
            $tables = MolajoFactory::getDbo()->getTableList();
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

