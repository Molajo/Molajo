<?php
/**
 * Extension Instances Support for Extensions Plugin
 *
 * @package    Molajo
 * @copyright  2014-2015 Amy Stephen. All rights reserved.
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 */
namespace Molajo\Plugins\Extensions;

/**
 * Extension Instances Support for Extensions Plugin
 *
 * @package     Molajo
 * @license     http://www.opensource.org/licenses/mit-license.html MIT License
 * @since       1.0
 */
abstract class ExtensionInstances extends Base
{
   /**
    * Check if Extension Instance is already in the Database
    *
    * @param   integer $catalog_type_id
    * @param   string  $extension_name
    * @param   string  $namespace
    *
    * @return  boolean
    * @since   1.0.0
    */
    protected function checkExtensionInstance($catalog_type_id, $extension_name, $namespace)
    {
        $this->setQueryController('Molajo//Model//Datasource//Extensioninstances.xml');

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
        $this->query->where('column', $prefix . '.extension_id', '=', 'integer', $this->parent_id);
        $this->query->where('column', $prefix . '.title', '=', 'string', $extension_name);
        $this->query->where('column', $prefix . '.namespace', '=', 'string', $namespace);

        $item = $this->runQuery();

        if (is_object($item)) {
            return true;
        }

        return false;
    }

    /**
     * Insert Extension Instance
     *
     * @param   integer $catalog_type_id
     * @param   string  $extension_name
     * @param   string  $catalog_type
     * @param   string  $namespace
     * @param   string  $theme
     *
     * @return  $this
     * @since   1.0.0
     */
    protected function insertExtensionInstance($catalog_type_id, $extension_name, $catalog_type, $namespace, $theme)
    {
        $this->setQueryController('Molajo//Model//Datasource//Extensioninstances.xml', 'Create');

        $this->setQueryControllerDefaults(
            $process_events = 1,
            $query_object = 'result',
            $get_customfields = 0,
            $use_special_joins = 0,
            $use_pagination = 0,
            $check_view_level_access = 0,
            $get_item_children = 0
        );

        $row = $this->query->initialiseRow();

        $this->setInsertValuesExtensionInstance(
            $row,
            $catalog_type_id,
            $extension_name,
            $catalog_type,
            $namespace,
            $theme
        );

        $this->query->setInsertStatement($row);

        $this->runQuery('insertData');

        return $this;
    }

    /**
     * Set row values for inserting extension instance
     *
     * @param   object  $row
     * @param   integer $catalog_type_id
     * @param   string  $extension_name
     * @param   string  $catalog_type
     * @param   string  $namespace
     * @param   string  $theme
     *
     * @return  object
     * @since   1.0.0
     */
    protected function setInsertValuesExtensionInstance(
        $row,
        $catalog_type_id,
        $extension_name,
        $catalog_type,
        $namespace,
        $theme)
    {
        $row->id                        = null;
        $row->extension_id              = $this->parent_id;
        $row->catalog_type_id           = $catalog_type_id;
        $row->title                     = ucfirst(strtolower($extension_name));
        $row->subtitle                  = ucfirst(strtolower($theme));
        $row->namespace                 = $namespace;
        $row->path                      = strtolower($catalog_type);
        $row->menu                      = '';
        $row->page_type                 = 'item';
        $row->content_text              = null;
        $row->protected                 = 0;
        $row->featured                  = 0;
        $row->stickied                  = 0;
        $row->status                    = 0;
        $row->start_publishing_datetime = null;
        $row->stop_publishing_datetime  = null;
        $row->version                   = 1;
        $row->version_of_id             = 0;
        $row->status_prior_to_version   = null;

        //todo move these into common code
        $row->created_datetime  = $this->date->getDate();
        $row->created_by        = $this->runtime_data->user->id;
        $row->modified_datetime = $this->date->getDate();
        $row->modified_by       = $this->runtime_data->user->id;
        $row->author_id         = $this->runtime_data->user->id;

        $row->checked_out_datetime = null;
        $row->checked_out_by       = null;

        $row->root                 = null;
        $row->parent_id            = $this->parent_id;
        $row->lft                  = null;
        $row->rgt                  = null;
        $row->lvl                  = null;
        $row->ordering             = 0;

        $row->home                 = null;

        $row->customfields         = '{}';
        $row->parameters           = '{}';
        $row->metadata             = '{}';

        $row->language             = $this->runtime_data->application->parameters->language;
        $row->translation_of_id    = 0;

        return $row;
    }
}
