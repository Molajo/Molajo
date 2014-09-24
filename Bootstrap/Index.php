<?php
/**
 * Bootstrap Application
 *
 * @package    Molajo
 * @copyright  2014 Amy Stephen. All rights reserved.
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 */

$base_path = substr(__DIR__, 0, strlen(__DIR__) - strlen('/Bootstrap'));
ini_set('display_errors', 'On');
ini_set('error_reporting', E_ALL);
if (function_exists('date_default_timezone_set') && function_exists('date_default_timezone_get')) {
    date_default_timezone_set(@date_default_timezone_get());
}

// delete below
/** These have changed (?) since Composer made it's map? */
require_once $base_path . '/vendor/commonapi/resource/MapInterface.php';
require_once $base_path . '/vendor/commonapi/controller/ErrorHandlingInterface.php';
require_once $base_path . '/vendor/commonapi/controller/ControllerInterface.php';
require_once $base_path . '/vendor/commonapi/controller/ReadControllerInterface.php';
require_once $base_path . '/vendor/commonapi/query/QueryInterface.php';
require_once $base_path . '/vendor/commonapi/query/ModelRegistryInterface.php';
require_once $base_path . '/vendor/commonapi/query/QueryBuilderInterface.php';
require_once $base_path . '/vendor/commonapi/model/ModelInterface.php';
require_once $base_path . '/vendor/commonapi/model/ReadModelInterface.php';
require_once $base_path . '/vendor/commonapi/event/AuthenticateInterface.php';
require_once $base_path . '/vendor/commonapi/event/CreateInterface.php';
require_once $base_path . '/vendor/commonapi/event/DisplayInterface.php';
require_once $base_path . '/vendor/commonapi/event/ReadInterface.php';
require_once $base_path . '/vendor/commonapi/event/UpdateInterface.php';
require_once $base_path . '/vendor/commonapi/event/DeleteInterface.php';
require_once $base_path . '/vendor/commonapi/event/SystemInterface.php';

include_once $base_path . '/vendor/molajo/query/Traits/QueryTrait.php';
include_once $base_path . '/vendor/molajo/query/Traits/ModelRegistryTrait.php';
include_once $base_path . '/vendor/molajo/log/Handler/Error.php';
include_once $base_path . '/vendor/molajo/log/Handler/Exception.php';

require_once $base_path . '/vendor/molajo/resource/Source/ResourceMap/Base.php';
require_once $base_path . '/vendor/molajo/resource/Source/ResourceMap/Folders.php';
require_once $base_path . '/vendor/molajo/resource/Source/ResourceMap/Prefixes.php';
require_once $base_path . '/vendor/molajo/resource/Source/ResourceMap.php';

require_once $base_path . '/vendor/molajo/resource/Source/ClassMap/Base.php';
require_once $base_path . '/vendor/molajo/resource/Source/ClassMap/Events.php';
require_once $base_path . '/vendor/molajo/resource/Source/ClassMap/Aggregate.php';
require_once $base_path . '/vendor/molajo/resource/Source/ClassMap/Items.php';

