<?php
/**
 * @package     Molajo
 * @subpackage  Doctrine
 * @copyright   Copyright (C) 2012 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die;
/**
 *  File Helper
 */
$fileHelper = new MolajoFileHelper();

require_once(PLATFORM_DOCTRINE . '/bootstrap.php');

$bootstrap = new DoctrineBootstrapper ();
$bootstrap->setEntityManager(bootstrapDoctrine());

/**
 * Initialize doctrine by setting the entities and proxies locaties. Also define
 * a default namespace for the proxies.
 */
function bootstrapDoctrine() {
    $doctrineProxy = new DoctrineBootstrapper( DoctrineBootstrapper::APP_MODE_DEVELOPMENT );
    $doctrineProxy->setEntityLibrary(MOLAJO_DOCTRINE_MODELS . '/models');
    $doctrineProxy->setProxyLibrary(MOLAJO_DOCTRINE_PROXIES . '/proxies');
    $doctrineProxy->setProxyNamespace('Proxies');
    $doctrineProxy->setConnectionOptions(getConfigurationOptions());
    $doctrineProxy->bootstrap();

    return $doctrineProxy->getEntityManager();
}

function getConfigurationOptions() {
    return array(
        'driver' => 'pdo_mysql',
        'path' => 'database.mysql',
        'dbname' => MolajoController::getApplication()->get('db'),
        'user' => MolajoController::getApplication()->get('user'),
        'password' => MolajoController::getApplication()->get('password')
    );
}

