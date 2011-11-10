<?php
/**
 * @package     Molajo
 * @subpackage  Extend
 * @copyright   Copyright (C) 2010-2011 Amy Stephen. All rights reserved. See http://molajo.org/copyright
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die;

/**
 * modelParameters
 *
 * Retrieves and updates the parameters column in the Molajo extensions table for the Extend Plugin
 *
 * @package	Content
 * @subpackage	Extend
 * @version	1.6
 */
class modelParameter
{
    /**
     * getData
     * 
     * Retrieves the parameters value from the extensions table for the extend plugin
     * saved by normal Molajo processing in order to append the dynamic Content Type
     * in order to append that value with the dynamic Content Type Parameters
     *
     * @return string parameters
     */
    public function getData ()
    {
        $db = MolajoFactory::getDbo();
        $app = MolajoFactory::getApplication();

        $query = $db->getQuery(true);
        $query->select('parameters');
        $query->from('#__extensions a');
        $query->where('a.element = '.$db->quote(trim('extend')));
        $query->where('a.type = '.$db->quote(trim('plugin')));

        $db->setQuery($query);
        $rows = $db->loadObject();

        if ($db->getErrorNum()) {
            $app->enqueueMessage($db->getErrorMsg(), 'error');
            return false;
        }
        if (count($rows)) {
            foreach ( $rows as $row )	{
                $parameters = $row;
            }
        }
        return $parameters;
    }

    /**
     * updateData
     *
     * Update Extensions Table parameters field for the Extend Parameter
     *
     * @param string $parameters
     *
     * @return boolean
     */
    public function updateData ($parameters)
    {
        $db = MolajoFactory::getDbo();
        $app = MolajoFactory::getApplication();
        
        $query = 'UPDATE '
                    .$db->namequote(trim('#__extensions'))
                    .' SET parameters = '.$db->quote(trim($parameters))
                    .' WHERE element = '.$db->quote(trim('extend'))
                    .' AND type = '.$db->quote(trim('plugin'));

        $db->setQuery($query);
        if (!$results = $db->query()) {
            $app->enqueueMessage($db->getErrorMsg(), 'error');
            return false;
        }
        return true;
    }
}