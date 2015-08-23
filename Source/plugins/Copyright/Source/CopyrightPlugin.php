<?php
/**
 * Copyright Plugin
 *
 * @package    Molajo
 * @copyright  2014-2015 Amy Stephen. All rights reserved.
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 */
namespace Molajo\Plugins\Copyright;

use stdClass;
use CommonApi\Event\SystemEventInterface;
use Molajo\Plugins\SystemEvent;

/**
 * Copyright Plugin
 *
 * @package     Molajo
 * @license     http://www.opensource.org/licenses/mit-license.html MIT License
 * @since       1.0
 */
final class CopyrightPlugin extends SystemEvent implements SystemEventInterface
{
    /**
     * Prepares formatted copyright statement with year span
     *
     * @return  $this
     * @since   1.0.0
     */
    public function onAfterReadRow()
    {
        if ($this->checkProcessPlugin() === false) {
            return $this;
        }

        return $this->processCopyright();
    }

    /**
     * Should plugin be executed?
     *
     * @return  boolean
     * @since   1.0.0
     */
    protected function checkProcessPlugin()
    {
        if (isset($this->plugin_data->render->token)
            && $this->plugin_data->render->token->type === 'template'
            && strtolower($this->plugin_data->render->token->name) === 'footer'
        ) {
            return true;
        }

        return false;
    }

    /**
     * Process Copyright
     *
     * @return  CopyrightPlugin
     * @since   1.0.0
     */
    protected function processCopyright()
    {
        $copyright_holder = $this->plugin_data->render->extension->parameters->copyright_holder;

        $row                      = new stdClass();
        $row->copyright_statement = '&#169;' . ' ' . $this->setCopyrightDatespan() . ' ' . $copyright_holder;
        $row->link                = $this->plugin_data->render->extension->parameters->link;
        $row->remaining_text      = $this->plugin_data->render->extension->parameters->link;

        $this->row[] = $row;

        return $this;
    }

    /**
     * Set Copyright Datespan
     *
     * @return  string
     * @since   1.0.0
     */
    protected function setCopyrightDatespan()
    {
        $current_year = $this->date->getDate('now', null, 'user', 'Y');
        $first_year   = $this->plugin_data->render->extension->parameters->copyright_first_year;

        if ($first_year === null || $first_year === '') {
            $ccDateSpan = $current_year;
        } elseif ($first_year === $current_year) {
            $ccDateSpan = $first_year;
        } else {
            $ccDateSpan = $first_year . '-' . $current_year;
        }

        return $ccDateSpan;
    }
}
