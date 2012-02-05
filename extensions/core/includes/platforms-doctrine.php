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

//$controller = JController::getInstance('Bugs');
// configure Doctrine thru the bootstrapper
$bootstrap = new DoctrineBootstrapper ();
$em = $bootstrap->setEntityManager(bootstrapDoctrine());

//echo '<pre>';
//var_dump($res);
//echo '</pre>';
$q = $em->createQueryBuilder();
$q = Doctrine_Query::create()
    ->select('a.username')
    ->from('molajo_users a');

echo $q->getSqlQuery();

//$controller->execute(JRequest::getCmd('task', 'index'));
//$controller->redirect();

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

