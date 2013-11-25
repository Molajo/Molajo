<?php
/**
 * Filesystem Dependency Injector
 *
 * @package    Molajo
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2013 Amy Stephen. All rights reserved.
 */
namespace Molajo\Service\Filesystem;

use Exception;
use CommonApi\Exception\RuntimeException;
use Molajo\IoC\Handler\AbstractInjector;
use CommonApi\IoC\ServiceHandlerInterface;

/**
 * Filesystem Service Dependency Injector
 *
 * @author     Amy Stephen
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2013 Amy Stephen. All rights reserved.
 * @since      1.0
 */
class FilesystemInjector extends AbstractInjector implements ServiceHandlerInterface
{
    /**
     * Adapter Instance
     *
     * @var     object
     * @since   1.0
     */
    public $adapter;

    /**
     * Adapter Handler Instance
     *
     * @var     object
     * @since   1.0
     */
    public $adapter_handler;

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
        $options['service_namespace']        = 'Molajo\\Filesystem\\Adapter';

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
        $options                = array();
        $handler                = $this->getAdapterHandler($options);
        $this->service_instance = $this->getAdapter($handler);

        return $this;
    }

    /**
     * Get the Filesystem specific Adapter Handler
     *
     * @param   string $options
     *
     * @return  object
     * @since   1.0
     * @throws  ServiceHandlerInterface
     */
    protected function getAdapterHandler($options = array())
    {
        $class = 'Molajo\\Filesystem\\Handler\\Local';

        try {
            return new $class($options);
        } catch (Exception $e) {
            throw new RuntimeException
            ('Filesystem: Could not instantiate Filesystem Adapter Handler: Local');
        }
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
    protected function getAdapter($handler)
    {
        $class = 'Molajo\\Filesystem\\Adapter';

        try {
            return new $class($handler);
        } catch (Exception $e) {

            throw new RuntimeException
            ('Filesystem: Could not instantiate Adapter for Filesystem Type: Local');
        }
    }

    /**
     * Quick file tests
     *
     * @return  $this
     * @since   1.0
     * @throws  \CommonApi\Exception\RuntimeException;
     */
    protected function testAPI()
    {
        /** 1. Exists */
        $true_or_false = $this->service_instance->exists(__DIR__ . '/Configuration.xml');

        if ($true_or_false === false) {
            echo 'LocalFilesystem Exists did not work when tested in FilesystemInjector'
                . __DIR__ . '/Configuration.xml. <br /> ';
            die;
        } else {
            echo 'LocalFilesystem Exists when tested in FilesystemInjector'
                . __DIR__ . '/Configuration.xml. <br /> ';
        }

        /** Metadata */
        $metadata = $this->service_instance->getMetadata(__DIR__ . '/Configuration.xml');

        /** List */
        $path            = BASE_FOLDER . '/Vendor' . '/Molajo';
        $recursive       = true;
        $extension_list  = null;
        $include_files   = true;
        $include_folders = false;
        $filename_mask   = null;

        $list_of_results = $this->service_instance->getList(
            $path,
            $recursive,
            $extension_list,
            $include_files,
            $include_folders,
            $filename_mask
        );

        /** Write */
        $path     = __DIR__ . '/Testfile.txt';
        $data     = 'Here is stuff to read.';
        $replace  = true;
        $append   = false;
        $truncate = false;

        $this->service_instance->write($path, $data, $replace, $append, $truncate);

        /** Read */
        $contents = $this->service_instance->read($path);
        echo $contents;

        $target_directory       = __DIR__;
        $target_name            = 'Newfile.txt';
        $replace                = true;
        $target_adapter_handler = 'Local';

        $this->service_instance->copy($path, $target_directory, $target_name, $replace, $target_adapter_handler);

        die;

        $this->service_instance->move($path, $target_directory, $target_name, $replace, $target_adapter_handler);

        $this->service_instance->rename($path, $new_name);

        $this->service_instance->delete($path, $recursive);

        $this->service_instance->changeOwner($path, $user_name, $recursive);

        $this->service_instance->changeGroup($path, $group_id, $recursive);

        $this->service_instance->changePermission($path, $permission, $recursive);

        $this->service_instance->touch($path, $modification_time, $access_time, $recursive);

        die;
    }
}
