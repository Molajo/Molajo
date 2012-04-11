<?php
/**
 * @package   Molajo
 * @copyright 2012 Amy Stephen. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */
namespace Molajo\Extension\Helper;

use Molajo\Application\MVC\Model\DisplayModel;
use Molajo\Application\Services;

defined('MOLAJO') or die;

/**
 * Content
 *
 * @package   Molajo
 * @subpackage  Helpers
 * @since       1.0
 */
abstract class ContentHelper
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
        $m = new DisplayModel();

        $m->query->select('a.*');
        $m->query->from($m->db->qn($content_table) . ' as a ');
        $m->query->where('a.' . $m->db->qn('id') . ' = ' . (int)$id);
        $m->query->where('a.' . $m->db->qn('status') .
            ' > ' . STATUS_UNPUBLISHED);

        $m->query->where('(a.start_publishing_datetime = ' .
                $m->db->q($m->nullDate) .
                ' OR a.start_publishing_datetime <= ' .
                $m->db->q($m->now) . ')'
        );
        $m->query->where('(a.stop_publishing_datetime = ' .
                $m->db->q($m->nullDate) .
                ' OR a.stop_publishing_datetime >= ' .
                $m->db->q($m->now) . ')'
        );

        /** Assets Join and View Access Check */
        Services::Access()->setQueryViewAccess(
            $m->query,
            $m->db,
            array('join_to_prefix' => 'a',
                'join_to_primary_key' => 'id',
                'asset_prefix' => 'b_assets',
                'select' => true
            )
        );

        //$m->db->setQuery($m->query->__toString());
        $rows = $m->runQuery();

        if (count($rows) == 0) {
            return array();
        }

        foreach ($rows as $row) {
        }

        return $row;
    }
}
