<?php
/**
 * Application Plugin
 *
 * @package    Molajo
 * @copyright  2013 Amy Stephen. All rights reserved.
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 */
namespace Molajo\Plugin\Application;

use stdClass;
use Exception;
use CommonApi\Event\SystemInterface;
use CommonApi\Exception\RuntimeException;
use Molajo\Plugin\SystemEventPlugin;

/**
 * Application Plugin
 *
 * @package  Molajo
 * @license  http://www.opensource.org/licenses/mit-license.html MIT License
 * @since    1.0
 */
class ApplicationPlugin extends SystemEventPlugin implements SystemInterface
{
    /**
     * Prepares Page Information storing results in $this->runtime_data->page
     *
     * @return  $this
     * @since   1.0
     * @throws  \CommonApi\Exception\RuntimeException
     */
    public function onBeforeExecute()
    {
        if ($this->runtime_data->request->client->ajax == 1) {
            return $this;
        }

        $page_type = strtolower($this->runtime_data->route->page_type);

        if ($page_type == 'item' || $page_type == 'form' || $page_type == 'list') {
            $current_menuitem_id = $this->runtime_data->resource->parameters->parent_menu_id;
        } else {
            $current_menuitem_id = $this->runtime_data->resource->menuitem->id;
        }

        $this->runtime_data->page->menuitem_id = $current_menuitem_id;
        $model                                 = 'Molajo//Menuitem//' . $current_menuitem_id;

        $this->runtime_data->page->menuitem = $this->resource->get('Menuitem:///' . $model);
        $this->runtime_data->page->menu     = $this->runtime_data->page->menuitem->menu;

        $this->runtime_data->page->current_menuitem_id = $current_menuitem_id;

        $this->getUrls();

        $this->runtime_data->page->breadcrumbs = $this->getMenuBreadcrumbIds();

        $this->getMenu();

        $this->getPageTitle($page_type);

        $this->setPageEligibleActions($page_type);

        $this->setPageMeta($page_type);

        return $this;
    }

    /**
     * Get Menu Item ID
     *
     * @return  int
     * @since   1.0
     * @throws  \CommonApi\Exception\RuntimeException
     */
    protected function getItemMenuItemId()
    {
        $options                 = array();
        $options['runtime_data'] = $this->runtime_data;
        $options['parameters']   = $this->parameters;
        $controller              = $this->resource->get('query:///Molajo//Datasource//Catalog.xml', $options);

        $controller->setModelRegistry('check_view_level_access', 0);
        $controller->setModelRegistry('process_events', 0);
        $controller->setModelRegistry('get_customfields', 0);
        $controller->setModelRegistry('query_object', 'result');

        $controller->model->query->select(
            $controller->model->database->qn($controller->getModelRegistry('primary_prefix', 'a'))
            . ' . '
            . $controller->model->database->qn('source_id')
        );

        $controller->model->query->where(
            $controller->model->database->qn($controller->getModelRegistry('primary_prefix', 'a'))
            . ' . '
            . $controller->model->database->qn('catalog_type_id')
            . ' = '
            . (int)$this->runtime_data->reference_data->catalog_type_menuitem_id
        );

        $controller->model->query->where(
            $controller->model->database->qn($controller->getModelRegistry('primary_prefix', 'a'))
            . ' . '
            . $controller->model->database->qn('application_id')
            . ' = '
            . (int)$this->runtime_data->application->id
        );

        $controller->model->query->where(
            $controller->model->database->qn($controller->getModelRegistry('primary_prefix', 'a'))
            . ' . '
            . $controller->model->database->qn('enabled')
            . ' = '
            . ' 1 '
        );

        $controller->model->query->where(
            $controller->model->database->qn($controller->getModelRegistry('primary_prefix', 'a'))
            . ' . '
            . $controller->model->database->qn('page_type')
            . ' <> '
            . $controller->model->database->q('link')
        );

        $controller->model->query->where(
            $controller->model->database->qn($controller->getModelRegistry('primary_prefix', 'a'))
            . ' . '
            . $controller->model->database->qn('sef_request')
            . ' = '
            . $controller->model->database->q($this->runtime_data->route->b_alias)
        );

        try {
            return $controller->getData();
        } catch (Exception $e) {
            throw new RuntimeException ($e->getMessage());
        }
    }

    /**
     * Build the home and page url to be used in links
     *
     * @return  $this
     * @since   1.0
     * @throws  \CommonApi\Exception\RuntimeException
     */
    protected function getUrls()
    {
        $this->runtime_data->page->urls['home']      = $this->runtime_data->application->base_url;
        $this->runtime_data->page->urls['page']      = $this->runtime_data->request->data->url;
        $this->runtime_data->page->urls['canonical'] = $this->runtime_data->request->data->url;
        $this->runtime_data->page->urls['resource']
                                                     = $this->runtime_data->application->base_url . strtolower(
                $this->runtime_data->route->b_alias
            );

        //@todo add links for prev and next
        return $this;
    }

