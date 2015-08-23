<?php
/**
 * Article Plugin
 *
 * @package    Molajo
 * @copyright  2014-2015 Amy Stephen. All rights reserved.
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 */
namespace Molajo\Plugins\Article;

use CommonApi\Event\DisplayEventInterface;
use CommonApi\Event\ReadEventInterface;
use Molajo\Plugins\ReadEvent;
use stdClass;

/**
 * Article Plugin
 *
 * @package  Molajo
 * @license  http://www.opensource.org/licenses/mit-license.html MIT License
 * @since    1.0
 */
final class ArticlePlugin extends ReadEvent implements ReadEventInterface, DisplayEventInterface
{
    /**
     * Article Id
     *
     * @var    integer
     * @since  1.0.0
     */
    protected $author_id;

    /**
     * Fires after read for each row
     *
     * @return  $this
     * @since   1.0.0
     * @throws  \CommonApi\Exception\RuntimeException
     */
    public function onAfterReadRow()
    {
        if ($this->checkOnAfterReadRowProcessPlugin() === false) {
            return $this;
        }

        $this->processOnAfterReadRowPlugin();

        return $this;
    }

    /**
     * Prepare Data for Injecting into Template
     *
     * @return  $this
     * @since   1.0.0
     */
    public function onGetTemplateData()
    {
        if ($this->checkOnGetTemplateDataProcessPlugin() === false) {
            return $this;
        }

        $this->processOnGetTemplateDataPlugin();

        return $this;
    }

    /**
     * Should plugin be executed for onAfterReadRow?
     *
     * @return  boolean
     * @since   1.0.0
     */
    protected function checkOnAfterReadRowProcessPlugin()
    {
        if (isset($this->runtime_data->application->id)) {
            if ((int)$this->runtime_data->application->id === 0) {
                return false;
            }
        }

        if (isset($this->controller['row']->extension_instances_id)) {
            if ((int)$this->controller['row']->extension_instances_id === 3000
                || (int)$this->controller['row']->extension_instances_id === 17000
            ) {
                return false;
            }
        }

        if (isset($this->controller['row']->catalog_catalog_type_id)) {
            if ((int)$this->controller['row']->catalog_catalog_type_id === 3000
                || (int)$this->controller['row']->catalog_catalog_type_id === 17000
            ) {
                return false;
            }
        }

        if (isset($this->controller['row']->created_by)) {
        } else {
            return false;
        }

        return true;
    }

    /**
     * Should plugin be executed for onGetTemplateData?
     *
     * @return  boolean
     * @since   1.0.0
     */
    protected function checkOnGetTemplateDataProcessPlugin()
    {
        if (strtolower($this->controller['parameters']->token->name) === 'author') {
        } else {
            return false;
        }

        if (isset($this->controller['parameters']->token->attributes['author'])) {
        } else {
            return false;
        }

        return true;
    }

    /**
     * Get Article Profile
     *
     * @return  $this
     * @since   1.0.0
     */
    protected function processOnAfterReadRowPlugin()
    {
        $this->author_id = $this->controller['row']->created_by;

        if ($this->getArticleCache() === true) {
        } else {
            $this->executeArticleQuery();
        }

        $this->setArticleData();

        $this->setArticleCache();

        return $this;
    }

    /**
     * Process onGetTemplateData
     *
     * @return  $this
     * @since   1.0.0
     */
    protected function processOnGetTemplateDataPlugin()
    {
        $this->author_id = $this->controller['parameters']->token->attributes['author'];

        if ($this->getArticleCache() === true) {
        } else {
            $this->executeArticleQuery();
            $this->setArticleCache();
        }

        $cache_key  = $this->getArticleCacheKey();

        $this->plugin_data->{strtolower($this->controller['parameters']->token->model_name)}
            = $this->plugin_data->$cache_key;

        return $this;
    }

    /**
     * Get Article Cache
     *
     * @return  boolean
     * @since   1.0.0
     */
    protected function getArticleCache()
    {
        $cache_key  = $this->getArticleCacheKey();

        if (is_object($this->plugin_data->$cache_key)) {
            return true;
        }

        if ($this->usePluginCache() === false) {
            return false;
        }

        $cache_item = $this->getPluginCache($cache_key);

        if ($cache_item->isHit() === false) {
            return false;
        }

        $this->plugin_data->$cache_key = $cache_item->getValue();

        return true;
    }

