<?php
/**
 * @package     Molajo
 * @subpackage  Extend
 * @copyright   Copyright (C) 2010-2011 Amy Stephen. All rights reserved. See http://molajo.org/copyright
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die;

/**
 * modelContentItem
 *
 * Table containing Custom Field Name and Values, linked to Component Items
 *
 * @package	Content
 * @subpackage	Extend
 * @version	1.6
 */
class modelContentItem
{
    /**
     * getData
     *
     * Retrieves Component Custom Field Values for a Component Content ID
     *
     * @param string $component_option
     * @param string $content_id
     *
     * @return object Component Custom Fields
     */
    public function getData ($component_option, $id, $whereString = null, $sql_table_name=null, $sql_table_name=null)
    {
        $db = MolajoFactory::getDbo();
        $systemPlugin =& MolajoPluginHelper::getPlugin('system', 'extend');
        $fieldParams = new JParameter($systemPlugin->params);

        if ($sql_table_name == null) {
            $sql_table_name = $fieldParams->def('sql_table_name', '#__molajo_custom_fields');
        }
	$query = $db->getQuery(true);

        $query->select('field_name, field_value');
        $query->from($db->namequote($sql_table_name));
        $query->where($db->namequote('component_option').' = '.$db->quote(trim($component_option)));
        $query->where('content_id = '. (int) $id);
        if ($whereString) {
            $query->where($whereString);
        }
        $query->order('ordering');

        $db->setQuery($query);
        $results = $db->loadObjectList();

        if ($db->getErrorNum()) {
            JError::raiseWarning(500, $db->stderr());
            return false;
        }

        return $results;
    }

    /**
     * insert
     *
     * Inserts Custom Field (Name and Value and link to related Component Item)
     *
     * @param string $name - custom field name
     * @param string $value - custom field value
     * @param int $ordering
     *
     * @return boolean 
     */
    public function insert ($component_option, $id, $name, $value, $ordering, $sql_table_name=null)
    {
        $db = MolajoFactory::getDbo();
        $app = MolajoFactory::getApplication();
        $systemPlugin =& MolajoPluginHelper::getPlugin('system', 'extend');
        $fieldParams = new JParameter($systemPlugin->params);

        if ($sql_table_name == null) {
            $sql_table_name = $fieldParams->def('sql_table_name', '#__molajo_custom_fields');
        }

        $query = 'INSERT INTO '.$db->namequote(trim($sql_table_name)) .
                    ' VALUES ( '
                    .$db->quote(trim($component_option)).', '
                    .(int) $id.', '
                    .$db->quote(trim($name)).', '
                    .$db->quote(trim($value)).', '
                    .$db->quote(trim($ordering)).') ';

        $db->setQuery($query);

        if (!$results = $db->query()) {
            $app->enqueueMessage($db->getErrorMsg(), 'error');
            return false;
        }

        return true;
    }

    /**
     * delete
     *
     * Deletes all custom fields for specified component item
     *
     * @return boolean
     */
    public function delete ($component_option, $id, $sql_table_name=null)
    {
        $app = MolajoFactory::getApplication();
        $db = MolajoFactory::getDbo();
        $systemPlugin =& MolajoPluginHelper::getPlugin('system', 'extend');
        $fieldParams = new JParameter($systemPlugin->params);

        if ($sql_table_name == null) {
            $sql_table_name = $fieldParams->def('sql_table_name', '#__molajo_custom_fields');
        }

        $query = 'DELETE FROM '
                    .$db->namequote(trim($sql_table_name))
                    .' WHERE '.$db->namequote('content_id').' = '.(int) $id
                    .' AND '.$db->namequote('component_option').' = '.$db->quote(trim($component_option));

        $db->setQuery($query);

        if (!$results = $db->query()) {
            $app->enqueueMessage($db->getErrorMsg(), 'error');
            return false;
        }
    }

