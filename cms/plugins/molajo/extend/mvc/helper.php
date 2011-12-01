<?php
/**
 * @package     Molajo
 * @subpackage  Extend
 * @copyright   Copyright (C) 2010-2011 Amy Stephen. All rights reserved. See http://molajo.org/copyright
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die;

/**
 * extendHelper
 *
 * Various functions needed in the application
 *
 * @package    Content
 * @subpackage    Extend
 * @version    1.6
 */
class extendHelper
{

    /**
     * getComponentOption
     *
     * Retrieves option from JRequest
     *
     * @return boolean
     */
    public function getComponentOption()
    {
        $component_option = JRequest::getVar('option');

        $valid = extendHelper::verifyComponentOption($component_option);
        if ($valid) {
            return $component_option;
        } else {
            return false;
        }
    }

    /**
     * verifyComponentOption
     *
     * Helper Function that validates the Option Value to the Extensions Table
     *
     * @return boolean
     */
    public function verifyComponentOption($component_option)
    {
        $db = MolajoFactory::getDbo();
        $app = MolajoFactory::getApplication();

        $db->setQuery(
            'SELECT element ' .
            ' FROM ' . $db->namequote(trim('#__extensions')) .
            ' WHERE element = ' . $db->quote(trim($component_option)) .
            '   AND type = "component" '
        );

        if (!$results = $db->query()) {
            $app->enqueueMessage($db->getErrorMsg(), 'error');
            return false;
        }

        if (count($results) == 0) {
            return false;
        } else {
            return true;
        }
    }

    /**
     * getComponentView
     *
     * Retrieves view from JRequest
     *
     * @return boolean
     */
    public function getComponentView($component_option)
    {
        $component_view = JRequest::getVar('view');

        $valid = extendHelper::verifyComponentView($component_option, $component_view);
        if ($valid) {
            return $component_view;
        } else {
            return false;
        }
    }

    /**
     * verifyComponentView
     *
     * Helper Function that validates the Option Value to the Extensions Table
     *
     * @return boolean
     */
    public function verifyComponentView($component_option, $component_view)
    {
        $app = MolajoFactory::getApplication();
        if ($app->getName() == 'administrator') {
            $folder = JPATH_ADMINISTRATOR . '/components/' . $component_option . '/views/' . $component_view;
        } else {
            $folder = JPATH_SITE . '/components/' . $component_option . '/views/' . $component_view;
        }

        if (JFolder::exists($folder)) {
            return true;
        } else {
            $app->enqueueMessage('ComponInvalid View' . $component_view, 'error');
            return false;
        }
    }

    /**
     * getComponentLayout
     *
     * Retrieves layout from JRequest
     *
     * @return boolean
     */
    public function getComponentLayout($component_option, $component_view)
    {
        $component_layout = JRequest::getVar('layout');

        $valid = extendHelper::verifyComponentLayout($component_option, $component_view, $component_layout);
        if ($valid) {
            return $component_layout;
        } else {
            return false;
        }
    }

    /**
     * verifyComponentLayout
     *
     * Helper Function that validates the Layout Option for a Component View
     *
     * @return boolean
     */
    public function verifyComponentLayout($component_option, $component_view, $component_layout)
    {
        $app = MolajoFactory::getApplication();

        if ($app->getName() == 'administrator') {
            $file = JPATH_ADMINISTRATOR . '/components/' . $component_option . '/views/' . $component_view . '/layouts/' . $component_layout . '.php';
        } else {
            $file = JPATH_SITE . '/components/' . $component_option . '/views/' . $component_view . '/layouts/' . $component_layout . '.php';
        }

        if (JFile::exists($file)) {
            return true;
        } else {
            $app->enqueueMessage('ComponInvalid Layout' . $component_layout, 'error');
            return false;
        }
    }

    /**
     * getComponentCategory
     *
     * Retrieves category value for item
     *
     * Molajo 1.7 Wish List
     *
     * Provide good information on the form about standard integration values
     * For example, it should be simple to determine the category id, or what the key field and value is, etc.
     * The form object is extremely difficult to analyze and reloading the form to review it when
     * you are simply trying to determine if you need to deal with it is costly
     *
     * @return boolean
     */
    public function getComponentCategory($context, $content, $form, $IsNew, $task, $component_option)
    {
        /** known table names **/
        $tableInfo = array(
            'banners' => array('field_name' => 'catid'),
            'categories' => array('field_name' => 'id'),
            'contact' => array('field_name' => 'catid'),
            'articles' => array('field_name' => 'catid'),
            'languages' => array('field_name' => false),
            'messages' => array('field_name' => false),
            'menu' => array('field_name' => false),
            'modules' => array('field_name' => false),
            'newsfeeds' => array('field_name' => 'catid'),
            'responses' => array('field_name' => 'catid'),
            'tags' => array('field_name' => 'catid'),
            'template_styles' => array('field_name' => false),
            'users' => array('field_name' => false),
            'weblinks' => array('field_name' => 'catid')
        );

        /** identify **/
        if (isset($tableInfo[$component_option])) {
            $name_name = $tableInfo[$component_option]['field_name'];
            if ($name_name == false) {
                return true;
            }
        } else {
            $name_name = 'catid';
        }

        if (isset($content->$name_name)) {
            $category = (int)$content->$name_name;
        }
        if (isset($form->$name_name)) {
            $category = $form->$name_name;
        }
        if (JRequest::getInt($name_name) > 0) {
            $category = JRequest::getInt($name_name);
        }
        $valid = extendHelper::verifyCategory($category);
        if ($valid) {
            return $category;
        } else {
            return false;
        }
    }

