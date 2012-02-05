<?php

// See :doc:`Configuration <../reference/configuration>` for up to date autoloading details.
use Doctrine\ORM\Tools\Setup;

require_once PLATFORM_DOCTRINE.'/ORM/Tools/Setup.php';

//Setup::registerAutoloadPEAR();

// Create a simple "default" Doctrine ORM configuration for XML Mapping
$isDevMode = true;
$config = Setup::createXMLMetadataConfiguration(array(__DIR__."/config/xml"), $isDevMode);

echo 'all is well';
die;
// or if you prefer yaml or annotations
//$config = Setup::createAnnotationMetadataConfiguration(array(__DIR__."/entities"), $isDevMode);
//$config = Setup::createYAMLMetadataConfiguration(array(__DIR__."/config/yaml"), $isDevMode);

// database configuration parameters

$conn = array(
    'driver' => 'pdo_sqlite',
    'path' => __DIR__ . '/db.sqlite',
);

// Define database configuration options
     // Define database configuration options
    $joomlaConfig = MolajoController::getApplication()->get('db');
    return array(
        'driver' => 'pdo_mysql',
        'path' => 'database.mysql',
        'dbname' => $joomlaConfig->get('db'),
        'user' =>  $joomlaConfig->get('user'),
        'password' =>  $joomlaConfig->get('password')
    );
