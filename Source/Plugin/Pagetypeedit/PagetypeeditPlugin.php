<?php
/**
 * Page Type Edit Plugin
 *
 * @package    Molajo
 * @copyright  2014 Amy Stephen. All rights reserved.
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 */
namespace Molajo\Plugin\Pagetypeedit;

use CommonApi\Event\DisplayInterface;
use Molajo\Plugin\DisplayEventPlugin;

/**
 * Page Type Edit Plugin
 *
 * @package     Molajo
 * @license     http://www.opensource.org/licenses/mit-license.html MIT License
 * @since       1.0
 */
class PagetypeeditPlugin extends DisplayEventPlugin implements DisplayInterface
{
    /**
     * Prepares Configuration Data
     *
     * @return  $this
     * @since   1.0
     */
    public function onBeforeRender()
    {
        $page_type = strtolower($this->runtime_data->route->page_type);

        if ($page_type == 'new' || $page_type == 'edit') {
        } else {
            return $this;
        }

        $parameters                   = $this->plugin_data->resource->parameters;
        $model_registry               = $this->plugin_data->resource->model_registry;
        $data                         = $this->plugin_data->resource->data;
        $customfieldgroups            = $model_registry['customfieldgroups'];
        $section_array                = $parameters->edit_array;

        $this->setFormSections($section_array);
        $this->setFormSectionFieldsets($parameters);
        $this->setFormFieldsetFields($parameters, $model_registry);

        $template_views = array();
        foreach ($this->form_section_fieldsets as $key => $item) {
            $template_views[] = $key;
        }

        foreach ($template_views as $template) {
            $temp = array();
            foreach ($this->form_section_fieldset_fields as $item) {

                if ($template == $item->template_view) {

                    $name        = $item->name;
                    $item->value = null;

                    if (isset($data->$name)) {
                        $item->value = $data->$name;
                    } else {

                        if (count($customfieldgroups) > 0 && is_array($customfieldgroups)) {
                            foreach ($customfieldgroups as $group) {
                                if ($group == 'parameters') {
                                    if (isset($parameters->$name)) {
                                        $item->value = $parameters->$name;
                                    }
                                } else {
                                    if (isset($data->$group->$name)) {
                                        $item->value = $data->$group->$name;
                                        break;
                                    }
                                }
                            }
                        }
                    }

                    if (isset($item->datalist)) {
                        $item->type      = 'selectlist';
                        $item->list_name = $item->datalist;
                        $this->getSelectlist($item->datalist);
                    }
                    $temp[$name] = $item;
                }
            }

            $template                     = strtolower($template);
            $this->plugin_data->$template = $temp;
        }

        return $this;
    }

    /**
     * Get Select List and save results in plugin data
     *
     * @param   $list
     *
     * @return  $this
     *
     */
    public function getSelectlist($list)
    {
        //@todo figure out selected value
        $selected = '';

        $list = strtolower($list);

        if (isset($this->plugin_data->datalists->$list)) {
            $value = $this->plugin_data->datalists->$list;
        } else {
            $value = $this->getFilter($list);
        }

        if (is_array($value) && count($value) > 0) {

            usort(
                $value,
                function ($a, $b) {
                    return strcmp($a->value, $b->value);
                }
            );

        } else {
            $value = array();
        }

        $this->plugin_data->$list = $value;

        return $this;
    }
}
