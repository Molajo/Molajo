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
            $row = new stdClass();
            $row->type = 'javascript_links';
            $row->url = $item['url'];
            $row->mimetype = $item['mimetype'];
            $row->defer = true;
            $row->async = $item['async'];
            $row->priority = $item['priority'];
            $this->data[] = $row;
        }

        /** type: javascript_declarations */
        $list = Molajo::Responder()->getScriptDeclarations(1);
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
