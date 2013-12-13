<?php
/**
 * Namespace Prefixes and Paths
 *
 * @package    Molajo
 * @copyright  2013 Amy Stephen. All rights reserved.
 * @license    http:/www.opensource.org/licenses/mit-license.html MIT License
 */
$class_loader->setNamespace('CommonApi\\Authorisation', 'vendor/commonapi/authorisation/');
$class_loader->setNamespace('CommonApi\\Cache', 'vendor/commonapi/cache/');
$class_loader->setNamespace('CommonApi\\Controller', 'vendor/commonapi/controller/');
$class_loader->setNamespace('CommonApi\\Database', 'vendor/commonapi/database/');
$class_loader->setNamespace('CommonApi\\Email', 'vendor/commonapi/email/');
$class_loader->setNamespace('CommonApi\\Event', 'vendor/commonapi/event/');
$class_loader->setNamespace('CommonApi\\Exception', 'vendor/commonapi/exception/');
$class_loader->setNamespace('CommonApi\\Filesystem', 'vendor/commonapi/filesystem/');
$class_loader->setNamespace('CommonApi\\Http', 'vendor/commonapi/http/');
$class_loader->setNamespace('CommonApi\\IoC', 'vendor/commonapi/ioc/');
$class_loader->setNamespace('CommonApi\\Language', 'vendor/commonapi/language/');
$class_loader->setNamespace('CommonApi\\Model', 'vendor/commonapi/model/');
$class_loader->setNamespace('CommonApi\\Query', 'vendor/commonapi/query/');
$class_loader->setNamespace('CommonApi\\Render', 'vendor/commonapi/render/');
$class_loader->setNamespace('CommonApi\\Resource', 'vendor/commonapi/resource/');
$class_loader->setNamespace('CommonApi\\Route', 'vendor/commonapi/route/');
$class_loader->setNamespace('CommonApi\\User', 'vendor/commonapi/user/');

