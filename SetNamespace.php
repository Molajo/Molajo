<?php
/**
 * Core Namespace Statements
 *
 * @package    Molajo
 * @copyright  2013 Amy Stephen. All rights reserved.
 * @license    http:/www.opensource.org/licenses/mit-license.html MIT License
 */

$class_loader->setNamespace('CommonApi\\Authorisation', 'Vendor/commonapi/authorisation/');
$class_loader->setNamespace('CommonApi\\IoC', 'Vendor/commonapi/ioc/');

$class_loader->setNamespace('CommonApi\\Cache', 'Vendor/commonapi/cache/');
$class_loader->setNamespace('CommonApi\\Controller', 'Vendor/commonapi/controller/');
$class_loader->setNamespace('CommonApi\\Database', 'Vendor/commonapi/database/');
$class_loader->setNamespace('CommonApi\\Email', 'Vendor/commonapi/email/');
$class_loader->setNamespace('CommonApi\\Event', 'Vendor/commonapi/event/');
$class_loader->setNamespace('CommonApi\\Filesystem', 'Vendor/commonapi/filesystem/');
$class_loader->setNamespace('CommonApi\\Http', 'Vendor/commonapi/http/');
$class_loader->setNamespace('CommonApi\\Language', 'Vendor/commonapi/language/');
$class_loader->setNamespace('CommonApi\\Model', 'Vendor/commonapi/model/');
$class_loader->setNamespace('CommonApi\\Route', 'Vendor/commonapi/route/');
$class_loader->setNamespace('CommonApi\\Render', 'Vendor/commonapi/render/');
$class_loader->setNamespace('CommonApi\\Resource', 'Vendor/commonapi/resource/');
$class_loader->setNamespace('CommonApi\\User', 'Vendor/commonapi/user/');

$class_loader->setNamespace('CommonApi\\Exception', 'Vendor/commonapi/exception/');

$class_loader->setNamespace('Molajo\\Administration', 'Application/Administration/');
$class_loader->setNamespace('Molajo\\Authorisation', 'Vendor/Molajo/Authorisation/');
$class_loader->setNamespace('Molajo\\Application', 'Application/Model/Application/');
$class_loader->setNamespace('Molajo\\Cache', 'Vendor/Molajo/Cache/');
$class_loader->setNamespace('Molajo\\Controller', 'Application/Controller/');
$class_loader->setNamespace('Molajo\\Controller\\NumberToText', 'Vendor/Molajo/NumberToText/');
$class_loader->setNamespace('Molajo\\Database', 'Vendor/Molajo/Database/');
$class_loader->setNamespace('Molajo\\Email', 'Vendor/Molajo/Email/');
$class_loader->setNamespace('Molajo\\Event', 'Vendor/Molajo/Event/');
$class_loader->setNamespace('Molajo\\Filesystem', 'Vendor/Molajo/Filesystem/');
$class_loader->setNamespace('Molajo\\Http', 'Vendor/Molajo/Http/');
$class_loader->setNamespace('Molajo\\IoC', 'Vendor/Molajo/IoC/');
$class_loader->setNamespace('Molajo\\Language', 'Vendor/Molajo/Language/');

