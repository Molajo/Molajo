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

        $defer = (int) $this->parameters->get('defer');

        /** get metadata (part used in base) */
        if ($defer == 1) {
        } else {
            $metadata = Services::Media()->get_metadata();

            if (count($metadata) > 0) {
                $row = new stdClass();
                $row->type = 'base';
                $row->title = Services::Security()->escapeText(
                    $metadata['standard']['title']
                );
                $row->mimetype = Services::Security()->escapeText(
                    Services::Media()->get_mime_encoding()
                );
                $row->base = Molajo::Request()->get('url_base');
                $row->last_modified = Services::Security()->escapeText(
                    Molajo::Request()->get('source_last_modified')
                );

                $this->query_results[] = $row;
            }

            /** metadata */
            if (count($metadata) > 0) {

                foreach ($metadata as $type => $tag) {
                    foreach ($tag as $name => $content) {
        //				if ($type == 'http-equiv') {
        //					$content .= '; charset=' . $document->getCharset();
        //					$buffer .= $tab . '<meta http-equiv="' . $name . '" content="' . htmlspecialchars($content) . '" />' . $lnEnd;
        //				} else {
                            $row = new stdClass();
                            $row->type = 'metadata';
                            $row->name = Services::Security()->escapeText($name);
                            $row->content = Services::Security()->escapeText($content);
                            $this->query_results[] = $row;
        //				}
                    }
                }
            }
        }


        /** type: links */
        if ($defer == 1) {
        } else {
            $row = new stdClass();

            $row->type = 'links';
            $row->url = Molajo::Request()->get('theme_favicon');
            $row->relation = 'shortcut icon';
            $row->attributes = ' type="' . 'image/vnd.microsoft.icon' . '"';
            $this->query_results[] = $row;

            $list = Services::Media()->get_links();

            if (count($list) > 0) {
                foreach ($list as $item) {
                    $row = new stdClass();

                    $row->type = 'links';
                    $row->url = $item['url'];
                    $row->relation = Services::Security()->escapeText(
                        $item['relation']
                    );
                    $row->relation_type = Services::Security()->escapeText(
                        $item['relation_type']
                    );

                    $row->attributes = '';
                    $temp = $item['attributes'];
                    if (trim($temp) == '') {
                    } else if (count($temp) == 1) {
                        $temp = array($temp);
                    }
                    if (is_array($temp) && count($temp) > 0) {
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
        }

        /** type: css */
        if ($defer == 1) {
        } else {
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
        }

        /** type: js */
        $list = Services::Media()->get_js($defer);

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
        $list = Services::Media()->get_js_declarations($defer);

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
