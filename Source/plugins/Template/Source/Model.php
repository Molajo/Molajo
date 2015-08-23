<?php
/**
 * Model
 *
 * @package    Molajo
 * @copyright  2014-2015 Amy Stephen. All rights reserved.
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 */
namespace Molajo\Plugins\Template;

/**
 * Set Model for Template
 *
 * @package     Molajo
 * @license     http://www.opensource.org/licenses/mit-license.html MIT License
 * @since       1.0
 */
abstract class Model extends Data
{
    /**
     * Model Type
     *
     * @var    string
     * @since  1.0.0
     */
    protected $model_type = '';

    /**
     * Model Name
     *
     * @var    string
     * @since  1.0.0
     */
    protected $model_name = '';

    /**
     * Field Name
     *
     * @var    string
     * @since  1.0.0
     */
    protected $field_name = '';

    /**
     * Set Model Type, Model Name and Field Name values used for data retrieval
     *
     * @return  array
     * @since   1.0.0
     */
    protected function setRenderTokenModel()
    {
        $this->model_type = strtolower(trim($this->setModelType()));
        $this->model_name = strtolower(trim($this->setModelName()));
        $this->field_name = strtolower(trim($this->setFieldName()));

        if ($this->model_type === '' || $this->model_name === '') {
            $this->setSpecialModelValues();
        }

        $this->controller['parameters']->token->model_type = $this->model_type;
        $this->controller['parameters']->token->model_name = $this->model_name;
        $this->controller['parameters']->token->field_name = $this->field_name;

        return $this;
    }

    /**
     * Set Model Type
     *
     * @return  string
     * @since   1.0.0
     */
    protected function setModelType()
    {
        if (isset($this->controller['parameters']->token->attributes['model_type'])) {
            if ($this->controller['parameters']->token->attributes['model_type'] === null
                || trim($this->controller['parameters']->token->attributes['model_type']) === ''
            ) {
            } else {
                return $this->controller['parameters']->token->attributes['model_type'];
            }
        }

        if (isset($this->controller['parameters']->token->extension->parameters->model_type)) {
            if ($this->controller['parameters']->token->extension->parameters->model_type === null
                || trim($this->controller['parameters']->token->extension->parameters->model_type) === ''
            ) {
            } else {
                return $this->controller['parameters']->token->extension->parameters->model_type;
            }
        }

        if (isset($this->plugin_data->render->extension->parameters->model_type)) {
            if ($this->plugin_data->render->extension->parameters->model_type === null
                || trim($this->plugin_data->render->extension->parameters->model_type) === ''
            ) {
            } else {
                return $this->plugin_data->render->extension->parameters->model_type;
            }
        }

        if (isset($this->plugin_data->render->extension->menuitem->parameters->model_type)) {
            if ($this->plugin_data->render->extension->menuitem->parameters->model_type === null
                || trim($this->plugin_data->render->extension->menuitem->parameters->model_type) === ''
            ) {
            } else {
                return $this->plugin_data->render->extension->menuitem->parameters->model_type;
            }
        }

        return 'plugin_data';
    }

    /**
     * Set Model Name - can be overridden on the include statement (will be in token)
     *
     * @return  string
     * @since   1.0.0
     */
    protected function setModelName()
    {
        if ($this->model_type === 'dataobject') {
            $this->model_type = 'plugin_data';
        }

        if (isset($this->controller['parameters']->token->attributes['model_name'])) {
            if ($this->controller['parameters']->token->attributes['model_name'] === null
                || trim($this->controller['parameters']->token->attributes['model_name']) === ''
            ) {
            } else {
                return $this->controller['parameters']->token->attributes['model_name'];
            }
        }

        if (isset($this->controller['parameters']->token->extension->parameters->model_name)) {
            if ($this->controller['parameters']->token->extension->parameters->model_name === null
                || trim($this->controller['parameters']->token->extension->parameters->model_name) === ''
            ) {
            } else {
                return $this->controller['parameters']->token->extension->parameters->model_name;
            }
        }

        if (isset($this->plugin_data->render->extension->parameters->model_name)) {
            if ($this->plugin_data->render->extension->parameters->model_name === null
                || trim($this->plugin_data->render->extension->parameters->model_name) === ''
            ) {
            } else {
                return $this->plugin_data->render->extension->parameters->model_name;
            }
        }

        if (isset($this->plugin_data->render->extension->menuitem->parameters->model_name)) {
            if ($this->plugin_data->render->extension->menuitem->parameters->model_name === null
                || trim($this->plugin_data->render->extension->menuitem->parameters->model_name) === ''
            ) {
            } else {
                return $this->plugin_data->render->extension->menuitem->parameters->model_name;
            }
        }

        return '';
    }

    /**
     * Set Field Name
     *
     * @return  string
     * @since   1.0.0
     */
    protected function setFieldName()
    {
        if (isset($this->controller['parameters']->token->attributes['field_name'])) {
            return $this->controller['parameters']->token->attributes['field_name'];
        }

        return '';
    }

    /**
     * Special Cases
     *
     * @return  $this
     * @since   1.0.0
     */
    protected function setSpecialModelValues()
    {
        $methods = array(
            'setPluginDataModelName',
            'setPrimaryModelName',
            'setEmptyData'
        );

        foreach ($methods as $method) {
            if ($this->model_type === '' || $this->model_name === '') {
                $this->$method();
            }
        }

        return '';
    }

    /**
     * Set Plugin Data Template View
     *
     * @return  $this
     * @since   1.0.0
     */
    protected function setPluginDataModelName()
    {
        $name = strtolower($this->controller['parameters']->token->name);

        if (isset($this->plugin_data->$name)) {
            $this->model_type = 'plugin_data';
            $this->model_name = $name;
        }

        return $this;
    }

    /**
     * Set Plugin Data Template View
     *
     * @return  $this
     * @since   1.0.0
     */
    protected function setPrimaryModelName()
    {
        if ($this->model_type === 'primary') {
            $this->model_type = 'runtime_data';
            $this->model_name = 'resource';
        }

        return $this;
    }

    /**
     * No data
     *
     * @return  $this
     * @since   1.0.0
     */
    protected function setEmptyData()
    {
        $this->model_type = '';
        $this->model_name = '';

        return $this;
    }
}