$class_loader->setNamespace('Molajo\\Model', 'Application/Model/');
$class_loader->setNamespace('Molajo\\Model\\Datalist', 'Application/Model/Datalist/');
$class_loader->setNamespace('Molajo\\Dataobject', 'Application/Model/Dataobject/');
$class_loader->setNamespace('Molajo\\Datasource', 'Application/Model/Datasource/');
$class_loader->setNamespace('Molajo\\Datasource\\Articles', 'Application/Extension/Resource/Articles/');
$class_loader->setNamespace('Molajo\\Datasource\\Audio', 'Application/Extension/Resource/Audio/');
$class_loader->setNamespace('Molajo\\Datasource\\Categories', 'Application/Extension/Resource/Categories/');
$class_loader->setNamespace('Molajo\\Datasource\\Comments', 'Application/Extension/Resource/Comments/');
$class_loader->setNamespace('Molajo\\Datasource\\Contacts', 'Application/Extension/Resource/Contacts/');
$class_loader->setNamespace('Molajo\\Datasource\\Files', 'Application/Extension/Resource/Files/');
$class_loader->setNamespace('Molajo\\Datasource\\Groups', 'Application/Extension/Resource/Groups/');
$class_loader->setNamespace('Molajo\\Datasource\\Links', 'Application/Extension/Resource/Links/');
$class_loader->setNamespace('Molajo\\Datasource\\Pages', 'Application/Extension/Resource/Pages/');
$class_loader->setNamespace('Molajo\\Datasource\\Tags', 'Application/Extension/Resource/Tags/');
$class_loader->setNamespace('Molajo\\Datasource\\Video', 'Application/Extension/Resource/Video/');
$class_loader->setNamespace('Molajo\\Field', 'Application/Model/Field/');
$class_loader->setNamespace('Molajo\\Fieldhandler', 'Vendor/Molajo/Fieldhandler/');
$class_loader->setNamespace('Molajo\\Include', 'Application/Model/Include/');
$class_loader->setNamespace('Molajo\\Menuitem', 'Application/Model/Menuitem/');
$class_loader->setNamespace('Molajo\\Menuitem', 'Application/Extension/Menuitem/');
$class_loader->setNamespace('Molajo\\Menuitem', 'Application/Extension/Resource/Articles/Menuitem/');
$class_loader->setNamespace('Molajo\\Menuitem', 'Application/Extension/Resource/Audio/Menuitem/');
$class_loader->setNamespace('Molajo\\Menuitem', 'Application/Extension/Resource/Categories/Menuitem/');
$class_loader->setNamespace('Molajo\\Menuitem', 'Application/Extension/Resource/Comments/Menuitem/');
$class_loader->setNamespace('Molajo\\Menuitem', 'Application/Extension/Resource/Contacts/Menuitem/');
$class_loader->setNamespace('Molajo\\Menuitem', 'Application/Extension/Resource/Files/Menuitem/');
$class_loader->setNamespace('Molajo\\Menuitem', 'Application/Extension/Resource/Groups/Menuitem/');
$class_loader->setNamespace('Molajo\\Menuitem', 'Application/Extension/Resource/Images/Menuitem/');
$class_loader->setNamespace('Molajo\\Menuitem', 'Application/Extension/Resource/Links/Menuitem/');
$class_loader->setNamespace('Molajo\\Menuitem', 'Application/Extension/Resource/Pages/Menuitem/');
$class_loader->setNamespace('Molajo\\Menuitem', 'Application/Extension/Resource/Tags/Menuitem/');
$class_loader->setNamespace('Molajo\\Menuitem', 'Application/Extension/Resource/Video/Menuitem/');
$class_loader->setNamespace('Molajo\\Menuitem', 'Application/Administration/Applications/Menuitem/');
$class_loader->setNamespace('Molajo\\Menuitem', 'Application/Administration/Fields/Menuitem/');
$class_loader->setNamespace('Molajo\\Menuitem', 'Application/Administration/Languages/Menuitem/');
$class_loader->setNamespace('Molajo\\Menuitem', 'Application/Administration/Languagestrings/Menuitem/');
$class_loader->setNamespace('Molajo\\Menuitem', 'Application/Administration/Menuitems/Menuitem/');
$class_loader->setNamespace('Molajo\\Menuitem', 'Application/Administration/Pageviews/Menuitem/');
$class_loader->setNamespace('Molajo\\Menuitem', 'Application/Administration/Permissions/Menuitem/');
$class_loader->setNamespace('Molajo\\Menuitem', 'Application/Administration/Plugins/Menuitem/');
$class_loader->setNamespace('Molajo\\Menuitem', 'Application/Administration/Privatemessages/Menuitem/');
$class_loader->setNamespace('Molajo\\Menuitem', 'Application/Administration/Resource/Menuitem/');
$class_loader->setNamespace('Molajo\\Menuitem', 'Application/Administration/Services/Menuitem/');
$class_loader->setNamespace('Molajo\\Menuitem', 'Application/Administration/Sites/Menuitem/');
$class_loader->setNamespace('Molajo\\Menuitem', 'Application/Administration/Templateviews/Menuitem/');
$class_loader->setNamespace('Molajo\\Menuitem', 'Application/Administration/Themes/Menuitem/');
$class_loader->setNamespace('Molajo\\Menuitem', 'Application/Administration/Users/Menuitem/');
$class_loader->setNamespace('Molajo\\Menuitem', 'Application/Administration/Wrapviews/Menuitem/');

