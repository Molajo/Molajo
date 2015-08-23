<?php
/**
 * Toolbar Plugin
 *
 * @package    Molajo
 * @copyright  2014-2015 Amy Stephen. All rights reserved.
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 */
namespace Molajo\Plugins\Toolbar;

use CommonApi\Event\DisplayEventInterface;
use Molajo\Plugins\DisplayEvent;
use stdClass;

/**
 * Toolbar Plugin
 *
 * @package  Molajo
 * @license  http://www.opensource.org/licenses/mit-license.html MIT License
 * @since    1.0
 */
final class ToolbarPlugin extends DisplayEvent implements DisplayEventInterface
{
    /**
     * Possible Actions
     *
     * @var    array
     * @since  1.0.0
     */
    protected $possible_listbox_actions
        = array(
            'action_archive'   => 'archive',
            'action_checkin'   => 'checkin',
            'action_delete'    => 'delete',
            'action_feature'   => 'feature',
            'action_publish'   => 'publish',
            'action_restore'   => 'restore',
            'action_sticky'    => 'sticky',
            'action_trash'     => 'trash',
            'action_unpublish' => 'unpublish'
        );

    /**
     * Toolbar Buttons
     *
     * @var    array
     * @since  1.0.0
     */
    protected $possible_toolbar_buttons
        = array(
            'toolbar_button_new'         => 'new',
            'toolbar_button_copy'        => 'copy',
            'toolbar_button_filter'      => 'filters',
            'toolbar_button_categories'  => 'categorize',
            'toolbar_button_tags'        => 'tag',
            'toolbar_button_permissions' => 'permissions',
        );

    /**
     * Button Class
     *
     * @var    string
     * @since  1.0.0
     */
    protected $button_class = 'radius small button';

    /**
     * Prepare Data for Injecting into Template
     *
     * @return  $this
     * @since   1.0.0
     */
    public function onGetTemplateData()
    {
        if ($this->checkProcessPlugin() === false) {
            return $this;
        }

        return $this;
        return $this->setToolbarActions();
    }

    /**
     * Should plugin be executed?
     *
     * @return  boolean
     * @since   1.0.0
     */
    protected function checkProcessPlugin()
    {
        if (strtolower($this->controller['parameters']->token->name) === 'toolbar') {
            return true;
        }

        return false;
    }

    /**
     * Get Data for Grid Page Type
     *
     * @return  $this
     * @since   1.0.0
     */
    protected function setToolbarActions()
    {
        $this->setActionsDatalist();

        $this->setToolbarButtons();

        return $this;
    }

    /**
     * Set Actions Datalist
     *
     * @return  $this
     * @since   1.0.0
     */
    protected function setActionsDatalist()
    {
        $this->plugin_data->datalist_actions = array();

        $initialised_list = $this->initialiseList($this->possible_listbox_actions);
        $this->setActionList($initialised_list);

        return $this;
    }

    /**
     * Set Actions Datalist
     *
     * @return  $this
     * @since   1.0.0
     */
    protected function setToolbarButtons()
    {
        $this->plugin_data->toolbar_buttons = array();

        $initialised_list = $this->initialiseList($this->possible_toolbar_buttons);
        $this->setToolbarButtons($initialised_list);

        return $this;
    }

    /**
     * Set Actions according to Parameter Selections
     *
     * @param   array $possible_list
     *
     * @return  array
     * @since   1.0.0
     */
    protected function initialiseList(array $possible_list = array())
    {
        $initialised_list = array();

        foreach ($possible_list as $key => $value) {

            $action = strtolower($this->runtime_data->resource->parameters->page_type)
                . '_action_'
                . $value;

            if ((int)$this->runtime_data->resource->parameters->$action === 0) {
            } else {
                $initialised_list[$action] = $value;
            }
        }

        return $initialised_list;
    }

    /**
     * Create Action List for Toolbar
     *
     * @param   array $list
     *
     * @return  $this
     * @since   1.0.0
     */
    protected function setActionList(array $list = array())
    {
        $actions = array();

        foreach ($list as $key => $value) {
            $actions = $this->setActionListItem($key, $actions);
        }

        $this->plugin_data->datalist_actions = $actions;

        return $this;
    }

    /**
     * Create Action List Item
     *
     * @param   string $button_key
     * @param   array  $actions
     *
     * @return  array
     * @since   1.0.0
     */
    protected function setActionListItem($button_key, array $actions = array())
    {
        $results = $this->authoriseAction($button_key, $this->runtime_data->resource->data->catalog_id);

        if ($results === false) {
        } else {
            $actions[] = $results;
        }

        return $actions;
    }

    /**
     * Create Toolbar Registry based on Authorized Access
     *
     * @return  $this
     * @since   1.0.0
     */
    protected function setToolbar()
    {
        $actions = array();

        foreach ($this->setToolbarList() as $key => $value) {

            $results = $this->authoriseAction($key, $this->runtime_data->resource->data->catalog_id);

            if ($results === false) {
            } else {

                $actions[] = $results;
            }
        }

        $this->plugin_data->grid_toolbar = $actions;

        return $this;
    }

    /**
     * Create Toolbar List
     *
     * @return  $this
     * @since   1.0.0
     */
    protected function setToolbarList()
    {
        $list = array();

        foreach ($this->possible_toolbar_buttons as $key => $value) {
            if ((int)$this->runtime_data->resource->parameters->$key === 1) {
                $list[$key] = $value;
            }
        }

        return $list;
    }

    /**
     * Authorise action
     *
     * @param   string $button
     * @param   int    $catalog_id
     *
     * @return  $this
     * @since   1.0.0
     */
    protected function authoriseAction($button, $catalog_id)
    {
        $options                = array();
        $options['resource_id'] = $catalog_id;
        $options['task']        = $this->possible_toolbar_buttons[$button];

        $button_array = array('filter', 'permissions', 'tags', 'new', 'category');

        $permissions = true;
        if (in_array($button, $button_array)) {
        } else {
            //$permissions = $this->authorisation->isUserAuthorised($options);
        }

        if ($permissions === false) {
            return false;
        }

        return $this->authoriseActionRow($button);
    }

    /**
     * Authorise action
     *
     * @param   string $button
     *
     * @return  stdClass
     * @since   1.0.0
     */
    protected function authoriseActionRow($button)
    {
        $temp_row = new stdClass();

        $temp_row->value = $this->language->translateString(
            strtoupper('TASK_' . strtoupper($button) . '_BUTTON')
        );

        $temp_row->id = $button;

        return $temp_row;
    }
}
