<?php
/**
 * Extensions Dependency Injector
 *
 * @package    Molajo
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2013 Amy Stephen. All rights reserved.
 */
namespace Molajo\Service\Extensions;

use stdClass;
use Exception;
use Molajo\IoC\Handler\AbstractInjector;
use CommonApi\IoC\ServiceHandlerInterface;
use CommonApi\Exception\RuntimeException;

/**
 * Rendering Extensions Dependency Injector
 *
 * @author     Amy Stephen
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2013 Amy Stephen. All rights reserved.
 * @since      1.0
 */
class ExtensionsInjector extends AbstractInjector implements ServiceHandlerInterface
{
    /**
     * Controller
     *
     * @var    object  CommonApi\Controller\ReadInterface
     * @since  1.0
     */
    protected $controller = null;

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
        $options['service_namespace']        = 'Molajo\\Resources\\Extensions';

        parent::__construct($options);
    }

    /**
     * Define Dependencies for the Service
     *
     * @return  array
     * @since   1.0
     * @throws  \CommonApi\Exception\RuntimeException;
     */
    public function setDependencies(array $reflection = null)
    {
        parent::setDependencies($reflection);

        $options                           = array();
        $this->dependencies['Resources']   = $options;
        $this->dependencies['Runtimedata'] = $options;
        $this->dependencies['Cache']       = $options;

        return $this->dependencies;
    }

    /**
     * Set Dependencies for Instantiation
     *
     * @return  array
     * @since   1.0
     * @throws  \CommonApi\Exception\RuntimeException;
     */
    public function processFulfilledDependencies(array $dependency_instances = null)
    {
        parent::processFulfilledDependencies($dependency_instances);

        $this->dependencies['extensions_filename'] = BASE_FOLDER . '/Vendor/Molajo/Resources/Files/Output/Extensions.json';

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
        $cache_results = $this->dependencies['Cache']->get('Extensions');

        if ($cache_results->isHit === false) {
            $class = 'Molajo\\Resources\\ExtensionMap';

            try {
                $this->service_instance = new $class(
                    $this->dependencies['Resources'],
                    $this->dependencies['Runtimedata'],
                    $this->dependencies['extensions_filename']
                );
            } catch (Exception $e) {
                throw new RuntimeException
                ('Render: Could not instantiate Handler: ' . $class);
            }

            $extensions             = $this->service_instance->createMap();
            $this->service_instance = $extensions;
            $this->dependencies['Cache']->set('Extensions', $extensions);

        } else {
            $this->service_instance = $cache_results->value;
        }

        return $this;
    }
}
