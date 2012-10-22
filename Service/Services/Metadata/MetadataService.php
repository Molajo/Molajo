<?php
/**
 * @package    Molajo
 * @copyright  2012 Individual Molajo Contributors. All rights reserved.
 * @license    GNU GPL v 2, or later and MIT, see License folder
 */
namespace Molajo\Service\Services\Metadata;

defined('MOLAJO') or die;

use Molajo\Service\Services;

/**
 * Metadata
 *
 * @package     Molajo
 * @subpackage  Services
 * @since       1.0
 */
Class MetadataService
{
    /**
     * Instance
     *
     * @var    object
     * @since  1.0
     */
    protected static $instance;

    /**
     * getInstance
     *
     * @static
     * @return object
     *
     * @since  1.0
     */
    public static function getInstance()
    {
        if (empty(self::$instance)) {
            self::$instance = new MetadataService();
        }

        return self::$instance;
    }

    /**
     * get application Metadata
     *
     * @return array Application Metadata
     *
     * @since   1.0
     */
    public function get($option = null)
    {
        if ($option == 'db') {
            return $this;

        } elseif ($option == 'count') {
            return count($this);

        } else {
            return $this;
        }
    }

    public function getNullDate()
    {
        return $this;
    }

    public function getQuery()
    {
        return $this;
    }

    public function toSql()
    {
        return $this;
    }

    public function clear()
    {
        return $this;
    }

    /**
     * getMetadata - retrieve header and metadata information
     *
     * @param  $model_type - head, defer, metadata
     *
     * @return array
     * @since   1.0
     */
    public function set($name, $content, $label = 'name')
    {
        Services::Registry()->set('Metadata', $name, array($content, $label));

        return;
    }

    /**
     * getMetadata - retrieve header and metadata information
     *
     * @param  $model_type - head, defer, metadata
     *
     * @return array
     * @since   1.0
     */
    public function getMetadata($model_type)
    {
        $query_results = array();

        $application_html5 = Services::Registry()->get('Configuration', 'application_html5', 1);

        if ((int) Services::Registry()->get('Configuration', 'application_html5', 1) == 1) {
            $end = '>' . chr(10);
        } else {
            $end = '/>' . chr(10);
        }

        if (strtolower($model_type) == 'head') {

            /** Create recordset for view */
            $query_results = array();

            $row = new \stdClass();

            /** Title */
            $title = Services::Registry()->get('Metadata', 'title', '');
            if (trim($title) == '') {
                $title = Services::Registry()->get('Configuration', 'site_name');
            }
            $row->title = Services::Filter()->escape_text($title);

            /** Mime Type */
            $mimetype = Services::Registry()->get('Metadata', 'mimetype', '');
            if (trim($mimetype) == '') {
                $mimetype = 'text/html';
            }
            $row->mimetype = Services::Filter()->escape_text($mimetype);

            Services::Registry()->set('Metadata', 'mimetype', $mimetype);

            /** Base URL for Site */
            $row->base = Services::Registry()->get('Configuration', 'site_base_url');

            /** Last Modified Date */
            $last_modified = Services::Registry()->get('Parameters', 'modified_datetime');
            if (trim($last_modified) == '') {
                $last_modified = Services::Date()->getDate();
            }
            $row->last_modified = Services::Filter()->escape_text($last_modified);

            /** Base URL */
            $row->base_url = BASE_URL;

            /** Language */
            $row->language_direction = 'lft';
            if ($row->language_direction == 'lft') {
                $row->language_direction = '';
            } else {
                $row->language_direction = ' dir="rtl"';
            }
            $row->language = 'en';

            /** HTML5 */
            $row->application_html5 = $application_html5;
            $row->end = $end;
            $query_results[] = $row;

        } elseif (strtolower($model_type) == 'metadata') {

            $metadata = Services::Registry()->get('Metadata');

            if (count($metadata) > 0) {

                foreach ($metadata as $name => $content) {

                    $row = new \stdClass();

                    $row->name = Services::Filter()->escape_text($name);

                    if (is_array($content)) {
                        $row->content = Services::Filter()->escape_text($content[0]);
                        $row->label = $content[1];
                    } else {
                        $row->content = Services::Filter()->escape_text($content);
                        $row->label = 'name';
                    }

                    /** HTML5 */
                    $row->application_html5 = $application_html5;
                    $row->end = $end;

                    $query_results[] = $row;
                }
            }
        } else {
            $row = new \stdClass();

            /** Metadata */
            $row->name = 'dummy row';

            /** HTML5 */
            $row->application_html5 = $application_html5;
            $row->end = $end;

            $query_results[] = $row;
        }

        return $query_results;
    }
}
