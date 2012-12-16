<?php
/**
 * @package    Niambie
 * @copyright  2012 Amy Stephen. All rights reserved.
 * @license    MIT, see License folder
 */
namespace Molajo\Service\Services\Metadata;

defined('NIAMBIE') or die;

use Molajo\Service\Services;

/**
 * Metadata
 *
 * @package     Niambie
 * @subpackage  Services
 * @since       1.0
 */
Class MetadataService
{
    /**
     * Initialise - activated by Services Class
     */
    public function initialise()
    {
        Services::Registry()->createRegistry(METADATA_LITERAL);

        return $this;
    }

    /**
     * Retrieve header and metadata information
     *
     * @param   $model_type - head or metadata
     *
     * @return  array
     * @since   1.0
     */
    public function set($name, $content, $label = 'name')
    {
        Services::Registry()->set(METADATA_LITERAL, $name, array($content, $label));

        return;
    }

    /**
     * Retrieve header and metadata information
     *
     * @param   $model_type - head, metadata
     *
     * @return  array
     * @since   1.0
     */
    public function get($model_type)
    {
        $query_results = array();

        $application_html5 = Services::Registry()->get(CONFIGURATION_LITERAL, 'application_html5', 1);

        if ((int)Services::Registry()->get(CONFIGURATION_LITERAL, 'application_html5', 1) == 1) {
            $end = '>' . chr(10);
        } else {
            $end = '/>' . chr(10);
        }

        if (strtolower($model_type) == 'head') {
            $query_results = $this->getHead($application_html5, $end);

        } elseif (strtolower($model_type) == METADATA_LITERAL) {
            $query_results = $this->getMetadata($application_html5, $end);

        } else {
            $temp_row = new \stdClass();

            $temp_row->name = 'dummy row';
            $temp_row->application_html5 = $application_html5;
            $temp_row->end = $end;

            $query_results[] = $temp_row;
        }

        return $query_results;
    }

    /**
     * getHead - Retrieve header information
     *
     * @param   $application_html5
     * @param   $end
     *
     * @return  array
     * @since   1.0
     */
    protected function getHead($application_html5, $end)
    {
        $query_results = array();

        $temp_row = new \stdClass();

        $temp = Services::Registry()->get(METADATA_LITERAL, 'title', '');
        $title = $temp[0];
        if (trim($title) == '') {
            $title = SITE_NAME;
        }
        $temp_row->title = Services::Filter()->escape_text($title);

        Services::Registry()->delete(METADATA_LITERAL, 'title');

        $mimetype = Services::Registry()->get(METADATA_LITERAL, 'mimetype', '');
        if (trim($mimetype) == '') {
            $mimetype = 'text/html';
        }
        $temp_row->mimetype = Services::Filter()->escape_text($mimetype);

        Services::Registry()->set(METADATA_LITERAL, 'mimetype', $mimetype);

        $temp_row->base = SITE_BASE_URL;

        $last_modified = Services::Registry()->get('parameters', 'modified_datetime');
        if (trim($last_modified) == '') {
            $last_modified = Services::Date()->getDate();
        }
        $temp_row->last_modified = Services::Filter()->escape_text($last_modified);

        $temp_row->base_url = BASE_URL;

        $temp_row->language_direction = 'lft';
        if ($temp_row->language_direction == 'lft') {
            $temp_row->language_direction = '';
        } else {
            $temp_row->language_direction = ' dir="rtl"';
        }
        //@todo get language from language services
        $temp_row->language = 'en';

        $temp_row->application_html5 = $application_html5;
        $temp_row->end = $end;

        $query_results[] = $temp_row;

        return $query_results;
    }

    /**
     * Retrieve metadata information
     *
     * @param   $application_html5
     * @param   $end
     *
     * @return  array
     * @since   1.0
     */
    protected function getMetadata($application_html5, $end)
    {
        $query_results = array();

        $temp_row = new \stdClass();

        $temp = Services::Registry()->get(METADATA_LITERAL, 'title', '');
        $title = $temp[0];
        if (trim($title) == '') {
            $title = SITE_NAME;
        }
        $temp_row->title = Services::Filter()->escape_text($title);

        Services::Registry()->delete(METADATA_LITERAL, 'title');

        $mimetype = Services::Registry()->get(METADATA_LITERAL, 'mimetype', '');
        if (trim($mimetype) == '') {
            $mimetype = 'text/html';
        }
        $temp_row->mimetype = Services::Filter()->escape_text($mimetype);

        Services::Registry()->set(METADATA_LITERAL, 'mimetype', $mimetype);

        $temp_row->base = SITE_BASE_URL;

        $last_modified = Services::Registry()->get('parameters', 'modified_datetime');
        if (trim($last_modified) == '') {
            $last_modified = Services::Date()->getDate();
        }
        $temp_row->last_modified = Services::Filter()->escape_text($last_modified);

        $temp_row->base_url = BASE_URL;

        $temp_row->language_direction = 'lft';
        if ($temp_row->language_direction == 'lft') {
            $temp_row->language_direction = '';
        } else {
            $temp_row->language_direction = ' dir="rtl"';
        }
        //@todo figure out what it's like this
        $temp_row->language = 'en';

        $temp_row->application_html5 = $application_html5;
        $temp_row->end = $end;

        $query_results[] = $temp_row;

        return $query_results;
    }
}