    /**
     * deleteMatching
     *
     * Deletes the custom field matching a specific field name for a specific component and item
     *
     * @return boolean
     */
    public function deleteMatching ($component_option, $id, $custom_field, $sql_table_name=null)
    {
        $db = MolajoFactory::getDbo();
        $systemPlugin =& MolajoPluginHelper::getPlugin('system', 'extend');
        $fieldParams = new JParameter($systemPlugin->params);

        if ($sql_table_name == null) {
            $sql_table_name = $fieldParams->def('sql_table_name', '#__molajo_custom_fields');
        }

        $query = 'DELETE FROM '
                    .$db->namequote(trim($sql_table_name))
                    .' WHERE '.$db->namequote('content_id').' = '.(int) $id
                    .' AND '.$db->namequote('component_option').' = '.$db->quote(trim($component_option))
                    .' AND '.$db->namequote('field_name').' = '.$db->quote(trim($custom_field));

        $db->setQuery($query);

        if (!$results = $db->query()) {
            $app->enqueueMessage($db->getErrorMsg(), 'error');
            return false;
        }
    }

    /**
     * buildWhereClause
     *
     * Helper function that uses $custom_fields to prepares a where clause of custom field names
     *
     * @return string
     */
    public function buildWhereClause ($custom_fields)
    {
        $nameString = '';
        foreach ($custom_fields as $customField) :
            if ($nameString == '') {
            } else {
                $nameString .= ', ';
            }
            $nameString .= $db->quote($customField->name);
        endforeach;

        /** return formatted string **/
        return $db->namequote('field_name').' IN ('.$nameString.')';
    }

    /**
     * checkTable
     *
     * Creates the Custom Fields table, if needed
     * Note: This is a configuration option by Content Types that requires check before use
     *
     * @return binary
     */
    public function checkTable ($sql_table_name, $sql_table_name=null)
    {
        $db = MolajoFactory::getDbo();
        $systemPlugin =& MolajoPluginHelper::getPlugin('system', 'extend');
        $fieldParams = new JParameter($systemPlugin->params);

        if ($sql_table_name == null) {
            $sql_table_name = $fieldParams->def('sql_table_name', '#__molajo_custom_fields');
        }

        $query = 'SELECT * FROM '
            . $db->namequote(trim($sql_table_name))
            .' WHERE 1 = 2 ';

        $db->setQuery($query);
        $db->query();

        if ((int) $db->getErrorNum() == 0) {
            return true;

        } else if ((int) $db->getErrorNum() == 1146) {
            return modelContentItem::createTable ($sql_table_name);

        } else {
            $app->enqueueMessage($db->getErrorMsg(), 'error');
            return false;
        }
    }

    /**
     * createTable
     *
     * Creates the named Custom Fields table
     * Note: This is a configuration option available by Content Type
     *
     * @return binary
     */
     public function createTable ($sql_table_name=null)
    {
        $db = MolajoFactory::getDbo();
        $systemPlugin =& MolajoPluginHelper::getPlugin('system', 'extend');
        $fieldParams = new JParameter($systemPlugin->params);

        if ($sql_table_name == null) {
            $sql_table_name = $fieldParams->def('sql_table_name', '#__molajo_custom_fields');
        }

        /** create table **/
        $query = "CREATE TABLE IF NOT EXISTS ";
        $query .= $db->namequote(trim($sql_table_name));
        $query .= "  (
          `component_option` varchar(100) NOT NULL DEFAULT '' COMMENT 'Option value, like com_articles; links to Component ',
          `content_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT 'FK to component table identified in Option.',
          `field_name` varchar(100) NOT NULL DEFAULT '' COMMENT 'Name of Custom Field defined within Content Type file.',
          `field_value` varchar(255) NOT NULL DEFAULT '' COMMENT 'Component, Content ID value for the Content Type Custom Field',
          `ordering` int(11) NOT NULL DEFAULT '0',
          UNIQUE KEY `idx_user_id_profile_key` (`component_option`,`content_id`,`field_name`)
        ) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='Extend Component Tables with Content Types and Custom Fields'; ";

        /** build table **/
        $db->setQuery($query);
        $db->query();

        if ($db->getErrorNum()) {
            $app->enqueueMessage($db->getErrorMsg(), 'error');
            return false;
        }

        /** ready for action **/
        return true;
    }
}