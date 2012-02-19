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
        $this->data = array();

        $row = new stdClass();
        $row->type = 'base';

        $metadata = Molajo::Responder()->getMetadata();
        if (count($metadata) > 0) {
            $row->title = $metadata['standard']['metadata_title'];
            $row->description = $metadata['standard']['metadata_description'];
            $row->keywords = $metadata['standard']['metadata_keywords'];
            $row->author = $metadata['standard']['metadata_author'];
            $row->content_rights = $metadata['standard']['metadata_content_rights'];
            $row->robots = $metadata['standard']['metadata_robots'];
        }

        $row->base = Molajo::Request()->get('url_base');
        $row->last_modified = Molajo::Request()->get('source_last_modified');
        $row->favicon = Molajo::Request()->get('theme_favicon');
        $this->data[] = $row;

        /** type: links */
        $list = Molajo::Responder()->getHeadLinks();

        if (count($list) > 0) {
            foreach ($list as $item) {
                $row = new stdClass();
                $row->type = 'links';
                $row->url = $item['url'];
                $row->relation = $item['relation'];
                $row->relation_type = $item['relation_type'];
                $row->attributes = $item['attributes'];
                $this->data[] = $row;
            }
        }

        /** type: stylesheet_links */
        $list = Molajo::Responder()->getStyleLinks();
        if (count($list) > 0) {
            foreach ($list as $item) {
                $row = new stdClass();
                $row->type = 'stylesheet_links';
                $row->url = $item['url'];
                $row->mimetype = $item['mimetype'];
                $row->media = $item['media'];
                $row->attributes = $item['attributes'];
                $row->priority = $item['priority'];
                $this->data[] = $row;
            }
        }

        /** type: stylesheet_declarations */
        $list = Molajo::Responder()->getStyleDeclarations();
        foreach ($list as $item) {
            $row = new stdClass();
            $row->type = 'stylesheet_declarations';
            $row->mimetype = $item['mimetype'];
            $row->content = $item['content'];
            $this->data[] = $row;
        }

        /** type: javascript_links */
        $list = Molajo::Responder()->getScriptLinks();
        foreach ($list as $item) {
            $row = new stdClass();
            $row->type = 'javascript_links';
            $row->url = $item['url'];
            $row->mimetype = $item['mimetype'];
            $row->defer = 0;
            $row->async = $item['async'];
            $row->priority = $item['priority'];
            $this->data[] = $row;
        }

        /** type: javascript_declarations */
        $list = Molajo::Responder()->getScriptDeclarations();
        foreach ($list as $item) {
            $row = new stdClass();
            $row->type = 'javascript_declarations';
            $row->mimetype = $item['mimetype'];
            $row->content = $item['content'];
            $this->data[] = $row;
        }

        return $this->data;
    }
}
