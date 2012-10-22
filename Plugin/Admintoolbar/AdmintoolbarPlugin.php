<?php
/**
 * @package    Molajo
 * @copyright  2012 Individual Molajo Contributors. All rights reserved.
 * @license    GNU GPL v 2, or later and MIT, see License folder
 */
namespace Molajo\Plugin\Admintoolbar;

use Molajo\Plugin\Plugin\Plugin;
use Molajo\Service\Services;

defined('MOLAJO') or die;

/**
 * @package     Molajo
 * @subpackage  Plugin
 * @since       1.0
 */
class AdmintoolbarPlugin extends Plugin
{
    /**
     * Prepares Page Title and Buttons for Rendering
     *
     * @return boolean
     * @since   1.0
     */
    public function onBeforeParse()
    {

        $this->setPageTitle();

        $buttonArray = $this->setButtonArray();

        if ($buttonArray === false) {
            $buttonCount = 0;
        } else {
            $buttonCount = count($buttonArray);
        }

        $query_results = array();

        $row = new \stdClass();
        $row->button_count = $buttonCount;
        $row->button_array = '';

        if ($buttonCount === 0) {
            $row->button_array = null;
        } else {
            foreach ($buttonArray as $button) {
                $row->button_array .= trim($button);
            }
        }

        $query_results[] = $row;

        Services::Registry()->set('Plugindata', 'Admintoolbar', $query_results);

        return true;
    }

    /**
     * Create Buttons based upon Page Type
     *
     * @return array
     * @since  1.0
     */
    protected function setButtonArray()
    {
        if ($this->get('page_type') == 'item') {
            return $this->setItemButtons();

        } elseif ($this->get('page_type') == 'form') {
            return $this->setEditButtons();

        } elseif ($this->get('page_type') == 'list') {
            return $this->setListButtons();
        }

        return $this->setDashboardButtons();
    }

    /**
     * Prepares Page Title
     *
     * @return boolean
     * @since   1.0
     */
    protected function setPageTitle()
    {

        $heading1 = $this->get('criteria_title');
        $page_type = $this->get('page_type');

        $request_action = $this->get('request_action');

        if ($page_type == 'menuitem') {
            $page_type = $this->get('menuitem_type');
            $heading2 = Services::Language()->translate(ucfirst(strtolower($page_type)));
        } else {
            $heading2 = Services::Language()->translate(ucfirst(strtolower($request_action))
                . ' ' . ucfirst(strtolower($page_type)));
        }

        Services::Registry()->set('Plugindata', 'heading1', $heading1);
        Services::Registry()->set('Plugindata', 'heading2', $heading2);

        return true;
    }

    /**
     * Create Item Buttons
     *
     * @return array
     * @since  1.0
     */
    protected function setItemButtons()
    {

        $buttons = array();

        /** Button 1: Back to Grid */
        $buttonTitle = str_replace(
            ' ',
            '&nbsp;',
            htmlentities(Services::Language()->translate('Back to Grid'), ENT_COMPAT, 'UTF-8')
        );
        $buttonIcon = htmlentities('icon-list-alt', ENT_COMPAT, 'UTF-8');
        $linkURL = '/admin/' . Services::Registry()->get('Parameters', 'catalog_alias');
        $buttonArray = 'button_title:'
            . trim($buttonTitle)
            . ','
            . 'button_type:secondary,'
            . 'button_link:'
            . $linkURL
            . ','
            . 'button_icon_prepend:'
            . $buttonIcon;

        $buttons[] = '{{' . trim($buttonArray) . '}}';

        /** Button 2: Edit Button */
        $buttonTitle = str_replace(
            ' ',
            '&nbsp;',
            htmlentities(Services::Language()->translate('Edit'), ENT_COMPAT, 'UTF-8')
        );
        $buttonIcon = htmlentities('icon-edit', ENT_COMPAT, 'UTF-8');
        $linkURL = Services::Registry()->get('Plugindata', 'page_url') . '/edit';
        $buttonArray = 'button_title:'
            . trim($buttonTitle)
            . ','
            . 'button_type:secondary,'
            . 'button_link:'
            . $linkURL
            . ','
            . 'button_icon_prepend:'
            . $buttonIcon;

        $buttons[] = '{{' . trim($buttonArray) . '}}';

        /** Button 3: Delete Button */
        $buttonTitle = str_replace(
            ' ',
            '&nbsp;',
            htmlentities(Services::Language()->translate('Delete'), ENT_COMPAT, 'UTF-8')
        );
        $buttonIcon = htmlentities('icon-trash', ENT_COMPAT, 'UTF-8');
        $linkURL = Services::Registry()->get('Plugindata', 'page_url') . '/delete';
        $buttonArray = 'button_title:'
            . trim($buttonTitle)
            . ','
            . 'button_type:alert,'
            . 'button_link:'
            . $linkURL
            . ','
            . 'button_icon_prepend:'
            . $buttonIcon;

        $buttons[] = '{{' . trim($buttonArray) . '}}';

        return $buttons;
    }

