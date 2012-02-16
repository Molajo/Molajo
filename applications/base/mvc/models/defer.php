<?php
/**
 * @package     Molajo
 * @subpackage  Model
 * @copyright   Copyright (C) 2012 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die;

/**
 * Content
 *
 * @package     Molajo
 * @subpackage  Model
 * @since       1.0
 */
class MolajoDeferModel extends MolajoDisplayModel
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

        /** type: javascript_links */
        $list = Molajo::Responder()->getScriptLinks(1);
        foreach ($list as $item) {
            $tempObject = new JObject();
            $tempObject->set('type', 'javascript_links');
            $tempObject->set('url', $item['url']);
            $tempObject->set('mimetype', $item['mimetype']);
            $tempObject->set('defer', true);
            $tempObject->set('async', $item['async']);
            $tempObject->set('priority', $item['priority']);
            $this->data[] = $tempObject;
        }

        /** type: javascript_declarations */
        $list = Molajo::Responder()->getScriptDeclarations(1);
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
