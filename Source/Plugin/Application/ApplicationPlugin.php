<?php
/**
 * Application Plugin
 *
 * @package    Molajo
 * @copyright  2014 Amy Stephen. All rights reserved.
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 */
namespace Molajo\Plugin\Application;

use CommonApi\Event\SystemInterface;
use CommonApi\Exception\RuntimeException;
use Molajo\Plugin\SystemEventPlugin;
use stdClass;

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
     * Prepares Page Information storing results in $this->plugin_data->page
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

        if ($page_type == 'dashboard') {
            return $this;
        }

        if ($page_type == 'item' || $page_type == 'edit') {
            $current_menuitem_id = $this->plugin_data->resource->parameters->parent_menu_id;
        } elseif ($page_type == 'list') {
            $current_menuitem_id = $this->plugin_data->resource->parameters->list_parent_menu_id;
        } else {
            $current_menuitem_id = $this->plugin_data->resource->menuitem->data->id;
        }

        if ((int)$current_menuitem_id == 0) {
            $this->plugin_data->page->menuitem_id         = 0;
            $this->plugin_data->page->menuitem            = new stdClass();
            $this->plugin_data->page->menu                = new stdClass();
            $this->plugin_data->page->current_menuitem_id = 0;
        } else {
            $this->plugin_data->page->menuitem_id         = $current_menuitem_id;
            $this->plugin_data->page->menuitem            = new stdClass();
            $this->plugin_data->page->menu                = new stdClass();
            $this->plugin_data->page->current_menuitem_id = $current_menuitem_id;
        }

        $this->getUrls();

        if ((int)$current_menuitem_id == 0) {
            $this->plugin_data->page->breadcrumbs = new stdClass();
        } else {
            $this->plugin_data->page->breadcrumbs = $this->getMenuBreadcrumbIds();
            $this->getMenu();
        }

        $this->getPageTitle($page_type);

        $this->setPageMeta();

        return $this;
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
        $this->plugin_data->page->urls['home']      = $this->runtime_data->application->base_url;
        $this->plugin_data->page->urls['base']      = $this->runtime_data->application->base_url;
        $this->plugin_data->page->urls['page']      = $this->runtime_data->request->data->url;
        $this->plugin_data->page->urls['canonical'] = $this->runtime_data->request->data->url;
        $this->plugin_data->page->urls['resource']  = $this->runtime_data->application->base_url
            . strtolower($this->runtime_data->route->b_alias);

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
        if ($this->plugin_data->page->current_menuitem_id == 0) {
            return array();
        }

        $controller = $this->resource->get('query:///Molajo//Datasource//MenuitemsNested.xml');

        $controller->setModelRegistry('check_view_level_access', 1);
        $controller->setModelRegistry('process_events', 1);
        $controller->setModelRegistry('get_customfields', 1);
        $controller->setModelRegistry('use_special_joins', 0);
        $controller->setModelRegistry('query_object', 'list');
        $controller->setModelRegistry('model_offset', 0);
        $controller->setModelRegistry('model_count', 9999);

        $controller->model->query->where(
            $controller->model->database->qn('id')
            . ' = ' . (int)$this->plugin_data->page->current_menuitem_id
        );
        $controller->model->query->where($controller->model->database->qn('a.status') . ' > 0');

        $controller->model->query->order('a.lft DESC');

        $row = $controller->getData();

        $look_for_parent = 0;

        $select = array();
        $i      = 0;
        foreach ($row as $item) {

            $this->plugin_data->page->extension_id = $item->extension_id;

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

            if ($this->plugin_data->page->current_menuitem_id == 0) {
                return array();
            }

            $breadcrumbs[] = $row[$index];
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
        $menu_id = $this->plugin_data->page->extension_id;

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

        $rows = $controller->getData();

        if (count($rows) === 0) {
            return $this;
        }

        $current_menu_item = $this->plugin_data->page->current_menuitem_id;
        $breadcrumbs       = $this->plugin_data->page->breadcrumbs;
        $menu_name         = '';

        foreach ($rows as $item) {

            $item->menu_id = $item->extension_id;

            $menu_name = $item->menu;

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

            $item->url  = $base . 'index.php?id=' . (int)$item->catalog_id;
            $item->link = $base . $item->catalog_sef_request;

            if ($item->subtitle == '' || $item->subtitle == null) {
                $item->link_text = $item->title;
            } else {
                $item->link_text = $item->subtitle;
            }

            if ($item->current === 1) {
                $this->plugin_data->page->menuitem = $item;
            }
        }

        $this->plugin_data->page->breadcrumbs = $breadcrumbs;

        $this->plugin_data->page->menu             = array();
        $this->plugin_data->page->menu[$menu_name] = $rows;

        return $this;
    }

    /**
     * Set the Header Title
     *
     * @param   string $page_type
     *
     * @return  $this
     * @since   1.0
     * @throws  \CommonApi\Exception\RuntimeException
     */
    protected function getPageTitle($page_type)
    {
        $this->plugin_data->page->page_type = $page_type;

        $title = $this->runtime_data->application->name;
        if ($title == '') {
            $title = $this->language_controller->translate('Molajo Application');
        }

        $this->plugin_data->page->header_title = $title;

        if (isset($this->plugin_data->resource->menuitem->parameters->criteria_title)) {
            $heading1 = $this->plugin_data->resource->menuitem->parameters->criteria_title;

        } elseif (isset($this->plugin_data->resource->parameters->criteria_title)) {
            $heading1 = $this->plugin_data->resource->parameters->criteria_title;

        } else {
            $heading1 = '';
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
            strtoupper($this->plugin_data->page->page_type)
        );

        $heading2 = ucfirst(strtolower($page_type));

        $this->plugin_data->page->heading1 = $heading1;
        $this->plugin_data->page->heading2 = $heading2;

        return $this;
    }

    /**
     * Set Page Meta Data
     *
     * @return  $this
     * @since   1.0
     */
    protected function setPageMeta()
    {
        $metadata_array                         = array();
        $metadata_array['metadata_title']       = null;
        $metadata_array['metadata_author']      = null;
        $metadata_array['metadata_description'] = null;
        $metadata_array['metadata_keywords']    = null;
        $metadata_array['metadata_robots']      = null;

        if (isset($this->plugin_data->resource->menuitem->data->metadata)) {
            $data = $this->plugin_data->resource->menuitem->data->metadata;

            foreach ($metadata_array as $key => $value) {
                $metadata_array[$key] = $this->setMetadata($data, $key, $value);
            }
        }

        if (isset($this->plugin_data->resource->data->metadata)) {
            $data = $this->plugin_data->resource->data->metadata;

            foreach ($metadata_array as $key => $value) {
                $metadata_array[$key] = $this->setMetadata($data, $key, $value);
            }
        }

        if (trim($metadata_array['metadata_title']) == '') {
            $title = $this->plugin_data->page->header_title;

            if ($title == '') {
            } else {
                $title .= ': ';
            }

            $title .= $this->runtime_data->site->name;

            $metadata_array['metadata_title'] = $title;
        }

        if (trim($metadata_array['metadata_robots']) == '') {
            $metadata_array['metadata_robots'] = 'follow,index';
        }

        foreach ($metadata_array as $key => $value) {
            if (isset($this->plugin_data->resource->menuitem)) {
                $this->plugin_data->resource->menuitem->data->metadata->$key = $value;
            } else {
                $this->plugin_data->resource->data->metadata->$key = $value;
            }
        }

        return $this;
    }

    /**
     * Set Meta Data for Key
     *
     * @param   object     $data
     * @param   string     $key
     * @param   null|mixed $value
     *
     * @return  null|$this
     * @since   1.0
     */
    protected function setMetadata($data, $key, $value = null)
    {
        if ($value === null) {
            if (isset($data->$key)) {
                return $data->$key;
            }
        }

        return $value;
    }
}
