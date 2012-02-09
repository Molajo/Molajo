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
 * @subpackage  Component
 * @since       1.0
 */
abstract class MolajoAssetHelper
{
    /**
     * getAsset
     *
     * Retrieve Asset and Asset Type for specific id or query request
     *
     * @param    int  $asset_id
     * @param    null $query_request
     *
     * @results  object
     * @since    1.0
     */
    public static function get($asset_id = 0, $query_request = null)
    {
        $db = Molajo::Application()->get('jdb', 'service');

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

        $query->where('a.' . $db->namequote('view_group_id') .
                ' IN (' . implode(',', Molajo::User()->get('view_groups')) . ')'
        );

        if ((int)$asset_id == 0) {
            $query->where('(a.' . $db->nameQuote('sef_request') .
                    ' = ' . $db->Quote($query_request) .
                    ' OR a.' . $db->nameQuote('request') . ' = ' .
                    $db->Quote($query_request) . ')'
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
            Molajo::Application()
                ->setMessage(
                $message = TextService::_('ERROR_DATABASE_QUERY'),
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

                if (Molajo::Application()->get('sef', 1) == 1) {
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
                    Molajo::Application()->get('home_asset_id', 0)) {
                    if ($query_request == '') {
                    } else {
                        $row->redirect_to_id =
                            Molajo::Application()->get('home_asset_id', 0);
                    }
                }
            }
        }

        return $row;
    }

    /**
     * getAssetRequestObject
     *
     * retrieves asset information for a specific url query or asset id
     *  and updates the request object for results
     *
     * @static
     * @param   $request
     *
     * @return  mixed
     * @since   1.0
     */
    public static function getAssetRequestObject($request)
    {
        $row = AssetHelper::get(
            (int)$request->get('request_asset_id'),
            $request->get('request_url_query')
        );

        /** not found: exit */
        if (count($row) == 0) {
            return $request->set('status_found', false);
        }
        if ((int)$row->routable == 0) {
            return $request->set('status_found', false);
        }

        /** request url */
        $request->set('request_asset_id', (int)$row->asset_id);
        $request->set('request_asset_type_id', (int)$row->asset_type_id);
        $request->set('request_url', $row->request);
        $request->set('request_url_sef', $row->sef_request);
        $request->set('request_url_redirect_to_id', (int)$row->redirect_to_id);

        /** home */
        if ((int)$request->get('request_asset_id', 0)
            == Molajo::Application()->get('home_asset_id', null)
        ) {
            $request->set('request_url_home', true);
        } else {
            $request->set('request_url_home', false);
        }

        $request->set('source_table', $row->source_table);
        $request->set('category_id', (int)$row->primary_category_id);

        /** mvc options and url parameters */
        $request->set('extension_instance_name', $row->request_option);
        $request->set('mvc_model', $row->request_model);
        $request->set('mvc_id', (int)$row->source_id);

        $parameterArray = array();
        $temp = substr($request->get('request_url'),
            10, (strlen($request->get('request_url')) - 10));
        $parameterArray = explode('&', $temp);
        $url_parameters = array();

        if (count($parameterArray) > 0) {
            foreach ($parameterArray as $q) {

                $pair = explode('=', $q);

                if ($pair[0] == 'task') {
                    $request->set('mvc_task', $pair[1]);

                } elseif ($pair[0] == 'theme') {
                    $request->set('theme_id', $pair[1]);

                } elseif ($pair[0] == 'page') {
                    $request->set('page_view_id', $pair[1]);

                } elseif ($pair[0] == 'template') {
                    $request->set('template_view_id', $pair[1]);

                } elseif ($pair[0] == 'wrap') {
                    $request->set('wrap_view_id', $pair[1]);
                }

                $url_parameters[$pair[0]] = $pair[1];
            }
        }
        $request->set('mvc_url_parameters', $url_parameters);

        if ($request->get('request_asset_type_id')
            == MOLAJO_ASSET_TYPE_MENU_ITEM_COMPONENT
        ) {
            $request->set('menu_item_id', $row->source_id);
        } else {
            $request->set('source_id', $row->source_id);
        }

        return $request;
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
        $db = Molajo::Application()->get('jdb', 'service');
        $query = $db->getQuery(true);

        $query->select('a.' . $db->namequote('id') . ' as asset_id');
        $query->from($db->namequote('#__assets') . ' as a');
        $query->where('a.' . $db->namequote('asset_type_id') .
            ' = ' . (int)$asset_type_id);
        $query->where('a.' . $db->namequote('source_id') .
            ' = ' . (int)$source_id);
        $query->where('a.' . $db->namequote('view_group_id') .
                ' IN (' . implode(',', Molajo::User()->get('view_groups')) . ')'
        );

        $db->setQuery($query->__toString());
        $asset_id = $db->loadResult();

        if ($error = $db->getErrorMsg()) {
            Molajo::Application()
                ->setMessage(
                $message = TextService::_('ERROR_DATABASE_QUERY').' '.$db->getErrorMsg(),
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
        $db = Molajo::Application()->get('jdb', 'service');
        $query = $db->getQuery(true);

        /** home */
        if ($asset_id == Molajo::Application()->get('home_asset_id', 0)) {
            return '';
        }

        /** retrieve id if not home */
        if (Molajo::Application()->get('sef', 1) == 1) {
            $query->select('a.' . $db->namequote('sef_request'));
        } else {
            $query->select('a.' . $db->namequote('request'));
        }
        $query->from($db->namequote('#__assets') . ' as a');
        $query->where('a.' . $db->namequote('id') .
            ' = ' . (int)$asset_id);
        $query->where('a.' . $db->namequote('view_group_id') .
                ' IN (' . implode(',', Molajo::User()->get('view_groups')) . ')'
        );

        $db->setQuery($query->__toString());
        $url = $db->loadResult();

        if ($error = $db->getErrorMsg()) {
            Molajo::Application()
                ->setMessage(
                $message = TextService::_('ERROR_DATABASE_QUERY').' '.$db->getErrorMsg(),
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
