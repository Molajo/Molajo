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
     * queryStatus
     *
     * sets criteria for all published status and sets date checks
     *
     * @param array $query
     * @param string $prefix
     *
     * @return  object
     * @since   1.0
     */
    public function queryStatus(
        $query = array(),
        $prefix = 'a')
    {
        $db = Services::DB();
        $now = Services::Date()->getDate()->toSql();
        $nullDate = $db->getNullDate();

        $query->where(
            $db->nq($prefix)
                . '.'
                . $db->nq('status')
                . ' > '
                . (int)MOLAJO_STATUS_UNPUBLISHED
        );

        $query->where('('
                . $db->nq($prefix)
                . '.'
                . $db->nq('start_publishing_datetime')
                . ' = '
                . $db->q($nullDate)
                . ' OR '
                . $db->nq($prefix)
                . '.'
                . $db->nq('start_publishing_datetime')
                . ' <= '
                . $db->q($now)
                . ')'
        );

        $query->where('('
                . $db->nq($prefix)
                . '.'
                . $db->nq('stop_publishing_datetime')
                . ' = '
                . $db->q($nullDate)
                . ' OR '
                . $db->nq($prefix)
                . '.'
                . $db->nq('stop_publishing_datetime')
                . ' >= '
                . $db->q($now)
                . ')'
        );

        return $query;
    }

    /**
     * queryPrimaryCategory
     *
     * Note: Assumes a join is in place to the assets table on a_assets
     *
     * sets the select, table, and where clause to retrieve
     * the primary category and description with content
     *
     * @param array $query
     *
     * @return  object
     * @since   1.0
     */
    public function queryPrimarycategory(
        $query = array(),
        $prefix = 'a')
    {
        $db = Services::DB();
        $now = Services::Date()->getDate()->toSql();
        $nullDate = $db->getNullDate();

        $query->select($db->nq('a_assets') . '.' . $db->nq('primary_category_id'));
        $query->select($db->nq('pcat').'.*');
        $query->from($db->nq('#__content') . ' as ' . $db->nq('pcat'));
        $query->where($db->nq('pcat') . '.' . $db->nq('id')
            . ' = ' . $db->nq('a_assets') . '.' . $db->nq('primary_category_id') );

        return $query;

    }

    /**
     * itemSplittext
     *
     * splits the content_text field into intro and full text on readmore
     *
     * @param array $item
     * @param array $parameters
     *
     * @return  array
     * @since   1.0
     */
    public function itemSplittext(
        $item = array(),
        $parameters = array())
    {
        if (isset($item->content_text)) {
        } else {
            $item->introtext = '';
            $item->fulltext = '';
            return $item;
        }

        $pattern = '#<hr\s+id=("|\')system-readmore("|\')\s*\/*>#i';

        $tagPos = preg_match($pattern, $item->content_text);

        if ($tagPos == 0) {
            $introtext = $item->content_text;
            $fulltext = '';
        } else {
            list($introtext, $fulltext) = preg_split($pattern, $item->content_text, 2);
        }

        $item->introtext = $introtext;
        $item->fulltext = $fulltext;

        return $item;
    }

    /**
     * itemSnippet
     *
     * Splits content_text field into intro and full text on readmore
     *
     * @param array $item
     * @param array $parameters
     *
     * @return  array
     * @since   1.0
     */
    public function itemSnippet(
        $item = array(),
        $parameters = array())
    {
        if (isset($item->content_text)) {
        } else {
            $item->snippet = '';
            return $item;
        }

        $item->snippet =
            substr(
                $item->content_text,
                0,
                $parameters->get('view_text_snippet_length', 200)
            );

        return $item;
    }

    /**
     * itemURL
     *
     * Determines the item URL
     *
     * @param array $item
     * @param array $parameters
     *
     * @return  array
     * @since   1.0
     */
    public function itemURL(
        $item = array(),
        $parameters = array())
    {
        if (isset($item->asset_id)) {
        } else {
            $item->url = '';
            return $item;
        }

        $item->url = AssetHelper::getURL($item->asset_id);

        return $item;
    }

    /**
     * itemDateformats
     *
     * Adds formatted dates to $item
     *
     * @param array $item
     * @param array $parameters
     *
     * @return  array
     * @since   1.0
     */
    public function itemDateformats(
        $item = array(),
        $parameters = array())
    {
        if (isset($item->created_datetime)) {
            if ($item->created_datetime == '0000-00-00 00:00:00') {
            } else {
                $item =
                    MolajoModelHelper::itemDateRoutine(
                        'created_datetime',
                        $item
                    );
            }
        }

        if (isset($item->modified_datetime)) {
            if ($item->modified_datetime == '0000-00-00 00:00:00') {
            } else {
                $item =
                    MolajoModelHelper::itemDateRoutine(
                        'modified_datetime',
                        $item
                    );
            }
        }

        if (isset($item->start_publishing_datetime)) {
            if ($item->start_publishing_datetime == '0000-00-00 00:00:00') {
            } else {
                $item =
                    MolajoModelHelper::itemDateRoutine(
                        'start_publishing_datetime',
                        $item
                    );
            }
        }

        if (isset($item->stop_publishing_datetime)) {
            if ($item->stop_publishing_datetime == '0000-00-00 00:00:00') {
            } else {
                $item =
                    MolajoModelHelper::itemDateRoutine(
                        'stop_publishing_datetime',
                        $item
                    );
            }
        }

        return $item;
    }

    /**
     * itemDateRoutine
     *
     * Creates formatted date fields based on a named field
     *
     * @param $fieldname
     * @param $item
     *
     * @return array
     * @since 1.0
     */
    public function itemDateRoutine(
        $fieldname,
        $item)
    {
        if ($item->$fieldname == '0000-00-00 00:00:00') {
            return $item;
        }

        $newField = $fieldname . '_ccyymmdd';
        $item->$newField =
            Services::Date()
                ->convertCCYYMMDD($item->$fieldname);
        $item->$newField =
            str_replace('-', '', $item->$newField);

        $newField = $fieldname . '_n_days_ago';
        $item->$newField =
            Services::Date()
                ->differenceDays(date('Y-m-d'), $item->$fieldname);

        $newField = $fieldname . '_pretty_date';
        $item->$newField =
            Services::Date()
                ->prettydate($item->$fieldname);

        return $item;
    }

    /**
     * itemExpandjsonfields
     *
     * Expands the json-encoded fields into normal fields
     *
     * @param array $item
     * @param array $parameters
     *
     * @return  array
     * @since   1.0
     */
    public function itemExpandjsonfields(
        $item = array(),
        $parameters = array())
    {
        $jsonfields[] = 'custom_fields';
        $jsonfields[] = 'parameters';
        $jsonfields[] = 'metadata';

        foreach ($jsonfields as $name) {
            $name = trim($name);
            if (property_exists($item, $name)) {
                $registry = new Registry;
                $registry->loadString($item->$name);
                $fields = $registry->toArray();

                while (list($jsonfield, $jsonfieldvalue) = each($fields)) {
                    if (property_exists($item, $jsonfield)) {
                    } else {
                        $item->$jsonfield = $jsonfieldvalue;
                    }
                }
                unset($item->$name);
            }
        }
        return $item;
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
     * validateToList
     *
     * Validate a value by verifying it exists in a list
     *
     * @return  boolean
     * @since   1.0
     */
    public function validateToList($name)
    {
        $db = Services::DB();
        $query = $db->getQuery(true);
        $now = Services::Date()->getDate()->toSql();
        $nullDate = $db->getNullDate();

        $query->select('a.' . $db->nq('view_group_id'));
        $query->select('a.' . $db->nq('asset'));
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

        $query->select('a.' . $db->nq('view_group_id'));
        $query->select('a.' . $db->nq('asset'));
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
