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
$resource_adapter->setNamespace('Molajo\\', 'Source/Resources/');

/**
 *  PRESENTATION LAYER
 */
$resource_adapter->setNamespace('Molajo\\Themes\\', 'vendor/molajo/foundation5/Source/Themes/');
$resource_adapter->setNamespace('Molajo\\Views\\Pages\\', 'vendor/molajo/foundation5/Source/Views/Pages/');
$resource_adapter->setNamespace('Molajo\\Views\\Templates\\', 'vendor/molajo/foundation5/Source/Views/Templates/');
$resource_adapter->setNamespace('Molajo\\Views\\Wraps\\', 'vendor/molajo/foundation5/Source/Views/Wraps/');


/**
 *  FRAMEWORK PACKAGES
 */
$resource_adapter->setNamespace('Molajo\\Authorisation\\', 'vendor/molajo/authorisation/Source/');
$resource_adapter->setNamespace(
    'Molajo\\Factories\\Authorisation\\',
    'vendor/molajo/authorisation/Factories/Authorisation/'
);

$resource_adapter->setNamespace('Molajo\\Cache\\', 'vendor/molajo/cache/Source/');
$resource_adapter->setNamespace('Molajo\\Factories\\', 'vendor/molajo/cache/Factories/');

$resource_adapter->setNamespace('Molajo\\Database\\', 'vendor/molajo/database/Source/');
$resource_adapter->setNamespace('Molajo\\Factories\\Database\\', 'vendor/molajo/database/Factories/Database/');

$resource_adapter->setNamespace('Molajo\\Email\\', 'vendor/molajo/email/source/');
$resource_adapter->setNamespace('Molajo\\Factories\\Email\\', 'vendor/molajo/email/Factories/Email/');

$resource_adapter->setNamespace('Molajo\\Event\\', 'vendor/molajo/event/Source/');
$resource_adapter->setNamespace('Molajo\\Factories\\Dispatcher\\', 'vendor/molajo/event/Factories/Dispatcher/');

$resource_adapter->setNamespace('Molajo\\Fieldhandler\\', 'vendor/molajo/fieldhandler/Source/');
$resource_adapter->setNamespace(
    'Molajo\\Factories\\Fieldhandler\\',
    'vendor/molajo/fieldhandler/Factories/Fieldhandler/'
);

$resource_adapter->setNamespace('Molajo\\Http\\', 'vendor/molajo/http/Source/');
$resource_adapter->setNamespace('Molajo\\Factories\\', 'vendor/molajo/http/Factories/');

$resource_adapter->setNamespace('Molajo\\IoC\\', 'vendor/molajo/ioc/Source/');

$resource_adapter->setNamespace('Molajo\\Language\\', 'vendor/molajo/Language/Source/');
$resource_adapter->setNamespace('Molajo\\Factories\\', 'vendor/molajo/language/Factories/');

$resource_adapter->setNamespace('Molajo\\Factories\\', 'vendor/molajo/Log/Factories/');
$resource_adapter->setNamespace('Molajo\\Controller\\', 'vendor/molajo/Log/Controller/');
$resource_adapter->setNamespace('Molajo\\Log\\', 'vendor/molajo/Log/Source/');
$resource_adapter->setNamespace('Psr\\Log\\', 'vendor/psr/log/Psr/Log/');

$resource_adapter->setNamespace('Molajito\\', 'vendor/molajo/molajito/Source/');
$resource_adapter->setNamespace('Molajo\\Factories\\', 'vendor/molajo/molajito/Factories/');

$resource_adapter->setNamespace('Molajo\\', 'vendor/molajo/pagination/Source/');

$resource_adapter->setNamespace('Molajo\\Render\\', 'vendor/molajo/render/Source/');
$resource_adapter->setNamespace('Molajo\\Factories\\Render\\', 'vendor/molajo/render/Factories/Render/');

$resource_adapter->setNamespace('Molajo\\Resource\\', 'vendor/molajo/resource/Source/');
$resource_adapter->setNamespace('Molajo\\Factories\\', 'vendor/molajo/resource/Factories/');

$resource_adapter->setNamespace('Molajo\\Controller\\', 'vendor/molajo/query/Controller/');
$resource_adapter->setNamespace('Molajo\\Model\\', 'vendor/molajo/query/Model/');
$resource_adapter->setNamespace('Molajo\\Resource\\', 'vendor/molajo/query/Resource/');

$resource_adapter->setNamespace('Molajo\\Factories\\', 'vendor/molajo/query/Factories/');
$resource_adapter->setNamespace('Molajo\\Query\\', 'vendor/molajo/query/Source/');
$resource_adapter->setNamespace('Molajo\\Query\\', 'vendor/molajo/query/Traits/');

$resource_adapter->setNamespace('Molajo\\Route\\', 'vendor/molajo/route/Source/');
$resource_adapter->setNamespace('Molajo\\Factories\\Route\\', 'vendor/molajo/route/Factories/Route/');

$resource_adapter->setNamespace('Molajo\\User\\', 'vendor/molajo/user/Source/');
$resource_adapter->setNamespace('Molajo\\Factories\\', 'vendor/molajo/user/Factories/');

/**
 *  APPLICATION EXTENSIONS
 */
$resource_adapter->setNamespace('Molajo\\Controller\\', 'vendor/molajo/application/Source/Controller/');
$resource_adapter->setNamespace('Molajo\\Model\\', 'vendor/molajo/application/Source/Model/');
$resource_adapter->setNamespace('Molajo\\Plugins\\', 'vendor/molajo/plugins/Source/');
$resource_adapter->setNamespace('Molajo\\Resource\\', 'vendor/molajo/application/Source/Resource/');
$resource_adapter->setNamespace('Molajo\\Factories\\', 'vendor/molajo/application/Factories/');
$resource_adapter->setNamespace('Molajo\\Resource\\', 'vendor/molajo/application/Source/Resource/');
$resource_adapter->setNamespace('Molajo\\', 'vendor/molajo/application/Source/Extensions/');

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
