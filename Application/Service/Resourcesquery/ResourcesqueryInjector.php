<?php
/**
 * Resourcesquery Dependency Injector
 *
 * @package    Molajo
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2013 Amy Stephen. All rights reserved.
 */
namespace Molajo\Service\Resourcesquery;

use Exception;
use Molajo\IoC\Handler\AbstractInjector;
use CommonApi\IoC\ServiceHandlerInterface;
use CommonApi\Exception\RuntimeException;

/**
 * Resourcesquery Service Dependency Injector
 *
 * @author     Amy Stephen
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2013 Amy Stephen. All rights reserved.
 * @since      1.0
 */
class ResourcesqueryInjector extends AbstractInjector implements ServiceHandlerInterface
{
    /**
     * Constructor
     *
     * @param  array $options
     *
     * @since  1.0
     */
    public function __construct(array $options = array())
    {
        $options['service_namespace'] = 'Molajo\\Resources\\Handler\\QueryHandler';
        $options['service_name']      = basename(__DIR__);

        parent::__construct($options);

        $this->options['resources_array'] = $options['resources_array'];
    }

    /**
     * Instantiate a new handler and inject it into the Adapter for the ServiceHandlerInterface
     * Retrieve a list of Interface dependencies and return the data ot the controller.
     *
     * @return  array
     * @since   1.0
     * @throws  \CommonApi\Exception\RuntimeException;
     */
    public function setDependencies(array $reflection = null)
    {
        parent::setDependencies($reflection);

        $this->dependencies['Resources']    = array();
        $this->dependencies['Triggerevent'] = array();

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

        $this->dependencies['query']        = $this->dependencies['Database']->getQueryObject();
        $this->dependencies['null_date']    = $this->dependencies['Database']->getNullDate();
        $this->dependencies['current_date'] = $this->dependencies['Database']->getDate();

        $this->dependencies['base_path']          = BASE_FOLDER;
        $this->dependencies['resource_map']       = $this->readFile(
            BASE_FOLDER . '/Vendor/Molajo/Resources/Files/Output/ResourceMap.json'
        );
        $this->options['Scheme']                  = $this->createScheme();
        $this->dependencies['namespace_prefixes'] = array();
        $this->dependencies['valid_file_extensions']
                                                  = $this->options['Scheme']->getScheme(
            'Query'
        )->include_file_extensions;

        $resources = $this->options['resources_array'];
        if (count($resources) > 0) {
            foreach ($resources as $key => $value) {
                $this->dependencies[$key] = $value;
                unset($this->options[$key]);
            }
        }
        $this->dependencies['resources_array'] = $resources;
        $this->dependencies['trigger_event']   = $this->dependencies['Triggerevent'];

        return $this->dependencies;
    }

    /**
     * Follows the completion of the instantiate service method
     *
     * @return  $this
     * @since   1.0
     */
    public function performAfterInstantiationLogic()
    {
        $this->dependencies['Resources']->setHandlerInstance('QueryHandler', $this->service_instance);

        return $this;
    }

    /**
     * Create Scheme Instance
     *
     * @return  object
     * @since   1.0
     * @throws  \CommonApi\Exception\RuntimeException;
     */
    protected function createScheme()
    {
        $class = 'Molajo\\Resources\\Scheme';

        $input = BASE_FOLDER . '/Vendor/Molajo/Resources/Files/Input/SchemeArray.json';

        try {
            $scheme = new $class ($input);
        } catch (Exception $e) {
            throw new RuntimeException ('Resources Scheme ' . $class
            . ' Exception during Instantiation: ' . $e->getMessage());
        }

        return $scheme;
    }

    /**
     * Read File
     *
     * @param  string $file_name
     *
     * @return array
     * @since  1.0
     */
    protected function readFile($file_name)
    {
        $temp_array = array();

        if (file_exists($file_name)) {
        } else {
            return array();
        }

        $input = file_get_contents($file_name);

        $temp = json_decode($input);

        if (count($temp) > 0) {
            $temp_array = array();
            foreach ($temp as $key => $value) {
                $temp_array[$key] = $value;
            }
        }

        return $temp_array;
    }
}
