<?php
/**
 * @package     Molajo
 * @subpackage  Model
 * @copyright   Copyright (C) 2012 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die;

/**
 * Footer
 *
 * @package     Molajo
 * @subpackage  Model
 * @since       1.0
 */
class MolajoPagefooterModuleModel extends MolajoDisplayModel
{
    /**
     * __construct
     *
     * Constructor.
     *
     * @param  $config
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
     * getItems
     *
     * @return    array    An empty array
     *
     * @since    1.0
     */
    public function getItems()
    {
        $this->items = array();

        $tempObject = new JObject();
        $date = Services::Date()
            ->getDate()
            ->format('Y-m-d-H-i-s');

        /** footer line 1 */
        if (JString::strpos(
            Services::Language()->_('FOOTER_LINE1'), '%date%')) {
            $line1 = str_replace('%date%',
                Services::Date()->getDate()->format('Y'),
                Services::Language()->_('FOOTER_LINE1'));
        } else {
            $line1 = Services::Language()->_('FOOTER_LINE1');
        }

        if (JString::strpos($line1, '%site_name%')) {
            $line1 = str_replace('%site_name%',
                Services::Configuration()->get('site_name', 'Molajo'),
                $line1
            );
        }
        $tempObject->set('line1', $line1);

        /** footer line 2 */
        $link = $this->parameters->def('link', 'http://molajo.org');
        $linked_text = $this->parameters->def('linked_text', 'Molajo&#153;');
        $remaining_text = $this->parameters->def('remaining_text', ' is free software.');
        $version = $this->parameters->def('version', Services::Language()->_(MOLAJOVERSION));

        $tempObject->set('link', $link);
        $tempObject->set('linked_text', $linked_text);
        $tempObject->set('remaining_text', $remaining_text);
        $tempObject->set('version', $version);

        $line2 = '<a href="' . $link . '">' . $linked_text . ' v.' . $version . '</a>';
        $line2 .= $remaining_text;
        $tempObject->set('line2', $line2);

        /** save recordset */
        $this->items[] = $tempObject;

        return $this->items;
    }
}
