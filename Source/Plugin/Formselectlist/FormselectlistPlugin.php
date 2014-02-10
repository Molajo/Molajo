<?php
/**
 * Form Select List Plugin
 *
 * @package    Molajo
 * @copyright  2014 Amy Stephen. All rights reserved.
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 */
namespace Molajo\Plugin\Formselectlist;

use CommonApi\Event\DisplayInterface;
use Molajo\Plugin\DisplayEventPlugin;

/**
 * Form Select List Plugin
 *
 * @package     Molajo
 * @license     http://www.opensource.org/licenses/mit-license.html MIT License
 * @since       1.0
 */
class FormselectlistPlugin extends DisplayEventPlugin implements DisplayInterface
{
    /**
     * Prepares listbox contents
     *
     * @return  $this
     * @since   1.0
     */
    public function onBeforeRenderView()
    {
        return $this;

        if (isset($this->plugin_data->render->token)
            && $this->plugin_data->render->token->type == 'template'
            && strtolower($this->plugin_data->render->token->name) == 'formselectlist'
        ) {
        } else {
            return $this;
        }

        if (isset($this->plugin_data->render->token->attributes['datalist'])) {
            $list = $this->plugin_data->render->token->attributes['datalist'];
        } else {
            return $this;
        }

//        $temp_row = $this->plugin_data->grid_filters[$list];

        $this->plugin_data->form_select_list = array();
        //$this->plugin_data->datalists->datalist[$list];

        $this->row = $this->plugin_data->form_select_list;

        return $this;
    }

    /**
     * Remove Registry just rendered
     *
     * @return  object
     * @since   1.0
     */
    public function onAfterInclude()
    {
        $this->registry->delete('Template', $this->get('template_view_path_node', '', 'runtime_data'));

        return $this;
    }
}
