<?php
/**
 * Copyright Plugin
 *
 * @package    Molajo
 * @copyright  2014 Amy Stephen. All rights reserved.
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 */
namespace Molajo\Plugin\Copyright;

use stdClass;
use CommonApi\Event\SystemInterface;
use Molajo\Plugin\SystemEventPlugin;

/**
 * Copyright Plugin
 *
 * @package     Molajo
 * @license     http://www.opensource.org/licenses/mit-license.html MIT License
 * @since       1.0
 */
class CopyrightPlugin extends SystemEventPlugin implements SystemInterface
{
    /**
     * Prepares formatted copyright statement with year span
     *
     * @return  $this
     * @since   1.0
     */
    public function onAfterRead()
    {
        if (isset($this->plugin_data->render->token)
            && $this->plugin_data->render->token->type == 'template'
            && strtolower($this->plugin_data->render->token->name) == 'footer'
        ) {
        } else {
            return $this;
        }

        $row          = new stdClass();
        $current_year = $this->date_controller->getDate('now', null, 'user', 'Y');

        $first_year = $this->plugin_data->render->extension->parameters->copyright_first_year;

        if ($first_year == null || $first_year == '') {
            $ccDateSpan = $current_year;
        } elseif ($first_year == $current_year) {
            $ccDateSpan = $first_year;
        } else {
            $ccDateSpan = $first_year . '-' . $current_year;
        }

        $copyright_holder = $this->plugin_data->render->extension->parameters->copyright_holder;

        $row->copyright_statement = '&#169;' . ' ' . $ccDateSpan . ' ' . $copyright_holder;
        $row->link                = $this->plugin_data->render->extension->parameters->link;
        $row->remaining_text      = $this->plugin_data->render->extension->parameters->link;

        $this->row[] = $row;

        return $this;
    }
}
