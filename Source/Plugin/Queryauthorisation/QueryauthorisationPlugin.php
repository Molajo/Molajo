<?php
/**
 * Query Authorisation Plugin
 *
 * @package    Molajo
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2014 Amy Stephen. All rights reserved.
 */
namespace Molajo\Plugin\Queryauthorisation;

use Molajo\Plugin\ReadEventPlugin;
use CommonApi\Event\ReadInterface;

/**
 * Query Authorisation Plugin
 *
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2014 Amy Stephen. All rights reserved.
 * @since      1.0
 */
class Queryauthorisation extends ReadEventPlugin implements ReadInterface
{
    /**
     * Append Criteria for Authorisation to Query Object
     *
     * @return  $this
     * @since   1.0
     */
    public function onBeforeRead()
    {
        if (isset($this->model_registry['check_view_level_access'])) {
            $check = $this->model_registry['check_view_level_access'];
        } else {
            $check = 1;
        }

        if ((int)$check === 0) {
            return $this;
        }

        if (is_array($this->permissions->view_groups)) {
        } else {
            return $this->query;
        }

        if (isset($model_registry['select'])
            && $model_registry['select'] === true
        ) {
            $this->query->select(
                $this->db->qn('authorisation') .
                '.' .
                $this->db->qn('view_group_id')
            );

            $this->query->select(
                $this->db->qn('authorisation') .
                '.' .
                $this->db->qn('id') .
                ' as ' .
                $this->db->qn('catalog_id')
            );
        }

        $this->query->from(
            $this->db->qn('#__catalog') .
            ' as ' .
            $this->db->qn('authorisation')
        );

        $this->query->where(
            $this->db->qn('authorisation') .
            '.' .
            $this->db->qn('source_id') .
            ' = ' .
            $this->db->qn($model_registry['primary_prefix']) .
            '.' .
            $this->db->qn($model_registry['primary_key'])
        );

        $this->query->where(
            $this->db->qn('authorisation') .
            '.' . $this->db->qn('catalog_type_id') .
            ' = ' .
            $this->db->qn($model_registry['primary_prefix']) .
            '.' .
            $this->db->qn('catalog_type_id')
        );

        $this->query->where(
            $this->db->qn('authorisation') .
            '.' . $this->db->qn('application_id') .
            ' = ' .
            $this->application_id
        );

        $vg = implode(',', array_unique($this->permissions->view_groups));

        $this->query->where(
            $this->db->qn('authorisation') .
            '.' .
            $this->db->qn('view_group_id') . ' IN (' . $vg . ')'
        );

        $this->query->where(
            $this->db->qn('authorisation') .
            '.' .
            $this->db->qn('redirect_to_id') .
            ' = 0'
        );

        return $this;
    }
}
