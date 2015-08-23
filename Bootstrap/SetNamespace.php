<?php
/**
 * Set Namespace Prefixes and Paths
 *
 * @package    Molajo
 * @copyright  2014-2015 Amy Stephen. All rights reserved.
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 */

/**
 *  FRAMEWORK PACKAGES
 */
$resource_adapter->setNamespace('Molajo\\Authorisation\\', 'vendor/molajo/authorisation/Source/');
$resource_adapter->setNamespace('Molajo\\Factories\\', 'vendor/molajo/authorisation/Factories/');

$resource_adapter->setNamespace('Molajo\\Cache\\', 'vendor/molajo/cache/Source/');
$resource_adapter->setNamespace('Molajo\\Factories\\', 'vendor/molajo/cache/Factories/');

$resource_adapter->setNamespace('Molajo\\Email\\', 'vendor/molajo/email/source/');
$resource_adapter->setNamespace('Molajo\\Factories\\Email\\', 'vendor/molajo/email/Factories/Email/');

$resource_adapter->setNamespace('Molajo\\Event\\', 'vendor/molajo/event/Source/');
$resource_adapter->setNamespace('Molajo\\Factories\\', 'vendor/molajo/event/Factories/');

$resource_adapter->setNamespace('Molajo\\Fieldhandler\\', 'vendor/molajo/fieldhandler/Source/');
$resource_adapter->setNamespace('Molajo\\Factories\\', 'vendor/molajo/fieldhandler/Factories/');

$resource_adapter->setNamespace('Molajo\\Http\\', 'vendor/molajo/http/Source/');
$resource_adapter->setNamespace('Molajo\\Factories\\', 'vendor/molajo/http/Factories/');

$resource_adapter->setNamespace('Molajo\\IoC\\', 'vendor/molajo/ioc/Source/');

$resource_adapter->setNamespace('Molajo\\Language\\', 'vendor/molajo/Language/Source/');
$resource_adapter->setNamespace('Molajo\\Factories\\', 'vendor/molajo/language/Factories/');

$resource_adapter->setNamespace('Molajo\\Log\\', 'vendor/molajo/Log/Source/');
$resource_adapter->setNamespace('Molajo\\Handler\\', 'vendor/molajo/Log/Handler/');
$resource_adapter->setNamespace('Psr\\Log\\', 'vendor/psr/log/Psr/Log/');
$resource_adapter->setNamespace('Molajo\\Factories\\', 'vendor/molajo/Log/Factories/');

$resource_adapter->setNamespace('Molajito\\', 'vendor/molajo/molajito/Source/');
$resource_adapter->setNamespace('Molajo\\Factories\\', 'vendor/molajo/molajito/Factories/');

$resource_adapter->setNamespace('Molajo\\', 'vendor/molajo/pagination/Source/');

$resource_adapter->setNamespace('Molajo\\Plugins\\', 'vendor/molajo/plugins/Source/');

$resource_adapter->setNamespace('Molajo\\Query\\', 'vendor/molajo/query/Source/Query/');
$resource_adapter->setNamespace('Molajo\\Query\\', 'vendor/molajo/query/Source/Traits/');
$resource_adapter->setNamespace('Molajo\\Controller\\', 'vendor/molajo/query/Source/Controller/');
$resource_adapter->setNamespace('Molajo\\Data\\', 'vendor/molajo/query/Source/Data/');
$resource_adapter->setNamespace('Molajo\\Model\\', 'vendor/molajo/query/Source/Model/');
$resource_adapter->setNamespace('Molajo\\Resource\\', 'vendor/molajo/query/Source/Resource/');
$resource_adapter->setNamespace('Molajo\\Factories\\', 'vendor/molajo/query/Factories/');

$resource_adapter->setNamespace('Molajo\\Render\\', 'vendor/molajo/render/Source/');
$resource_adapter->setNamespace('Molajo\\Factories\\', 'vendor/molajo/render/Factories/');

