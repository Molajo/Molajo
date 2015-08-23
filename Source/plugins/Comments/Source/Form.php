<?php
/**
 * Get and Set Comments Form Data
 *
 * @package    Molajo
 * @copyright  2014-2015 Amy Stephen. All rights reserved.
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 */
namespace Molajo\Plugins\Comments;

use stdClass;

/**
 * Get and Set Comments Form Data
 *
 * @package     Molajo
 * @license     http://www.opensource.org/licenses/mit-license.html MIT License
 * @since       1.0
 */
abstract class Form extends Base
{
    /**
     * Retrieve Data for Comment Form
     *
     * @return  $this
     * @since   1.0.0
     * @throws  \CommonApi\Exception\RuntimeException
     */
    protected function setCommentsForm()
    {
        $this->setCommentsFormData();
        $this->setCommentsFormModelRegistry();

        return $this;
    }

    /**
     * Build Comments Form Data
     *
     * @return  stdClass
     * @since   1.0.0
     */
    protected function setCommentsFormData()
    {
        $temp_row                = new stdClass();
        $temp_row->comments_open = (int)$this->comments_open;
        $temp_row->source_id     = (int)$this->source_id;

        $this->plugin_data->{$this->comments_form}->data = array($temp_row);

        return $this;
    }

    /**
     * Set Model Registry for Comments Heading Data
     *
     * @return  object
     * @since   1.0.0
     */
    protected function setCommentsFormModelRegistry()
    {
        $fields                  = array();
        $fields['comments_open'] = $this->setModelRegistryField('comments_open', 'integer');
        $fields['source_id']     = $this->setModelRegistryField('source_id', 'integer');

        $this->plugin_data->{$this->comments_form}->model_registry           = array();
        $this->plugin_data->{$this->comments_form}->model_registry['fields'] = $fields;

        return $this;
    }

    /**
     * Place Comment Form Data into Query Results and Model Registry objects for Rendering View
     *
     * @return  $this
     * @since   1.0.0
     */
    protected function getCommentsForm()
    {
        $this->controller['query_results']
            = $this->plugin_data->{$this->comments_form}->data;

        $this->controller['model_registry']
            = $this->plugin_data->{$this->comments_form}->model_registry;

        return $this;
    }
}
