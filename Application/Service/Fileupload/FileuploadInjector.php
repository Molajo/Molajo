<?php
/**
 * Fileupload Dependency Injector
 *
 * @package    Molajo
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2013 Amy Stephen. All rights reserved.
 */
namespace Molajo\Service\Fileupload;

use Exception;
use Molajo\IoC\Handler\AbstractInjector;
use CommonApi\IoC\ServiceHandlerInterface;
use CommonApi\Exception\RuntimeException;

/**
 * Fileupload Service Dependency Injector
 *
 * @author     Amy Stephen
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2013 Amy Stephen. All rights reserved.
 * @since      1.0
 */
class FileuploadInjector extends AbstractInjector implements ServiceHandlerInterface
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
        $options['service_namespace']        = 'Molajo\\FileUpload\\Upload';

        parent::__construct($options);
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
        $this->dependencies                = array();
        $this->dependencies['Filesystem']  = array();
        $this->dependencies['Runtimedata'] = array();

        return $this->dependencies;
    }

    /**
     * Instantiate Class
     *
     * @return  object
     * @since   1.0
     * @throws  \CommonApi\Exception\RuntimeException;
     */
    public function instantiateService()
    {
//todo: add to application configuration
        $options = array();
        if (isset($this->options['maximum_file_size'])) {
            $options['maximum_file_size'] = $this->options['maximum_file_size'];
        }
        if (isset($this->options['allowable_mimes_and_extensions'])) {
            $options['allowable_mimes_and_extensions'] = $this->options['allowable_mimes_and_extensions'];
        }
        if (isset($this->options['target_folder'])) {
            $options['target_folder'] = $this->options['target_folder'];
        }
        if (isset($this->options['overwrite_existing_file'])) {
            $options['overwrite_existing_file'] = $this->options['overwrite_existing_file'];
        }

        /** Typically, this is all the application will provide */
        if (isset($this->options['input_field_name'])) {
            $options['input_field_name'] = $this->options['input_field_name'];
        }
        if (isset($this->options['target_filename'])) {
            $options['target_filename'] = $this->options['target_filename'];
        }

        try {
            $class                  = $this->service_namespace;
            $this->service_instance = $class($options, $this->dependencies['Filesystem']);
            $this->service_instance->uploadFile();
        } catch (Exception $e) {

            throw new RuntimeException
            ('IoC: Injector Instance Failed for ' . $this->service_namespace
            . ' failed.' . $e->getMessage());
        }

        return $this;
    }
}
