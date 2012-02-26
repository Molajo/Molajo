<?php
/**
 * @package     Molajo
 * @subpackage  Model
 * @copyright   Copyright (C) 2012 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die;

/**
 * Display
 *
 * @package     Molajo
 * @subpackage  Model
 * @since       1.0
 */
class MolajoHeadModel extends MolajoModel
{
    /**
     * __construct
     *
     * Constructor.
     *
     * @param  $id
     * @since  1.0
     */
    public function __construct($id = null)
    {
        $this->name = get_class($this);

        return parent::__construct($id);
    }

    /**
     * getData
     *
     * @return    array
     *
     * @since    1.0
     */
    public function getData()
    {
        $this->query_results = array();

        /** base information */
        $metadata = Services::Media()->get_metadata();

        if (count($metadata) > 0) {
            $row = new stdClass();

            $row->type = 'base';
            $row->title = Services::Security()->escapeText(
                $metadata['standard']['metadata_title']
            );
            $row->description = Services::Security()->escapeText(
                $metadata['standard']['metadata_description']
            );
            $row->keywords = Services::Security()->escapeText(
                $metadata['standard']['metadata_keywords']
            );
            $row->author = Services::Security()->escapeText(
                $metadata['standard']['metadata_author']
            );
            $row->content_rights = Services::Security()->escapeText(
                $metadata['standard']['metadata_content_rights']
            );
            $row->robots = Services::Security()->escapeText(
                $metadata['standard']['metadata_robots']
            );

            $row->base = Molajo::Request()->get('url_base');
            $row->last_modified = Services::Security()->escapeText(
                Molajo::Request()->get('source_last_modified')
            );
            $row->favicon = Molajo::Request()->get('theme_favicon');

            $this->query_results[] = $row;
        }

        /** type: links */
        $list = Services::Media()->get_links();

        if (count($list) > 0) {
            foreach ($list as $item) {
                $row = new stdClass();

                $row->type = 'links';
                $row->url = $item['url'];
                $row->relation = $item['relation'];
                $row->relation_type = Services::Security()->escapeText(
                    $item['relation_type']
                );

                $row->attributes = '';
                $temp = $item['attributes'];
                if (count($temp) == 0) {
                } else if (count($temp) == 1) {
                    $temp = array($temp);
                }
                if (count($temp) > 0) {
                    foreach ($temp as $pair) {
                        $split = explode(',',$pair);
                        $row->attributes .= ' ' . $split[0]
                            . '="'
                            . Services::Security()->escapeText($split[1])
                            . '"';
                    }
                }

                $this->query_results[] = $row;
            }
        }

        /** type: css */
        $list = Services::Media()->get_css();

        if (count($list) > 0) {
            foreach ($list as $item) {
                $row = new stdClass();

                $row->type = 'css';
                $row->url = $item['url'];
                $row->mimetype = $item['mimetype'];
                $row->media = $item['media'];
                $row->attributes = $item['attributes'];
                $row->priority = $item['priority'];

                $this->query_results[] = $row;
            }
        }

        /** type: css_declarations */
        $list = Services::Media()->get_css_declarations();

        foreach ($list as $item) {
            $row = new stdClass();

            $row->type = 'css_declarations';
            $row->mimetype = $item['mimetype'];
            $row->content = $item['content'];

            $this->query_results[] = $row;
        }

        /** type: js */
        $list = Services::Media()->get_js();

        foreach ($list as $item) {
            $row = new stdClass();

            $row->type = 'js';
            $row->url = $item['url'];
            $row->mimetype = $item['mimetype'];
            $row->defer = 0;
            $row->async = $item['async'];
            $row->priority = $item['priority'];

            $this->query_results[] = $row;
        }

        /** type: js_declarations */
        $list = Services::Media()->get_js_declarations();

        foreach ($list as $item) {
            $row = new stdClass();

            $row->type = 'js_declarations';
            $row->mimetype = $item['mimetype'];
            $row->content = $item['content'];

            $this->query_results[] = $row;
        }

        return $this->query_results;
    }
}
