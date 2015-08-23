<?php
/**
 * Base Comments Plugin Data
 *
 * @package    Molajo
 * @copyright  2014-2015 Amy Stephen. All rights reserved.
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 */
namespace Molajo\Plugins\Comments;

use Molajo\Plugins\DisplayEvent;
use stdClass;

/**
 * Base Comments Plugin Data
 *
 * @package     Molajo
 * @license     http://www.opensource.org/licenses/mit-license.html MIT License
 * @since       1.0
 */
abstract class Base extends DisplayEvent
{
    /**
     * Source ID - for primary data
     *
     * @var    integer
     * @since  1.0.0
     */
    protected $source_id = null;

    /**
     * Start Publishing Date
     *
     * @var    string
     * @since  1.0.0
     */
    protected $start_publishing_date = null;

    /**
     * Open Days
     *
     * @var    integer
     * @since  1.0.0
     */
    protected $open_days = 0;

    /**
     * Comments Open
     *
     * @var    integer
     * @since  1.0.0
     */
    protected $comments_open = 0;

    /**
     * Comments Name
     *
     * @var    string
     * @since  1.0.0
     */
    protected $comments_name = null;

    /**
     * Comments Form Name
     *
     * @var    string
     * @since  1.0.0
     */
    protected $comments_form = null;

    /**
     * Comments Heading Name
     *
     * @var    string
     * @since  1.0.0
     */
    protected $comments_heading = null;

    /**
     * Comments List Name
     *
     * @var    string
     * @since  1.0.0
     */
    protected $comments_list = null;

    /**
     * Comments View
     *
     * @var    integer
     * @since  1.0.0
     */
    protected $comments_view = '';

    /**
     * Initialise Plugindata for Comments
     *
     * @return  $this
     * @since   1.0.0
     */
    protected function initialiseCommentsPlugin()
    {
        $this->plugin_data->{$this->comments_name}                    = new stdClass();
        $this->plugin_data->{$this->comments_name}->parameters        = new stdClass();
        $this->plugin_data->{$this->comments_form}                    = new stdClass();
        $this->plugin_data->{$this->comments_form}->data              = new stdClass();
        $this->plugin_data->{$this->comments_form}->model_registry    = new stdClass();
        $this->plugin_data->{$this->comments_heading}                 = new stdClass();
        $this->plugin_data->{$this->comments_heading}->data           = new stdClass();
        $this->plugin_data->{$this->comments_heading}->model_registry = new stdClass();
        $this->plugin_data->{$this->comments_list}                    = new stdClass();
        $this->plugin_data->{$this->comments_list}->data              = new stdClass();
        $this->plugin_data->{$this->comments_list}->model_registry    = new stdClass();

        return $this;
    }

    /**
     * Set Model Registry Field
     *
     * @param   string $name
     * @param   string $type
     *
     * @return  array
     * @since   1.0.0
     */
    protected function setModelRegistryField($name, $type = 'string')
    {
        $field = array();

        $field['name']       = $name;
        $field['type']       = $type;
        $field['calculated'] = 1;

        return $field;
    }
}
