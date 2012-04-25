<?php
/**
 * @package   Molajo
 * @copyright 2012 Amy Stephen. All rights reserved.
 * @license   GNU General Public License version 2 or later; see LICENSE
 */
namespace Molajo\Extension\Helper;

use Molajo\Application\MVC\Model\TableModel;

use Molajo\Application\Services;

defined('MOLAJO') or die;

/**
 * Catalog
 *
 * @package   	Molajo
 * @subpackage  Helper
 * @since       1.0
 */
abstract class CatalogHelper
{
    /**
     * getCatalog
     *
     * Retrieve Catalog and Catalog Type for specific id or query request
     *
     * View Access is verified in Molajo::Request to identify 403 errors
     *
     * @param    int  $catalog_id
     * @param    null $query_request
     *
     * @results  object
     * @since    1.0
     */
    public static function get($catalog_id = 0, $query_request = null,
                               $request_option = '', $source_id = 0)
    {
        $m = new TableModel('Catalog');

        $m->query->select('a.' . $m->db->qn('id'));
        $m->query->select('a.' . $m->db->qn('catalog_type_id'));
        $m->query->select('a.' . $m->db->qn('source_id'));
        $m->query->select('a.' . $m->db->qn('routable'));
        $m->query->select('a.' . $m->db->qn('sef_request'));
        $m->query->select('a.' . $m->db->qn('request'));
        $m->query->select('a.' . $m->db->qn('request_option'));
        $m->query->select('a.' . $m->db->qn('request_model'));
        $m->query->select('a.' . $m->db->qn('redirect_to_id'));
        $m->query->select('a.' . $m->db->qn('view_group_id'));
        $m->query->select('a.' . $m->db->qn('primary_category_id'));
        $m->query->select('b.' . $m->db->qn('source_table'));

        $m->query->from($m->db->qn('#__catalog') . ' as a');
        $m->query->from($m->db->qn('#__catalog_types') . ' as b');

        $m->query->where('a.' . $m->db->qn('catalog_type_id') .
            ' = b.' . $m->db->qn('id'));

        if ((int)$catalog_id > 0) {

            $m->query->where('a.' . $m->db->qn('id') . ' = ' . (int)$catalog_id);

        } else if ((int)$source_id > 0) {

            $m->query->where('a.' . $m->db->qn('request_option') .
                ' = ' . $m->db->q($request_option));
            $m->query->where('a.' . $m->db->qn('redirect_to_id') . ' = 0 ');

            $m->query->where('a.' . $m->db->qn('source_id') . ' = ' . (int)$source_id);

        } else {

            $m->query->where('(a.' . $m->db->qn('sef_request') . ' = ' . $m->db->q($query_request) .
                    ' OR a.' . $m->db->qn('request') . ' = ' . $m->db->q($query_request) . ')'
            );
        }

        $row = $m->loadObject();

        if (count($row) == 0) {
            return array();
        }

        if ((int)$source_id > 0) {

        } else if ((int)$catalog_id == 0) {

            if (Services::Registry()->get('Configuration', 'sef', 1) == 1) {
                if ($row->sef_request == $query_request) {

                } else {
                    $row->redirect_to_id = (int)$row->id;
                }

            } else {
                if ($row->request == $query_request) {

                } else {
                    $row->redirect_to_id = (int)$row->id;
                }
            }

            if ($row->id == Services::Registry()->get('Configuration', 'home_catalog_id', 0)) {
                if ($query_request == '') {
                } else {
                    $row->redirect_to_id =
                        Services::Registry()->get('Configuration', 'home_catalog_id', 0);
                }
            }
        }

		//todo: remove after testing
		$row->redirect_to_id = 0;
        return $row;
    }

    /**
     * getID
     *
     * Retrieves Catalog ID
     *
     * @param  null $catalog_type_id
     * @param  null $source_id
     *
     * @return bool|mixed
     * @since  1.0
     */
    public static function getID($catalog_type_id, $source_id)
    {
        $m = new TableModel('Catalog');

        $m->query->select('a.' . $m->db->qn('id') . ' as catalog_id');
        $m->query->where('a.' . $m->db->qn('catalog_type_id') . ' = ' . (int)$catalog_type_id);
        $m->query->where('a.' . $m->db->qn('source_id') . ' = ' . (int)$source_id);
        $m->query->where('a.' . $m->db->qn('view_group_id')
				. ' IN (' . implode(',', Services::Registry()->get('User', 'ViewGroups')) . ')');

        return $m->loadResult();
    }

    /**
     * getURL
     *
     * Retrieves URL based on Catalog ID
     *
     * @param  null $catalog_id
     *
     * @return string
     * @since  1.0
     */
    public static function getURL($catalog_id)
    {
        /** home */
        if ($catalog_id == Services::Registry()->get('Configuration', 'home_catalog_id', 0)) {
            return '';
        }

        $m = new TableModel('Catalog');

        if (Services::Registry()->get('Configuration', 'sef', 1) == 1) {
            $m->query->select('a.' . $m->db->qn('sef_request'));
        } else {
            $m->query->select('a.' . $m->db->qn('request'));
        }

        $m->query->where('a.' . $m->db->qn('id') . ' = ' . (int)$catalog_id);
        $m->query->where('a.' . $m->db->qn('view_group_id') .  ' IN (' .
                implode(',', Services::Registry()->get('User', 'ViewGroups')) . ')'
        );

        return $m->loadResult();
    }

    /**
     * getRedirectURL
     *
     * Function to retrieve catalog information for the Request or Catalog ID
     *
     * @return  string url
     * @since   1.0
     */
    public static function getRedirectURL($catalog_id)
    {
        if ((int)$catalog_id == Services::Registry()->get('Configuration', 'home_catalog_id', 0)
        ) {
            return '';
        }

        $m = new TableModel('Catalog');

        if (Services::Registry()->get('Configuration', 'sef', 1) == 0) {
            $m->query->select($m->db->qn('sef_request'));
        } else {
            $m->query->select($m->db->qn('request'));
        }

        $m->query->where($m->db->qn('id') . ' = ' . (int)$catalog_id);

        return $m->loadResult();
    }
}
