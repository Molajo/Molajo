<?php
namespace Molajo\MVC\Model;

use Molajo\Service\Services;

defined('NIAMBIE') or die;

/**
 * As instructed by the Delete Controller. the Delete Model uses model registry data to prepare
 * data, create and run SQL statements needed to delete data.
 *
 * @package      Niambie
 * @license      GPL v 2, or later and MIT
 * @copyright    2012 Amy Stephen. All rights reserved.
 * @since        1.0
 */
class DeleteModel extends Model
{
    /**
     * delete - deletes a row from a table
     *
     * @param   $data
     * @param   $model_registry
     *
     * @return bool
     * @since   1.0
     */
    public function delete($data, $model_registry)
    {
        $table_name = Services::Registry()->get($model_registry, 'table_name');
        $primary_prefix = Services::Registry()->get($model_registry, 'primary_prefix');
        $name_key = Services::Registry()->get($model_registry, 'name_key');
        $primary_key = Services::Registry()->get($model_registry, 'primary_key');

        /** Build Delete Statement */
        $deleteSQL = 'DELETE FROM ' . $this->db->qn($table_name);

        if (isset($data->$primary_key)) {
            $deleteSQL .= ' WHERE ' . $this->db->qn($primary_key) . ' = ' . (int) $data->$primary_key;

        } elseif (isset($data->$name_key)) {
            $deleteSQL .= ' WHERE ' . $this->db->qn($name_key) . ' = ' . $this->db->q($data->$name_key);

        } else {
            //only 1 row at a time with primary title or id key
            return false;
        }

        if (isset($data->catalog_type_id)) {
            $deleteSQL .= ' AND ' . $this->db->qn('catalog_type_id') . ' = ' . (int) $data->catalog_type_id;
        }

        $sql = $deleteSQL;

        $this->db->setQuery($sql);

        $this->db->execute();

        return true;
    }
}