    /**
     * Create Edit Buttons
     *
     * @return array
     * @since  1.0
     */
    protected function setEditButtons()
    {
        $buttons = array();

        /** Button 1: Back to Grid */
        $buttonTitle = str_replace(
        ' ',
        '&nbsp;',
        htmlentities(Services::Language()->translate('Back to Grid'), ENT_COMPAT, 'UTF-8')
        );
        $buttonIcon = htmlentities('icon-list-alt', ENT_COMPAT, 'UTF-8');
        $linkURL = '/admin/' . Services::Registry()->get('Parameters', 'catalog_alias');
        $buttonArray = 'button_title:'
            . trim($buttonTitle)
            . ','
            . 'button_type:secondary,'
            . 'button_link:'
            . $linkURL
            . ','
            . 'button_icon_prepend:'
            . $buttonIcon;

        $buttons[] = '{{' . trim($buttonArray) . '}}';

        /** Button 2: Revisions */
        $buttonTitle = str_replace(
            ' ',
            '&nbsp;',
            htmlentities(Services::Language()->translate('Revisions'), ENT_COMPAT, 'UTF-8')
        );
        $buttonLinkExtra = htmlentities('data-reveal-id:item-revisions', ENT_COMPAT, 'UTF-8');
        $buttonIcon = htmlentities('icon-time', ENT_COMPAT, 'UTF-8');
        $linkURL = $linkURL = Services::Registry()->get('Plugindata', 'page_url');
        $buttonArray = 'button_title:'
            . $buttonTitle
            . ','
            . 'button_type:secondary,'
            . 'button_link:' .
            $linkURL . ','
            . 'button_link_extra:'
            . $buttonLinkExtra . ','
            . 'button_icon_prepend:'
            . $buttonIcon;

        $buttons[] = '{{' . trim($buttonArray) . '}}';

        /** Button 3: Options */
        $buttonTitle = str_replace(
            ' ',
            '&nbsp;',
            htmlentities(Services::Language()->translate('Options'), ENT_COMPAT, 'UTF-8')
        );
        $buttonLinkExtra = htmlentities('data-reveal-id:item-options', ENT_COMPAT, 'UTF-8');
        $buttonIcon = htmlentities('icon-wrench', ENT_COMPAT, 'UTF-8');
        $linkURL = Services::Registry()->get('Plugindata', 'page_url');
        $buttonArray = 'button_title:'
            . $buttonTitle
            . ','
            . 'button_type:secondary,'
            . 'button_link:'
            . $linkURL
            . ','
            . 'button_link_extra:'
            . $buttonLinkExtra
            . ','
            . 'button_icon_prepend:'
            . $buttonIcon;

        $buttons[] = '{{' . trim($buttonArray) . '}}';

        return $buttons;
    }

