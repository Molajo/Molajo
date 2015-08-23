<?php
/**
 * Title Class for Application Plugin
 *
 * @package    Molajo
 * @copyright  2014-2015 Amy Stephen. All rights reserved.
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 */
namespace Molajo\Plugins\Application;

use Molajo\Plugins\SystemEvent;

/**
 * Title Class for Application Plugin
 *
 * @package  Molajo
 * @license  http://www.opensource.org/licenses/mit-license.html MIT License
 * @since    1.0
 */
abstract class Title extends SystemEvent
{
    /**
     * Page Type
     *
     * @var    string
     * @since  1.0.0
     */
    protected $page_type;

    /**
     * Set the Header Title
     *
     * @return  $this
     * @since   1.0.0
     */
    protected function getPageTitle()
    {
        $this->plugin_data->page->header_title = $this->setPageHeaderTitle();
        $this->plugin_data->page->heading1     = $this->setPageHeading1();
        $this->plugin_data->page->heading2     = $this->setPageHeading2();

        return $this;
    }

    /**
     * Set Page Header Title
     *
     * @return  string
     * @since   1.0.0
     */
    protected function setPageHeaderTitle()
    {
        $title = $this->runtime_data->application->name;

        if ($title === '') {
            $title = $this->language->translateString('Molajo Application');
        }

        $this->plugin_data->page->header_title = $title;

        return $title;
    }

    /**
     * Set Heading 1
     *
     * @return  $this
     * @since   1.0.0
     */
    protected function setPageHeading1()
    {
        $heading1 = '';

        if (isset($this->runtime_data->resource->parameters->criteria_title)) {
            $heading1 = $this->runtime_data->resource->parameters->criteria_title;
        }

        return $heading1;
    }

    /**
     * Get Heading 2
     *
     * @return  string
     * @since   1.0.0
     */
    protected function setPageHeading2()
    {
        return ucfirst(strtolower($this->runtime_data->route->page_type));
    }
}