$class_loader->setNamespace('Molajo\\Application', 'vendor/molajo/framework/Source/Model/Application/');
$class_loader->setNamespace('Molajo\\Articles', 'Source/Resource/Articles/');
$class_loader->setNamespace('Molajo\\Audio', 'Source/Resource/Audio/');
$class_loader->setNamespace('Molajo\\Authorisation', 'vendor/molajo/authorisation/Source/');
$class_loader->setNamespace('Molajo\\Cache', 'vendor/molajo/cache/Source/');
$class_loader->setNamespace('Molajo\\Categories', 'Source/Resource/Categories/');
$class_loader->setNamespace('Molajo\\Comments', 'Source/Resource/Comments/');
$class_loader->setNamespace('Molajo\\Contacts', 'Source/Resource/Contacts/');
$class_loader->setNamespace('Molajo\\Controller', 'vendor/molajo/framework/Source/Controller/');
$class_loader->setNamespace('Molajo\\Controller', 'vendor/molajo/framework/Source/Resource/');
$class_loader->setNamespace('Molajo\\Database', 'vendor/molajo/database/Source/');
$class_loader->setNamespace('Molajo\\Dataobject', 'vendor/molajo/framework/Source/Model/Dataobject/');
$class_loader->setNamespace('Molajo\\Datasource', 'vendor/molajo/framework/Source/Model/Datasource/');
$class_loader->setNamespace('Molajo\\Datasource\\Articles', 'Source/Resource/Articles/');
$class_loader->setNamespace('Molajo\\Datasource\\Audio', 'Source/Resource/Audio/');
$class_loader->setNamespace('Molajo\\Datasource\\Categories', 'Source/Resource/Categories/');
$class_loader->setNamespace('Molajo\\Datasource\\Comments', 'Source/Resource/Comments/');
$class_loader->setNamespace('Molajo\\Datasource\\Contacts', 'Source/Resource/Contacts/');
$class_loader->setNamespace('Molajo\\Datasource\\Files', 'Source/Resource/Files/');
$class_loader->setNamespace('Molajo\\Datasource\\Groups', 'Source/Resource/Groups/');
$class_loader->setNamespace('Molajo\\Datasource\\Links', 'Source/Resource/Links/');
$class_loader->setNamespace('Molajo\\Datasource\\Pages', 'Source/Resource/Pages/');
$class_loader->setNamespace('Molajo\\Datasource\\Tags', 'Source/Resource/Tags/');
$class_loader->setNamespace('Molajo\\Datasource\\Video', 'Source/Resource/Video/');
$class_loader->setNamespace('Molajo\\Email', 'vendor/molajo/email/source/');
$class_loader->setNamespace('Molajo\\Event', 'vendor/molajo/event/Source/');
$class_loader->setNamespace('Molajo\\Field', 'vendor/molajo/framework/Source/Model/Field/');
$class_loader->setNamespace('Molajo\\Fieldhandler', 'vendor/molajo/fieldhandler/Source/');
$class_loader->setNamespace('Molajo\\Files', 'Source/Resource/Files/');
$class_loader->setNamespace('Molajo\\Filesystem', 'vendor/molajo/filesystem/Source/');
$class_loader->setNamespace('Molajo\\Groups', 'Source/Resource/Groups/');
$class_loader->setNamespace('Molajo\\Http', 'vendor/molajo/Http/Source/');
$class_loader->setNamespace('Molajo\\Images', 'Source/Resource/Images/');
$class_loader->setNamespace('Molajo\\Include', 'vendor/molajo/framework/Source/Model/Include/');
$class_loader->setNamespace('Molajo\\IoC', 'vendor/molajo/ioc/Source');
$class_loader->setNamespace('Molajo\\IoC\\Api', 'vendor/molajo/ioc/Source/Api');
$class_loader->setNamespace('Molajo\\Language', 'vendor/molajo/Language/Source/');
$class_loader->setNamespace('Molajo\\Links', 'Source/Resource/Links/');
//$class_loader->setNamespace('Molajo\\Log', 'vendor/molajo/Log/Source/');
$class_loader->setNamespace('Molajo\\Menuitem', 'Source/Administration/Applications/Menuitem/');
$class_loader->setNamespace('Molajo\\Menuitem', 'Source/Administration/Fields/Menuitem/');
$class_loader->setNamespace('Molajo\\Menuitem', 'Source/Administration/Languages/Menuitem/');
$class_loader->setNamespace('Molajo\\Menuitem', 'Source/Administration/Languagestrings/Menuitem/');
$class_loader->setNamespace('Molajo\\Menuitem', 'Source/Administration/Menuitems/Menuitem/');
$class_loader->setNamespace('Molajo\\Menuitem', 'Source/Administration/Pageviews/Menuitem/');
$class_loader->setNamespace('Molajo\\Menuitem', 'Source/Administration/Permissions/Menuitem/');
$class_loader->setNamespace('Molajo\\Menuitem', 'Source/Administration/Plugins/Menuitem/');
$class_loader->setNamespace('Molajo\\Menuitem', 'Source/Administration/Privatemessages/Menuitem/');
$class_loader->setNamespace('Molajo\\Menuitem', 'Source/Administration/Resources/Menuitem/');
$class_loader->setNamespace('Molajo\\Menuitem', 'Source/Administration/Services/Menuitem/');
$class_loader->setNamespace('Molajo\\Menuitem', 'Source/Administration/Sites/Menuitem/');
$class_loader->setNamespace('Molajo\\Menuitem', 'Source/Administration/Templateviews/Menuitem/');
$class_loader->setNamespace('Molajo\\Menuitem', 'Source/Administration/Themes/Menuitem/');
$class_loader->setNamespace('Molajo\\Menuitem', 'Source/Administration/Users/Menuitem/');
$class_loader->setNamespace('Molajo\\Menuitem', 'Source/Administration/Wrapviews/Menuitem/');
$class_loader->setNamespace('Molajo\\Menuitem', 'Source/Menuitem/');
$class_loader->setNamespace('Molajo\\Menuitem', 'Source/Resource/Articles/Menuitem/');
$class_loader->setNamespace('Molajo\\Menuitem', 'Source/Resource/Audio/Menuitem/');
$class_loader->setNamespace('Molajo\\Menuitem', 'Source/Resource/Categories/Menuitem/');
$class_loader->setNamespace('Molajo\\Menuitem', 'Source/Resource/Comments/Menuitem/');
$class_loader->setNamespace('Molajo\\Menuitem', 'Source/Resource/Contacts/Menuitem/');
$class_loader->setNamespace('Molajo\\Menuitem', 'Source/Resource/Files/Menuitem/');
$class_loader->setNamespace('Molajo\\Menuitem', 'Source/Resource/Groups/Menuitem/');
$class_loader->setNamespace('Molajo\\Menuitem', 'Source/Resource/Images/Menuitem/');
$class_loader->setNamespace('Molajo\\Menuitem', 'Source/Resource/Links/Menuitem/');
$class_loader->setNamespace('Molajo\\Menuitem', 'Source/Resource/Pages/Menuitem/');
$class_loader->setNamespace('Molajo\\Menuitem', 'Source/Resource/Tags/Menuitem/');
$class_loader->setNamespace('Molajo\\Menuitem', 'Source/Resource/Video/Menuitem/');
$class_loader->setNamespace('Molajo\\Menuitem', 'vendor/molajo/framework/Source/Model/Menuitem/');
$class_loader->setNamespace('Molajo\\Model', 'vendor/molajo/framework/Source/Model/');
$class_loader->setNamespace('Molajo\\Model\\Datalist', 'vendor/molajo/framework/Source/Model/Datalist/');
$class_loader->setNamespace('Molajo\\Pages', 'Source/Resource/Pages/');
$class_loader->setNamespace('Molajo\\Plugin', 'vendor/molajo/framework/Source/Plugin/');
$class_loader->setNamespace('Molajo\\Plugin', 'Source/Plugin/');
$class_loader->setNamespace('Molajo\\Plugin', 'Source/Theme/Foundation/Plugin/');
$class_loader->setNamespace('Molajo\\Render', 'vendor/molajo/Render/Source/');
$class_loader->setNamespace('Molajo\\Resource', 'Source/Resource/');
$class_loader->setNamespace('Molajo\\Resource', 'vendor/molajo/resource/Source/');
$class_loader->setNamespace('Molajo\\Resource', 'vendor/molajo/framework/Source/Resource/');
$class_loader->setNamespace('Molajo\\Route', 'vendor/molajo/route/Source/');
$class_loader->setNamespace('Molajo\\Service', 'vendor/molajo/authorisation/Service/');
$class_loader->setNamespace('Molajo\\Service', 'vendor/molajo/cache/Service/');
$class_loader->setNamespace('Molajo\\Service', 'vendor/molajo/database/Service/');
$class_loader->setNamespace('Molajo\\Service', 'vendor/molajo/email/Service/');
$class_loader->setNamespace('Molajo\\Service', 'vendor/molajo/event/Service/');
$class_loader->setNamespace('Molajo\\Service', 'vendor/molajo/fieldhandler/Service/');
$class_loader->setNamespace('Molajo\\Service', 'vendor/molajo/filesystem/Service/');
$class_loader->setNamespace('Molajo\\Service', 'vendor/molajo/framework/Service/');
$class_loader->setNamespace('Molajo\\Service', 'vendor/molajo/Http/Service/');
$class_loader->setNamespace('Molajo\\Service', 'vendor/molajo/Language/Service/');
//$class_loader->setNamespace('Molajo\\Service', 'vendor/molajo/Log/Service');
$class_loader->setNamespace('Molajo\\Service', 'vendor/molajo/render/Service/');
$class_loader->setNamespace('Molajo\\Service', 'vendor/molajo/resource/Service/');
$class_loader->setNamespace('Molajo\\Service', 'vendor/molajo/route/Service/');
$class_loader->setNamespace('Molajo\\Service', 'vendor/molajo/user/Service/');
$class_loader->setNamespace('Molajo\\Site2', 'Sites/');
$class_loader->setNamespace('Molajo\\Sites', 'Sites/');
$class_loader->setNamespace('Molajo\\Tags', 'Source/Resource/Tags/');
$class_loader->setNamespace('Molajo\\Theme', 'Source/Theme/');
$class_loader->setNamespace('Molajo\\User', 'vendor/molajo/user/Source/');
$class_loader->setNamespace('Molajo\\Video', 'Source/Resource/Video/');
$class_loader->setNamespace('Molajo\\View\\Page', 'Source/Theme/Foundation/View/Page/');
$class_loader->setNamespace('Molajo\\View\\Page', 'Source/Theme/System/View/Page/');
$class_loader->setNamespace('Molajo\\View\\Page', 'Source/View/Page/');
$class_loader->setNamespace('Molajo\\View\\Template', 'Source/Theme/Foundation/View/Template/');
$class_loader->setNamespace('Molajo\\View\\Template', 'Source/Theme/System/View/Template/');
$class_loader->setNamespace('Molajo\\View\\Template', 'Source/View/Template/');
$class_loader->setNamespace('Molajo\\View\\Wrap', 'Source/Theme/Foundation/View/Wrap/');
$class_loader->setNamespace('Molajo\\View\\Wrap', 'Source/Theme/System/View/Wrap/');
$class_loader->setNamespace('Molajo\\View\\Wrap', 'Source/View/Wrap/');
