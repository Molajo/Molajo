<?php
/**
 * @package   Molajo
 * @copyright 2012 Amy Stephen. All rights reserved.
 * @license   GNU General Public License version 2 or later; see LICENSE
 */
namespace Molajo\Extension\Helper;

use Molajo\Service\Services;

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
        $m = Services::Model()->connect('Catalog');

		$m->model->query->select($m->model->db->qn('a.id'));
        $m->model->query->select('a.*');
        $m->model->query->from($m->db->qn($content_table) . ' as a ');
        $m->model->query->where('a.' . $m->db->qn('id') . ' = ' . (int)$id);
        $m->model->query->where('a.' . $m->db->qn('status') .
            ' > ' . STATUS_UNPUBLISHED);

        $m->model->query->where('(a.start_publishing_datetime = ' .
                $m->db->q($m->nullDate) .
                ' OR a.start_publishing_datetime <= ' .
                $m->db->q($m->now) . ')'
        );
        $m->model->query->where('(a.stop_publishing_datetime = ' .
                $m->db->q($m->nullDate) .
                ' OR a.stop_publishing_datetime >= ' .
                $m->db->q($m->now) . ')'
        );

        /** Catalog Join and View Access Check */
        Services::Authorisation()->setQueryViewAccess(
            $m->model->query,
            $m->db,
            array('join_to_prefix' => 'a',
                'join_to_primary_key' => 'id',
                'catalog_prefix' => 'b_catalog',
                'select' => true
            )
        );

        //$m->db->setQuery($m->model->query->__toString());

		$rows = $m->execute('loadObject');

        if (count($rows) == 0) {
            return array();
        }

        foreach ($rows as $row) {
        }

        return $row;
    }
}
