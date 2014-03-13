<?php
/**
 * Set Namespace Prefixes and Paths
 *
 * @package    Molajo
 * @copyright  2014 Amy Stephen. All rights reserved.
 * @license    http:/www.opensource.org/licenses/mit-license.html MIT License
 */

/**
 *  DISTRIBUTION LAYER
 *  - change to site/cache, site/log, etc.
 *  - plugins should each be installable
 *  - resources should each be installable
 */
$resource_adapter->setNamespace('Molajo\\Sites\\', 'Sites/2/');
$resource_adapter->setNamespace('Molajo\\Plugins\\', 'Source/Plugins/');
$resource_adapter->setNamespace('Molajo\\', 'Source/Resources/');

/**
 *  PRESENTATION LAYER
 */
$resource_adapter->setNamespace('Molajo\\Plugins\\', 'vendor/molajo/foundation5/Source/Plugins/');
$resource_adapter->setNamespace('Molajo\\Themes\\', 'vendor/molajo/foundation5/Source/Themes/');
$resource_adapter->setNamespace('Molajo\\Views\\Pages\\', 'vendor/molajo/foundation5/Source/Views/Pages/');
$resource_adapter->setNamespace('Molajo\\Views\\Templates\\', 'vendor/molajo/foundation5/Source/Views/Templates/');
$resource_adapter->setNamespace('Molajo\\Views\\Wraps\\', 'vendor/molajo/foundation5/Source/Views/Wraps/');

/**
 *  APPLICATION EXTENSIONS
 */
$resource_adapter->setNamespace('Molajo\\Controller\\', 'vendor/molajo/application/Source/Controller/');
$resource_adapter->setNamespace('Molajo\\Model\\', 'vendor/molajo/application/Source/Model/');
$resource_adapter->setNamespace('Molajo\\Plugins\\', 'vendor/molajo/application/Source/Plugins/');
$resource_adapter->setNamespace('Molajo\\Resource\\', 'vendor/molajo/application/Source/Resource/');
$resource_adapter->setNamespace('Molajo\\Factories\\', 'vendor/molajo/application/Factories/');
$resource_adapter->setNamespace('Molajo\\Resource\\', 'vendor/molajo/application/Source/Resource/');
$resource_adapter->setNamespace('Molajo\\', 'vendor/molajo/application/Source/Extensions/');

/**
 *  FRAMEWORK PACKAGES
 */
$resource_adapter->setNamespace('Molajo\\Authorisation\\', 'vendor/molajo/authorisation/Source/');
$resource_adapter->setNamespace('Molajo\\Factories\\', 'vendor/molajo/authorisation/Factories/');

$resource_adapter->setNamespace('Molajo\\Cache\\', 'vendor/molajo/cache/Source/');
$resource_adapter->setNamespace('Molajo\\Factories\\', 'vendor/molajo/cache/Factories/');

$resource_adapter->setNamespace('Molajo\\Database\\', 'vendor/molajo/database/Source/');
$resource_adapter->setNamespace('Molajo\\Factories\\', 'vendor/molajo/database/Factories/');

$resource_adapter->setNamespace('Molajo\\Email\\', 'vendor/molajo/email/source/');
$resource_adapter->setNamespace('Molajo\\Factories\\', 'vendor/molajo/email/Factories/');

$resource_adapter->setNamespace('Molajo\\Event\\', 'vendor/molajo/event/Source/');
$resource_adapter->setNamespace('Molajo\\Plugins\\', 'vendor/molajo/event/Plugins/');
$resource_adapter->setNamespace('Molajo\\Factories\\', 'vendor/molajo/event/Factories/');

$resource_adapter->setNamespace('Molajo\\Fieldhandler\\', 'vendor/molajo/fieldhandler/Source/');
$resource_adapter->setNamespace('Molajo\\Factories\\', 'vendor/molajo/fieldhandler/Factories/');

