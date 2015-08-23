<?php
/**
 * Base Customfields Class
 *
 * @package    Molajo
 * @copyright  2014-2015 Amy Stephen. All rights reserved.
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 */
namespace Molajo\Plugins\Customfields;

use Molajo\Plugins\ReadEvent;

/**
 * Base Customfields Class
 *
 * @package  Molajo
 * @license  http://www.opensource.org/licenses/mit-license.html MIT License
 * @since    1.0
 */
abstract class Base extends ReadEvent
{
    /**
     * Standard Page Types
     *
     * @var    array
     * @since  1.0.0
     */
    protected $standard_page_types = array('form', 'item', 'list', 'menuitem');

    /**
     * Page Type
     *
     * @var    string
     * @since  1.0.0
     */
    protected $page_type = null;

    /**
     * Content Data
     *
     * @var    object
     * @since  1.0.0
     */
    protected $content_data = null;

    /**
     * Extension Data
     *
     * @var    object
     * @since  1.0.0
     */
    protected $extension_data = null;

    /**
     * Application Data
     *
     * @var    object
     * @since  1.0.0
     */
    protected $application_data = null;

    /**
     * Model Registry Merged (Application, Extension, Content)
     *
     * @var    array
     * @since  1.0.0
     */
    protected $model_registry_merged = array();

    /**
     * Set Page Type
     *
     * @return  $this
     * @since   1.0.0
     */
    protected function setPageType()
    {
        $this->page_type = '';

        if (isset($this->runtime_data->route->page_type)) {
            $this->page_type = strtolower($this->runtime_data->route->page_type);
        }

        if ($this->page_type === 'edit' || $this->page_type === 'new') {
            $this->page_type = 'form';

        } elseif (in_array($this->page_type, $this->standard_page_types) === true) {

        } else {
            $this->page_type = 'menuitem';
        }

        return $this;
    }
}
