<?php
/**
 * Render Dependency Injector
 *
 * @package    Molajo
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2013 Amy Stephen. All rights reserved.
 */
namespace Molajo\Service\Render;

use Exception;
use Molajo\IoC\Handler\AbstractInjector;
use CommonApi\IoC\ServiceHandlerInterface;
use CommonApi\Exception\RuntimeException;

/**
 * Render Service Dependency Injector
 *
 * @author     Amy Stephen
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2013 Amy Stephen. All rights reserved.
 * @since      1.0
 */
class RenderInjector extends AbstractInjector implements ServiceHandlerInterface
{
    /**
     * Constructor
     *
     * @param  $options
     *
     * @since  1.0
     */
    public function __construct(array $options = array())
    {
        $options['service_name']             = basename(__DIR__);
        $options['store_instance_indicator'] = true;
        $options['service_namespace']        = null;

        parent::__construct($options);
    }

    /**
     * Instantiate Class
     *
     * @return  $this
     * @since   1.0
     * @throws  \CommonApi\Exception\RuntimeException;
     */
    public function instantiateService()
    {
        $handler = $this->getAdapterHandler();

        $class = 'Molajo\\Render\\Adapter';

        try {
            $this->service_instance = new $class($handler);
        } catch (Exception $e) {
            throw new RuntimeException
            ('Render: Could not instantiate Handler: ' . $class);
        }

        return $this;
    }

    /**
     * Get Filesystem Adapter, inject with specific Filesystem Adapter Handler
     *
     * @param   object $handler
     *
     * @return  object
     * @since   1.0
     * @throws  ServiceHandlerInterface
     */
    protected function getAdapterHandler()
    {
        $class = 'Molajo\\Render\\Handler\\Molajito';

        try {
            return new $class($this->options['triggerEvent'], $this->options['language_controller']);
        } catch (Exception $e) {
            throw new RuntimeException
            ('Render: Could not instantiate Handler: ' . $class);
        }
    }
}
