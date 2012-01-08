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
 * Retrieve Molajo System Head
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

        $title = MolajoController::getApplication()->getTitle();
        $description  = MolajoController::getApplication()->getDescription();

        $base = MolajoController::getApplication()->getBase();

        $metadata = MolajoController::getApplication()->getTitle();

        $links = MolajoController::getApplication()->getTitle();
        $stylesheets = MolajoController::getApplication()->getTitle();
        $style = MolajoController::getApplication()->getTitle();
        $scripts = MolajoController::getApplication()->getTitle();
        $script = MolajoController::getApplication()->getTitle();
        $custom = MolajoController::getApplication()->getTitle();

        foreach($messages as $message) {
            $tempObject = new JObject();
            $tempObject->set('title', $message['type']);
            $tempObject->set('type', $message['type']);
            $tempObject->set('content_text', $message['message']);
            $this->items[] = $tempObject;
        }

        return $this->items;
    }
}