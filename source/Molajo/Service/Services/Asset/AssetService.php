<?php
/**
 * @package   Molajo
 * @copyright 2012 Amy Stephen. All rights reserved.
 * @license   GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
namespace Molajo\Service\Services\Asset;

use Molajo\Service\Services;

defined('MOLAJO') or die;

/**
 * Asset
 *
 * @package     Molajo
 * @subpackage  Service
 * @since       1.0
 */
Class AssetService
{
    /**
     * Static instance
     *
     * @var    object
     * @since  1.0
     */
    protected static $instance;

    /**
     * Assets
     *
     * @var    array
     * @since  1.0
     */
    protected $assets = array();

    /**
     * getInstance
     *
     * @static
     * @return bool|object
     * @since  1.0
     */
    public static function getInstance()
    {
        if (empty(self::$instance)) {
            self::$instance = new AssetService();
        }

        return self::$instance;
    }

    /**
     * Set the system asset.
     *
     * @param string  $asset
     * @param string  $type  asset, notice, warning, and error
     * @param integer $code
     *
     * @return bool
     * @since   1.0
     */
    public function set()
    {
        return true;
    }

    /**
     * get application assets
     *
     * @return array Application assets
     *
     * @since   1.0
     */
    public function get($option = null)
    {
        if ($option == 'db') {
            return $this;

        } elseif ($option == 'count') {
            return count($this->assets);

        } else {
            return $this;
        }
    }

    /**
     *     Dummy functions to pass service off as a DBO to interact with model
     */
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
     * getData
     *
     * @return array
     *
     * @since    1.0
     */
    public function getAssets()
    {
        $query_results = array();

        $defer = (int) Services::Registry()->get('Parameters', 'defer', 0);

        /** get metadata (part used in base) */
        if ($defer == 1) {
        } else {
            $metadata = Services::Registry()->get('Metadata');

            if (count($metadata) > 0) {

                $row = new \stdClass();
                $row->type = 'base';

                /** Title */
                $title = $metadata['title'];
                if (trim($title) == '') {
                    $title = Services::Registry()->get('Configuration', 'title', 'Molajo');
                }
                $row->title = Services::Filter()->escape_text($title);

                /** Mimetype */
                $mimetype = Services::Document()->get_mime_encoding();
                if (trim($mimetype) == '') {
                    $mimetype = 'text/html';
                }
                $row->mimetype = Services::Filter()->escape_text($mimetype);

                /** Base URL for Site */
                $row->base = Services::Registry()->get('Configuration', 'site_base_url');

                /** Last Modified Date */
                $last_modified = Services::Registry()->get('Parameters', 'modified_datetime');
                if (trim($last_modified) == '') {
                    $last_modified = Services::Date()->getDate();
                }
                $row->last_modified = Services::Filter()->escape_text($last_modified);

                $query_results[] = $row;
            }

            /** metadata */
            if (count($metadata) > 0) {

                foreach ($metadata as $name => $content) {

                        //				if ($type == 'http-equiv') {
                        //					$content .= '; charset=' . $document->getCharset();
                        //					$buffer .= $tab . '<meta http-equiv="' . $name . '" content="' . htmlspecialchars($content) . '" />' . $lnEnd;
                        //				} else {
                        if (trim($content) == '') {
                        } else {
                            $row = new \stdClass();
                            $row->type = 'metadata';
                            $row->name = Services::Filter()->escape_text($name);
                            $row->content = Services::Filter()->escape_text($content);
                            $query_results[] = $row;
                        }
                        //				}
                    }
                }

        }

        /** type: links */
        if ($defer == 1) {
        } else {
            $row = new \stdClass();

            $row->type = 'links';
            $row->url = Services::Registry()->get('Theme', 'favicon');
            $row->relation = 'shortcut icon';
            $row->attributes = ' type="' . 'image/vnd.microsoft.icon' . '"';
            $query_results[] = $row;

            $list = Services::Document()->get_links();

            if (count($list) > 0) {
                foreach ($list as $item) {
                    $row = new \stdClass();

                    $row->type = 'links';
                    $row->url = $item['url'];
                    $row->relation = Services::Filter()->escape_text(
                        $item['relation']
                    );
                    $row->relation_type = Services::Filter()->escape_text(
                        $item['relation_type']
                    );

                    $row->attributes = '';
                    $temp = $item['attributes'];
                    if (trim($temp) == '') {
                    } elseif (count($temp) == 1) {
                        $temp = array($temp);
                    }
                    if (is_array($temp) && count($temp) > 0) {
                        foreach ($temp as $pair) {
                            $split = explode(',', $pair);
                            $row->attributes .= ' ' . $split[0]
                                . '="'
                                . Services::Filter()->escape_text($split[1])
                                . '"';
                        }
                    }
                    $query_results[] = $row;
                }
            }
        }

        /** type: css */
        if ($defer == 1) {
        } else {
            $list = Services::Document()->get_css();

            if (count($list) > 0) {
                foreach ($list as $item) {
                    $row = new \stdClass();

                    $row->type = 'css';
                    $row->url = $item['url'];
                    $row->mimetype = $item['mimetype'];
                    $row->media = $item['media'];
                    $row->attributes = $item['attributes'];
                    $row->priority = $item['priority'];

                    $query_results[] = $row;
                }
            }

            /** type: css_declarations */
            $list = Services::Document()->get_css_declarations();

            foreach ($list as $item) {
                $row = new \stdClass();

                $row->type = 'css_declarations';
                $row->mimetype = $item['mimetype'];
                $row->content = $item['content'];

                $query_results[] = $row;
            }
        }

        /** type: js */
        $list = Services::Document()->get_js($defer);

        foreach ($list as $item) {
            $row = new \stdClass();

            $row->type = 'js';
            $row->url = $item['url'];
            $row->mimetype = $item['mimetype'];
            $row->defer = 0;
            $row->async = $item['async'];
            $row->priority = $item['priority'];

            $query_results[] = $row;
        }

        /** type: js_declarations */
        $list = Services::Document()->get_js_declarations($defer);

        foreach ($list as $item) {
            $row = new \stdClass();

            $row->type = 'js_declarations';
            $row->mimetype = $item['mimetype'];
            $row->content = $item['content'];

            $query_results[] = $row;
        }

        return $query_results;
    }
}
