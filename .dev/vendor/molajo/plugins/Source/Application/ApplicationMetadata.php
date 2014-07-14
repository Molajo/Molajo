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
 * Application Metadata
 *
 * @package  Molajo
 * @license  http://www.opensource.org/licenses/mit-license.html MIT License
 * @since    1.0
 */
abstract class ApplicationMetadata extends ApplicationTitle
{
    /**
     * Set Page Meta Data
     *
     * @return  $this
     * @since   1.0.0
     */
    protected function setPageMeta()
    {
        $metadata = $this->initialiseMetadataSources();

        if (trim($metadata['metadata_robots']) === '') {
            $metadata['metadata_robots'] = 'follow,index';
        }

        foreach ($metadata as $key => $value) {
            if (isset($this->runtime_data->resource->menuitem)) {
                $this->runtime_data->resource->menuitem->data->metadata->$key = $value;
            } else {
                $this->runtime_data->resource->data->metadata->$key = $value;
            }
        }

        return $this;
    }

    /**
     * Process Metadata sources
     *
     * @return  array
     * @since   1.0.0
     */
    protected function initialiseMetadataSources()
    {
        $metadata = $this->initialiseMetadataObject();

        $datalist = array(
            'runtime_data->resource->menuitem->data->metadata',
            'runtime_data->resource->data->metadata'
        );

        foreach ($datalist as $item) {
            if (isset($this->$item)) {
                $metadata = $this->initialiseMetadata($this->$item, array());
            }
        }

        return $metadata;
    }

    /**
     * Process Metadata sources
     *
     * @return  array
     * @since   1.0.0
     */
    protected function initialiseMetadataObject()
    {
        $metadata                         = array();
        $metadata['metadata_title']       = null;
        $metadata['metadata_author']      = null;
        $metadata['metadata_description'] = null;
        $metadata['metadata_keywords']    = null;
        $metadata['metadata_robots']      = null;

        return $metadata;
    }

    /**
     * Intialise Metadata
     *
     * @param   array $data
     * @param   array $metadata
     *
     * @return  array
     * @since   1.0.0
     */
    protected function initialiseMetadata($data, $metadata)
    {
        if (count($data) > 0) {
            foreach ($data as $key => $value) {
                $metadata[$key] = $this->setMetadata($data, $key, $value);
            }
        }

        return $metadata;
    }

    /**
     * Set Meta Data for Key
     *
     * @param   object $data
     * @param   string $key
     * @param   mixed  $value
     *
     * @return  mixed
     * @since   1.0.0
     */
    protected function setMetadata($data, $key, $value = null)
    {
        if ($value === null) {
            if (isset($data->$key)) {
                return $data->$key;
            }
        }

        return $value;
    }

    /**
     * Set Metadata Title
     *
     * @param   array $metadata
     *
     * @return  array
     * @since   1.0.0
     */
    protected function setMetadataTitle($metadata)
    {
        if (trim($metadata['metadata_title']) === '') {
            $title = $this->plugin_data->page->header_title;

            if ($title === '') {
            } else {
                $title .= ': ';
            }

            $title .= $this->runtime_data->site->name;

            $metadata['metadata_title'] = $title;
        }

        return $metadata;
    }
}
