<?php
/**
 * @package     Molajo
 * @subpackage  Footer
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
class FooterModelModule extends MolajoModelDisplay
{
    /**
     * $request
     *
     * @var      array
     * @since    1.0
     */
    public $requestArray = array();

    /**
     * $this->parameters
     *
     * @var      string
     * @since    1.0
     */
    public $parameters = array();

    /**
     * $items
     *
     * @var      string
     * @since    1.0
     */
    public $items = array();

    /**
     * $pagination
     *
     * @var      string
     * @since    1.0
     */
    public $pagination = array();

    /**
     * $context
     *
     * @var      string
     * @since    1.0
     */
    public $context = null;

    /**
     * $task
     *
     * @var      string
     * @since    1.0
     */
    public $task = null;

    /**
     * populateState
     *
     * Method to auto-populate the model state.
     *
     * @return   void
     * @since    1.0
     */
    protected function populateState()
    {
        //$this->context = strtolower($this->requestArray['option'] . '.' . $this->getName()) . '.' . $this->requestArray['view'];
    }

    /**
     * getRequest
     *
     * @return   array    An empty array
     *
     * @since    1.0
     */
    public function getRequest()
    {
        return $this->requestArray;
    }

    /**
     * getParameters
     *
     * @return    array    An empty array
     *
     * @since    1.0
     */
    public function getParameters()
    {
        return $this->parameters;
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

        /** footer line 1 */
        if (JString::strpos(MolajoTextHelper :: _('FOOTER_LINE1'), '%date%')) {
            $line1 = str_replace('%date%', MolajoController::getDate()->format('Y'), MolajoTextHelper :: _('FOOTER_LINE1'));
        } else {
            $line1 = MolajoTextHelper :: _('FOOTER_LINE1');
        }
        if (JString::strpos($line1, '%sitename%')) {
            $line1 = str_replace('%sitename%', MolajoController::getApplication()->get('sitename', 'Molajo'), $line1);
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

    /**
     * getPagination
     *
     * @return    array    An empty array
     *
     * @since    1.0
     */
    public function getPagination()
    {
        return $this->pagination;
    }
}