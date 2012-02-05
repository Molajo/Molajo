<?php
/**
 * @package     Molajo
 * @subpackage  Page
 * @copyright   Copyright (C) 2012 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die;

use Doctrine\Common\ClassLoader,
    Doctrine\ORM\EntityManager,
    Doctrine\ORM\Configuration,
   Doctrine\Common\Cache\ArrayCache;

if( !class_exists('\Doctrine\Common\Classloader')) {
    require_once dirname(__FILE__) . '/../doctrine/Doctrine/Common/ClassLoader.php';
}

class DoctrineBootstrapper {

    const APP_MODE_DEVELOPMENT = 1;
    const APP_MODE_PRODUCTION  = 2;

    private $applicationMode;
    private $cache;
    private $entityLibrary;
    private $proxyLibrary;
    private $proxyNamespace;
    private $entityManager;
    private $connectionOptions;

    public function __construct($applicationMode=2) {
        $this->applicationMode = $applicationMode;
    }

    public function getConnectionOptions() {
        return $this->connectionOptions;
    }

    public function setConnectionOptions($connectionOptions) {
        $this->connectionOptions = $connectionOptions;
    }

    public function getProxyLibrary() {
        return $this->proxyLibrary;
    }

    public function setProxyLibrary($proxyLibrary) {
        $this->proxyLibrary = $proxyLibrary;
    }

    public function getProxyNamespace() {
        return $this->proxyNamespace;
    }

    public function setProxyNamespace($proxyNamespace) {
        $this->proxyNamespace = $proxyNamespace;
    }

    public function getCache() {
        return $this->cache;
    }

    public function setCache($cache) {
        $this->cache = $cache;
    }

    public function getEntityLibrary() {
        return $this->entityLibrary;
    }

    public function setEntityLibrary($entityLibrary) {
        $this->entityLibrary = $entityLibrary;
    }

    public function getApplicationMode() {
        return $this->applicationMode;
    }

    public function setApplicationMode($applicationMode) {
        $this->applicationMode = $applicationMode;
    }

    public function getEntityManager() {
        return $this->entityManager;
    }

    public function setEntityManager($entityManager) {
        $this->entityManager = $entityManager;
    }

    /**
     * Bootstrap Doctrine, setting the libraries and namespaces and creating
     * the entitymanager
     */
    public function bootstrap() {
        $this->registerClassLoader();

        // Load cache
        if ($this->getApplicationMode() == self::APP_MODE_DEVELOPMENT) {
            $this->cache = new ArrayCache;
        } else {
            $this->cache = new ApcCache;
        }

        /** @var $config Doctrine\ORM\Configuration */
        $config = new Configuration;
        $config->setMetadataCacheImpl($this->cache);
        $driverImpl = $config->newDefaultAnnotationDriver($this->getEntityLibrary());

        $config->setMetadataDriverImpl($driverImpl);
        $config->setQueryCacheImpl($this->cache);
        $config->setProxyDir($this->getProxyLibrary());
        $config->setProxyNamespace($this->getProxyNamespace());

        if ($this->applicationMode == self::APP_MODE_DEVELOPMENT) {
            $config->setAutoGenerateProxyClasses(true);
        } else {
            $config->setAutoGenerateProxyClasses(false);
        }

        $this->entityManager = EntityManager::create($this->getConnectionOptions(), $config);
        echo '<pre>';
        var_dump($this->entityManager);
        echo '</pre>';
    }

    /**
     * Register the different classloaders for each type.
     */
    private function registerClassLoader() {
        // Autoloader for all the Doctrine library files
        $classLoader = new ClassLoader('Doctrine', dirname(__FILE__) . '/');
        $classLoader->register();

        // Autoloader for all Entities
        $modelLoader = new ClassLoader('Entities', $this->getEntityLibrary());
        $modelLoader->register();

        // Autoloader for all Proxies
        $proxiesClassLoader = new ClassLoader('Proxies', $this->getProxyLibrary());
        $proxiesClassLoader->register();
    }

}
