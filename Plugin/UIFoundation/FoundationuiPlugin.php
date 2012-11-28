<?php
/**
 * @package    Molajo
 * @copyright  2012 Individual Molajo Contributors. All rights reserved.
 * @license    GNU GPL v 2, or later and MIT, see License folder
 */
namespace Molajo\Plugin\UIFoundation;

use Molajo\Plugin\Plugin\Plugin;

defined('MOLAJO') or die;

/**
 * @package     Molajo
 * @subpackage  Plugin
 * @since       1.0
 */
class FoundationuiPlugin extends Plugin
{
	/**
	 * Prepares Application2 Menus
	 *
	 * @return boolean
	 * @since   1.0
	 */
	public function onBeforeParse()
	{
		$this->setUIObjects();

		return true;
	}

    /**
     * Prepares data for the Uifoundation UI Views
     *
     * @return boolean
     * @since   1.0
     */
    public function onBeforeViewRender()
    {

        if (strtolower($this->get('template_view_path_node')) == 'uibuttonfoundation') {
            $this->button_general();

        } elseif (strtolower($this->get('template_view_path_node')) == 'uibuttongroupfoundation') {
            $this->button_group();

        } elseif (strtolower($this->get('template_view_path_node')) == 'uibuttondropdownfoundation') {
            $this->button_dropdown();

        } elseif (strtolower($this->get('template_view_path_node')) == 'uinavigationtabfoundation') {
            $this->tab();

        } else {
            return true;
        }

        return true;
    }

