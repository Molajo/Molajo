<?php
/**
 * Resources Injector
 *
 * @package    Molajo
 * @license    http:/www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2013 Amy Stephen. All rights reserved.
 */
namespace Molajo\Service\Resources;

use Exception;
use CommonApi\Exception\RuntimeException;
use Molajo\IoC\Handler\AbstractInjector;
use CommonApi\IoC\ServiceHandlerInterface;

/**
 * Resources Dependency Injector
 *
 * @author     Amy Stephen
 * @license    http:/www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2013 Amy Stephen. All rights reserved.
 * @since      1.0
 */
class ResourcesInjector extends AbstractInjector implements ServiceHandlerInterface
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
        $options['service_namespace']        = 'Molajo\\Resources\\Adapter';
        $options['store_instance_indicator'] = true;
        $options['service_name']             = basename(__DIR__);

        parent::__construct($options);
    }

    /**
     * Set Dependencies for Service
     *
     * @param   array $reflection
     *
     * @return  array|bool
     * @since   1.0
     * @throws  \CommonApi\Exception\RuntimeException;
     */
    public function setDependencies(array $reflection = null)
    {
        if ($reflection === null) {
            $this->reflection = array();
        } else {
            $this->reflection = $reflection;
        }

        $this->options['Scheme'] = $this->createScheme();

        $handler_instance = array();

        $resource_map = $this->readFile(BASE_FOLDER . '/Vendor/Molajo/Resources/Files/Output/ResourceMap.json');

        /**
         * NOTE:
         *  Css, Cssdeclarations, Jsdeclarations, and JsHandler loaded in ApplicationInjector
         *  QueryHandler loaded following DatabaseInjector
         */
        $handler_instance['AssetHandler']
            = $this->createHandler(
            'AssetHandler',
            BASE_FOLDER,
            $resource_map,
            array(),
            $this->options['Scheme']->getScheme('Asset')->include_file_extensions
        );
        $handler_instance['ClassHandler']
            = $this->createHandler(
            'ClassHandler',
            BASE_FOLDER,
            $resource_map,
            array(),
            $this->options['Scheme']->getScheme('Class')->include_file_extensions
        );
        $handler_instance['FileHandler']
            = $this->createHandler(
            'FileHandler',
            BASE_FOLDER,
            $resource_map,
            array(),
            $this->options['Scheme']->getScheme('File')->include_file_extensions
        );
        $handler_instance['FolderHandler']
            = $this->createHandler(
            'FolderHandler',
            BASE_FOLDER,
            $resource_map,
            array(),
            $this->options['Scheme']->getScheme('Folder')->include_file_extensions
        );
        $handler_instance['HeadHandler']
            = $this->createHandler(
            'HeadHandler',
            BASE_FOLDER,
            $resource_map,
            array(),
            $this->options['Scheme']->getScheme('Head')->include_file_extensions
        );
        $handler_instance['XmlHandler']
            = $this->createHandler(
            'XmlHandler',
            BASE_FOLDER,
            $resource_map,
            array(),
            $this->options['Scheme']->getScheme('Xml')->include_file_extensions
        );

        $this->options['handler_instance_array'] = $handler_instance;

        $this->dependencies = array();

        return $this->dependencies;
    }

    /**
     * Fulfill Dependencies
     *
     * @param   array $dependency_instances (ignored in Service Item Adapter, based in from handler)
     *
     * @return  $this
     * @since   1.0
     */
    public function processFulfilledDependencies(array $dependency_instances = null)
    {
        $this->dependencies['Scheme']                 = $this->options['Scheme'];
        $this->dependencies['handler_instance_array'] = $this->options['handler_instance_array'];

        return $this->dependencies;
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
        $class = 'Molajo\\Resources\\Adapter';

        $this->service_instance = new $class(
            $this->dependencies['Scheme'],
            $this->dependencies['handler_instance_array']
        );

        return $this;
    }

    /**
     * Follows the completion of the instantiate service method
     *
     * @return  object
     * @since   1.0
     */
    public function performAfterInstantiationLogic()
    {
        $this->service_instance->setNamespace(
            'PasswordLib\\PasswordLib',
            BASE_FOLDER . '/Vendor' . '/Molajo' . '/User/Encrypt/PasswordLib.phar'
        );

        return $this;
    }

    /**
     * Schedule the Next Service
     *
     * @return  object
     * @since   1.0
     */
    public function scheduleNextService()
    {
        $this->schedule_service = array();

        $options                             = array();
        $options['service_namespace']        = 'Molajo\\Resources\\Configuration\\Registry';
        $options['store_instance_indicator'] = true;
        $options['service_name']             = 'Registry';
        $this->schedule_service['Registry']  = $options;

        $options                                = array();
        $options['service_namespace']           = 'Molajo\\Fieldhandler\\Adapter';
        $options['store_instance_indicator']    = true;
        $options['service_name']                = 'Fieldhandler';
        $this->schedule_service['Fieldhandler'] = $options;

        $options                                 = array();
        $options['Resources']                    = $this->service_instance;
        $this->schedule_service['Resourcesdata'] = $options;

        $options                                     = array();
        $options['service_namespace']                = 'Exception\\ControllerHandlingController';
        $options['store_instance_indicator']         = true;
        $options['service_name']                     = 'Exceptionhandling';
        $this->schedule_service['Exceptionhandling'] = $options;

        return $this->schedule_service;
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
     * Create Handler Instance
     *
     * @param   string $handler
     * @param   string $base_path
     * @param   array  $resource_map
     * @param   array  $namespace_prefixes
     * @param   array  $valid_file_extensions
     *
     * @return  mixed
     * @since   1.0
     * @throws  \CommonApi\Exception\RuntimeException;
     */
    protected function createHandler($handler, $base_path, $resource_map, $namespace_prefixes, $valid_file_extensions)
    {
        $class = 'Molajo\\Resources\\Handler\\' . $handler;

        try {
            $handler_instance = new $class (
                $base_path,
                $resource_map,
                $namespace_prefixes,
                $valid_file_extensions);
        } catch (Exception $e) {
            throw new RuntimeException ('Resources Handler ' . $handler
            . ' Exception during Instantiation: ' . $e->getMessage());
        }

        return $handler_instance;
    }
}