    /**
     * Create List Buttons
     *
     * @return array
     * @since  1.0
     */
    protected function setListButtons()
    {
        $buttons = array();

        /** Button 1: Add Item */
        $buttonTitle = str_replace(
            ' ',
            '&nbsp;',
            htmlentities(Services::Language()->translate('Add Item'), ENT_COMPAT, 'UTF-8')
        );
        $buttonLinkExtra = htmlentities('data-reveal-id:resource-options', ENT_COMPAT, 'UTF-8');
        $buttonIcon = htmlentities('icon-plus', ENT_COMPAT, 'UTF-8');
        $linkURL = $linkURL = Services::Registry()->get('Plugindata', 'page_url');
        $buttonArray = 'button_title:'
            . $buttonTitle
            . ','
            . 'button_type:primary,'
            . 'button_link:' .
            $linkURL . ','
            . 'button_link_extra:'
            . $buttonLinkExtra . ','
            . 'button_icon_prepend:'
            . $buttonIcon;

        $buttons[] = '{{' . trim($buttonArray) . '}}';

        /** Button 2: Edit Resource */
        $buttonTitle = str_replace(
            ' ',
            '&nbsp;',
            htmlentities(Services::Language()->translate('Edit Resource'), ENT_COMPAT, 'UTF-8')
        );
        $buttonLinkExtra = htmlentities('data-reveal-id:item-options', ENT_COMPAT, 'UTF-8');
        $buttonIcon = htmlentities('icon-wrench', ENT_COMPAT, 'UTF-8');
        $linkURL = Services::Registry()->get('Plugindata', 'page_url');
        $buttonArray = 'button_title:'
            . $buttonTitle
            . ','
            . 'button_type:primary,'
            . 'button_link:'
            . $linkURL
            . ','
            . 'button_link_extra:'
            . $buttonLinkExtra
            . ','
            . 'button_icon_prepend:'
            . $buttonIcon;

        $buttons[] = '{{' . trim($buttonArray) . '}}';

        return $buttons;
    }

    /**
     * Create Dashboard Page Buttons
     *
     * @return array
     */
    protected function setDashboardButtons()
    {

        $buttons = array();

        /** Button 1: Add Portlet */
        $buttonTitle = str_replace(
            ' ',
            '&nbsp;',
            htmlentities(Services::Language()->translate('Add Portlet'), ENT_COMPAT, 'UTF-8')
        );
        $buttonIcon = htmlentities('icon-plus', ENT_COMPAT, 'UTF-8');
        $linkURL = '/admin/' . Services::Registry()->get('Parameters', 'catalog_alias');
        $buttonArray = 'button_title:'
            . trim($buttonTitle)
            . ','
            . 'button_type:primary,'
            . 'button_link:'
            . $linkURL
            . ','
            . 'button_icon_prepend:'
            . $buttonIcon;

        $buttons[] = '{{' . trim($buttonArray) . '}}';

        /** Button 2: Edit Resource */
        $buttonTitle = str_replace(
            ' ',
            '&nbsp;',
            htmlentities(Services::Language()->translate('Edit Dashboard'), ENT_COMPAT, 'UTF-8')
        );
        $buttonLinkExtra = htmlentities('data-reveal-id:item-options', ENT_COMPAT, 'UTF-8');
        $buttonIcon = htmlentities('icon-wrench', ENT_COMPAT, 'UTF-8');
        $linkURL = Services::Registry()->get('Plugindata', 'page_url');
        $buttonArray = 'button_title:'
            . $buttonTitle
            . ','
            . 'button_type:primary,'
            . 'button_link:'
            . $linkURL
            . ','
            . 'button_link_extra:'
            . $buttonLinkExtra
            . ','
            . 'button_icon_prepend:'
            . $buttonIcon;

        $buttons[] = '{{' . trim($buttonArray) . '}}';

        return $buttons;
    }
}
