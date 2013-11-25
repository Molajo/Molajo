<?php
/**
 * Resource Dependency Injector
 *
 * @package    Molajo
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2013 Amy Stephen. All rights reserved.
 */
namespace Molajo\Service\Resource;

use Molajo\IoC\Handler\AbstractInjector;
use CommonApi\IoC\ServiceHandlerInterface;

/**
 * Resource Service Dependency Injector
 *
 * @author     Amy Stephen
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2013 Amy Stephen. All rights reserved.
 * @since      1.0
 */
class ResourceInjector extends AbstractInjector implements ServiceHandlerInterface
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
        $options['service_name'] = basename(__DIR__);

        parent::__construct($options);
    }

    /**
     * IoC Controller triggers the DI Handler to create the Class for the Service
     *
     * @return  $this
     * @since   1.0
     * @throws  \CommonApi\Exception\RuntimeException;
     */
    public function instantiateService()
    {
        if ($this->options['Runtimedata']->route->catalog_type_id == 11000) {
            $model = 'Molajo//Menuitem//'
                . ucfirst(strtolower($this->options['Runtimedata']->route->page_type))
                . '//Configuration.xml';

        } else {
            $model = 'Molajo//Datasource//'
                . $this->options['Runtimedata']->route->model_name
                . '//Configuration.xml';
        }

        $this->options['resource_query']
            = $this->options['Resources']->get(
            'query:///' . $model,
            array(
                'runtime_data',
                $this->options['Runtimedata']
            )
        );

        $class_name = 'Molajo\\Controller\\ResourceController';

        $this->service_instance = new $class_name
        (
            $this->options['Resources'],
            $this->options['Runtimedata'],      // no parameters yet
            $this->options['resource_query']
        );

        return $this;
    }
}
