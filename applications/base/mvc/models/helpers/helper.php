<?php
/**
 * @package     Molajo
 * @subpackage  Helper
 * @copyright   Copyright (C) 2012 Amy Stephen. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */
defined('MOLAJO') or die;

/**
 * Model
 *
 * @package     Molajo
 * @subpackage  Helper
 * @since       1.0
 */
class MolajoModelHelper
{
    /**
     * getCategoryList
     *
     * @return  list
     * @since   1.0
     */
    public function getCategoryList()
    {
        $db = Services::DB();
        $query = $db->getQuery(true);
        $location = 'MolajoModelHelper::getCategoryList';

        $query->select('a.' . $db->namequote('id'));
        $query->select('a.' . $db->namequote('title'));
        $query->from($db->namequote('#__content').' as a');
        $query->where('a.' . $db->namequote('asset_type_id') .
            ' = '.MOLAJO_ASSET_TYPE_CATEGORY_LIST);
        $query->order('a.' . $db->namequote('title') . ' ASC');

        return MolajoModelHelper::getList($query, $location);
    }

    /**
     * getGroupList
     *
     * @return  list
     * @since   1.0
     */
    public function getGroupList()
    {
        $db = Services::DB();
        $query = $db->getQuery(true);
        $location = 'MolajoModelHelper::getGroupList';

        $query->select('a.' . $db->namequote('id'));
        $query->select('a.' . $db->namequote('title'));
        $query->from($db->namequote('#__content').' a');
        $query->where('a.' . $db->namequote('asset_type_id') .
            ' IN ('.MOLAJO_ASSET_TYPE_GROUP_SYSTEM.', ' .
                    MOLAJO_ASSET_TYPE_GROUP_NORMAL.')');
        $query->order('a. title ASC');

        return MolajoModelHelper::getList($query, $location);
    }

    /**
     * getLanguageList
     *
     * @return  list
     * @since   1.0
     */
    public function getLanguageList()
    {
        return MolajoLanguageHelper::createLanguageList();
    }

    /**
     * getStatusList
     *
     * @return  list
     * @since   1.0
     */
    public function getStatusList()
    {
        $rowset = array();

        $obj = new stdClass();
        $obj->key = MOLAJO_STATUS_ARCHIVED;
        $obj->value = Services::Language()->_('Archived');
        $rowset[] = $obj;

        $obj = new stdClass();
        $obj->key = MOLAJO_STATUS_PUBLISHED;
        $obj->value = Services::Language()->_('Published');
        $rowset[] = $obj;

        $obj = new stdClass();
        $obj->key = MOLAJO_STATUS_UNPUBLISHED;
        $obj->value = Services::Language()->_('Unpublished');
        $rowset[] = $obj;

        $obj = new stdClass();
        $obj->key = MOLAJO_STATUS_TRASHED;
        $obj->value = Services::Language()->_('Trashed');
        $rowset[] = $obj;

        $obj = new stdClass();
        $obj->key = MOLAJO_STATUS_SPAMMED;
        $obj->value = Services::Language()->_('Spammed');
        $rowset[] = $obj;

        $obj = new stdClass();
        $obj->key = MOLAJO_STATUS_DRAFT;
        $obj->value = Services::Language()->_('Draft');
        $rowset[] = $obj;

        $obj = new stdClass();
        $obj->key = MOLAJO_STATUS_VERSION;
        $obj->value = Services::Language()->_('Version');
        $rowset[] = $obj;

        return $rowset;
    }

    /**
     * getTagList
     *
     * @return  list
     * @since   1.0
     */
    public function getTagList()
    {
        $db = Services::DB();
        $query = $db->getQuery(true);
        $location = 'MolajoModelHelper::getTagList';

        $query->select('a.' . $db->namequote('id'));
        $query->select('a.' . $db->namequote('title'));
        $query->from($db->namequote('#__content').' as a');
        $query->where('a.' . $db->namequote('asset_type_id') .
            ' = '.MOLAJO_ASSET_TYPE_CATEGORY_TAG);
        $query->order('a.' . $db->namequote('title') . ' ASC');

        return MolajoModelHelper::getList($query, $location);
    }

    /**
     * getList
     *
     * @return  list
     * @since   1.0
     */
    public function getList($query, $location)
    {
        $db = Services::DB();
        $now = Services::Date()->getDate()->toSql();
        $nullDate = $db->getNullDate();

        $db->setQuery($query);

        $list = $db->loadObjectList();

        if ($db->getErrorNum() == 0) {
            return $list;
        } else {
            Services::Message()
                ->set(
                $message = Services::Language()->_('ERROR_DATABASE_QUERY') . ' ' .
                    $db->getErrorNum() . ' ' .
                    $db->getErrorMsg(),
                $type = MOLAJO_MESSAGE_TYPE_ERROR,
                $code = 500,
                $debug_location = $location,
                $debug_object = $db
            );
            return null;
        }
    }

    /**
     * validateCheckedOut
     *
     * Verify that the row has been checked out for update by the user
     *
     * @return  boolean  True if checked out to user
     * @since   1.0
     */
    public function validateCheckedOut($table)
    {
        $db = Services::DB();
        $query = $db->getQuery(true);
        $now = Services::Date()->getDate()->toSql();
        $nullDate = $db->getNullDate();

        $query->select('a.' . $db->namequote('view_group_id'));
        $query->select('a.' . $db->namequote('asset'));

    }

    /**
     * validateAlias
     *
     * Verify that the alias is unique for this component
     *
     * @return  boolean
     * @since   1.0
     */
    public function validateAlias($table)
    {

    }

    /**
     * validateDates
     *
     * Verify and set defaults for dates
     *
     * @return  boolean
     * @since   1.0
     */
    public function validateDates($table)
    {

    }

    /**
     * validateLanguage
     *
     * Verify language setting
     *
     * @return  boolean
     * @since   1.0
     */
    public function validateLanguage($table)
    {

    }


}
