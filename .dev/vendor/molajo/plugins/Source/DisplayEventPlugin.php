<?php
/**
 * Display Event for Molajo Plugins
 *
 * @package    Molajo
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2014 Amy Stephen. All rights reserved.
 */
namespace Molajo\Plugins;

use CommonApi\Event\DisplayInterface;

/**
 * Display Abstract Class
 *
 * @author     Amy Stephen
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2014 Amy Stephen. All rights reserved.
 * @since      1.0.0
 */
abstract class DisplayEventPlugin extends AbstractFieldsPlugin implements DisplayInterface
{
    /**
     * Before any parsing or rendering, after Execute
     *
     * @return  $this
     * @since   1.0.0
     */
    public function onBeforeRender()
    {
        return $this;
    }

    /**
     * Before parsing of rendered_page to extract tokens for rendering
     *  This is a recursive process - parse - render - parse - render - until no tokens found
     *  exclude_tokens contains values that are not processed during this parsing
     *
     * @return  $this
     * @since   1.0.0
     */
    public function onBeforeParse()
    {
        return $this;
    }

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
     * After the Read Query has executed but rendering the view
     *
     * @return  $this
     * @since   1.0.0
     */
    public function onBeforeRenderView()
    {
        return $this;
    }

    /**
     * During Template View rendering, before the rendering of the Head
     *
     * @return  $this
     * @since   1.0.0
     */
    public function onBeforeRenderViewHead()
    {
        return $this;
    }

    /**
     * During Template View rendering for each item
     *
     * @return  $this
     * @since   1.0.0
     */
    public function onBeforeRenderViewItem()
    {
        return $this;
    }

    /**
     * During Template View rendering, before the rendering of the Footer
     *
     * @return  $this
     * @since   1.0.0
     */
    public function onBeforeRenderViewFooter()
    {
        return $this;
    }

    /**
     * After the View has been rendered but before it has been inserted into the rendered_page
     *
     * @return  $this
     * @since   1.0.0
     */
    public function onAfterRenderView()
    {
        return $this;
    }

    /**
     * On after rendering the entire document
     *
     * @return  $this
     * @since   1.0.0
     */
    public function onAfterRender()
    {
        return $this;
    }
}
