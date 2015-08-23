<?php
/**
 * Catalog Plugin
 *
 * @package    Molajo
 * @copyright  2014-2015 Amy Stephen. All rights reserved.
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 */
namespace Molajo\Plugins\Catalog;

use CommonApi\Event\CreateEventInterface;
use CommonApi\Event\DeleteEventInterface;
use CommonApi\Event\UpdateEventInterface;
use Molajo\Plugins\DeleteEvent;

/**
 * Catalog Plugin
 *
 * @package  Molajo
 * @license  http://www.opensource.org/licenses/mit-license.html MIT License
 * @since    1.0
 */
final class CatalogPlugin extends DeleteEvent implements CreateEventInterface, UpdateEventInterface, DeleteEventInterface
{
    /**
     * Pre-create processing
     *
     * @return  $this
     * @since   1.0.0
     */
    public function onAfterCreate()
    {
        if ($this->checkProcessPlugin() === false) {
            return $this;
        }

        $this->createCatalog(1);
        $this->createCatalog(2);

        return $this;
    }

    /**
     * After update processing
     *
     * @return  $this
     * @since   1.0.0
     */
    public function onAfterUpdate()
    {
        if ($this->checkProcessPlugin() === false) {
            return $this;
        }

        return $this;

    }

    /**
     * After Delete processing
     *
     * @return  $this
     * @since   1.0.0
     */
    public function onAfterDelete()
    {
        if ($this->checkProcessPlugin() === false) {
            return $this;
        }

        return $this;
    }

    /**
     * Check if the plugin should run
     *
     * @return  boolean
     * @since   1.0.0
     */
    protected function checkProcessPlugin()
    {
        if (isset($this->controller['row']->catalog_type_id)) {
        } else {
            return false;
        }

        if (isset($this->controller['row']->path)) {
        } else {
            return false;
        }

        if ($this->controller['query']->getModelRegistry('table_name') === '#__catalog') {
            return false;
        }

        return true;
    }

    /**
     * Create Catalog Row
     *
     * @param   integer $application_id
     *
     * @return  $this
     * @since   1.0.0
     */
    protected function createCatalog($application_id)
    {
        $this->connectCatalogController('Molajo//Model//Datasource//Catalog.xml', 'Create');

        $row = $this->query->initialiseRow();
        $row = $this->setInsertValues($row, $application_id);
        $this->query->setInsertStatement($row);

        $row = $this->runQuery('insertData');

        $extension_instance_id = $row->extension_instance_id;

        $model = 'Molajo//Model//Datasource//Applicationextensioninstances.xml';
        $this->createApplicationSiteExtension($model, $extension_instance_id, $application_id);

        $model = 'Molajo//Model//Datasource//Siteextensioninstances.xml';
        $this->createApplicationSiteExtension($model, $extension_instance_id);

        return $this;
    }

    /**
     * Set Language Field Values for Insert
     *
     * @param   object  $row
     * @param   integer $application_id
     *
     * @return  object
     * @since   1.0.0
     */
    protected function setInsertValues($row, $application_id = 0)
    {
        if (isset($this->controller['row']->extension_instance_id)) {
            $extension_instance_id = $this->controller['row']->extension_instance_id;
        } else {
            $extension_instance_id = $this->controller['row']->id;
        }

        $row->id              = null;
        $row->application_id  = $application_id;
        $row->catalog_type_id = $this->controller['row']->catalog_type_id;
        $row->source_id       = $this->controller['row']->id;
        $row->enabled         = 1;

        if (trim($this->controller['row']->path) === '') {
            $sef_request = $this->controller['row']->alias;
        } else {
            $sef_request = $this->controller['row']->path . '/' . $this->controller['row']->alias;
        }

        $row->sef_request = strtolower($sef_request);

        if (isset($this->controller['row']->page_type)) {
            $row->page_type = $this->controller['row']->page_type;
        } else {
            $row->page_type = 'Item';
        }

        $row->extension_instance_id = $extension_instance_id;
        $row->view_group_id         = 1;
        $row->primary_category_id   = 12;

        return $row;
    }

    /**
     * Create Application and Site Filters, if not already existing
     *
     * @param   string  $model
     * @param   integer $extension_instance_id
     * @param   integer $application_id
     *
     * @return  $this
     * @since   1.0.0
     */
    protected function createApplicationSiteExtension($model, $extension_instance_id, $application_id = 0)
    {
        if ($this->existsApplicationSiteExtension($model, $extension_instance_id, $application_id) === true) {
            return $this;
        }

        $this->connectCatalogController($model, 'Create');

        $row = $this->query->initialiseRow();

        if ($this->query->getModelRegistry('table_name') === '#__application_extension_instances') {
            $row->application_id = $application_id;
        } else {
            $row->site_id = $this->runtime_data->site->id;
        }

        $row->extension_instance_id = $extension_instance_id;

        $this->query->setInsertStatement($row);

        $this->runQuery('insertData');

        return $this;
    }

    /**
     * Check if the row exists for Application or Site Filter
     *
     * @param   string  $model
     * @param   integer $extension_instance_id
     * @param   integer $application_id
     *
     * @return  $this
     * @since   1.0.0
     */
    protected function existsApplicationSiteExtension($model, $extension_instance_id, $application_id = 0)
    {
        $this->connectCatalogController($model, 'Read');

        $prefix = $this->query->getModelRegistry('primary_prefix', 'a');

        if ($this->query->getModelRegistry('table_name') === '#__application_extension_instances') {
            $field_name = $prefix . '.application_id';
            $value      = $application_id;
        } else {
            $field_name = $prefix . '.site_id';
            $value      = $this->runtime_data->site->id;
        }

        $this->query->select($prefix . '.extension_instance_id');
        $this->query->where('column', $field_name, '=', 'integer', $value);
        $this->query->where('column', $prefix . '.extension_instance_id', '=', 'integer', $extension_instance_id);

        $results = $this->runQuery();

        if ((int)$results === $extension_instance_id) {
            return true;
        }

        return false;
    }

    /**
     * Get Query Connection for Catalog
     *
     * @param   string $model
     * @param   string $crud_type
     *
     * @return  $this
     * @since   1.0.0
     */
    protected function connectCatalogController($model, $crud_type = 'Read')
    {
        $this->setQueryController($model, $crud_type);

        $this->setQueryControllerDefaults(
            $process_events = 0,
            $query_object = 'result',
            $get_customfields = 0,
            $use_special_joins = 0,
            $use_pagination = 0,
            $check_view_level_access = 0,
            $get_item_children = 0
        );

        return $this;
    }
}
