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
use stdClass;

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

        $parameters                                 = $this->runtime_data->resource->parameters;
        $model_registry                             = $this->runtime_data->resource->model_registry;
        $data                                       = $this->runtime_data->resource->data;

        $tab_array                                  = $parameters->edit_array;
        $this->runtime_data->plugin_data->edit_tabs = $this->setTabs($tab_array);
        $this->setTabFormFieldsets($parameters);
        $this->setTabSectionFields($parameters, $model_registry);

        $template_views = array();
        foreach ($this->tab_form_fieldsets as $key => $item) {
            $template_views[] = $key;
        }

        foreach ($template_views as $template) {
            $temp = array();
            foreach ($this->tab_form_fieldsets_fields as $item) {
                if ($template == $item->template_view) {
                    $name   = $item->name;
                    if (isset($data->$name)) {
                        $item->value = $data->$name;
                    } else {
                        $item->value = null;
                    }
                    $temp[] = $item;
                }
            }
            $this->runtime_data->plugin_data->$template = $temp;

            echo $template . '<br />';
            echo '<pre>';
            var_dump($this->runtime_data->plugin_data->$template);
            echo '</pre>';
        }

        return $this;
    }
}
