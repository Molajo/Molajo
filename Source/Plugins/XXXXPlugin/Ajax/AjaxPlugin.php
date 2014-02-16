<?php
/**
 * Ajax Plugin
 *
 * @package    Molajo
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2014 Amy Stephen. All rights reserved.
 */
namespace Molajo\Plugins\Ajax;

use stdClass;
use CommonApi\Event\SystemInterface;
use Molajo\Plugins\SystemEventPlugin;

/**
 * Ajax Plugin
 *
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2014 Amy Stephen. All rights reserved.
 * @since      1.0
 */
class AjaxPlugin extends SystemEventPlugin implements SystemInterface
{
    /**
     * Identify Ajax Request and adapt the Template, Wrap and Tokens
     *
     * @return  $this
     * @since   1.0
     */
    public function onBeforeExecute()
    {
        if ($this->runtime_data->request->client->ajax == 0) {
            return $this;
        }

        $this->plugin_data->resource->template = $this->resource->get('Template:///Molajo//Views//Templates//' . 1342);
        $this->plugin_data->resource->wrap     = $this->resource->get('Wrap:///Molajo//Views//Wraps//' . 2090);

        $sequence = array();
        $x        = $this->resource->get('xml:///Molajo//Model//Application//Ajax_sequence.xml')->include;
        foreach ($x as $y) {
            $sequence[] = (string)$y;
        }

        if (isset($this->plugin_data->render)) {
        } else {
            $this->plugin_data->render = new stdClass();
        }
        $this->plugin_data->render->token_sequence = $sequence;

        $final_sequence = array();
        $x              = $this->resource->get('xml:///Molajo//Model//Application//Ajax_final.xml')->include;

        if ($x === null || count($x) > 0) {
        } else {
            foreach ($x as $y) {
                $final_sequence[] = (string)$y;
            }
        }

        $this->plugin_data->render->exclude_tokens = $final_sequence;

        return $this;
    }
}
