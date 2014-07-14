<?php
/**
 * Authenticate Event Plugin
 *
 * @package    Molajo
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2014 Amy Stephen. All rights reserved.
 */
namespace Molajo\Plugins;

use CommonApi\Event\AuthenticateInterface;

/**
 * Authenticate Event Plugin
 *
 * @author     Amy Stephen
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2014 Amy Stephen. All rights reserved.
 * @since      1.0.0
 */
abstract class AuthenticateEventPlugin extends AbstractFieldsPlugin implements AuthenticateInterface
{
    /**
     * Before logging in processing
     *
     * @return  $this
     * @since   1.0.0
     */
    public function onBeforeAuthenticate()
    {
        return $this;
    }

    /**
     * After Logging in event
     *
     * @return  $this
     * @since   1.0.0
     */
    public function onAfterAuthenticate()
    {
        return $this;
    }

    /**
     * Before logging out processing
     *
     * @return  $this
     * @since   1.0.0
     */
    public function onBeforeLogout()
    {
        return $this;
    }

    /**
     * After Logging out event
     *
     * @return  $this
     * @since   1.0.0
     */
    public function onAfterLogout()
    {
        return $this;
    }
}