    /**
     * Retrieves an array of active menuitems, including the current menuitem and its parents
     *
     * @return  array
     * @since   1.0
     */
    public function getMenuBreadcrumbIds()
    {
        if ($this->runtime_data->page->current_menuitem_id == 0) {
            return array();
        }

        $controller = $this->resource->get('query:///Molajo//Datasource//MenuitemsNested.xml');

        $controller->setModelRegistry('check_view_level_access', 1);
        $controller->setModelRegistry('process_events', 0);
        $controller->setModelRegistry('get_customfields', 1);
        $controller->setModelRegistry('query_object', 'list');
        $controller->setModelRegistry('model_offset', 0);
        $controller->setModelRegistry('model_count', 9999);

        $controller->model->query->where(
            $controller->model->database->qn('current_menuitem.id')
            . ' = ' . (int)$this->runtime_data->page->current_menuitem_id
        );
        $controller->model->query->where($controller->model->database->qn('a.status') . ' > 0');

        $controller->model->query->order('a.lft DESC');

        $query_results = $controller->getData();

        $look_for_parent = 0;

        $select = array();
        $i      = 0;
        foreach ($query_results as $item) {

            $this->runtime_data->page->extension_id = $item->extension_id;

            if ($look_for_parent == 0) {
                $select[]        = $i;
                $look_for_parent = $item->parent_id;

            } else {
                if ($look_for_parent == $item->id) {
                    $select[]        = $i;
                    $look_for_parent = $item->parent_id;
                }
            }
            $i ++;
        }

        rsort($select);
        $breadcrumbs = array();
        foreach ($select as $index) {
            $breadcrumbs[] = $query_results[$index];
        }

        return $breadcrumbs;
    }

    /**
     * Retrieve an array of values that represent the active menuitem ids for a specific menu
     *
     * @return  $this
     * @since   1.0
     * @throws  \CommonApi\Exception\RuntimeException
     */
    protected function getMenu()
    {
        $menu_id = $this->runtime_data->page->extension_id;

        $controller = $this->resource->get('query:///Molajo//Datasource//Menuitem.xml');

        $controller->setModelRegistry('check_view_level_access', 1);
        $controller->setModelRegistry('process_events', 0);
        $controller->setModelRegistry('get_customfields', 0);
        $controller->setModelRegistry('query_object', 'list');
        $controller->setModelRegistry('model_offset', 0);
        $controller->setModelRegistry('model_count', 9999);

        $prefix = $controller->getModelRegistry('primary_prefix', 'a');

        $controller->model->query->where(
            $controller->model->database->qn($prefix)
            . '.' . $controller->model->database->qn('extension_id')
            . ' = ' . (int)$menu_id
        );
        $controller->model->query->where(
            $controller->model->database->qn($prefix)
            . '.' . $controller->model->database->qn('status')
            . ' > 0 '
        );
        $controller->model->query->where(
            $controller->model->database->qn('catalog')
            . '.' . $controller->model->database->qn('enabled')
            . ' = 1 '
        );
        $controller->model->query->where(
            $controller->model->database->qn('catalog')
            . '.' . $controller->model->database->qn('application_id')
            . ' = ' . (int)$this->runtime_data->application->id
        );

        $controller->model->query->order(
            $controller->model->database->qn($prefix)
            . '.' . $controller->model->database->qn('menu')
        );
        $controller->model->query->order(
            $controller->model->database->qn($prefix)
            . '.' . $controller->model->database->qn('lft')
        );

        $query_results = $controller->getData();

        if (count($query_results) === 0) {
            return $this;
        }

        $current_menu_item = $this->runtime_data->page->current_menuitem_id;
        $breadcrumbs       = $this->runtime_data->page->breadcrumbs;

        foreach ($query_results as $item) {

            $item->menu_id = $item->extension_id;
            $menu_name     = $item->menu;

            if ($item->id == $current_menu_item && (int)$current_menu_item > 0) {
                $item->css_class = 'current';
                $item->current   = 1;
            } else {
                $item->css_class = '';
                $item->current   = 0;
            }

            $item->active = 0;
            foreach ($breadcrumbs as $crumb) {
                if ($item->id == $crumb->id) {
                    $item->css_class .= ' active';
                    $item->active = 1;
                }
            }

            $item->css_class = trim($item->css_class);

            $base = $this->runtime_data->application->base_url;

            if ($this->runtime_data->application->parameters->url_sef == 1) {
                $item->url = $base . $item->catalog_sef_request;
            } else {
                $item->url = $base . 'index.php?id=' . (int)$item->catalog_id;
            }

            if ($item->subtitle == '' || $item->subtitle == null) {
                $item->link_text = $item->title;
            } else {
                $item->link_text = $item->subtitle;
            }

            $item->link = $item->url;
        }

        $this->runtime_data->page->breadcrumbs = $breadcrumbs;

        $this->runtime_data->page->menu             = array();
        $this->runtime_data->page->menu[$menu_name] = $query_results;

        return $this;
    }

