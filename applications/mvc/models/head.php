<?php
/**
 * @package     Molajo
 * @subpackage  Model
 * @copyright   Copyright (C) 2012 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die;

/**
 * Head
 *
 * @package     Molajo
 * @subpackage  Model
 * @since       1.0
 */
class MolajoModelHead extends MolajoModel
{
    /**
     * __construct
     *
     * Constructor.
     *
     * @param  $config
     * @since  1.0
     */
    public function __construct($config = array())
    {
        $this->_name = get_class($this);
        parent::__construct($config = array());
    }

    /**
     * getItems
     *
     * @return    array
     *
     * @since    1.0
     */
    public function getItems()
    {
        $this->items = array();

        /** type: base */
        $tempObject = new JObject();
        $tempObject->set('type', 'base');
        $tempObject->set('title', MolajoController::getRequest()->get('metadata_title'));
        $tempObject->set('base', MolajoController::getRequest()->get('url_base'));
        $tempObject->set('last_modified', MolajoController::getRequest()->get('source_last_modified'));
        $tempObject->set('description', MolajoController::getRequest()->get('metadata_description'));
        $tempObject->set('favicon', MolajoController::getRequest()->get('theme_favicon'));
        $tempObject->set('keywords', MolajoController::getRequest()->get('metadata_keywords'));
        $tempObject->set('author', MolajoController::getRequest()->get('metadata_author'));
        $tempObject->set('content_rights', MolajoController::getRequest()->get('metadata_content_rights'));
        $tempObject->set('robots', MolajoController::getRequest()->get('metadata_robots'));
        $this->items[] = $tempObject;

        /** type: links */
        $list = MolajoController::getApplication()->getHeadLinks();

        if (count($list) > 0) {
            foreach ($list as $item) {
                $tempObject = new JObject();
                $tempObject->set('type', 'links');
                $tempObject->set('url', $item['url']);
                $tempObject->set('relation', $item['relation']);
                $tempObject->set('relation_type', $item['relation_type']);
                $tempObject->set('attributes', $item['attributes']);
                $this->items[] = $tempObject;
            }
        }

        /** type: stylesheet_links */
        $list = MolajoController::getApplication()->getStyleLinks();
        if (count($list) > 0) {
            foreach ($list as $item) {
                $tempObject = new JObject();
                $tempObject->set('type', 'stylesheet_links');
                $tempObject->set('url', $item['url']);
                $tempObject->set('mimetype', $item['mimetype']);
                $tempObject->set('media', $item['media']);
                $tempObject->set('attributes', $item['attributes']);
                $tempObject->set('priority', $item['priority']);
                $this->items[] = $tempObject;
            }
        }

        /** type: stylesheet_declarations */
        $list = MolajoController::getApplication()->getStyleDeclarations();
        foreach ($list as $item) {
            $tempObject = new JObject();
            $tempObject->set('type', 'stylesheet_declarations');
            $tempObject->set('mimetype', $item['mimetype']);
            $tempObject->set('content', $item['content']);
            $this->items[] = $tempObject;
        }

        /** type: javascript_links */
        $list = MolajoController::getApplication()->getScriptLinks();
        foreach ($list as $item) {
            $tempObject = new JObject();
            $tempObject->set('type', 'javascript_links');
            $tempObject->set('url', $item['url']);
            $tempObject->set('mimetype', $item['mimetype']);
            $tempObject->set('defer', 0);
            $tempObject->set('async', $item['async']);
            $tempObject->set('priority', $item['priority']);
            $this->items[] = $tempObject;
        }

        /** type: javascript_declarations */
        $list = MolajoController::getApplication()->getScriptDeclarations();
        foreach ($list as $item) {
            $tempObject = new JObject();
            $tempObject->set('type', 'javascript_declarations');
            $tempObject->set('mimetype', $item['mimetype']);
            $tempObject->set('content', $item['content']);
            $this->items[] = $tempObject;
        }

        return $this->items;
        /** custom */
    }
}
