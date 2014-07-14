<?php
/**
 * Read Event Plugin
 *
 * @package    Molajo
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2014 Amy Stephen. All rights reserved.
 */
namespace Molajo\Plugins;

use CommonApi\Event\ReadInterface;

/**
 * Read Event Plugin
 *
 * @author     Amy Stephen
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2014 Amy Stephen. All rights reserved.
 * @since      1.0.0
 */
abstract class ReadEventPlugin extends AbstractFieldsPlugin implements ReadInterface
{
    /**
     * Pre-read processing
     *
     * @return  $this
     * @since   1.0.0
     */
    public function onBeforeRead()
    {
        return $this;
    }

    /**
     * Post-read processing - one row at a time
     *
     * @return  $this
     * @since   1.0.0
     */
    public function onAfterRead()
    {
        return $this;
    }

    /**
     * Post-read processing - all rows at one time from query_results
     *
     * @return  $this
     * @since   1.0.0
     */
    public function onAfterReadall()
    {
        return $this;
    }
}
