<?php
/**
 * Database Factory Method
 *
 * @package    Molajo
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2014 Amy Stephen. All rights reserved.
 */
namespace Molajo\Factories\Database;

use Exception;
use CommonApi\Exception\RuntimeException;
use CommonApi\IoC\FactoryInterface;
use CommonApi\IoC\FactoryBatchInterface;
use Molajo\IoC\FactoryMethod\Base as FactoryMethodBase;

/**
 * Database Factory Method
 *
 * @author     Amy Stephen
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2014 Amy Stephen. All rights reserved.
 * @since      1.0.0
 */
class DatabaseFactoryMethod extends FactoryMethodBase implements FactoryInterface, FactoryBatchInterface
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
        $options['product_name']             = basename(__DIR__);
        $options['store_instance_indicator'] = true;
        $options['product_namespace']        = 'Molajo\\Database\\Driver';

        parent::__construct($options);
    }

    /**
     * Instantiate a new adapter and inject it into the Adapter for the FactoryInterface
     *
     * @return  array
     * @since   1.0
     * @throws  \CommonApi\Exception\RuntimeException
     */
    public function setDependencies(array $reflection = array())
    {
        parent::setDependencies($reflection);

        $this->dependencies                = array();
        $this->dependencies['Resource']    = array();
        $this->dependencies['Runtimedata'] = array();

        return $this->dependencies;
    }

    /**
     * Instantiate Class
     *
     * @return  object
     * @since   1.0
     * @throws  \CommonApi\Exception\RuntimeException
     */
    public function instantiateClass()
    {
//        $this->dependencies['Resource']->setNamespace(
//           'molajo\\dataobject',
//           $this->dependencies['Runtimedata']->site->base_path . '/Dataobject/',
//            true
//        );

//        $configuration = $this->dependencies['Resource']->get('xml:///Molajo//Model//Dataobject//Database.xml');
        /**
         * $options                    = array();
         * $options['db_type']         = $configuration['db_type'];
         * $options['db_host']         = $configuration['db_host'];
         * $options['db_user']         = $configuration['db_user'];
         * $options['db_password']     = $configuration['db_password'];
         * $options['db_name']         = $configuration['db'];
         * $options['db_prefix']       = $configuration['db_prefix'];
         * $options['process_events'] = $configuration['process_events'];
         * $options['select']          = true;
         */
        $db_type        = "mysqli";
        $db_host        = "localhost";
        $db_user        = "root";
        $db_password    = "root";
        $db             = "molajo_site2";
        $db_prefix      = "molajo_";
        $process_events = "1";

        $options                   = array();
        $options['db_type']        = $db_type;
        $options['db_host']        = $db_host;
        $options['db_user']        = $db_user;
        $options['db_password']    = $db_password;
        $options['db_name']        = $db;
        $options['db_prefix']      = $db_prefix;
        $options['process_events'] = $process_events;
        $options['select']         = true;

        try {
            $adapter = $this->getAdapter($options);

            $this->product_result = $this->getDriver($adapter);

        } catch (Exception $e) {
            echo $e->getMessage();

            throw new RuntimeException
            (
                'Database Factory Method Adapter Instance Failed for ' . $this->product_namespace
                . ' failed.' . $e->getMessage()
            );
        }

        return $this;
    }

    /**
     * Get the Database specific Adapter Handler
     *
     * @param   string $options
     *
     * @return  object
     * @since   1.0
     * @throws  \CommonApi\Exception\RuntimeException
     */
    protected function getAdapter($options)
    {
        $class = 'Molajo\\Database\\Adapter\\Joomla';

        try {
            $adapter = new $class($options);
        } catch (Exception $e) {
            throw new RuntimeException
            (
                'Database: Could not instantiate Database Adapter ' . $class
            );
        }

        return $adapter;
    }

    /**
     * Get Database Adapter, inject with specific Database Adapter Handler
     *
     * @param   object $adapter
     *
     * @return  object
     * @since   1.0
     * @throws  \CommonApi\Exception\RuntimeException
     */
    protected function getDriver($adapter)
    {
        try {
            $class = $this->product_namespace;
            return new $class($adapter);
        } catch (Exception $e) {
            throw new RuntimeException
            (
                'Database: Could not instantiate Adapter'
            );
        }
    }
}
