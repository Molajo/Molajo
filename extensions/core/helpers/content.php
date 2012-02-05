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
 * @subpackage  Content
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
        $db = MolajoController::getDbo();
        $query = $db->getQuery(true);
        $date = MolajoController::getDate();
        $now = $date->toMySQL();
        $nullDate = $db->getNullDate();

        $query->select('a.' . $db->namequote('id'));
        $query->select('a.' . $db->namequote('extension_instance_id'));
        $query->select('a.' . $db->namequote('asset_type_id'));
        $query->select('a.' . $db->namequote('title'));
        $query->select('a.' . $db->namequote('subtitle'));
        $query->select('a.' . $db->namequote('path'));
        $query->select('a.' . $db->namequote('alias'));
        $query->select('a.' . $db->namequote('status'));
        $query->select('a.' . $db->namequote('start_publishing_datetime'));
        $query->select('a.' . $db->namequote('stop_publishing_datetime'));
        $query->select('a.' . $db->namequote('modified_datetime'));
        $query->select('a.' . $db->namequote('custom_fields'));
        $query->select('a.' . $db->namequote('parameters'));
        $query->select('a.' . $db->namequote('metadata'));
        $query->select('a.' . $db->namequote('language'));
        $query->select('a.' . $db->namequote('translation_of_id'));
        $query->select('a.' . $db->namequote('ordering'));

        $query->from($db->namequote('#' . $content_table) . ' as a');
        $query->where('a.' . $db->namequote('id') .
            ' = ' . (int)$id);
        $query->where('a.' . $db->namequote('status') .
            ' = ' . MOLAJO_STATUS_PUBLISHED);

        $query->where('(a.start_publishing_datetime = ' .
            $db->Quote($nullDate) .
            ' OR a.start_publishing_datetime <= ' .
            $db->Quote($now) . ')'
        );
        $query->where('(a.stop_publishing_datetime = ' .
            $db->Quote($nullDate) .
            ' OR a.stop_publishing_datetime >= ' .
            $db->Quote($now) . ')'
        );

        /** Assets Join and View Access Check */
        MolajoAccess::setQueryViewAccess(
            $query,
            array('join_to_prefix' => 'a',
                'join_to_primary_key' => 'id',
                'asset_prefix' => 'b_assets',
                'select' => true
            )
        );

        $db->setQuery($query->__toString());
        $rows = $db->loadObjectList();

        if ($db->getErrorNum() == 0) {

        } else {
            MolajoController::getApplication()
                ->setMessage(
                $message = MolajoTextHelper::_('ERROR_DATABASE_QUERY').' '.
                    $db->getErrorNum().' '.
                    $db->getErrorMsg(),
                $type = MOLAJO_MESSAGE_TYPE_ERROR,
                $code = 500,
                $debug_location = 'MolajoContentHelper::_get',
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
