<?php
/**
 * Template Plugin
 *
 * @package    Molajo
 * @copyright  2014-2015 Amy Stephen. All rights reserved.
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 */
namespace Molajo\Plugins\Template;

use CommonApi\Event\DisplayEventInterface;

/**
 * Template Plugin
 *
 * @package     Molajo
 * @license     http://www.opensource.org/licenses/mit-license.html MIT License
 * @since       1.0
 */
final class TemplatePlugin extends Model implements DisplayEventInterface
{
    /**
     * Before Template View is Rendered
     *
     * @return  $this
     * @since   1.0.0
     */
    public function onBeforeRenderTemplate()
    {
        $this->setRenderTokenView();

        $this->controller['parameters']->token->include_path
            = $this->plugin_data->render->extension->path;

        $this->controller['parameters']->token->display_view_on_no_results
            = (int) $this->plugin_data->render->extension->criteria_display_view_on_no_results;

        $this->setRenderTokenModel();

        return $this;
    }

    /**
     * Prepare Data for Injecting into Template
     *
     * @return  $this
     * @since   1.0.0
     */
    public function onGetTemplateData()
    {
        $this->getTemplateRenderData();

        return $this;
    }
}