$resource_adapter->setNamespace('Molajo\\Filesystem\\', 'vendor/molajo/filesystem/Source/');
$resource_adapter->setNamespace('Molajo\\Factories\\', 'vendor/molajo/filesystem/Factories/');

$resource_adapter->setNamespace('Molajo\\Http\\', 'vendor/molajo/http/Source/');
$resource_adapter->setNamespace('Molajo\\Factories\\', 'vendor/molajo/http/Factories/');

$resource_adapter->setNamespace('Molajo\\IoC\\', 'vendor/molajo/ioc/Source');
$resource_adapter->setNamespace('Molajo\\Language\\', 'vendor/molajo/Language/Source/');
$resource_adapter->setNamespace('Molajo\\Factories\\', 'vendor/molajo/language/Factories/');
//$resource_adapter->setNamespace('Molajo\\Factories', 'vendor/molajo/Log/Service');
//$resource_adapter->setNamespace('Molajo\\Log', 'vendor/molajo/Log/Source/');

$resource_adapter->setNamespace('Molajito\\', 'vendor/molajo/molajito/Source/');
$resource_adapter->setNamespace('Molajo\\Factories\\', 'vendor/molajo/molajito/Factories/');

$resource_adapter->setNamespace('Molajo\\Pagination\\', 'vendor/molajo/pagination/Source/');

$resource_adapter->setNamespace('Molajo\\Render\\', 'vendor/molajo/render/Source/');
$resource_adapter->setNamespace('Molajo\\Factories\\', 'vendor/molajo/render/Factories/');

$resource_adapter->setNamespace('Molajo\\Resource\\', 'vendor/molajo/resource/Source/');
$resource_adapter->setNamespace('Molajo\\Factories\\', 'vendor/molajo/resource/Factories/');

$resource_adapter->setNamespace('Molajo\\Route\\', 'vendor/molajo/route/Source/');
$resource_adapter->setNamespace('Molajo\\Factories\\', 'vendor/molajo/route/Factories/');

$resource_adapter->setNamespace('Molajo\\User\\', 'vendor/molajo/user/Source/');
$resource_adapter->setNamespace('Molajo\\Factories\\', 'vendor/molajo/user/Factories/');

/**
 *  COMMON API
 */
$resource_adapter->setNamespace('CommonApi\\Authorisation\\', 'vendor/commonapi/authorisation/');
$resource_adapter->setNamespace('CommonApi\\Cache\\', 'vendor/commonapi/cache/');
$resource_adapter->setNamespace('CommonApi\\Controller\\', 'vendor/commonapi/controller/');
$resource_adapter->setNamespace('CommonApi\\Database\\', 'vendor/commonapi/database/');
$resource_adapter->setNamespace('CommonApi\\Email\\', 'vendor/commonapi/email/');
$resource_adapter->setNamespace('CommonApi\\Event\\', 'vendor/commonapi/event/');
$resource_adapter->setNamespace('CommonApi\\Exception\\', 'vendor/commonapi/exception/');
$resource_adapter->setNamespace('CommonApi\\Filesystem\\', 'vendor/commonapi/filesystem/');
$resource_adapter->setNamespace('CommonApi\\Http\\', 'vendor/commonapi/http/');
$resource_adapter->setNamespace('CommonApi\\IoC\\', 'vendor/commonapi/ioc/');
$resource_adapter->setNamespace('CommonApi\\Language\\', 'vendor/commonapi/language/');
$resource_adapter->setNamespace('CommonApi\\Model\\', 'vendor/commonapi/model/');
$resource_adapter->setNamespace('CommonApi\\Query\\', 'vendor/commonapi/query/');
$resource_adapter->setNamespace('CommonApi\\Render\\', 'vendor/commonapi/render/');
$resource_adapter->setNamespace('CommonApi\\Resource\\', 'vendor/commonapi/resource/');
$resource_adapter->setNamespace('CommonApi\\Route\\', 'vendor/commonapi/route/');
$resource_adapter->setNamespace('CommonApi\\User\\', 'vendor/commonapi/user/');
