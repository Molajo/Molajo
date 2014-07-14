<?php
/**
 * Create Event Plugin
 *
 * @package    Molajo
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2014 Amy Stephen. All rights reserved.
 */
namespace Molajo\Plugins;

use CommonApi\Event\CreateInterface;

/**
 * Create Event Plugin
 *
 * @author     Amy Stephen
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2014 Amy Stephen. All rights reserved.
 * @since      1.0.0
 */
abstract class CreateEventPlugin extends AbstractFieldsPlugin implements CreateInterface
{
    /**
     * Pre-create processing
     *
     * @return  $this
     * @since   1.0.0
     */
    public function onBeforeCreate()
    {
        return $this;
    }

    /**
     * Post-create processing
     *
     * @return  $this
     * @since   1.0.0
     */
    public function onAfterCreate()
    {
        return $this;
    }
}
