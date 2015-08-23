<?php
/**
 * Page Plugin
 *
 * @package    Molajo
 * @copyright  2014-2015 Amy Stephen. All rights reserved.
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 */
namespace Molajo\Plugins\Page;

use CommonApi\Event\DisplayEventInterface;
use Molajo\Plugins\DisplayEvent;

/**
 * Page Plugin
 *
 * @package     Molajo
 * @license     http://www.opensource.org/licenses/mit-license.html MIT License
 * @since       1.0
 */
final class PagePlugin extends DisplayEvent implements DisplayEventInterface
{
    /**
     * Before Page View is Rendered
     *
     * @return  $this
     * @since   1.0.0
     */
    public function onBeforeRenderPage()
    {
        $this->setRenderTokenView();

        $this->controller['parameters']->token->include_path = $this->plugin_data->render->extension->path;

        return $this;
    }
}