require_once $base_path . '/vendor/molajo/plugins/Source/AbstractPlugin.php';
require_once $base_path . '/vendor/molajo/plugins/Source/AbstractFieldsPlugin.php';
require_once $base_path . '/vendor/molajo/plugins/Source/AuthenticateEventPlugin.php';
require_once $base_path . '/vendor/molajo/plugins/Source/CreateEventPlugin.php';
require_once $base_path . '/vendor/molajo/plugins/Source/DisplayEventPlugin.php';
require_once $base_path . '/vendor/molajo/plugins/Source/ReadEventPlugin.php';
require_once $base_path . '/vendor/molajo/plugins/Source/SystemEventPlugin.php';
require_once $base_path . '/vendor/molajo/plugins/Source/UpdateEventPlugin.php';
require_once $base_path . '/vendor/molajo/plugins/Source/Alias/AliasPlugin.php';
require_once $base_path . '/vendor/molajo/plugins/Source/Author/AuthorPlugin.php';
require_once $base_path . '/vendor/molajo/plugins/Source/Blockquote/BlockquotePlugin.php';
require_once $base_path . '/vendor/molajo/plugins/Source/Application/ApplicationBreadcrumbs.php';
require_once $base_path . '/vendor/molajo/plugins/Source/Application/ApplicationMenu.php';
require_once $base_path . '/vendor/molajo/plugins/Source/Application/ApplicationTitle.php';
require_once $base_path . '/vendor/molajo/plugins/Source/Application/ApplicationMetadata.php';
require_once $base_path . '/vendor/molajo/plugins/Source/Application/ApplicationPlugin.php';
require_once $base_path . '/vendor/molajo/plugins/Source/Comments/ProcessComments.php';
require_once $base_path . '/vendor/molajo/plugins/Source/Comments/Comments.php';
require_once $base_path . '/vendor/molajo/plugins/Source/Comments/CommentsHeading.php';
require_once $base_path . '/vendor/molajo/plugins/Source/Comments/CommentsPlugin.php';
require_once $base_path . '/vendor/molajo/plugins/Source/Copyright/CopyrightPlugin.php';
require_once $base_path . '/vendor/molajo/plugins/Source/Csrftoken/CsrftokenPlugin.php';
require_once $base_path . '/vendor/molajo/plugins/Source/Customfields/ProcessCustomFields.php';
require_once $base_path . '/vendor/molajo/plugins/Source/Customfields/CustomfieldGroupValue.php';
require_once $base_path . '/vendor/molajo/plugins/Source/Customfields/CustomfieldsPlugin.php';
require_once $base_path . '/vendor/molajo/plugins/Source/Dateformats/DateformatsPlugin.php';
require_once $base_path . '/vendor/molajo/plugins/Source/Email/EmailPlugin.php';
require_once $base_path . '/vendor/molajo/plugins/Source/Events/EventsPlugin.php';
require_once $base_path . '/vendor/molajo/plugins/Source/Fields/Base.php';
require_once $base_path . '/vendor/molajo/plugins/Source/Fields/StandardFields.php';
require_once $base_path . '/vendor/molajo/plugins/Source/Fields/CustomFields.php';
require_once $base_path . '/vendor/molajo/plugins/Source/Fields/JoinFields.php';
require_once $base_path . '/vendor/molajo/plugins/Source/Fields/FieldsPlugin.php';
require_once $base_path . '/vendor/molajo/plugins/Source/Footer/FooterPlugin.php';
require_once $base_path . '/vendor/molajo/plugins/Source/Fullname/FullnamePlugin.php';
require_once $base_path . '/vendor/molajo/plugins/Source/Linebreaks/LinebreaksPlugin.php';
require_once $base_path . '/vendor/molajo/plugins/Source/Links/LinksPlugin.php';
require_once $base_path . '/vendor/molajo/plugins/Source/Login/LoginPlugin.php';
require_once $base_path . '/vendor/molajo/plugins/Source/Logout/LogoutPlugin.php';
require_once $base_path . '/vendor/molajo/plugins/Source/Menuitems/MenuitemsPlugin.php';
require_once $base_path . '/vendor/molajo/plugins/Source/Messages/MessagesPlugin.php';
require_once $base_path . '/vendor/molajo/plugins/Source/Pagetypeapplication/PagetypeapplicationPlugin.php';
require_once $base_path . '/vendor/molajo/plugins/Source/Pagetypeconfiguration/PagetypeconfigurationPlugin.php';
require_once $base_path . '/vendor/molajo/plugins/Source/Pagetypedashboard/PagetypedashboardPlugin.php';
require_once $base_path . '/vendor/molajo/plugins/Source/Pagetypeedit/PagetypeeditPlugin.php';
require_once $base_path . '/vendor/molajo/plugins/Source/Pagetypegrid/Base.php';
require_once $base_path . '/vendor/molajo/plugins/Source/Pagetypegrid/GridQuery.php';
require_once $base_path . '/vendor/molajo/plugins/Source/Pagetypegrid/PagetypegridPlugin.php';
require_once $base_path . '/vendor/molajo/plugins/Source/Pagetypeitem/PagetypeitemPlugin.php';
require_once $base_path . '/vendor/molajo/plugins/Source/Pagetypelist/PagetypelistPlugin.php';
require_once $base_path . '/vendor/molajo/plugins/Source/Pagetypenew/PagetypenewPlugin.php';
require_once $base_path . '/vendor/molajo/plugins/Source/Pagination/PaginationPlugin.php';
require_once $base_path . '/vendor/molajo/plugins/Source/Queryauthorisation/QueryauthorisationPlugin.php';
require_once $base_path . '/vendor/molajo/plugins/Source/Readmore/ReadmorePlugin.php';
require_once $base_path . '/vendor/molajo/plugins/Source/Sites/SitesPlugin.php';
require_once $base_path . '/vendor/molajo/plugins/Source/Smilies/SmiliesPlugin.php';
require_once $base_path . '/vendor/molajo/plugins/Source/Snippet/SnippetPlugin.php';
require_once $base_path . '/vendor/molajo/plugins/Source/Status/StatusPlugin.php';

