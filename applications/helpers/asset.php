<?php
/**
 * @package     Molajo
 * @subpackage  Helper
 * @copyright   Copyright (C) 2012 Amy Stephen. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */
defined('MOLAJO') or die;

/**
 * Asset
 *
 * @package     Molajo
 * @subpackage  Helper
 * @since       1.0
 */
abstract class MolajoAssetHelper
{
    /**
     * getAsset
     *
     * Retrieve Asset and Asset Type for specific id or query request
     *
     * View Access is verified in Molajo::Request to identify 403 errors
     *
     * @param    int  $asset_id
     * @param    null $query_request
     *
     * @results  object
     * @since    1.0
     */
    public static function get($asset_id = 0, $query_request = null)
    {
        $user = Services::User();
        $db = Services::DB();

        $query = $db->getQuery(true);

        $query->select('a.' . $db->nameQuote('id') . ' as asset_id');
        $query->select('a.' . $db->nameQuote('asset_type_id'));
        $query->select('a.' . $db->nameQuote('source_id'));
        $query->select('a.' . $db->nameQuote('routable'));
        $query->select('a.' . $db->nameQuote('sef_request'));
        $query->select('a.' . $db->nameQuote('request'));
        $query->select('a.' . $db->nameQuote('request_option'));
        $query->select('a.' . $db->nameQuote('request_model'));
        $query->select('a.' . $db->nameQuote('redirect_to_id'));
        $query->select('a.' . $db->nameQuote('view_group_id'));
        $query->select('a.' . $db->nameQuote('primary_category_id'));
        $query->select('b.' . $db->nameQuote('source_table'));

        $query->from($db->nameQuote('#__assets') . ' as a');
        $query->from($db->nameQuote('#__asset_types') . ' as b');

        $query->where('a.' . $db->nameQuote('asset_type_id') .
            ' = b.' . $db->nameQuote('id'));

        if ((int)$asset_id == 0) {
            $query->where('(a.' . $db->nameQuote('sef_request') .
                    ' = ' . $db->quote($query_request) .
                    ' OR a.' . $db->nameQuote('request') . ' = ' .
                    $db->quote($query_request) . ')'
            );
        } else {
            $query->where('a.' . $db->nameQuote('id') . ' = ' .
                    (int)$asset_id
            );
        }

        $db->setQuery($query->__toString());
        $rows = $db->loadObjectList();

        if ($db->getErrorNum() == 0) {

        } else {
            Services::Message()
                ->set(
                $message = Services::Language()->_('ERROR_DATABASE_QUERY'),
                $type = MOLAJO_MESSAGE_TYPE_ERROR,
                $code = 500,
                $debug_location = 'AssetHelper::get',
                $debug_object = $db
            );
            return false;
        }

        if (count($rows) == 0) {
            return array();
        }

        $row = array();
        foreach ($rows as $row) {

            if ((int)$asset_id == 0) {

                if (Services::Configuration()->get('sef', 1) == 1) {
                    if ($row->sef_request == $query_request) {

                    } else {
                        $row->redirect_to_id = (int)$row->asset_id;
                    }

                } else {
                    if ($row->request == $query_request) {

                    } else {
                        $row->redirect_to_id = (int)$row->asset_id;
                    }
                }

                if ($row->asset_id ==
                    Services::Configuration()->get('home_asset_id', 0)
                ) {
                    if ($query_request == '') {
                    } else {
                        $row->redirect_to_id =
                            Services::Configuration()->get('home_asset_id', 0);
                    }
                }
            }
        }

        return $row;
    }

    /**
     * getID
     *
     * Retrieves Asset ID
     *
     * @param  null $asset_type_id
     * @param  null $source_id
     *
     * @return bool|mixed
     * @since  1.0
     */
    public static function getID($asset_type_id, $source_id)
    {
        $user = Services::User();
        $db = Services::DB();
        $query = $db->getQuery(true);

        $query->select('a.' . $db->namequote('id') . ' as asset_id');
        $query->from($db->namequote('#__assets') . ' as a');
        $query->where('a.' . $db->namequote('asset_type_id') .
            ' = ' . (int)$asset_type_id);
        $query->where('a.' . $db->namequote('source_id') .
            ' = ' . (int)$source_id);
        $query->where('a.' . $db->namequote('view_group_id') .
                ' IN (' .
                implode(',', $user->get('view_groups')) . ')'
        );

        $db->setQuery($query->__toString());
        $asset_id = $db->loadResult();

        if ($error = $db->getErrorMsg()) {
            Services::Message()
                ->set(
                $message = Services::Language()->_('ERROR_DATABASE_QUERY')
                    . ' ' . $db->getErrorMsg(),
                $type = MOLAJO_MESSAGE_TYPE_ERROR,
                $code = 500,
                $debug_location = 'AssetHelper::getID',
                $debug_object = $db
            );
            return false;
        }

        return $asset_id;
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
        $user = Services::User();
        $db = Services::DB();
        $query = $db->getQuery(true);

        /** home */
        if ($asset_id == Services::Configuration()->get('home_asset_id', 0)) {
            return '';
        }

        /** retrieve id if not home */
        if (Services::Configuration()->get('sef', 1) == 1) {
            $query->select('a.' . $db->namequote('sef_request'));
        } else {
            $query->select('a.' . $db->namequote('request'));
        }
        $query->from($db->namequote('#__assets') . ' as a');
        $query->where('a.' . $db->namequote('id') .
            ' = ' . (int)$asset_id);
        $query->where('a.' . $db->namequote('view_group_id') .
                ' IN (' .
                implode(',', $user->get('view_groups')) . ')'
        );

        $db->setQuery($query->__toString());
        $url = $db->loadResult();

        if ($error = $db->getErrorMsg()) {
            Services::Message()
                ->set(
                $message = Services::Language()->_('ERROR_DATABASE_QUERY')
                    . ' ' . $db->getErrorMsg(),
                $type = MOLAJO_MESSAGE_TYPE_ERROR,
                $code = 500,
                $debug_location = 'AssetHelper::getURL',
                $debug_object = $db
            );
            return false;
        }

        return $url;
    }
}
