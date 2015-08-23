<?php
/**
 * Metadata Class for Application Plugin
 *
 * @package    Molajo
 * @copyright  2014-2015 Amy Stephen. All rights reserved.
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 */
namespace Molajo\Plugins\Application;

use stdClass;

/**
 * Metadata Class for Application Plugin
 *
 * @package  Molajo
 * @license  http://www.opensource.org/licenses/mit-license.html MIT License
 * @since    1.0
 */
abstract class Metadata extends Title
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
            $this->runtime_data->resource->data->metadata->$key = $value;
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

        if (isset($this->runtime_data->resource->data->metadata)) {
            $metadata = $this->setMetadata($this->runtime_data->resource->data->metadata, $metadata);
        } else {
            $this->runtime_data->resource->data->metadata = new stdClass();
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
     * Set Metadata Values
     *
     * @param   array $data
     * @param   array $metadata_object
     *
     * @return  array
     * @since   1.0.0
     */
    protected function setMetadata($data, array $metadata_object)
    {
        if (count($data) > 0) {
            foreach ($data as $key => $value) {
                $metadata_object[$key] = trim($value);
            }
        }

        return $metadata_object;
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
