<?php
/**
 * Url Dependency Injector
 *
 * @package    Molajo
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2013 Amy Stephen. All rights reserved.
 */
namespace Molajo\Service\Url;

use Molajo\IoC\Handler\AbstractInjector;
use CommonApi\IoC\ServiceHandlerInterface;
use CommonApi\Exception\RuntimeException;

/**
 * Url Controller Dependency Injector
 *
 * @author     Amy Stephen
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2013 Amy Stephen. All rights reserved.
 * @since      1.0
 */
class UrlInjector extends AbstractInjector implements ServiceHandlerInterface
{
    /**
     * Constructor
     *
     * @param   $options
     *
     * @since   1.0
     */
    public function __construct(array $options = array())
    {
        $options['service_namespace']        = 'Molajo\\Controller\\UrlController';
        $options['store_instance_indicator'] = true;
        $options['service_name']             = basename(__DIR__);

        parent::__construct($options);
    }

    /**
     * Identify Class Dependencies for Constructor Injection
     *
     * @return  array
     * @since   1.0
     * @throws  \CommonApi\Exception\RuntimeException;
     */
    public function setDependencies(array $reflection = null)
    {
        $reflection = null;

        $this->dependencies['Resources']   = array('if_exists' => true);
        $this->dependencies['Runtimedata'] = array();

        return $this->dependencies;
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
        $class = 'Molajo\\Controller\\UrlController';

        $this->service_instance = new $class(
            $this->dependencies['Resources'],
            $this->dependencies['Runtimedata']->site->base_url,
            $this->dependencies['Runtimedata']->application->id,
            $this->dependencies['Runtimedata']->application->base_url,
            $this->dependencies['Runtimedata']->application->parameters->application_home_catalog_id,
            $this->dependencies['Runtimedata']->application->parameters->url_sef
        );

        return $this;
    }
}
