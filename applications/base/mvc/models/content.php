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
class MolajoContentModel extends MolajoDisplayModel
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
        $this->table = '#__content';
        $this->primary_key = 'id';

        return parent::__construct($id);
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

        $tempObject = new JObject();

        $tempObject->set('title', 'Test Title');
        $tempObject->set('content_text', 'Test Paragraph.');
        $tempObject->set('start_publishing_datetime', '2012-02-13');
        $tempObject->set('author', 'Amy Stephen');

        $this->items[] = $tempObject;

        return $this->items;
    }
}