	/**
	 * Prepares Page Title and Buttons for Rendering
	 *
	 * @return boolean
	 * @since   1.0
	 */
	protected function setUIObjects()
	{
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

		Services::Registry()->set('Plugindata', 'Toolbar', $query_results);

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
		if ($this->get('page_type') == QUERY_OBJECT_ITEM) {

			return $this->setItemButtons();

		} elseif ($this->get('page_type') == QUERY_OBJECT_ITEM) {
			return $this->setEditButtons();

		} elseif ($this->get('page_type') == QUERY_OBJECT_LIST) {
			return $this->setListButtons();
		}

		return $this->setDashboardButtons();
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
		$linkURL = '/admin/' . Services::Registry()->get(PARAMETERS_LITERAL, 'catalog_alias');
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
		$linkURL = Services::Registry()->get(STRUCTURE_LITERAL, 'page_url') . '/edit';
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
		$linkURL = Services::Registry()->get(STRUCTURE_LITERAL, 'page_url') . '/delete';
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
		$linkURL = '/admin/' . Services::Registry()->get(PARAMETERS_LITERAL, 'catalog_alias');
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
		$linkURL = $linkURL = Services::Registry()->get(STRUCTURE_LITERAL, 'page_url');
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
		$linkURL = Services::Registry()->get(STRUCTURE_LITERAL, 'page_url');
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
		$linkURL = $linkURL = Services::Registry()->get(STRUCTURE_LITERAL, 'page_url');
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
		$linkURL = Services::Registry()->get(STRUCTURE_LITERAL, 'page_url');
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
		$linkURL = '/admin/' . Services::Registry()->get(PARAMETERS_LITERAL, 'catalog_alias');
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
		$linkURL = Services::Registry()->get(STRUCTURE_LITERAL, 'page_url');
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
     * Uifoundation Buttons
     *
     * @return boolean
     * @since   1.0
     */
    protected function button_general()
    {
        $button_type = $this->data->button_type;
        $button_size = $this->data->button_size;
        $button_shape = $this->data->button_shape;

        $button_class = trim($button_type);
        $button_class = trim($button_class) . ' ' . trim($button_shape);
        $button_class = trim($button_class) . ' ' . trim($button_size);
        $button_class = trim($button_class) . ' ' . 'button';

        $button_class = ' class="' . htmlspecialchars(trim($button_class), ENT_NOQUOTES, 'UTF-8') . '"';

        $this->saveField(null, 'button_class', $button_class);

        return true;
    }

    /**
     * Uifoundation Button Group and Button Bar
     *
     * @return boolean
     * @since   1.0
     */
    protected function button_group()
    {

        $button_type = $this->data->button_group_type;
        $button_size = $this->data->button_group_size;
        $button_shape = $this->data->button_group_shape;
        $button_class = str_replace(',', ' ', $this->data->button_group_class);

        $button_group_class = trim($button_type);
        $button_group_class = trim($button_group_class) . ' ' . trim($button_shape);
        $button_group_class = trim($button_group_class) . ' ' . trim($button_size);
        $button_group_class = trim($button_group_class) . ' ' . trim($button_class);
        $button_group_class = trim($button_group_class) . ' ' . 'button-group';

        $button_group_class = ' class="' . htmlspecialchars(trim($button_group_class), ENT_NOQUOTES, 'UTF-8') . '"';
        $this->saveField(null, 'button_group_class', $button_group_class);

        $button_array = $this->getButtons($this->data->button_group_array);
        $this->saveField(null, 'button_group_array', $button_array);

        return true;
    }

    /**
     * Uifoundation Dropdown buttons
     *
     * @return boolean
     * @since   1.0
     */
    protected function button_dropdown()
    {
        $button_type = $this->data->button_dropdown_type;
        $button_size = $this->data->button_dropdown_size;
        $button_shape = $this->data->button_dropdown_shape;
        $button_class = str_replace(',', ' ', $this->data->button_dropdown_class);

        $button_dropdown_class = trim($button_type);
        $button_dropdown_class = trim($button_dropdown_class) . ' ' . trim($button_shape);
        $button_dropdown_class = trim($button_dropdown_class) . ' ' . trim($button_size);
        $button_dropdown_class = trim($button_dropdown_class) . ' ' . trim($button_class);
        $button_dropdown_class = trim($button_dropdown_class) . ' ' . 'button';

        $button_dropdown_class = ' class="' . htmlspecialchars(trim($button_dropdown_class), ENT_NOQUOTES, 'UTF-8') . '"';

        $this->saveField(null, 'button_dropdown_class', $button_dropdown_class);

        $button_array = $this->getButtons($this->data->button_group_array);
        $this->saveField(null, 'button_group_array', $button_array);

        return true;
    }

    /**
     * Get individual buttons
     *
     * @param $buttons
     * @return array
     */
    protected function getButtons($buttons)
    {
        $button_array = array();
        $temp = explode('}}', $buttons);

        foreach ($temp as $set) {
            $set = str_replace(',', ' ', $set);
            $set = str_replace(':', '=', $set);
            $set = str_replace('{{', '', $set);
            $set = str_replace('http=', 'http:', $set);
            if (trim($set) == '') {
            } else {
                $button_array[] = trim($set);
            }
        }

        return $button_array;
    }

    /**
     * Uifoundation Tab Group
     *
     * @return boolean
     * @since   1.0
     */
    protected function page()
    {
        $page_array = $this->getPages($this->data->page_array);
        $this->saveField(null, 'page_array', $page_array);

        return true;
    }

    /**
     * Get page sections
     *
     * @param $pages
     * @return array
     */
    protected function getPages($pages)
    {
        $page_array = array();
        $temp_array = array();
        $temp = explode('}}', $pages);

        foreach ($temp as $set) {
            $set = str_replace(',', ' ', $set);
            $set = str_replace(':', '=', $set);
            $set = str_replace('{{', '', $set);
            $set = str_replace('http=', 'http:', $set);
            if (trim($set) == '') {
            } else {
                $temp_array[] = trim($set);
            }
        }

        foreach ($temp_array as $set) {

            $fields = explode(' ', $set);
            foreach ($fields as $field) {
                $temp = explode('=', $field);
                $pairs[$temp[0]] = $temp[1];
            }

            $row = new \stdClass();
            foreach ($pairs as $key=>$value) {
                $row->$key = $value;
            }
            $page_array[] = $row;
        }

        return $page_array;
    }
}