    /**
     * Set the Header Title
     *
     * @param   string  $page_type
     *
     * @return  $this
     * @since   1.0
     * @throws  \CommonApi\Exception\RuntimeException
     */
    protected function getPageTitle($page_type)
    {
        $this->runtime_data->page->page_type = $page_type;

        $title = $this->runtime_data->application->name;
        if ($title == '') {
            $title = 'Molajo Application';
        }

        $this->runtime_data->page->header_title = $title;

        if ($page_type == 'item') {
            $heading1 = $this->runtime_data->resource->parameters->criteria_title;

        } elseif ($page_type == 'list') {

        } else {
            $heading1 = $this->runtime_data->resource->parameters->criteria_title;
        }

        $list_current          = 0;
        $configuration_current = 0;
        $new_current           = 0;
        if ($page_type == 'item') {
            $new_current = 1;
        } elseif ($page_type == 'configuration') {
            $configuration_current = 1;
        } else {
            $list_current = 1;
        }

        $display_page_type = $this->language_controller->translate(
            strtoupper($this->runtime_data->page->page_type)
        );

//		$action_id = $this->get('request_action');
        $heading2 = ucfirst($page_type);

        $this->runtime_data->page->heading1 = $heading1;
        $this->runtime_data->page->heading2 = $heading2;

        $temp_row             = new stdClass();
        $temp_row->link_text  = $this->language_controller->translate('GRID');
        $temp_row->link       = $this->runtime_data->page->urls['resource'];
        $temp_row->current    = $list_current;
        $temp_query_results[] = $temp_row;

        $temp_row             = new stdClass();
        $temp_row->link_text  = $this->language_controller->translate('Configuration');
        $temp_row->link       = $this->runtime_data->page->urls['resource'] . '/' . 'configuration';
        $temp_row->current    = $configuration_current;
        $temp_query_results[] = $temp_row;

        $temp_row             = new stdClass();
        $temp_row->link_text  = $this->language_controller->translate('NEW');
        $temp_row->link       = $this->runtime_data->page->urls['resource'] . '/' . 'new';
        $temp_row->current    = $new_current;
        $temp_query_results[] = $temp_row;

        $this->runtime_data->page->menu['PageSubmenu'] = $temp_query_results;

        return $this;
    }

    /**
     * Prepares Page Title and Actions for Rendering
     *
     * @param   string  $page_type
     *
     * @return  $this
     * @since   1.0
     * @throws  \CommonApi\Exception\RuntimeException
     */
    protected function setPageEligibleActions($page_type)
    {
        if ($page_type == 'item') {

            if ($this->runtime_data->request->data->method == 'GET') {
                $actions = $this->setItemActions();
            } else {
                $actions = $this->setEditActions();
            }

        } elseif ($page_type == 'grid'
            || $page_type == 'list'
        ) {
            $actions = $this->setListActions();

        } else {
            $actions = $this->setMenuitemActions();
        }

        $actionCount = 0;
        if (is_array($actions)) {
            $actionCount = count($actions);
        }

        $temp_query_results = array();

        $temp_row               = new stdClass();
        $temp_row->action_count = $actionCount;
        $temp_row->action_array = '';

        if ($actionCount === 0) {
            $temp_row->action_array = null;
        } else {
            foreach ($actions as $action) {
                $temp_row->action_array .= trim($action);
            }
        }

        $temp_query_results[] = $temp_row;

        $this->runtime_data->page->page_eligible_actions = $temp_query_results;

        return $this;
    }

