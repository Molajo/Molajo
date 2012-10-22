<?php
/**
 * @package    Molajo
 * @copyright  2012 Individual Molajo Contributors. All rights reserved.
 * @license    GNU GPL v 2, or later and MIT, see License folder
 */
namespace Molajo\Plugin\Application2;

use Molajo\Application;
use Molajo\Plugin\Plugin\Plugin;
use Molajo\Service\Services;

defined('MOLAJO') or die;

/**
 * @package     Molajo
 * @subpackage  Plugin
 * @since       1.0
 */
class Application2Plugin extends Plugin
{
    /**
     * Prepares Application2 Menus
     *
     * @return boolean
     * @since   1.0
     */
    public function onBeforeParse()
    {
        /** Only used for the Site */
        if (APPLICATION_ID == 2) {
        } else {
            return true;
        }

        $current_menuitem_id = (int) $this->get('menuitem_id');

        $item_indicator = 0;
        if ((int) $current_menuitem_id == 0) {
            $item_indicator = 1;
            $current_menuitem_id = (int) $this->get('parent_menu_id');
        }

        if ((int) $current_menuitem_id == 0) {
            return true;
        }

        $this->urls();

        $this->setBreadcrumbs($current_menuitem_id);

        $this->setMenu($current_menuitem_id);

        $this->setPageTitle($item_indicator);

		$this->setPageActions();

        return true;
    }


	/**
	 * Prepares Application2 Menus
	 *
	 * @return boolean
	 * @since   1.0
	 */
	public function onBeforeHead()
	{
		/** Only used for the Site */
		if (APPLICATION_ID == 2) {
		} else {
			return true;
		}

		$this->setPageMeta();
	}

    /**
     * Build the home and page url to be used in links
     *
     * @return boolean
     * @since   1.0
     */
    protected function urls()
    {
        $url = Application::Request()->get('base_url_path_for_application') .
            Application::Request()->get('requested_resource_for_route');

        Services::Registry()->set('Plugindata', 'page_url', $url);

        Services::Asset()->addLink($url, 'canonical', 'rel', array(), 1);

        $url = Services::Registry()->get('Configuration', 'application_base_url');

        Services::Registry()->set('Plugindata', 'home_url', $url);

        return true;
    }

    /**
     * Set breadcrumbs
     *
     * @return boolean
     * @since  1.0
     */
    protected function setBreadcrumbs($current_menuitem_id)
    {
        $bread_crumbs = Services::Menu()->getMenuBreadcrumbIds($current_menuitem_id);

        Services::Registry()->set('Plugindata', 'Breadcrumbs', $bread_crumbs);

        return true;
    }

    /**
     * Retrieve an array of values that represent the active menuitem ids for a specific menu
     *
     * @return boolean
     * @since  1.0
     */
    protected function setMenu($current_menu_item = 0)
    {
        $bread_crumbs = Services::Registry()->get('Plugindata', 'Breadcrumbs');

        if ($bread_crumbs == false || count($bread_crumbs) == 0) {
            $query_results = array();
        } else {
            $menu_id = $bread_crumbs[0]->extension_id;

            $query_results = Services::Menu()->get($menu_id, $current_menu_item, $bread_crumbs);
        }

        Services::Registry()->set('Plugindata', 'Adminapplicationmenu', $query_results);

        return true;
    }

    /**
     * Set the Header Title
     *
     * @return boolean
     * @since   1.0
     */
    protected function setPageTitle($item_indicator = 0)
    {
        $title = '<strong> Molajo</strong> '. Services::Language()->translate('Site');

        Services::Registry()->set('Plugindata', 'HeaderTitle', $title);

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
	 * Prepares Page Title and Buttons for Rendering
	 *
	 * @return boolean
	 * @since   1.0
	 */
	protected function setPageActions()
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

	/**
	 * Set Page Meta Data
	 *
	 * @return boolean
	 * @since   1.0
	 */
	protected function setPageMeta()
	{
		$title = Services::Registry()->get('Metadata', 'title', '');
		$description = Services::Registry()->get('Metadata', 'description', '');
		$author = Services::Registry()->get('Metadata', 'author', '');
		$robots = Services::Registry()->get('Metadata', 'robots', '');

		if ($title == '' || $description == '' || $author == '' || $robots == '') {
		} else {
			return true;
		}

		$data = Services::Registry()->get('Plugindata', 'primary_query_results');
		$type = strtolower(Services::Registry()->get('RouteParameters', 'page_type'));
		$type = strtolower($type);

		if (trim($title) == '') {
			if ($type == 'item') {
				if (isset($data[0]->title)) {
					$title = $data[0]->title;
				}
			}
			if ($title == '') {
				$title = Services::Registry()->get('RouteParameters', 'criteria_title', '');
			}

			if ($title == '') {
			} else {
				$title .= ': ';
			}

			$title .= Services::Registry()->get('Configuration', 'site_name');

			Services::Metadata()->set('title', $title);
		}

		if (trim($description) == '') {

			if ($type == 'item') {

				if (isset($data[0]->description)) {
					$description = $data[0]->description;

				} elseif (isset($data[0]->content_text_snippet)) {
					$description = $data[0]->content_text_snippet;
				}
			}

			Services::Metadata()->set('description', $description);
		}

		if (trim($author) == '') {

			if ($type == 'item') {

				if (isset($data[0]->author_full_name)) {
					$author = $data[0]->author_full_name;
					Services::Metadata()->set('author', $author);
				}
			}
		}

		if (trim($robots) == '') {
			Services::Metadata()->set('robots', 'follow,index');
		}

		return true;
	}

}
