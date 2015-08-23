<?php
/**
 * Bootstrap Application
 *
 * @package    Molajo
 * @copyright  2014-2015 Amy Stephen. All rights reserved.
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 */

define('SKIP_MOLAJO_INSTALL_CHECK', true);

$base_path = substr(__DIR__, 0, strlen(__DIR__) - strlen('/Bootstrap'));

ini_set('display_errors', 'On');
ini_set('error_reporting', E_ALL);
if (function_exists('date_default_timezone_set') && function_exists('date_default_timezone_get')) {
    date_default_timezone_set(@date_default_timezone_get());
}

require_once $base_path . '/vendor/autoload.php';

//todo: remove once in composer
require_once $base_path . '/vendor/commonapi/application/ApplicationInterface.php';
require_once $base_path . '/vendor/commonapi/application/DateInterface.php';
require_once $base_path . '/vendor/commonapi/application/ErrorHandlingInterface.php';
require_once $base_path . '/vendor/commonapi/application/FrontControllerInterface.php';
require_once $base_path . '/vendor/commonapi/application/ImageInterface.php';
require_once $base_path . '/vendor/commonapi/application/NumberToTextInterface.php';
require_once $base_path . '/vendor/commonapi/application/RandomStringInterface.php';
require_once $base_path . '/vendor/commonapi/application/ResourceInterface.php';
require_once $base_path . '/vendor/commonapi/application/SiteInterface.php';
require_once $base_path . '/vendor/commonapi/application/TextInterface.php';
require_once $base_path . '/vendor/commonapi/application/UrlInterface.php';

require_once $base_path . '/vendor/commonapi/fieldhandler/ConstraintInterface.php';
require_once $base_path . '/vendor/commonapi/fieldhandler/EscapeInterface.php';
require_once $base_path . '/vendor/commonapi/fieldhandler/FormatInterface.php';
require_once $base_path . '/vendor/commonapi/fieldhandler/HandleResponseInterface.php';
require_once $base_path . '/vendor/commonapi/fieldhandler/SanitizeInterface.php';
require_once $base_path . '/vendor/commonapi/fieldhandler/ValidateInterface.php';
require_once $base_path . '/vendor/commonapi/fieldhandler/ValidateResponseInterface.php';
require_once $base_path . '/vendor/commonapi/fieldhandler/FieldhandlerInterface.php';
require_once $base_path . '/vendor/commonapi/fieldhandler/FieldhandlerUsageTrait.php';

require_once $base_path . '/vendor/molajo/application/Source/Controller/Randomstring.php';

require_once $base_path . '/vendor/molajo/application/Source/Resource/Adapter/Field.php';
require_once $base_path . '/vendor/commonapi/query/QueryUsageTrait.php';

require_once $base_path . '/vendor/molajo/query/Source/Resource/Adapter/ConfigurationFactory.php';
require_once $base_path . '/vendor/molajo/query/Source/Resource/Adapter/Xml.php';

require_once $base_path . '/vendor/molajo/resource/Source/Proxy/SchemeTrait.php';

require_once $base_path . '/vendor/molajo/application/Source/Resource/ExtensionMap/Base.php';
require_once $base_path . '/vendor/molajo/application/Source/Resource/ExtensionMap/ModelName.php';
require_once $base_path . '/vendor/molajo/application/Source/Resource/ExtensionMap/Extension.php';
require_once $base_path . '/vendor/molajo/application/Source/Resource/ExtensionMap/Extensions.php';
require_once $base_path . '/vendor/molajo/application/Source/Resource/ExtensionMap/CatalogTypes.php';
require_once $base_path . '/vendor/molajo/application/Source/Resource/ExtensionMap.php';

