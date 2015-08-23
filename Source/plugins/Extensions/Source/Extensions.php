<?php
/**
 * Extension Support for Extensions Plugin
 *
 * @package    Molajo
 * @copyright  2014-2015 Amy Stephen. All rights reserved.
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 */
namespace Molajo\Plugins\Extensions;

/**
 * Extension Support for Extensions Plugin
 *
 * @package     Molajo
 * @license     http://www.opensource.org/licenses/mit-license.html MIT License
 * @since       1.0
 */
abstract class Extensions extends ExtensionInstances
{
    /**
     * Process Extension, install if needed
     *
     * @param   integer $catalog_type_id
     * @param   string  $catalog_type
     * @param   string  $extension_name
     * @param   string  $namespace
     * @param   string  $theme
     *
     * @return  $this
     * @since   1.0.0
     */
    public function processExtension($catalog_type_id, $catalog_type, $extension_name, $namespace, $theme = '')
    {
        if ($this->searchExtension($catalog_type_id, $extension_name) === true) {
            return $this;
        }

        if ($this->checkExtension($catalog_type_id, $extension_name, $namespace) === false) {
            $this->insertExtension($catalog_type_id, $extension_name, $namespace, $theme);
        }

        if ($this->checkExtensionInstance($catalog_type_id, $extension_name, $namespace) === false) {
            $this->insertExtensionInstance($catalog_type_id, $extension_name, $catalog_type, $namespace, $theme);
        }

        return $this;
    }

    /**
     * Has extension already been installed? (only lists enabled)
     *
     * @param   integer $catalog_type_id
     * @param   string  $extension_name
     *
     * @return  boolean
     * @since   1.0.0
     */
    protected function searchExtension($catalog_type_id, $extension_name)
    {
        if (isset($this->runtime_data->reference_data->extensions
                      ->extensions[$catalog_type_id]->names[strtolower($extension_name)])) {
            return true;
        }

        return false;
    }

    /**
     * Check if it is already in the database
     *
     * @param   integer $catalog_type_id
     * @param   string  $extension_name
     * @param   string  $namespace
     *
     * @return  boolean
     * @since   1.0.0
     */
    protected function checkExtension($catalog_type_id, $extension_name, $namespace)
    {
        $this->setQueryController('Molajo//Model//Datasource//Extensions.xml');

        $this->setQueryControllerDefaults(
            $process_events = 0,
            $query_object = 'item',
            $get_customfields = 0,
            $use_special_joins = 0,
            $use_pagination = 0,
            $check_view_level_access = 0,
            $get_item_children = 0
        );

        $prefix = $this->query->getModelRegistry('primary_prefix', 'a');

        $this->query->where('column', $prefix . '.catalog_type_id', '=', 'integer', $catalog_type_id);
        $this->query->where('column', $prefix . '.title', '=', 'string', $extension_name);
        $this->query->where('column', $prefix . '.namespace', '=', 'string', $namespace);

        $item = $this->runQuery();

        if (is_object($item)) {
        } else {
            return false;
        }

        $this->parent_id = $item->id;

        return true;
    }

    /**
     * Insert Extension
     *
     * @param   integer $catalog_type_id
     * @param   string  $extension_name
     * @param   string  $namespace
     * @param   string  $theme
     *
     * @return  $this
     * @since   1.0.0
     */
    protected function insertExtension($catalog_type_id, $extension_name, $namespace, $theme)
    {
        $this->setQueryController('Molajo//Model//Datasource//Extensions.xml', 'Create');

        $this->setQueryControllerDefaults(
            $process_events = 0,
            $query_object = 'result',
            $get_customfields = 0,
            $use_special_joins = 0,
            $use_pagination = 0,
            $check_view_level_access = 0,
            $get_item_children = 0
        );

        $row = $this->query->initialiseRow();
        $this->setInsertValuesExtension($row, $catalog_type_id, $extension_name, $namespace, $theme);
        $this->query->setInsertStatement($row);

        $row = $this->runQuery('insertData');

        $this->parent_id = $row->id;

        return $this;
    }

    /**
     * Set row values for inserting extension
     *
     * @param   object  $row
     * @param   integer $catalog_type_id
     * @param   string  $extension_name
     * @param   string  $namespace
     * @param   string  $theme
     *
     * @return  object
     * @since   1.0.0
     */
    protected function setInsertValuesExtension($row, $catalog_type_id, $extension_name, $namespace, $theme = '')
    {
        $row->id                = null;
        $row->extension_site_id = 1;
        $row->catalog_type_id   = $catalog_type_id;
        $row->title             = ucfirst(strtolower($extension_name));
        $row->subtitle          = $theme;
        $row->namespace         = addslashes($namespace);
        $row->language          = $this->runtime_data->application->parameters->language;
        $row->translation_of_id = 0;
        $row->ordering          = 0;

        return $row;
    }
}
