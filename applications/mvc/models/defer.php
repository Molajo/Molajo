<?php
/**
 * @package     Molajo
 * @subpackage  Model
 * @copyright   Copyright (C) 2012 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die;

/**
 * Defer
 *
 * @package     Molajo
 * @subpackage  Model
 * @since       1.0
 */
class MolajoModelDefer extends MolajoModel
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

        /** type: javascript_links */
        $list = MolajoController::getApplication()->getScriptLinks(true);
        foreach ($list as $item) {
            $tempObject = new JObject();
            $tempObject->set('type', 'javascript_links');
            $tempObject->set('url', $item['url']);
            $tempObject->set('mimetype', $item['mimetype']);
            $tempObject->set('defer', $item['defer']);
            $tempObject->set('async', $item['async']);
            $tempObject->set('priority', $item['priority']);
            $this->items[] = $tempObject;
        }

        /** type: javascript_declarations */
        $list = MolajoController::getApplication()->getScriptDeclarations(true);
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

    /**
     * compressJS
     *
     * @return    array
     *
     * @since    1.0
     */
    public function compressJS()
    {
    }
}