$resource_adapter->setNamespace('Molajo\\Resource\\', 'vendor/molajo/resource/Source/');
$resource_adapter->setNamespace('Molajo\\Factories\\', 'vendor/molajo/resource/Factories/');

$resource_adapter->setNamespace('Molajo\\Route\\', 'vendor/molajo/route/Source/');
$resource_adapter->setNamespace('Molajo\\Factories\\', 'vendor/molajo/route/Factories/');

$resource_adapter->setNamespace('Molajo\\User\\', 'vendor/molajo/user/Source/');
$resource_adapter->setNamespace('Molajo\\Factories\\', 'vendor/molajo/user/Factories/');

/**
 *  APPLICATION
 */
$resource_adapter->setNamespace('Molajo\\Controller\\', 'vendor/molajo/application/Source/Controller/');
$resource_adapter->setNamespace('Molajo\\System\\', 'vendor/molajo/application/Source/Extensions/');
$resource_adapter->setNamespace('Molajo\\Model\\', 'vendor/molajo/application/Source/Model/');
$resource_adapter->setNamespace('Molajo\\Resource\\', 'vendor/molajo/application/Source/Resource/');
$resource_adapter->setNamespace('Molajo\\Resources\\', 'vendor/molajo/application/Source/Resources/');
$resource_adapter->setNamespace('Molajo\\Factories\\', 'vendor/molajo/application/Factories/');

/**
 *  COMMON API
 */
$resource_adapter->setNamespace('CommonApi\\Application\\', 'vendor/commonapi/application/');
$resource_adapter->setNamespace('CommonApi\\Authorisation\\', 'vendor/commonapi/authorisation/');
$resource_adapter->setNamespace('CommonApi\\Cache\\', 'vendor/commonapi/cache/');
$resource_adapter->setNamespace('CommonApi\\Email\\', 'vendor/commonapi/email/');
$resource_adapter->setNamespace('CommonApi\\Event\\', 'vendor/commonapi/event/');
$resource_adapter->setNamespace('CommonApi\\Exception\\', 'vendor/commonapi/exception/');
$resource_adapter->setNamespace('CommonApi\\Fieldhandler\\', 'vendor/commonapi/fieldhandler/');
$resource_adapter->setNamespace('CommonApi\\Filesystem\\', 'vendor/commonapi/filesystem/');
$resource_adapter->setNamespace('CommonApi\\Http\\', 'vendor/commonapi/http/');
$resource_adapter->setNamespace('CommonApi\\IoC\\', 'vendor/commonapi/ioc/');
$resource_adapter->setNamespace('CommonApi\\Language\\', 'vendor/commonapi/language/');
$resource_adapter->setNamespace('CommonApi\\Query\\', 'vendor/commonapi/query/');
$resource_adapter->setNamespace('CommonApi\\Render\\', 'vendor/commonapi/render/');
$resource_adapter->setNamespace('CommonApi\\Resource\\', 'vendor/commonapi/resource/');
$resource_adapter->setNamespace('CommonApi\\Route\\', 'vendor/commonapi/route/');
$resource_adapter->setNamespace('CommonApi\\User\\', 'vendor/commonapi/user/');

/**
 *  DISTRIBUTION LAYER
 *  - change to site/cache, site/log, etc.
 *  - plugins should each be installable
 *  - resources should each be installable
 */
