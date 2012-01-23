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
        $acl = new MolajoACL ();

        $query->select('a.' . $db->namequote('id') . ' as asset_id');
        $query->from($db->namequote('#__assets') . ' as a');
        $query->where('a.' . $db->namequote('asset_type_id') . ' = ' . (int)$asset_type_id);
        $query->where('a.' . $db->namequote('source_id') . ' = ' . (int)$source_id);

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
        $query->select('a.' . $db->nameQuote('routable'));
        $query->select('a.' . $db->nameQuote('sef_request'));
        $query->select('a.' . $db->nameQuote('request'));
        $query->select('a.' . $db->nameQuote('request_option'));
        $query->select('a.' . $db->nameQuote('request_model'));
        $query->select('a.' . $db->nameQuote('request_id'));
        $query->select('a.' . $db->nameQuote('redirect_to_id'));
        $query->select('a.' . $db->nameQuote('view_group_id'));
        $query->select('a.' . $db->nameQuote('primary_category_id'));

        $query->select('b.' . $db->nameQuote('component_option') . ' as ' . $db->nameQuote('option'));
        $query->select('b.' . $db->nameQuote('source_table'));

        $query->from($db->nameQuote('#__assets') . ' as a');
        $query->from($db->nameQuote('#__asset_types') . ' as b');

        $query->where('a.' . $db->nameQuote('asset_type_id') . ' = b.' . $db->nameQuote('id'));

        if ((int)$asset_id == 0) {
            $query->where('(a.' . $db->nameQuote('sef_request') . ' = ' . $db->Quote($query_request).
                ' OR a.' . $db->nameQuote('request') . ' = ' . $db->Quote($query_request).')');
        } else {
            $query->where('a.' . $db->nameQuote('id') . ' = ' . (int)$asset_id);
        }

        $db->setQuery($query->__toString());
        $rows = $db->loadObjectList();

        if ($db->getErrorNum() == 0) {

        } else {
            MolajoController::getApplication()->setMessage($db->getErrorMsg(), MOLAJO_MESSAGE_TYPE_ERROR);
            return false;
        }

        if (count($rows) == 0) {
            return array();
        }

        foreach ($rows as $row) {

            if ((int)$asset_id == 0) {

                if (MolajoController::getApplication()->get('sef', 1) == 1) {
                    if ($row->sef_request == $query_request) {

                    } else {
                        $row->redirect_to_id = (int) $row->asset_id;
                    }

                } else {
                    if ($row->request == $query_request) {

                    } else {
                        $row->redirect_to_id = (int) $row->asset_id;
                    }
                }

                /** Home */
                if ($row->asset_id == MolajoController::getApplication()->get('home_asset_id', 0)) {
                    if ($query_request == '') {
                    } else {
                        $row->redirect_to_id = MolajoController::getApplication()->get('home_asset_id', 0);
                    }
                }
            }
        }

        return $row;
    }
    /**
     * getURL
     *
     * Retrieves URL based on Asset ID
     *
     * @param  null $asset_id
     *
     * @return string
     * @since  1.0
     */
    public static function getURL($asset_id)
    {
        $db = MolajoController::getDbo();
        $query = $db->getQuery(true);
        $acl = new MolajoACL ();

        /** home */
        if ($asset_id == MolajoController::getApplication()->get('home_asset_id', 0)) {
            return '';
        }

        /** retrieve id if not home */
        if (MolajoController::getApplication()->get('sef', 1) == 1) {
            $query->select('a.'.$db->namequote('sef_request'));
        } else {
            $query->select('a.'.$db->namequote('request'));
        }
        $query->from($db->namequote('#__assets') . ' as a');
        $query->where('a.' . $db->namequote('id') . ' = ' . (int)$asset_id);

        $acl->getQueryInformation('', $query, 'viewaccess', array('table_prefix' => 'a'));

        $db->setQuery($query->__toString());
        $url = $db->loadResult();

        if ($error = $db->getErrorMsg()) {
            MolajoError::raiseWarning(500, $error);
            return false;
        }

        return $url;
    }
}