    /**
     * Execute Article Query
     *
     * @return  $this
     * @since   1.0.0
     */
    protected function executeArticleQuery()
    {
        $this->setArticleQuery();

        $author_object                 = new stdClass();
        $author_object->data           = $this->runQuery();
        $author_object->model_registry = $this->query->getModelRegistry();

        $key = $this->getArticleCacheKey();

        $this->plugin_data->$key = $author_object;

        return $this;
    }

    /**
     * Set Article Cache
     *
     * @return  $this
     * @since   1.0.0
     */
    protected function setArticleCache()
    {
        if ($this->usePluginCache() === false) {
            return $this;
        }

        $cache_key = $this->getArticleCacheKey();

        $this->setPluginCache($cache_key, $this->plugin_data->$cache_key);

        return $this;
    }

    /**
     * Set Article Profile Data into Primary Row
     *
     * @param   array $model_registry
     *
     * @return  $this
     * @since   1.0.0
     */
    protected function setArticleData(array $model_registry = array())
    {
        $cache_key            = $this->getArticleCacheKey();
        $author_object        = $this->plugin_data->$cache_key;
        $this->row            = $author_object->row;
        $this->model_registry = $author_object->model_registry;

        $this->setArticleModelRegistryFields($model_registry);
        $this->setArticleModelRegistryCustomFields($model_registry);

        return $this;
    }

    /**
     * Get Article Cache Key
     *
     * @return  string
     * @since   1.0.0
     */
    protected function getArticleCacheKey()
    {
        return 'Article-' . (int)$this->author_id;
    }

    /**
     * Get Article Profile Query Object
     *
     * @return  $this
     * @since   1.0.0
     */
    protected function setArticleQuery()
    {
        $this->setQueryController('Molajo//Model//Datasource//User.xml');

        $this->setQueryControllerDefaults(
            $process_events = 1,
            $query_object = 'item',
            $get_customfields = 1,
            $use_special_joins = 1,
            $use_pagination = 0,
            $check_view_level_access = 1,
            $get_item_children = 0
        );

        $prefix = $this->query->getModelRegistry('primary_prefix', 'a');

        $this->query->where('column', $prefix . '.id', '=', 'integer', (int)$this->author_id);

        return $this;
    }

    /**
     * Add Article Fields to Model Registry
     *
     * @param   array $model_registry
     *
     * @return  $this
     * @since   1.0.0
     */
    protected function setArticleModelRegistryFields(array $model_registry = array())
    {
        $fields            = $model_registry['fields'];
        $customfieldgroups = $model_registry['customfieldgroups'];

        $new_fields = array();

        foreach ($fields as $field) {
            if (in_array($field['name'], $customfieldgroups)) {
            } else {
                $new_fields[$field['name']] = $field;
            }
        }

        $this->setArticleFields('fields', $new_fields);

        return $this;
    }

    /**
     * Add Article Custom Fields to Model Registry
     *
     * @param   array $model_registry
     *
     * @return  $this
     * @since   1.0.0
     */
    protected function setArticleModelRegistryCustomFields(array $model_registry = array())
    {
        $customfieldgroups = $model_registry['customfieldgroups'];

        if (count($customfieldgroups) === 0) {
            return $this;
        }

        foreach ($customfieldgroups as $group) {
            $this->setArticleFields($group, $model_registry[$group]);
        }

        return $this;
    }

    /**
     * Process Article Fields by Group
     *
     * @param   string $source
     * @param   array  $fields
     *
     * @return  $this
     * @since   1.0.0
     */
    protected function setArticleFields($source, array $fields = array())
    {
        if (count($fields) === 0) {
            return $this;
        }

        foreach ($fields as $field) {

            $name            = strtolower($field['name']);
            $field['source'] = 'fields';

            if ($source === 'fields') {
                $value = $this->row->$name;
            } else {
                $value = $this->row->$source->$name;
            }

            $this->setArticleField($field, $value);
        }

        return $this;
    }

    /**
     * Save Article Fields
     *
     * @param   array $field
     * @param   mixed $value
     *
     * @return  $this
     * @since   1.0.0
     */
    protected function setArticleField($field, $value)
    {
        $new_field           = $field;
        $new_field['name']   = 'author_' . $field['name'];
        $new_field['value']  = $value;
        $new_field['source'] = 'fields';

        $this->setField($new_field['name'], $new_field['value'], $new_field);

        return $this;
    }
}
