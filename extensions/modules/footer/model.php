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
class FooterModuleModelDisplay extends MolajoModel
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
     * @return    array    An empty array
     *
     * @since    1.0
     */
    public function getItems()
    {
        $this->items = array();
echo 'in getItems';
        die;
        $tempObject = new JObject();

        /** footer line 1 */
        if (JString::strpos(MolajoTextHelper :: _('FOOTER_LINE1'), '%date%')) {
            $line1 = str_replace('%date%', MolajoController::getDate()->format('Y'), MolajoTextHelper :: _('FOOTER_LINE1'));
        } else {
            $line1 = MolajoTextHelper :: _('FOOTER_LINE1');
        }
        if (JString::strpos($line1, '%site_name%')) {
            $line1 = str_replace('%site_name%', MolajoController::getApplication()->get('site_name', 'Molajo'), $line1);
        }
        $tempObject->set('line1', $line1);

        /** footer line 2 */
        $link = $this->parameters->def('link', 'http://molajo.org');
        $linked_text = $this->parameters->def('linked_text', 'Molajo&#153;');
        $remaining_text = $this->parameters->def('remaining_text', ' is free software.');
        $version = $this->parameters->def('version', MolajoTextHelper::_(MOLAJOVERSION));

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
