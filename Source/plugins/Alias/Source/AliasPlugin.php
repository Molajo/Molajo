<?php
/**
 * Alias Plugin
 *
 * @package    Molajo
 * @copyright  2014-2015 Amy Stephen. All rights reserved.
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 */
namespace Molajo\Plugins\Alias;

use Molajo\Plugins\UpdateEvent;
use CommonApi\Event\CreateEventInterface;
use CommonApi\Event\UpdateEventInterface;

/**
 * Alias Plugin
 *
 * @package  Molajo
 * @license  http://www.opensource.org/licenses/mit-license.html MIT License
 * @since    1.0
 */
final class AliasPlugin extends UpdateEvent implements CreateEventInterface, UpdateEventInterface
{
    /**
     * Pre-create processing
     *
     * @return  $this
     * @since   1.0.0
     */
    public function onBeforeCreate()
    {
        $this->performAliasProcessing();

        return $this;
    }

    /**
     * Pre-update processing
     *
     * @return  $this
     * @since   1.0.0
     */
    public function onBeforeUpdate()
    {
        $this->performAliasProcessing();

        return $this;
    }

    /**
     * Alias Processing
     *
     * @return  $this
     * @since   1.0.0
     */
    protected function performAliasProcessing()
    {
        if ($this->checkProcessPlugin() === false) {
            return $this;
        }

        $this->controller['row']->path = strtolower($this->controller['row']->path);

        $this->checkAlias();

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
        if (isset($this->controller['row']->title)
            && isset($this->controller['row']->alias)
            && isset($this->controller['row']->path)
        ) {
            return true;
        }

        return false;
    }

    /**
     * Create and/or validate alias value
     *
     * @return  $this
     * @since   1.0.0
     */
    protected function checkAlias()
    {
        if ($this->checkIfAliasValueSet() === true) {
            $field                          = $this->getAliasField($this->controller['row']->alias);
            $this->controller['row']->alias = $this->processField('format', $field);

        } elseif ($this->checkIfTitleValueSet() === true) {
            $field                          = $this->getAliasField($this->controller['row']->title);
            $this->controller['row']->alias = $this->processField('format', $field);
        }

        if ($this->checkIfAliasValueSet() === false) {
            $this->controller['row']->alias = $this->setUniqueId();
        }

        if ($this->verifyReservedWords() === false) {
            $this->controller['row']->alias = $this->setUniqueId();
        }

        if ($this->verifyAliasIsUnique() === false) {
            $this->controller['row']->alias = $this->setUniqueId();
        }

        return $this;
    }

    /**
     * Create Alias Field
     *
     * @param   string $alias
     *
     * @return  $array
     * @since   1.0.0
     */
    protected function getAliasField($alias)
    {
        $field          = $this->controller['model_registry']['fields']['alias'];
        $field['value'] = $alias;

        return $field;
    }

    /**
     * Is there a value set for alias?
     *
     * @return  boolean
     * @since   1.0.0
     */
    protected function checkIfAliasValueSet()
    {
        if (trim($this->controller['row']->alias) === '' || $this->controller['row']->alias === null) {
            return false;
        }

        return true;
    }

    /**
     * Is there a value set for Title?
     *
     * @return  boolean
     * @since   1.0.0
     */
    protected function checkIfTitleValueSet()
    {
        if (trim($this->controller['row']->title) === '' || $this->controller['row']->title === null) {
            return false;
        }

        return true;
    }

    /**
     * Set a unique value for alias
     *
     * @return  string
     * @since   1.0.0
     */
    protected function setUniqueId()
    {
        return uniqid();
    }

    /**
     * Verify Alias is Unique
     *
     * @return  boolean
     * @since   1.0.0
     */
    protected function verifyAliasIsUnique()
    {
        $options                 = array();
        $options['runtime_data'] = $this->runtime_data;

        $model_type = $this->controller['query']->getModelRegistry('model_type');
        $model_name = $this->controller['query']->getModelRegistry('model_name');
        $source     = 'Molajo//Model//' . $model_type . '//' . $model_name . '.xml';

        $this->setQueryController($source);

        $this->setQueryControllerDefaults(
            $process_events = 0,
            $query_object = 'result',
            $get_customfields = 0,
            $use_special_joins = 0,
            $use_pagination = 0,
            $check_view_level_access = 0,
            $get_item_children = 0
        );

        $prefix      = $this->query->getModelRegistry('primary_prefix', 'a');
        $primary_key = $this->query->getModelRegistry('primary_key', 'id');

        $this->query->setDistinct(true);
        $this->query->select($prefix . '.alias');
        $this->query->where('column', $prefix . '.' . 'status', '>', 'integer', 0);
        $this->query->where('column', $prefix . '.' . 'alias', '=', 'string', $this->controller['row']->alias);
        $this->query->where('column', $prefix . '.' . 'path', '=', 'string', $this->controller['row']->path);

        if (isset($this->controller['row']->catalog_type_id)) {
            $value = $this->controller['row']->catalog_type_id;
            $this->query->where('column', $prefix . '.' . 'catalog_type_id', '=', 'integer', $value);
        }

        if (isset($this->controller['row']->extension_id)) {
            $value = $this->controller['row']->extension_id;
            $this->query->where('column', $prefix . '.' . 'extension_id', '=', 'integer', $value);
        }

        if (isset($this->controller['row']->extension_instance_id)) {
            $value = $this->controller['row']->extension_instance_id;
            $this->query->where('column', $prefix . '.' . 'extension_instance_id', '=', 'integer', $value);
        }

        if (isset($this->controller['row']->$primary_key)) {
            $value = $this->controller['row']->$primary_key;
            $this->query->where('column', $prefix . '.' . $primary_key, '<>', 'integer', $value);
        }

        $results = $this->runQuery();

        if ($results === null) {
            return true;
        }

        return false;
    }

    /**
     * Verify Reserved Words not used in Alias
     *
     * @return  boolean
     * @since   1.0.0
     */
    protected function verifyReservedWords()
    {
        if (isset($this->runtime_data->reserved)
            && isset($this->runtime_data->reserved->filters)
            && isset($this->runtime_data->reserved->actions)
        ) {
        } else {
            return $this;
        }

        foreach ($this->runtime_data->reserved->filters as $key) {
            if ($this->controller['row']->alias === $key) {
                return false;
            }
        }

        foreach ($this->runtime_data->reserved->actions as $crud => $values) {
            foreach ($values as $key => $value) {
                if ($this->controller['row']->alias === $key) {
                    return false;
                }
            }
        }

        return true;
    }
}