// remove below
require_once $base_path . '/vendor/molajo/plugins/Source/AbstractPlugin.php';
require_once $base_path . '/vendor/molajo/plugins/Source/Cache.php';
require_once $base_path . '/vendor/molajo/plugins/Source/Query.php';
require_once $base_path . '/vendor/molajo/plugins/Source/Lists.php';
require_once $base_path . '/vendor/molajo/plugins/Source/Fieldlists.php';
require_once $base_path . '/vendor/molajo/plugins/Source/Forms.php';
require_once $base_path . '/vendor/molajo/plugins/Source/Fields.php';
require_once $base_path . '/vendor/molajo/plugins/Source/Image.php';
require_once $base_path . '/vendor/molajo/plugins/Source/Content.php';
require_once $base_path . '/vendor/molajo/plugins/Source/UserEvent.php';
require_once $base_path . '/vendor/molajo/plugins/Source/SystemEvent.php';
require_once $base_path . '/vendor/molajo/plugins/Source/DisplayEvent.php';
require_once $base_path . '/vendor/molajo/plugins/Source/ReadEvent.php';
require_once $base_path . '/vendor/molajo/plugins/Source/InitialiseEvent.php';
require_once $base_path . '/vendor/molajo/plugins/Source/CreateEvent.php';
require_once $base_path . '/vendor/molajo/plugins/Source/UpdateEvent.php';
require_once $base_path . '/vendor/molajo/plugins/Source/DeleteEvent.php';

