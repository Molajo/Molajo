<?php
/**
 * Position Plugin
 *
 * @package    Molajo
 * @copyright  2014-2015 Amy Stephen. All rights reserved.
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 */
namespace Molajo\Plugins\Position;

use CommonApi\Event\DisplayEventInterface;
use Molajo\Plugins\DisplayEvent;

/**
 * Position Plugin
 *
 * @package  Molajo
 * @license  http://www.opensource.org/licenses/mit-license.html MIT License
 * @since    1.0
 */
final class PositionPlugin extends DisplayEvent implements DisplayEventInterface
{
    /**
     * After parsing for tokens (recursive), parameters->tokens contains parsed results
     *
     * @return  $this
     * @since   1.0.0
     */
    public function onAfterParse()
    {
        return $this;
    }

    /**
     * Before the View has been rendered but before it has been inserted into the rendered_page
     *
     * @return  $this
     * @since   1.0.0
     */
    public function onBeforeRenderPosition()
    {
        // catalog_type_id = 10500

        // or define position=>template_ids in theme

        // consider how theme in path could help with templates sharing names

        /**
        if (in_array($key, array('page', 'template', 'wrap'))) {
        $row->get_view = true;
        } else {
        $row->get_view = false;
        }

        if (in_array($key, array('template', 'wrap'))) {
        $row->get_data = true;
        } else {
        $row->get_data = false;
        }
         */

        if ($this->checkProcessPlugin() === false) {
            return $this;
        }

        return $this;
    }

    /**
     * After the View has been rendered but before it has been inserted into the rendered_page
     *
     * @return  $this
     * @since   1.0.0
     */
    public function onAfterRenderPosition()
    {
        if ($this->checkProcessPlugin() === false) {
            return $this;
        }

        return $this;
    }

    /**
     * Should plugin be executed?
     *
     * @return  boolean
     * @since   1.0.0
     */
    protected function checkProcessPlugin()
    {
        return false;
    }
}
