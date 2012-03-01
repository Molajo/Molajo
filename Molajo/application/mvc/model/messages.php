<?php
/**
 * @package     Molajo
 * @subpackage  Model
 * @copyright   Copyright (C) 2012 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
namespace Molajo\Application\MVC\Model;

defined('MOLAJO') or die;


/**
 * Messages
 *
 * @package     Molajo
 * @subpackage  Model
 * @since       1.0
 */
Class MessagesModel extends Model
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

        $messages = Services::Message()->get();
        if (count($messages) == 0) {
            return array();
        }

        foreach($messages as $message) {
            $row = new stdClass();
            $row->content_text = $message['message'];
            $row->title = $message['type'];
            $row->code = $message['code'];
            $row->debug_location = $message['debug_location'];
            $row->debug_object = $message['debug_object'];

            $this->query_results[] = $row;
        }

        return $this->query_results;
    }
}
