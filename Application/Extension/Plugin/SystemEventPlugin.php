<?php
/**
 * System Event Plugin
 *
 * @package    Molajo
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2013 Amy Stephen. All rights reserved.
 */
namespace Molajo\Plugin;

use CommonApi\Event\SystemInterface;
use Exception\Plugin\SystemEventException;

/**
 * System Event Plugin
 *
 * @author     Amy Stephen
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2013 Amy Stephen. All rights reserved.
 * @since      1.0
 */
abstract class SystemEventPlugin extends AbstractPlugin implements SystemInterface
{
    /**
     * After Initialise Processing
     *
     * @return  $this
     * @since   1.0
     * @throws  \Exception\Plugin\SystemEventException
     */
    public function onAfterInitialise()
    {
        return $this;
    }

    /**
     * After Route Processing
     *
     * @return  $this
     * @since   1.0
     * @throws  \Exception\Plugin\SystemEventException
     */
    public function onAfterRoute()
    {
        return $this;
    }

    /**
     * After Authorise Processing
     *
     * @return  $this
     * @since   1.0
     * @throws  \Exception\Plugin\SystemEventException
     */
    public function onAfterAuthorise()
    {
        return $this;
    }

    /**
     * After Resource Processing
     *
     * @return  $this
     * @since   1.0
     * @throws  \Exception\Plugin\SystemEventException
     */
    public function onAfterResource()
    {
        return $this;
    }

    /**
     * After Execute Processing
     *
     * @return  $this
     * @since   1.0
     * @throws  \Exception\Plugin\SystemEventException
     */
    public function onAfterExecute()
    {
        return $this;
    }
}
