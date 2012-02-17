<?php
/**
 * @package     Molajo
 * @subpackage  Helper
 * @copyright   Copyright (C) 2012 Amy Stephen. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */
defined('MOLAJO') or die;

/**
 * Content
 *
 * @package     Molajo
 * @subpackage  Helper
 * @since       1.0
 */
abstract class MolajoContentHelper
{
    /**
     * get
     *
     * Get the content data for the id specified
     *
     * @return  mixed    An object containing an array of data
     * @since   1.0
     */
    static public function get($id, $content_table)
    {
        $db = Services::DB();
        $query = $db->getQuery(true);
        $now = Services::Date()->getDate()->toSql();
        $nullDate = $db->getNullDate();
        $table_name = Services::Configuration()->get('dbprefix').$content_table;

        $query->select('a.' . $db->nq('id'));
        $query->select('a.' . $db->nq('extension_instance_id'));
        $query->select('a.' . $db->nq('asset_type_id'));
        $query->select('a.' . $db->nq('title'));
        $query->select('a.' . $db->nq('subtitle'));
        $query->select('a.' . $db->nq('path'));
        $query->select('a.' . $db->nq('alias'));
        $query->select('a.' . $db->nq('status'));
        $query->select('a.' . $db->nq('start_publishing_datetime'));
        $query->select('a.' . $db->nq('stop_publishing_datetime'));
        $query->select('a.' . $db->nq('modified_datetime'));
        $query->select('a.' . $db->nq('custom_fields'));
        $query->select('a.' . $db->nq('parameters'));
        $query->select('a.' . $db->nq('metadata'));
        $query->select('a.' . $db->nq('language'));
        $query->select('a.' . $db->nq('translation_of_id'));
        $query->select('a.' . $db->nq('ordering'));
        $query->from('#__content as a ');
        $query->where('a.' . $db->nq('id') .
            ' = ' . (int)$id);
        $query->where('a.' . $db->nq('status') .
            ' = ' . MOLAJO_STATUS_PUBLISHED);

        $query->where('(a.start_publishing_datetime = ' .
                $db->q($nullDate) .
                ' OR a.start_publishing_datetime <= ' .
                $db->q($now) . ')'
        );
        $query->where('(a.stop_publishing_datetime = ' .
                $db->q($nullDate) .
                ' OR a.stop_publishing_datetime >= ' .
                $db->q($now) . ')'
        );

        /** Assets Join and View Access Check */
        MolajoAccessService::setQueryViewAccess(
            $query,
            array('join_to_prefix' => 'a',
                'join_to_primary_key' => 'id',
                'asset_prefix' => 'b_assets',
                'select' => true
            )
        );

        //$db->setQuery($query->__toString());
        $db->setQuery($query);
        $rows = $db->loadObjectList();

        if ($db->getErrorNum() == 0) {

        } else {
            Services::Message()
                ->set(
                $message = Services::Language()->_('ERROR_DATABASE_QUERY') . ' ' .
                    $db->getErrorNum() . ' ' .
                    $db->getErrorMsg(),
                $type = MOLAJO_MESSAGE_TYPE_ERROR,
                $code = 500,
                $debug_location = 'ContentHelper::_get',
                $debug_object = $db
            );
            return $this->request->set('status_found', false);
        }

        if (count($rows) == 0) {
            return array();
        }

        foreach ($rows as $row) {
        }

        return $row;
    }
}