    /**
     * verifyCategory
     *
     * Helper Function that validates the Category Value to the Database
     *
     * @return boolean
     */
    public function verifyCategory($component_option, $category)
    {
        $db = MolajoFactory::getDbo();

        $db->setQuery(
            'SELECT id ' .
            ' FROM ' . $db->namequote(trim('#__categories')) .
            ' WHERE extension = ' . $db->namequote($component_option) .
            '   AND id = ' . (int)$category
        );

        if (!$results = $db->query()) {
            $app->enqueueMessage($db->getErrorMsg(), 'error');
            return false;
        }

        if (count($results) == 0) {
            return false;
        } else {
            return true;
        }
    }

    /**
     * getComponentID
     *
     * Retrieves id value for item
     *
     * Molajo 1.7 Wish List
     *
     * Consistency and/or APIs to overcome consistency issues
     *   Don't want to load a table object to find out the key
     *   Especially considering the table name is not easy to figure out...
     *
     * @return boolean
     */
    public function getComponentID($context, $content, $form, $isnNew, $component_option)
    {

        /** known table names **/
        $tableInfo = array(
            'banners' => array('field_name' => 'id'),
            'categories' => array('field_name' => 'id'),
            'contact' => array('field_name' => 'id'),
            'articles' => array('field_name' => 'id'),
            'languages' => array('field_name' => 'id'),
            'messages' => array('field_name' => 'message_id'),
            'modules' => array('field_name' => 'id'),
            'menu' => array('field_name' => 'id'),
            'newsfeeds' => array('field_name' => 'id'),
            'responses' => array('field_name' => 'catid'),
            'tags' => array('field_name' => 'catid'),
            'template_styles' => array('field_name' => 'id'),
            'users' => array('field_name' => 'id'),
            'weblinks' => array('field_name' => 'id')
        );

        /** identify **/
        if (isset($tableInfo[$component_option])) {
            $name_name = $tableInfo[$component_option]['field_name'];
        } else {
            $name_name = 'id';
        }

        if (!JRequest::getInt('id') == 0) {
            $id = (int)JRequest::getVar('id');

        } else if ($component_option == 'users') {
            if (is_object($content)) {
                $id = $content->id;

            } else {
                return false;
            }

        } else if (is_object($content)) {
            $id = $content->id;

        } else if (is_object($form)) {
            $id = $form->id;
        }

        if ($id == null) {
            if ($isNew) {
                return 0;
            } else {
                return false;
            }
        }

        $valid = extendHelper::verifyComponentID($component_option, $id, $name_name);
        if ($valid) {
            return $id;
        } else {
            return false;
        }
    }

    /**
     * verifyComponentID
     *
     * Helper Function that validates the Component ID Value to the Database
     *
     * 1.7 Wishlist
     *
     * Per component, it would be very helpful to know the names of the table and primary key
     *
     * @return boolean
     */
    public function verifyComponentID($component_option, $id, $name_name)
    {
        $db = MolajoFactory::getDbo();
        $app = MolajoFactory::getApplication();

        /** known table names **/
        $tableInfo = array(
            'banners' => array('table_name' => '#__banners'),
            'contact' => array('table_name' => '#__contact_details'),
            'categories' => array('table_name' => '#__categories'),
            'articles' => array('table_name' => '#__content'),
            'languages' => array('table_name' => '#__languages'),
            'menu' => array('table_name' => '#__content'),
            'messages' => array('table_name' => '#__messages'),
            'modules' => array('table_name' => '#__modules'),
            'newsfeeds' => array('table_name' => '#__newsfeeds'),
            'responses' => array('table_name' => '#__responses'),
            'tags' => array('table_name' => '#__tags'),
            'users' => array('table_name' => '#__users'),
            'weblinks' => array('table_name' => '#__weblinks')
        );

        /** identify **/
        if (isset($tableInfo[$component_option])) {
            $table = $tableInfo[$component_option]['table_name'];
        } else {
            return false;
        }

        /** language does not use id **/
        $db->setQuery(
            'SELECT ' . $name_name .
            ' FROM ' . $db->namequote(trim($table)) .
            '   WHERE ' . $name_name . ' = ' . (int)$id
        );

        if (!$results = $db->query()) {
            $app->enqueueMessage($db->getErrorMsg(), 'error');
            return false;
        }

        if (count($results) == 0) {
            return false;
        } else {
            return true;
        }
    }

    /**
     * getCustomFieldsWhereClause
     *
     * Helper function that uses custom_fields to prepares a where clause of custom field names
     *
     * @return string
     */
    public function getCustomFieldsWhereClause($custom_fields)
    {
        $nameString = '';
        foreach (custom_fields as $customField) :
            if ($nameString == '') {
            } else {
                $nameString .= ', ';
            }
            $nameString .= $db->quote($customField->name);
        endforeach;

        /** return formatted string **/
        return ' AND ' . $db->namequote('field_name') . ' IN (' . $nameString . ')';
    }
}