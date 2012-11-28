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
     * Note: Services::Registry()->get(PARAMETERS_LITERAL) is valid during this method.
     *  Following, Services::Registry()->get('RequestParameters') should be used
     *
     * @return  boolean
     * @since   1.0
     */
    public function onBeforeParse()
    {
        $current_menuitem_id = (int)$this->get('menuitem_id');

        $item_indicator = 0;
        if ((int)$current_menuitem_id == 0) {
            $item_indicator = 1;
            $current_menuitem_id = (int)$this->get('parent_menu_id');
        }

        if ((int)$current_menuitem_id == 0) {
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
     * @return  boolean
     * @since   1.0
     */
    public function onBeforeDocumenthead()
    {
        $this->setPageMeta();
    }

    /**
     * Build the home and page url to be used in links
     *
     * @return  boolean
     * @since   1.0
     */
    protected function urls()
    {
        $url = Services::Registry()->get(CONFIGURATION_LITERAL, 'application_base_url');
        Services::Registry()->set(STRUCTURE_LITERAL, 'home_url', $url);

        $url = Services::Registry()->get(PARAMETERS_LITERAL, 'request_base_url_path') .
            Services::Registry()->get(PARAMETERS_LITERAL, 'request_url');
        Services::Registry()->set(STRUCTURE_LITERAL, 'page_url', $url);
        Services::Asset()->addLink($url, 'canonical', 'rel', array(), 1);

        $resource = $this->get('extension_name_path_node', '');
        $url = Services::Registry()->get(CONFIGURATION_LITERAL, 'application_base_url') . '/' . strtolower($resource);
        Services::Registry()->set(STRUCTURE_LITERAL, 'resource_url', $url);

        //todo: add links for prev and next

        return true;
    }

    /**
     * Set Breadcrumbs for the page
     *
     * @return  boolean
     * @since   1.0
     */
    protected function setBreadcrumbs($current_menuitem_id)
    {
        $bread_crumbs = Services::Menu()->getMenuBreadcrumbIds($current_menuitem_id);

        Services::Registry()->set(STRUCTURE_LITERAL, 'Breadcrumbs', $bread_crumbs);

        return true;
    }

    /**
     * Retrieve an array of values that represent the active menuitem ids for a specific menu
     *
     * @return  boolean
     * @since   1.0
     */
    protected function setMenu($current_menu_item = 0)
    {
        $bread_crumbs = Services::Registry()->get(STRUCTURE_LITERAL, 'Breadcrumbs');

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

        Services::Registry()->set(STRUCTURE_LITERAL, $menuname, $query_results);

        return true;
    }

    /**
     * Set the Header Title
     *
     * @return  boolean
     * @since   1.0
     */
    protected function setPageTitle($item_indicator = 0)
    {
        $title = Services::Registry()->get(CONFIGURATION_LITERAL, 'application_name');
        if ($title == '') {
            $title = '<strong> Molajo</strong> ' . Services::Language()->translate(APPLICATION_NAME);
        }
        Services::Registry()->set(STRUCTURE_LITERAL, 'HeaderTitle', $title);

        Services::Registry()->set(STRUCTURE_LITERAL, 'page_type', $this->get('page_type'));

        $heading1 = $this->get('criteria_title');
        $page_type = $this->get('page_type');
        if ($page_type == 'Grid') {
            $page_type = QUERY_OBJECT_LIST;
        }

        $list_current = 0;
        $configuration_current = 0;
        $new_current = 0;
        if (strtolower($page_type) == QUERY_OBJECT_ITEM) {
            $new_current = 1;
        } elseif (strtolower($page_type) == PAGE_TYPE_CONFIGURATION) {
            $configuration_current = 1;
        } else {
            $list_current = 1;
        }

        $display_page_type = Services::Language()->translate(strtoupper($page_type));
//		$request_action = $this->get('request_action');
        $heading2 = ucfirst(strtolower($page_type));

        Services::Registry()->set(STRUCTURE_LITERAL, 'heading1', $heading1);
        Services::Registry()->set(STRUCTURE_LITERAL, 'heading2', $heading2);

        $resource_menu_item = array();

        Services::Registry()->get(STRUCTURE_LITERAL, 'resource_url');

        $row = new \stdClass();
        $row->link_text = Services::Language()->translate('GRID');
        $row->link = Services::Registry()->get(STRUCTURE_LITERAL, 'resource_url');
        $row->current = $list_current;
        $query_results[] = $row;

        $row = new \stdClass();
        $row->link_text = Services::Language()->translate(CONFIGURATION_LITERAL);
        $row->link = Services::Registry()->get(STRUCTURE_LITERAL, 'resource_url') . '/' . CONFIGURATION_LITERAL;
        $row->current = $configuration_current;
        $query_results[] = $row;

        $row = new \stdClass();
        $row->link_text = Services::Language()->translate('NEW');
        $row->link = Services::Registry()->get(STRUCTURE_LITERAL, 'resource_url') . '/' . 'new';
        $row->current = $new_current;
        $query_results[] = $row;

        Services::Registry()->set(STRUCTURE_LITERAL, 'PageSubmenu', $query_results);

        return true;
    }

    /**
     * Prepares Page Title and Actions for Rendering
     *
     * @return  boolean
     * @since   1.0
     */
    protected function setPageEligibleActions()
    {
        if ($this->get('page_type') == QUERY_OBJECT_ITEM) {

            if (strtolower(Services::Registry()->get(PARAMETERS_LITERAL, 'request_action')) == ACTION_VIEW) {
                $actions = $this->setItemActions();
            } else {
                $actions = $this->setEditActions();
            }

        } elseif ($this->get('page_type') == QUERY_OBJECT_LIST) {
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

        Services::Registry()->set(STRUCTURE_LITERAL, 'PageEligibleActions', $query_results);

        return true;
    }

    /**
     * Create Item Actions
     *
     * @return  array
     * @since   1.0
     */
    protected function setItemActions()
    {
        // Currently display

        $actions = array();
        $actions[] = 'create';
        $actions[] = 'copy';
        $actions[] = ACTION_VIEW;
        $actions[] = 'edit';

        // editing item
        $actions[] = ACTION_VIEW;
        $actions[] = 'copy';
        $actions[] = 'draft';
        $actions[] = 'save';
        $actions[] = 'restore';
        $actions[] = 'cancel';

        // either
        $actions[] = 'tag';
        $actions[] = 'categorize';
        $actions[] = 'status'; // archive, publish, unpublish, trash, spam, version
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
     * @return  array
     * @since   1.0
     */
    protected function setEditActions()
    {
        $actions = array();

        return $actions;
    }

    /**
     * Create List Actions
     *
     * @return  array
     * @since   1.0
     */
    protected function setListActions()
    {
        $actions = array();

        $actions[] = 'create';
        $actions[] = 'copy';
        $actions[] = 'edit';

        $actions[] = 'tag';
        $actions[] = 'categorize';
        $actions[] = 'status'; // archive, publish, unpublish, trash, spam, version
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
     * Menu Item Actions
     *
     * @return  array
     * @since   1.0
     */
    protected function setMenuitemActions()
    {
        $actions = array();

        return $actions;
    }

    /**
     * Set Page Meta Data
     *
     * @return  boolean
     * @since   1.0
     */
    protected function setPageMeta()
    {
        $title = Services::Registry()->get(METADATA_LITERAL, 'title', '');
        $description = Services::Registry()->get(METADATA_LITERAL, 'description', '');
        $author = Services::Registry()->get(METADATA_LITERAL, 'author', '');
        $robots = Services::Registry()->get(METADATA_LITERAL, 'robots', '');

        if ($title == '' || $description == '' || $author == '' || $robots == '') {
        } else {
            return true;
        }

        $data = Services::Registry()->get(
            PRIMARY_LITERAL,
            DATA_LITERAL
        );

        $type = strtolower(Services::Registry()->get(STRUCTURE_LITERAL, 'page_type'));
        $type = strtolower($type);

        if (trim($title) == '') {
            if ($type == QUERY_OBJECT_ITEM) {
                if (isset($data[0]->title)) {
                    $title = $data[0]->title;
                }
            }
            if ($title == '') {
                $title = Services::Registry()->get(ROUTE_PARAMETERS_LITERAL, 'criteria_title', '');
            }

            if ($title == '') {
                $title = Services::Registry()->set(STRUCTURE_LITERAL, 'HeaderTitle', '');
            }

            if ($title == '') {
            } else {
                $title .= ': ';
            }

            $title .= SITE_NAME;

            Services::Metadata()->set('title', $title);
        }

        if (trim($description) == '') {

            if ($type == QUERY_OBJECT_ITEM) {

                if (isset($data[0]->description)) {
                    $description = $data[0]->description;

                } elseif (isset($data[0]->content_text_snippet)) {
                    $description = $data[0]->content_text_snippet;
                }
            }

            Services::Metadata()->set('description', $description);
        }

        if (trim($author) == '') {

            if ($type == QUERY_OBJECT_ITEM) {

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
