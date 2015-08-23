<?php
/**
 * Theme Plugin
 *
 * @package    Molajo
 * @copyright  2014-2015 Amy Stephen. All rights reserved.
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 */
namespace Molajo\Plugins\Theme;

use CommonApi\Event\DisplayEventInterface;
use Molajo\Plugins\DisplayEvent;

/**
 * Theme Plugin
 *
 * @package     Molajo
 * @license     http://www.opensource.org/licenses/mit-license.html MIT License
 * @since       1.0
 */
final class ThemePlugin extends DisplayEvent implements DisplayEventInterface
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
     * Before Theme View is Rendered
     *
     * @return  $this
     * @since   1.0.0
     */
    public function onBeforeRenderTheme()
    {
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
        return $this;
    }

    /**
     * After the Theme/Wrap View has been rendered but before it has been inserted into the rendered_page
     *
     * @return  $this
     * @since   1.0.0
     */
    public function onAfterRenderTheme()
    {
        return $this;
    }
}
