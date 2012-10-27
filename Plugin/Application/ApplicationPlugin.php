<?php
/**
 * @package    Molajo
 * @copyright  2012 Individual Molajo Contributors. All rights reserved.
 * @license    GNU GPL v 2, or later and MIT, see License folder
 */
namespace Molajo\Plugin\Application;

use Molajo\Application;
use Molajo\Plugin\Plugin\Plugin;
use Molajo\Service\Services;

defined('MOLAJO') or die;

/**
 * @package     Molajo
 * @subpackage  Plugin
 * @since       1.0
 */
class ApplicationPlugin extends Plugin
{
    /**
     * Prepares Application Menus
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

		$this->setPageEligibleActions();

        return true;
    }

	/**
	 * Prepares Application Menus
	 *
	 * @return boolean
	 * @since   1.0
	 */
	public function onBeforeHead()
	{
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

		//todo: add links for prev and next

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

		$menuname = '';
		$query_results = array();

        if ($bread_crumbs == false || count($bread_crumbs) == 0) {
			return true;
        }

		$menu_id = $bread_crumbs[0]->extension_id;

		$query_results = Services::Menu()->get($menu_id, $current_menu_item, $bread_crumbs);

		if ($query_results == false || count($query_results) == 0) {
			$menuname = '';
		} else {
			$menuname = $query_results[0]->extensions_name;
		}

		if ($menuname == '') {
			return true;
		}

        Services::Registry()->set('Plugindata', $menuname, $query_results);

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
		$title = Services::Registry()->get('Configuration', 'application_name');
		if ($title == '') {
        	$title = '<strong> Molajo</strong> '. Services::Language()->translate(APPLICATION_NAME);
		}

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
	 * Prepares Page Title and Actions for Rendering
	 *
	 * @return boolean
	 * @since   1.0
	 */
	protected function setPageEligibleActions()
	{

		if ($this->get('page_type') == 'item') {
			$actions = $this->setItemActions();

		} elseif ($this->get('page_type') == 'form') {
			$actions = $this->setEditActions();

		} elseif ($this->get('page_type') == 'list') {
			$actions = $this->setListActions();

		} else {
			$actions = $this->setMenuitemActions();
		}

		if ($actions === false) {
			$actionCount = 0;
		} else {
			$actionCount = count($actions);
		}

		$query_results = array();

		$row = new \stdClass();
		$row->action_count = $actionCount;
		$row->action_array = '';

		if ($actionCount === 0) {
			$row->action_array = null;
		} else {
			foreach ($actions as $action) {
				$row->action_array .= trim($action);
			}
		}

		$query_results[] = $row;

		Services::Registry()->set('Plugindata', 'PageEligibleActions', $query_results);

		return true;
	}

	/**
	 * Create Item Actions
	 *
	 * @return array
	 * @since  1.0
	 */
	protected function setItemActions()
	{
		// Currently display

		$actions = array();
		$actions[] = 'create';
		$actions[] = 'copy';
		$actions[] = 'display';
		$actions[] = 'edit';

		// editing item
		$actions[] = 'display';
		$actions[] = 'copy';
		$actions[] = 'draft';
		$actions[] = 'save';
		$actions[] = 'restore';
		$actions[] = 'cancel';

		// either
		$actions[] = 'tag';
		$actions[] = 'categorize';
		$actions[] = 'status';  // archive, publish, unpublish, trash, spam, version
		$actions[] = 'sticky';
		$actions[] = 'feature';
		$actions[] = 'delete';

		// list
		$actions[] = 'orderup';
		$actions[] = 'orderdown';
		$actions[] = 'reorder';
		$actions[] = 'status';


		return $actions;
	}

	/**
	 * Create Edit Actions
	 *
	 * @return array
	 * @since  1.0
	 */
	protected function setEditActions()
	{
		$actions = array();


		return $actions;
	}

	/**
	 * Create List Actions
	 *
	 * @return array
	 * @since  1.0
	 */
	protected function setListActions()
	{
		$actions = array();

		$actions[] = 'create';
		$actions[] = 'copy';
		$actions[] = 'edit';

		$actions[] = 'tag';
		$actions[] = 'categorize';
		$actions[] = 'status';  // archive, publish, unpublish, trash, spam, version
		$actions[] = 'sticky';
		$actions[] = 'feature';
		$actions[] = 'delete';

		$actions[] = 'orderup';
		$actions[] = 'orderdown';
		$actions[] = 'reorder';
		$actions[] = 'status';


		return $actions;
	}

	/**
	 * Create Dashboard Page Actions
	 *
	 * @return array
	 */
	protected function setMenuitemActions()
	{
		$actions = array();

		return $actions;
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