require_once $base_path . '/vendor/molajo/query/Controller/Base.php';
require_once $base_path . '/vendor/molajo/query/Controller/QueryController.php';
require_once $base_path . '/vendor/molajo/query/Controller/ReadController.php';
require_once $base_path . '/vendor/molajo/query/Model/Base.php';
require_once $base_path . '/vendor/molajo/query/Model/ReadModel.php';
require_once $base_path . '/vendor/molajo/query/Source/Builder/Base.php';
require_once $base_path . '/vendor/molajo/query/Source/Builder/FilterData.php';
require_once $base_path . '/vendor/molajo/query/Source/Builder/EditData.php';
require_once $base_path . '/vendor/molajo/query/Source/Builder/SetData.php';
require_once $base_path . '/vendor/molajo/query/Source/Builder/BuildSqlGroups.php';
require_once $base_path . '/vendor/molajo/query/Source/Builder/BuildSqlElements.php';
require_once $base_path . '/vendor/molajo/query/Source/Builder/BuildSql.php';
require_once $base_path . '/vendor/molajo/query/Source/Builder/Sql.php';

require_once $base_path . '/vendor/molajo/query/Source/Model/Base.php';
require_once $base_path . '/vendor/molajo/query/Source/Model/Query.php';
require_once $base_path . '/vendor/molajo/query/Source/Model/Utilities.php';
require_once $base_path . '/vendor/molajo/query/Source/Model/Defaults.php';
require_once $base_path . '/vendor/molajo/query/Source/Model/Table.php';
require_once $base_path . '/vendor/molajo/query/Source/Model/Columns.php';
require_once $base_path . '/vendor/molajo/query/Source/Model/Criteria.php';
require_once $base_path . '/vendor/molajo/query/Source/Model/Registry.php';

require_once $base_path . '/vendor/molajo/query/Source/Adapter/Mysql.php';
require_once $base_path . '/vendor/molajo/query/Source/Adapter/Postgresql.php';
require_once $base_path . '/vendor/molajo/query/Source/Adapter/Sqllite.php';
require_once $base_path . '/vendor/molajo/query/Source/Adapter/Sqlserver.php';

require_once $base_path . '/vendor/molajo/query/Source/QueryBuilder.php';
require_once $base_path . '/vendor/molajo/query/Source/QueryProxy.php';
// delete

require_once $base_path . '/vendor/autoload.php';
require_once __DIR__ . '/ReadJsonFile.php';
require_once __DIR__ . '/ResourceMaps.php';
require_once __DIR__ . '/Autoload.php';
require_once __DIR__ . '/SetNamespace.php';
require_once __DIR__ . '/IoCC.php';
require_once __DIR__ . '/Files/Input/Requests.php';
require_once __DIR__ . '/Frontcontroller.php';
