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
        $this->table = '';
        $this->primary_key = '';

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

        $tempObject = new JObject();
        $tempObject->set('type', 'base');

        $metadata = Molajo::Responder()->getMetadata();

        if (count($metadata) > 0) {
            $tempObject->set('title', $metadata['standard']['metadata_title']);
            $tempObject->set('description', $metadata['standard']['metadata_description']);
            $tempObject->set('keywords', $metadata['standard']['metadata_keywords']);
            $tempObject->set('author', $metadata['standard']['metadata_author']);
            $tempObject->set('content_rights', $metadata['standard']['metadata_content_rights']);
            $tempObject->set('robots', $metadata['standard']['metadata_robots']);
        }

        $tempObject->set('base', Molajo::Request()->get('url_base'));
        $tempObject->set('last_modified', Molajo::Request()->get('source_last_modified'));
        $tempObject->set('favicon', Molajo::Request()->get('theme_favicon'));
        $this->data[] = $tempObject;

        /** type: links */
        $list = Molajo::Responder()->getHeadLinks();

        if (count($list) > 0) {
            foreach ($list as $item) {
                $tempObject = new JObject();
                $tempObject->set('type', 'links');
                $tempObject->set('url', $item['url']);
                $tempObject->set('relation', $item['relation']);
                $tempObject->set('relation_type', $item['relation_type']);
                $tempObject->set('attributes', $item['attributes']);
                $this->data[] = $tempObject;
            }
        }

        /** type: stylesheet_links */
        $list = Molajo::Responder()->getStyleLinks();
        if (count($list) > 0) {
            foreach ($list as $item) {
                $tempObject = new JObject();
                $tempObject->set('type', 'stylesheet_links');
                $tempObject->set('url', $item['url']);
                $tempObject->set('mimetype', $item['mimetype']);
                $tempObject->set('media', $item['media']);
                $tempObject->set('attributes', $item['attributes']);
                $tempObject->set('priority', $item['priority']);
                $this->data[] = $tempObject;
            }
        }

        /** type: stylesheet_declarations */
        $list = Molajo::Responder()->getStyleDeclarations();
        foreach ($list as $item) {
            $tempObject = new JObject();
            $tempObject->set('type', 'stylesheet_declarations');
            $tempObject->set('mimetype', $item['mimetype']);
            $tempObject->set('content', $item['content']);
            $this->data[] = $tempObject;
        }

        /** type: javascript_links */
        $list = Molajo::Responder()->getScriptLinks();
        foreach ($list as $item) {
            $tempObject = new JObject();
            $tempObject->set('type', 'javascript_links');
            $tempObject->set('url', $item['url']);
            $tempObject->set('mimetype', $item['mimetype']);
            $tempObject->set('defer', 0);
            $tempObject->set('async', $item['async']);
            $tempObject->set('priority', $item['priority']);
            $this->data[] = $tempObject;
        }

        /** type: javascript_declarations */
        $list = Molajo::Responder()->getScriptDeclarations();
        foreach ($list as $item) {
            $tempObject = new JObject();
            $tempObject->set('type', 'javascript_declarations');
            $tempObject->set('mimetype', $item['mimetype']);
            $tempObject->set('content', $item['content']);
            $this->data[] = $tempObject;
        }

        return $this->data;
        /** custom */
    }
}
