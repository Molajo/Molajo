<?php
/**
 * Display Event Interface
 *
 * @package    Event
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2014 Amy Stephen. All rights reserved.
 */
namespace CommonApi\Event;

/**
 * Display Event Interface
 *
 * @author     Amy Stephen
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2014 Amy Stephen. All rights reserved.
 * @since      1.0
 */
interface DisplayInterface
{
    /**
     * Before any parsing or rendering, after Execute
     *
     * @return  $this
     * @since   1.0.0
     */
    public function onBeforeRender();

    /**
     * Before parsing of rendered_page to extract tokens for rendering
     *  This is a recursive process - parse - render - parse - render - until no tokens found
     *  exclude_tokens contains values that are not processed during this parsing
     *
     * @return  $this
     * @since   1.0.0
     */
    public function onBeforeParse();

    /**
     * After parsing for tokens (recursive), parameters->tokens contains parsed results
     *
     * @return  $this
     * @since   1.0.0
     */
    public function onAfterParse();

    /**
     * After the Read Query has executed but rendering the view
     *
     * @return  $this
     * @since   1.0.0
     */
    public function onBeforeRenderView();

    /**
     * During Template View rendering, before the rendering of the Head
     *
     * @return  $this
     * @since   1.0.0
     */
    public function onBeforeRenderViewHead();

    /**
     * During Template View rendering for each item
     *
     * @return  $this
     * @since   1.0.0
     */
    public function onBeforeRenderViewItem();

    /**
     * During Template View rendering, before the rendering of the Footer
     *
     * @return  $this
     * @since   1.0.0
     */
    public function onBeforeRenderViewFooter();

    /**
     * After the View has been rendered but before it has been inserted into the rendered_page
     *
     * @return  $this
     * @since   1.0.0
     */
    public function onAfterRenderView();

    /**
     * On after rendering the entire document
     *
     * @return  $this
     * @since   1.0.0
     */
    public function onAfterRender();
}