$class_loader->setNamespace('Molajo\\Plugin', 'Application/Extension/Plugin/');
$class_loader->setNamespace('Molajo\\Plugin', 'Application/Extension/Theme/Foundation/Plugin/');
$class_loader->setNamespace('Molajo\\Resource', 'Application/Extension/Resource/');
$class_loader->setNamespace('Molajo\\Resource', 'Application/Resource/');
$class_loader->setNamespace('Molajo\\Resource', 'Vendor/Molajo/Resource/');
$class_loader->setNamespace('Molajo\\Route', 'Vendor/Molajo/Route/');
$class_loader->setNamespace('Molajo\\Render', 'Vendor/Molajo/Render/');
$class_loader->setNamespace('Molajo\\Service', 'Application/Service/');
$class_loader->setNamespace('Molajo\\Service', 'Vendor/Molajo/User/Service/');
$class_loader->setNamespace('Molajo\\Sites', 'Sites/');
$class_loader->setNamespace('Molajo\\Site2', 'Sites/');
$class_loader->setNamespace('Molajo\\User', 'Vendor/Molajo/User/');
$class_loader->setNamespace('Molajo\\View\\Page', 'Application/Extension/View/Page/');
$class_loader->setNamespace('Molajo\\View\\Page', 'Application/Extension/Theme/System/View/Page/');
$class_loader->setNamespace('Molajo\\View\\Page', 'Application/Extension/Theme/Foundation/View/Page/');
$class_loader->setNamespace('Molajo\\View\\Template', 'Application/Extension/View/Template/');
$class_loader->setNamespace('Molajo\\View\\Template', 'Application/Extension/Theme/System/View/Template/');
$class_loader->setNamespace('Molajo\\View\\Template', 'Application/Extension/Theme/Foundation/View/Template/');
$class_loader->setNamespace('Molajo\\View\\Wrap', 'Application/Extension/View/Wrap/');
$class_loader->setNamespace('Molajo\\View\\Wrap', 'Application/Extension/Theme/System/View/Wrap/');
$class_loader->setNamespace('Molajo\\View\\Wrap', 'Application/Extension/Theme/Foundation/View/Wrap/');
$class_loader->setNamespace('Molajo\\Theme', 'Application/Extension/Theme/');

$class_loader->setNamespace('Molajo\\Articles', 'Application/Extension/Resource/Articles/');
$class_loader->setNamespace('Molajo\\Audio', 'Application/Extension/Resource/Audio/');
$class_loader->setNamespace('Molajo\\Categories', 'Application/Extension/Resource/Categories/');
$class_loader->setNamespace('Molajo\\Comments', 'Application/Extension/Resource/Comments/');
$class_loader->setNamespace('Molajo\\Contacts', 'Application/Extension/Resource/Contacts/');
$class_loader->setNamespace('Molajo\\Files', 'Application/Extension/Resource/Files/');
$class_loader->setNamespace('Molajo\\Groups', 'Application/Extension/Resource/Groups/');
$class_loader->setNamespace('Molajo\\Images', 'Application/Extension/Resource/Images/');
$class_loader->setNamespace('Molajo\\Links', 'Application/Extension/Resource/Links/');
$class_loader->setNamespace('Molajo\\Pages', 'Application/Extension/Resource/Pages/');
$class_loader->setNamespace('Molajo\\Tags', 'Application/Extension/Resource/Tags/');
$class_loader->setNamespace('Molajo\\Video', 'Application/Extension/Resource/Video/');