    /**
     * Create Item Actions
     *
     * @return  $this
     * @since   1.0
     * @throws  \CommonApi\Exception\RuntimeException
     */
    protected function setItemActions()
    {
        // Currently display

        $actions   = array();
        $actions[] = 'create';
        $actions[] = 'copy';
        $actions[] = 'read';
        $actions[] = 'edit';

        // editing item
        $actions[] = 'read';
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
     * @return  $this
     * @since   1.0
     * @throws  \CommonApi\Exception\RuntimeException
     */
    protected function setEditActions()
    {
        $actions = array();

        return $actions;
    }

    /**
     * Create List Actions
     *
     * @return  $this
     * @since   1.0
     * @throws  \CommonApi\Exception\RuntimeException
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
     * @return  $this
     * @since   1.0
     * @throws  \CommonApi\Exception\RuntimeException
     */
    protected function setMenuitemActions()
    {
        $actions = array();

        return $actions;
    }

    /**
     * Set Page Meta Data during onBeforeParse, can be modified at any point during the document body rendering
     *
     * @param   string  $page_type
     *
     * @return  $this
     * @since   1.0
     */
    protected function setPageMeta($page_type)
    {
        if ($page_type == 'item') {
            return $this->setPageMetaItem();

        }

        if ($page_type == 'list') {
            return $this->setPageMetaList();

        }
        return $this->setPageMetaMenuItem();
    }

    /**
     * Set Page Meta Data during onBeforeParse, can be modified at any point during the document body rendering
     *
     * @return  $this
     * @since   1.0
     */
    protected function setPageMetaItem()
    {
        if (isset($this->runtime_data->resource->data->metadata->title)) {
            $title = $this->runtime_data->resource->data->metadata->title;
        } else {
            $title = '';
        }
        if (isset($this->runtime_data->resource->data->metadata->author)) {
            $author = $this->runtime_data->resource->data->metadata->author;
        } else {
            $author = '';
        }
        if (isset($this->runtime_data->resource->data->metadata->description)) {
            $description = $this->runtime_data->resource->data->metadata->description;
        } else {
            $description = '';
        }
        if (isset($this->runtime_data->resource->data->metadata->keywords)) {
            $keywords = $this->runtime_data->resource->data->metadata->keywords;
        } else {
            $keywords = '';
        }
        if (isset($this->runtime_data->resource->data->metadata->robots)) {
            $robots = $this->runtime_data->resource->data->metadata->robots;
        } else {
            $robots = '';
        }

        if (trim($title) == '') {
            if (isset($this->runtime_data->resource->data->title)) {
                $title = $this->runtime_data->resource->data->title;
            }

            if ($title == '') {
                $title = $this->runtime_data->page->header_title;
            }

            if ($title == '') {
            } else {
                $title .= ': ';
            }

            $title .= $this->runtime_data->site->name;
        }

        $this->runtime_data->resource->data->metadata->title = $title;

        if (trim($description) == '') {

            if (isset($this->runtime_data->resource->data->description)) {
                $description = $this->runtime_data->resource->data->description;

            } elseif (isset($this->runtime_data->resource->data->content_text_snippet)) {
                $description = $this->runtime_data->resource->data->content_text_snippet;
            }
        }

        $this->runtime_data->resource->data->metadata->description = $description;

        if (trim($author) == '') {

            if (isset($this->runtime_data->resource->data->author_full_name)) {
                $author = $this->runtime_data->resource->data->author_full_name;
            }
        }

        $this->runtime_data->resource->data->metadata->author = $author;

        if (trim($robots) == '') {
            $robots = 'follow,index';
        }

        $this->runtime_data->resource->data->metadata->robots = $robots;

        return $this;
    }

    /**
     * Set Page Meta Data during onBeforeParse, can be modified at any point during the document body rendering
     *
     * @return  $this
     * @since   1.0
     */
    protected function setPageMetaList()
    {

    }

    /**
     * Set Page Meta Data during onBeforeParse, can be modified at any point during the document body rendering
     *
     * @return  $this
     * @since   1.0
     */
    protected function setPageMetaMenuItem()
    {
        if (isset($this->runtime_data->resource->metadata->title)) {
            $title = $this->runtime_data->resource->metadata->title;
        } else {
            $title = '';
        }
        if (isset($this->runtime_data->resource->metadata->author)) {
            $author = $this->runtime_data->resource->metadata->author;
        } else {
            $author = '';
        }
        if (isset($this->runtime_data->resource->metadata->description)) {
            $description = $this->runtime_data->resource->metadata->description;
        } else {
            $description = '';
        }
        if (isset($this->runtime_data->resource->metadata->keywords)) {
            $keywords = $this->runtime_data->resource->metadata->keywords;
        } else {
            $keywords = '';
        }
        if (isset($this->runtime_data->resource->metadata->robots)) {
            $robots = $this->runtime_data->resource->metadata->robots;
        } else {
            $robots = '';
        }

        if (trim($title) == '') {
            $title = $this->runtime_data->page->header_title;

            if ($title == '') {
            } else {
                $title .= ': ';
            }

            $title .= $this->runtime_data->site->name;
        }

        $this->runtime_data->resource->metadata->title       = $title;
        $this->runtime_data->resource->metadata->description = $description;
        $this->runtime_data->resource->metadata->author      = $author;

        if (trim($robots) == '') {
            $robots = 'follow,index';
        }

        $this->runtime_data->resource->metadata->robots = $robots;

        return $this;
    }
}
