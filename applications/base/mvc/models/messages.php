<?php
/**
 * @package     Molajo
 * @subpackage  Model
 * @copyright   Copyright (C) 2012 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die;

/**
 * Messages
 *
 * @package     Molajo
 * @subpackage  Model
 * @since       1.0
 */
class MolajoMessagesModel extends MolajoModel
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

        $messages = Services::Message()->get();
        if (count($messages) == 0) {
            return array();
        }

        foreach($messages as $message) {
            $tempObject = new JObject();
            $tempObject->set('content_text', $message['message']);
            $tempObject->set('title', $message['type']);
            $tempObject->set('code', $message['code']);
            $tempObject->set('debug_location', $message['debug_location']);
            $tempObject->set('debug_object', $message['debug_object']);

            $this->data[] = $tempObject;
        }

        return $this->data;
    }
}
