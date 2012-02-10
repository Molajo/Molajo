<?php
/**
 * @package     Molajo
 * @subpackage  Service
 * @copyright   Copyright (C) 2012 Amy Stephen. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */
defined('MOLAJO') or die;

/**
 * Configuration
 *
 * @package     Molajo
 * @subpackage  Service
 * @since       1.0
 */
class MolajoDoctrineService
{
    /**
     * get
     *
     * Get a database object for Doctrine
     *
     * @return Database object
     * @since 1.0
     */
    public static function get($config = null)
    {
        return;
        if ($config instanceof JRegistry) {
            $site = $config;
        } else {
            /** database connect procedes instantiation of application */
            $configClass = new ApplicationService();
            $site = $configClass->getSite();
        }

        $doctrineProxy = new DoctrineBootstrapper(1);

        $doctrineProxy->setEntityLibrary(MOLAJO_DOCTRINE_MODELS . '/models');
        $doctrineProxy->setProxyLibrary(MOLAJO_DOCTRINE_PROXIES . '/proxies');
        $doctrineProxy->setProxyNamespace('Proxies');
        $doctrineProxy->setConnectionOptions(
            array(
                'driver' => 'pdo_mysql',
                'path' => 'database.mysql',
                'dbname' => $site->db,
                'user' => $site->user,
                'password' => $site->password
            )
        );
        $entityManager = $doctrineProxy->bootstrap();

        return $entityManager;
    }
}