//require_once $base_path . '/Source/Plugins/A/APlugin.php';
require_once $base_path . '/Source/Plugins/Activity/ActivityPlugin.php';
require_once $base_path . '/Source/Plugins/Alias/AliasPlugin.php';
require_once $base_path . '/Source/Plugins/Application/Title.php';
require_once $base_path . '/Source/Plugins/Application/Metadata.php';
require_once $base_path . '/Source/Plugins/Application/ApplicationPlugin.php';
require_once $base_path . '/Source/Plugins/Article/ArticlePlugin.php';
require_once $base_path . '/Source/Plugins/Author/AuthorPlugin.php';
require_once $base_path . '/Source/Plugins/Authorisation/AuthorisationPlugin.php';
require_once $base_path . '/Source/Plugins/Blockquote/BlockquotePlugin.php';
require_once $base_path . '/Source/Plugins/Breadcrumbs/BreadcrumbsPlugin.php';
require_once $base_path . '/Source/Plugins/Catalog/CatalogPlugin.php';
require_once $base_path . '/Source/Plugins/Checkin/CheckinPlugin.php';
require_once $base_path . '/Source/Plugins/Checkout/CheckoutPlugin.php';
require_once $base_path . '/Source/Plugins/Comments/Base.php';
require_once $base_path . '/Source/Plugins/Comments/Form.php';
require_once $base_path . '/Source/Plugins/Comments/Data.php';
require_once $base_path . '/Source/Plugins/Comments/Heading.php';
require_once $base_path . '/Source/Plugins/Comments/Process.php';
require_once $base_path . '/Source/Plugins/Comments/CommentsPlugin.php';
require_once $base_path . '/Source/Plugins/Copyright/CopyrightPlugin.php';
require_once $base_path . '/Source/Plugins/Csrftoken/CsrftokenPlugin.php';
require_once $base_path . '/Source/Plugins/Customfields/Base.php';
require_once $base_path . '/Source/Plugins/Customfields/ProcessCustomFields.php';
require_once $base_path . '/Source/Plugins/Customfields/CustomfieldsContent.php';
require_once $base_path . '/Source/Plugins/Customfields/CustomfieldsModelRegistry.php';
require_once $base_path . '/Source/Plugins/Customfields/CustomfieldsPlugin.php';
require_once $base_path . '/Source/Plugins/Dateformats/DateformatsPlugin.php';
require_once $base_path . '/Source/Plugins/Defer/DeferPlugin.php';
require_once $base_path . '/Source/Plugins/Email/EmailPlugin.php';
require_once $base_path . '/Source/Plugins/Events/EventsPlugin.php';
require_once $base_path . '/Source/Plugins/Extensions/Base.php';
require_once $base_path . '/Source/Plugins/Extensions/ExtensionInstances.php';
require_once $base_path . '/Source/Plugins/Extensions/Extensions.php';
require_once $base_path . '/Source/Plugins/Extensions/ExtensionsPlugin.php';
require_once $base_path . '/Source/Plugins/Feed/FeedPlugin.php';
require_once $base_path . '/Source/Plugins/Fields/FieldsPlugin.php';
require_once $base_path . '/Source/Plugins/Footer/FooterPlugin.php';
require_once $base_path . '/Source/Plugins/Fullname/FullnamePlugin.php';
require_once $base_path . '/Source/Plugins/Gravatar/GravatarPlugin.php';
require_once $base_path . '/Source/Plugins/Head/HeadPlugin.php';
require_once $base_path . '/Source/Plugins/Header/HeaderPlugin.php';
require_once $base_path . '/Source/Plugins/Image/Key.php';
require_once $base_path . '/Source/Plugins/Image/Html.php';
require_once $base_path . '/Source/Plugins/Image/ImagePlugin.php';
require_once $base_path . '/Source/Plugins/Item/ItemPlugin.php';
require_once $base_path . '/Source/Plugins/Linebreaks/LinebreaksPlugin.php';
require_once $base_path . '/Source/Plugins/Links/LinksPlugin.php';
require_once $base_path . '/Source/Plugins/Login/LoginPlugin.php';
require_once $base_path . '/Source/Plugins/Logout/LogoutPlugin.php';
require_once $base_path . '/Source/Plugins/Menuitems/MenuitemsPlugin.php';
require_once $base_path . '/Source/Plugins/Messages/MessagesPlugin.php';
require_once $base_path . '/Source/Plugins/Navbar/Menu.php';
require_once $base_path . '/Source/Plugins/Navbar/NavbarPlugin.php';
require_once $base_path . '/Source/Plugins/Ordering/OrderingPlugin.php';
require_once $base_path . '/Source/Plugins/Page/PagePlugin.php';
require_once $base_path . '/Source/Plugins/Pagetypeapplication/PagetypeapplicationPlugin.php';
require_once $base_path . '/Source/Plugins/Pagetypeconfiguration/Base.php';
require_once $base_path . '/Source/Plugins/Pagetypeconfiguration/GridMenuitem.php';
require_once $base_path . '/Source/Plugins/Pagetypeconfiguration/Lists.php';
require_once $base_path . '/Source/Plugins/Pagetypeconfiguration/PagetypeconfigurationPlugin.php';
require_once $base_path . '/Source/Plugins/Pagetypedashboard/PagetypedashboardPlugin.php';
require_once $base_path . '/Source/Plugins/Pagetypeedit/PagetypeeditPlugin.php';
require_once $base_path . '/Source/Plugins/Pagetypegrid/Base.php';
require_once $base_path . '/Source/Plugins/Pagetypegrid/GridQuery.php';
require_once $base_path . '/Source/Plugins/Pagetypegrid/PagetypegridPlugin.php';
require_once $base_path . '/Source/Plugins/Pagetypeitem/PagetypeitemPlugin.php';
require_once $base_path . '/Source/Plugins/Pagetypelist/PagetypelistPlugin.php';
require_once $base_path . '/Source/Plugins/Pagetypenew/PagetypenewPlugin.php';
require_once $base_path . '/Source/Plugins/Pagination/PaginationPlugin.php';
require_once $base_path . '/Source/Plugins/Paging/PagingPlugin.php';
require_once $base_path . '/Source/Plugins/Parse/ParsePlugin.php';
require_once $base_path . '/Source/Plugins/Position/PositionPlugin.php';
require_once $base_path . '/Source/Plugins/Queryauthorisation/QueryauthorisationPlugin.php';
require_once $base_path . '/Source/Plugins/Readmore/ReadmorePlugin.php';
require_once $base_path . '/Source/Plugins/Sites/SitesPlugin.php';
require_once $base_path . '/Source/Plugins/Smilies/SmiliesPlugin.php';
require_once $base_path . '/Source/Plugins/Snippet/SnippetPlugin.php';
require_once $base_path . '/Source/Plugins/Spamprotection/SpamprotectionPlugin.php';
require_once $base_path . '/Source/Plugins/Status/StatusPlugin.php';
require_once $base_path . '/Source/Plugins/Template/Data.php';
require_once $base_path . '/Source/Plugins/Template/Model.php';
require_once $base_path . '/Source/Plugins/Template/TemplatePlugin.php';
require_once $base_path . '/Source/Plugins/Theme/ThemePlugin.php';
require_once $base_path . '/Source/Plugins/Toolbar/ToolbarPlugin.php';
require_once $base_path . '/Source/Plugins/Wrap/WrapPlugin.php';

require_once __DIR__ . '/ReadJsonFile.php';
require_once __DIR__ . '/ResourceMaps.php';
require_once __DIR__ . '/IoCC.php';
require_once __DIR__ . '/Files/Input/Requests.php';
require_once __DIR__ . '/Frontcontroller.php';
