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
 * Retrieves and updates the params column in the Molajo extensions table for the Extend Plugin
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
     * Retrieves the params value from the extensions table for the extend plugin
     * saved by normal Molajo processing in order to append the dynamic Content Type
     * in order to append that value with the dynamic Content Type Parameters
     *
     * @return string params
     */
    public function getData ()
    {
        $db = JFactory::getDbo();
        $app = JFactory::getApplication();

        $query = $db->getQuery(true);
        $query->select('params');
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
                $params = $row;
            }
        }
        return $params;
    }

    /**
     * updateData
     *
     * Update Extensions Table params field for the Extend Parameter
     *
     * @param string $params
     *
     * @return boolean
     */
    public function updateData ($params)
    {
        $db = JFactory::getDbo();
        $app = JFactory::getApplication();
        
        $query = 'UPDATE '
                    .$db->namequote(trim('#__extensions'))
                    .' SET params = '.$db->quote(trim($params))
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