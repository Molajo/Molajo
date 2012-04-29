<?php
/**
 * @package   Molajo
 * @copyright 2012 Amy Stephen. All rights reserved.
 * @license   GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
namespace Molajo\MVC\Model;

defined('MOLAJO') or die;

/**
 * Head
 *
 * @package     Molajo
 * @subpackage  Model
 * @since       1.0
 */
Class HeadModel extends Model
{
    /**
     * Constructor.
     *
     * @param  $id
     * @since  1.0
     */
    public function __construct()
    {
        $this->class_name = get_class($this);

        return parent::__construct();
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

        $defer = (int)$this->parameters->get('defer');

        /** get metadata (part used in base) */
        if ($defer == 1) {
        } else {
            $metadata = Services::Document()->get_metadata();

            if (count($metadata) > 0) {

                $row = new \stdClass();
                $row->type = 'base';

                $title = $metadata['standard']['title'];
                if (trim($title) == '') {
                    $title = Services::Registry()->get('Configuration', 'metadata_title', 'Molajo');
                }
                $row->title = Services::Filter()->escape_text($title);

                $mimetype = Services::Document()->get_mime_encoding();
                if (trim($mimetype) == '') {
                    $mimetype = 'text/html';
                }
                $row->mimetype = Services::Filter()->escape_text($mimetype);

                $row->base = Services::Registry()->get('Request', 'url_base');

                $last_modified = Services::Registry()->get('Request', 'source_last_modified');
                if (trim($last_modified) == '') {
                    $last_modified = Services::Date()->getDate()->toSql();
                }
                $row->last_modified = Services::Filter()->escape_text($last_modified);

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
                        if (trim($content) == '') {
                        } else {
                            $row = new \stdClass();
                            $row->type = 'metadata';
                            $row->name = Services::Filter()->escape_text($name);
                            $row->content = Services::Filter()->escape_text($content);
                            $this->query_results[] = $row;
                        }
                        //				}
                    }
                }
            }
        }

        /** type: links */
        if ($defer == 1) {
        } else {
            $row = new \stdClass();

            $row->type = 'links';
            $row->url = Services::Registry()->get('Request', 'theme_favicon');
            $row->relation = 'shortcut icon';
            $row->attributes = ' type="' . 'image/vnd.microsoft.icon' . '"';
            $this->query_results[] = $row;

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
                    } else if (count($temp) == 1) {
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
                    $this->query_results[] = $row;
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

                    $this->query_results[] = $row;
                }
            }

            /** type: css_declarations */
            $list = Services::Document()->get_css_declarations();

            foreach ($list as $item) {
                $row = new \stdClass();

                $row->type = 'css_declarations';
                $row->mimetype = $item['mimetype'];
                $row->content = $item['content'];

                $this->query_results[] = $row;
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

            $this->query_results[] = $row;
        }

        /** type: js_declarations */
        $list = Services::Document()->get_js_declarations($defer);

        foreach ($list as $item) {
            $row = new \stdClass();

            $row->type = 'js_declarations';
            $row->mimetype = $item['mimetype'];
            $row->content = $item['content'];

            $this->query_results[] = $row;
        }

        return $this->query_results;
    }
}