$resource_adapter->setNamespace('Molajo\\Sites\\', 'Sites/');
$resource_adapter->setNamespace('Molajo\\Plugins\\', 'Source/Plugins/');
/**$resource_adapter->setNamespace('Molajo\\Plugins\\Activity\\', 'Source/Plugins/Activity/');

$resource_adapter->setNamespace('Molajo\\Plugins\\Alias\\', 'Source/Plugins/Alias/');
$resource_adapter->setNamespace('Molajo\\Plugins\\Application\\', 'Source/Plugins/Application/');
$resource_adapter->setNamespace('Molajo\\Plugins\\Article\\', 'Source/Plugins/Article/');
$resource_adapter->setNamespace('Molajo\\Plugins\\Author\\', 'Source/Plugins/Author/');
$resource_adapter->setNamespace('Molajo\\Plugins\\Authorisation\\', 'Source/Plugins/Authorisation/');
$resource_adapter->setNamespace('Molajo\\Plugins\\Blockquote\\', 'Source/Plugins/Blockquote/');
$resource_adapter->setNamespace('Molajo\\Plugins\\Breadcrumbs\\', 'Source/Plugins/Breadcrumbs/');
$resource_adapter->setNamespace('Molajo\\Plugins\\Catalog\\', 'Source/Plugins/Catalog/');
$resource_adapter->setNamespace('Molajo\\Plugins\\Checkin\\', 'Source/Plugins/Checkin/');
$resource_adapter->setNamespace('Molajo\\Plugins\\Checkout\\', 'Source/Plugins/Checkout/');
$resource_adapter->setNamespace('Molajo\\Plugins\\Comments\\', 'Source/Plugins/Comments/');
$resource_adapter->setNamespace('Molajo\\Plugins\\Copyright\\', 'Source/Plugins/Copyright/');
$resource_adapter->setNamespace('Molajo\\Plugins\\Csrftoken\\', 'Source/Plugins/Csrftoken/');
$resource_adapter->setNamespace('Molajo\\Plugins\\Customfields\\', 'Source/Plugins/Customfields/');
$resource_adapter->setNamespace('Molajo\\Plugins\\Dateformats\\', 'Source/Plugins/Dateformats/');
$resource_adapter->setNamespace('Molajo\\Plugins\\Defer\\', 'Source/Plugins/Defer/');
$resource_adapter->setNamespace('Molajo\\Plugins\\Email\\', 'Source/Plugins/Email/');
$resource_adapter->setNamespace('Molajo\\Plugins\\Events\\', 'Source/Plugins/Events/');
$resource_adapter->setNamespace('Molajo\\Plugins\\Extensions\\', 'Source/Plugins/Extensions/');
$resource_adapter->setNamespace('Molajo\\Plugins\\Feed\\', 'Source/Plugins/Feed/');
$resource_adapter->setNamespace('Molajo\\Plugins\\Fields\\', 'Source/Plugins/Fields/');
$resource_adapter->setNamespace('Molajo\\Plugins\\Footer\\', 'Source/Plugins/Footer/');
$resource_adapter->setNamespace('Molajo\\Plugins\\Fullname\\', 'Source/Plugins/Fullname/');
$resource_adapter->setNamespace('Molajo\\Plugins\\Gravatar\\', 'Source/Plugins/Gravatar/');
$resource_adapter->setNamespace('Molajo\\Plugins\\Head\\', 'Source/Plugins/Head/');
$resource_adapter->setNamespace('Molajo\\Plugins\\Header\\', 'Source/Plugins/Header/');
$resource_adapter->setNamespace('Molajo\\Plugins\\Image\\', 'Source/Plugins/Image/');
$resource_adapter->setNamespace('Molajo\\Plugins\\Item\\', 'Source/Plugins/Item/');
$resource_adapter->setNamespace('Molajo\\Plugins\\Linebreaks\\', 'Source/Plugins/Linebreaks/');
$resource_adapter->setNamespace('Molajo\\Plugins\\Links\\', 'Source/Plugins/Links/');
$resource_adapter->setNamespace('Molajo\\Plugins\\Login\\', 'Source/Plugins/Login/');
$resource_adapter->setNamespace('Molajo\\Plugins\\Logout\\', 'Source/Plugins/Logout/');
$resource_adapter->setNamespace('Molajo\\Plugins\\Menuitems\\', 'Source/Plugins/Menuitems/');
$resource_adapter->setNamespace('Molajo\\Plugins\\Messages\\', 'Source/Plugins/Messages/');
$resource_adapter->setNamespace('Molajo\\Plugins\\Navbar\\', 'Source/Plugins/Navbar/');
$resource_adapter->setNamespace('Molajo\\Plugins\\Ordering\\', 'Source/Plugins/Ordering/');
$resource_adapter->setNamespace('Molajo\\Plugins\\Page\\', 'Source/Plugins/Page/');
$resource_adapter->setNamespace('Molajo\\Plugins\\Pagetypeapplication\\', 'Source/Plugins/Pagetypeapplication/');
$resource_adapter->setNamespace('Molajo\\Plugins\\Pagetypeconfiguration\\', 'Source/Plugins/Pagetypeconfiguration/');
$resource_adapter->setNamespace('Molajo\\Plugins\\Pagetypedashboard\\', 'Source/Plugins/Pagetypedashboard/');
$resource_adapter->setNamespace('Molajo\\Plugins\\Pagetypeedit\\', 'Source/Plugins/Pagetypeedit/');
$resource_adapter->setNamespace('Molajo\\Plugins\\Pagetypegrid\\', 'Source/Plugins/Pagetypegrid/');
$resource_adapter->setNamespace('Molajo\\Plugins\\Pagetypeitem\\', 'Source/Plugins/Pagetypeitem/');
$resource_adapter->setNamespace('Molajo\\Plugins\\Pagetypelist\\', 'Source/Plugins/Pagetypelist/');
$resource_adapter->setNamespace('Molajo\\Plugins\\Pagetypenew\\', 'Source/Plugins/Pagetypenew/');
$resource_adapter->setNamespace('Molajo\\Plugins\\Pagination\\', 'Source/Plugins/Pagination/');
$resource_adapter->setNamespace('Molajo\\Plugins\\Paging\\', 'Source/Plugins/Paging/');
$resource_adapter->setNamespace('Molajo\\Plugins\\Parse\\', 'Source/Plugins/Parse/');
$resource_adapter->setNamespace('Molajo\\Plugins\\Position\\', 'Source/Plugins/Position/');
$resource_adapter->setNamespace('Molajo\\Plugins\\Queryauthorisation\\', 'Source/Plugins/Queryauthorisation/');
$resource_adapter->setNamespace('Molajo\\Plugins\\Readmore\\', 'Source/Plugins/Readmore/');
$resource_adapter->setNamespace('Molajo\\Plugins\\Sites\\', 'Source/Plugins/Sites/');
$resource_adapter->setNamespace('Molajo\\Plugins\\Smilies\\', 'Source/Plugins/Smilies/');
$resource_adapter->setNamespace('Molajo\\Plugins\\Snippet\\', 'Source/Plugins/Snippet/');
$resource_adapter->setNamespace('Molajo\\Plugins\\Source\\', 'Source/Plugins/Source/');
$resource_adapter->setNamespace('Molajo\\Plugins\\Spamprotection\\', 'Source/Plugins/Spamprotection/');
$resource_adapter->setNamespace('Molajo\\Plugins\\Status\\', 'Source/Plugins/Status/');
$resource_adapter->setNamespace('Molajo\\Plugins\\Template\\', 'Source/Plugins/Template/');
$resource_adapter->setNamespace('Molajo\\Plugins\\Theme\\', 'Source/Plugins/Theme/');
$resource_adapter->setNamespace('Molajo\\Plugins\\Toolbar\\', 'Source/Plugins/Toolbar/');
$resource_adapter->setNamespace('Molajo\\Plugins\\Wrap\\', 'Source/Plugins/Wrap/');
*/
$resource_adapter->setNamespace('Molajo\\Resources\\', 'Source/Resources/');
$resource_adapter->setNamespace('Molajo\\Themes\\', 'Source/Themes/');
