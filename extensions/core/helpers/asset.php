<?php
/**
 * @package     Molajo
 * @subpackage  Asset
 * @copyright   Copyright (C) 2012 Amy Stephen. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */
defined('MOLAJO') or die;

/**
 * Asset
 *
 * Various queries for Asset support
 *
 * @package     Molajo
 * @subpackage  Asset
 * @since       1.0
 */
abstract class MolajoAssetHelper
{
    /**
     * getAssetID
     *
     * Retrieves Asset ID
     * 
     * @param  null $asset_type_id
     * 
     * @param  null $source_id
     * 
     * @return bool|mixed
     * @since  1.0
     */
    public static function getAssetID($asset_type_id, $source_id)
    {
        $db = MolajoController::getDbo();
        $query = $db->getQuery(true);
        $date = MolajoController::getDate();
        $now = $date->toMySQL();
        $nullDate = $db->getNullDate();
        $acl = new MolajoACL ();

        /**
         *  Assets Table
         */
        $query->select('a.' . $db->namequote('id') . ' as asset_id');
        $query->from($db->namequote('#__assets') . ' as a');
        $query->where('a.' . $db->namequote('asset_type_id') . ' = ' . (int)$asset_type_id);
        $query->where('a.' . $db->namequote('source_id') . ' = ' . (int)$source_id);
        
        /** Asset Instance ACL */
        $acl->getQueryInformation('', $query, 'viewaccess', array('table_prefix' => 'a'));

        $db->setQuery($query->__toString());
        $asset_id = $db->loadResult();

        if ($error = $db->getErrorMsg()) {
            MolajoError::raiseWarning(500, $error);
            return false;
        }

        return $asset_id;
    }

    /**
     * getAsset
     *
     * Retrieve Asset and Asset Type data for a specific asset id or query request
     *
     * @param    int  $asset_id
     * @param    null $query_request
     *
     * @results  object
     * @since    1.0
     */
    public static function getAsset($asset_id = 0, $query_request = null)
    {
        $db = MolajoController::getDbo();
        $query = $db->getQuery(true);

        $query->select('a.' . $db->nameQuote('id') . ' as asset_id');
        $query->select('a.' . $db->nameQuote('asset_type_id'));
        $query->select('a.' . $db->nameQuote('source_id'));
        $query->select('a.' . $db->nameQuote('title'));
        $query->select('a.' . $db->nameQuote('sef_request'));
        $query->select('a.' . $db->nameQuote('request'));
        $query->select('a.' . $db->nameQuote('primary_category_id'));
        $query->select('a.' . $db->nameQuote('template_id'));
        $query->select('a.' . $db->nameQuote('template_page'));
        $query->select('a.' . $db->nameQuote('language'));
        $query->select('a.' . $db->nameQuote('translation_of_id'));
        $query->select('a.' . $db->nameQuote('redirect_to_id'));
        $query->select('a.' . $db->nameQuote('view_group_id'));

        $query->select('b.' . $db->nameQuote('component_option') . ' as ' . $db->nameQuote('option'));
        $query->select('b.' . $db->nameQuote('source_table'));

        $query->from($db->nameQuote('#__assets') . ' as a');
        $query->from($db->nameQuote('#__asset_types') . ' as b');

        $query->where('a.' . $db->nameQuote('asset_type_id') . ' = b.' . $db->nameQuote('id'));

        if ((int)$asset_id == 0) {
            if (MolajoController::getApplication()->get('sef', 1) == 1) {
                $query->where('a.' . $db->nameQuote('sef_request') . ' = ' . $db->Quote($query_request));
            } else {
                $query->where('a.' . $db->nameQuote('request') . ' = ' . $db->Quote($query_request));
            }
        } else {
            $query->where('a.' . $db->nameQuote('id') . ' = ' . (int)$asset_id);
        }

        $db->setQuery($query->__toString());
        $results = $db->loadObjectList();

        if ($db->getErrorNum() == 0) {
        } else {
            MolajoController::getApplication()->setMessage($db->getErrorMsg(), MOLAJO_MESSAGE_TYPE_ERROR);
            return false;
        }

        return $results;
    }
}