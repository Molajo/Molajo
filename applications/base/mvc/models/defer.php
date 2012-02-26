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
        $this->query_results = array();

        /** type: js */
        $list = Services::Media()->get_js($defer = 1);

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
        $list = Services::Media()->get_js_declarations($defer = 1);

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
