<?php

use Doctrine\Common\ClassLoader,
Doctrine\ORM\Configuration,
Doctrine\ORM\EntityManager,
Doctrine\Common\Cache\ArrayCache,
Doctrine\DBAL\Logging\EchoSqlLogger;

class Doctrine
{

    public $em = null;

    public function __construct()
    {
        // load database configuration from molajo
        require_once APPPATH . 'configuration.php';
        // Set up class loading. You could use different autoloaders, provided by your favorite framework,
        // if you want to.
        require_once APPPATH . 'libraries/Doctrine/Common/ClassLoader.php';

        $doctrineClassLoader = new ClassLoader('Doctrine', APPPATH . 'libraries');
        $doctrineClassLoader->register();
        // Location of the Models - This referes to /models - ie. User class will be in /models/User.php
        $entitiesClassLoader = new ClassLoader('models', rtrim(APPPATH, '/'));
        $entitiesClassLoader->register();
        $proxiesClassLoader = new ClassLoader('Proxies', APPPATH . 'models/proxies');
        $proxiesClassLoader->register();

        // Set up caches
        $config = new Configuration;
        $cache = new ArrayCache;
        $config->setMetadataCacheImpl($cache);
        $config->setQueryCacheImpl($cache);

        // Set up driver - Uses Annotation - Change to XML or YAML as required
        $Doctrine_AnnotationReader = new \Doctrine\Common\Annotations\AnnotationReader($cache);
        $Doctrine_AnnotationReader->setDefaultAnnotationNamespace('Doctrine\ORM\Mapping\\');
        $driver = new \Doctrine\ORM\Mapping\Driver\AnnotationDriver($Doctrine_AnnotationReader, APPPATH . 'models');
        $config->setMetadataDriverImpl($driver);

        // Proxy configuration
        $config->setProxyDir(APPPATH . '/models/proxies');
        $config->setProxyNamespace('Proxies');

        // Set up logger
        //$logger = new EchoSqlLogger;
        //$config->setSqlLogger($logger);

        $config->setAutoGenerateProxyClasses(TRUE);

        // Database connection information
        $connectionOptions = array(
            'driver' => 'pdo_mysql',
            'user' => $db['default']['username'],
            'password' => $db['default']['password'],
            'host' => $db['default']['hostname'],
            'dbname' => $db['default']['database']
        );

        // Create EntityManager
        $this->em = EntityManager::create($connectionOptions, $config);
    }
}