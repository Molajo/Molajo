<?php
/**
 * Application Plugin
 *
 * @package    Molajo
 * @copyright  2014 Amy Stephen. All rights reserved.
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 */
namespace Molajo\Plugins\Application;

/**
 * Application Plugin
 *
 * @package  Molajo
 * @license  http://www.opensource.org/licenses/mit-license.html MIT License
 * @since    1.0
 */
abstract class ApplicationTitle extends ApplicationMenu
{
    /**
     * Set the Header Title
     *
     * @return  $this
     * @since   1.0.0
     * @throws  \CommonApi\Exception\RuntimeException
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
     * @throws  \CommonApi\Exception\RuntimeException
     */
    protected function setPageHeaderTitle()
    {
        $title = $this->runtime_data->application->name;
        if ($title === '') {
            $title = $this->language_controller->translateString('Molajo Application');
        }

        $this->plugin_data->page->header_title = $title;

        return $title;
    }

    /**
     * Set Heading 1
     *
     * @return  $this
     * @since   1.0.0
     * @throws  \CommonApi\Exception\RuntimeException
     */
    protected function setPageHeading1()
    {
        if (isset($this->runtime_data->resource->menuitem->parameters->criteria_title)) {
            $heading1 = $this->runtime_data->resource->menuitem->parameters->criteria_title;

        } elseif (isset($this->runtime_data->resource->parameters->criteria_title)) {
            $heading1 = $this->runtime_data->resource->parameters->criteria_title;

        } else {
            $heading1 = '';
        }

        return $heading1;
    }

    /**
     * Get Heading 2
     *
     * @return  string
     * @since   1.0.0
     * @throws  \CommonApi\Exception\RuntimeException
     */
    protected function setPageHeading2()
    {
        return ucfirst(strtolower($this->plugin_data->page->page_type));
    }
}
