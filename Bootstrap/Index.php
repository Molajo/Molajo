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

//require_once $base_path . '/Source/Plugins/A/APlugin.php';
require_once $base_path . '/Source/Plugins/Activity/Source/ActivityPlugin.php';
require_once $base_path . '/Source/Plugins/Alias/Source/AliasPlugin.php';
require_once $base_path . '/Source/Plugins/Application/Source/Title.php';
require_once $base_path . '/Source/Plugins/Application/Source/Metadata.php';
require_once $base_path . '/Source/Plugins/Application/Source/ApplicationPlugin.php';
require_once $base_path . '/Source/Plugins/Article/Source/ArticlePlugin.php';
require_once $base_path . '/Source/Plugins/Author/Source/AuthorPlugin.php';
require_once $base_path . '/Source/Plugins/Authorisation/Source/AuthorisationPlugin.php';
require_once $base_path . '/Source/Plugins/Blockquote/Source/BlockquotePlugin.php';
require_once $base_path . '/Source/Plugins/Breadcrumbs/Source/BreadcrumbsPlugin.php';
require_once $base_path . '/Source/Plugins/Catalog/Source/CatalogPlugin.php';
require_once $base_path . '/Source/Plugins/Checkin/Source/CheckinPlugin.php';
require_once $base_path . '/Source/Plugins/Checkout/Source/CheckoutPlugin.php';
require_once $base_path . '/Source/Plugins/Comments/Source/Base.php';
require_once $base_path . '/Source/Plugins/Comments/Source/Form.php';
require_once $base_path . '/Source/Plugins/Comments/Source/Data.php';
require_once $base_path . '/Source/Plugins/Comments/Source/Heading.php';
require_once $base_path . '/Source/Plugins/Comments/Source/Process.php';
require_once $base_path . '/Source/Plugins/Comments/Source/CommentsPlugin.php';
require_once $base_path . '/Source/Plugins/Copyright/Source/CopyrightPlugin.php';
require_once $base_path . '/Source/Plugins/Csrftoken/Source/CsrftokenPlugin.php';
require_once $base_path . '/Source/Plugins/Customfields/Source/Base.php';
require_once $base_path . '/Source/Plugins/Customfields/Source/ProcessCustomFields.php';
require_once $base_path . '/Source/Plugins/Customfields/Source/CustomfieldsContent.php';
require_once $base_path . '/Source/Plugins/Customfields/Source/CustomfieldsModelRegistry.php';
require_once $base_path . '/Source/Plugins/Customfields/Source/CustomfieldsPlugin.php';
require_once $base_path . '/Source/Plugins/Dateformats/Source/DateformatsPlugin.php';
require_once $base_path . '/Source/Plugins/Defer/Source/DeferPlugin.php';
require_once $base_path . '/Source/Plugins/Email/Source/EmailPlugin.php';
require_once $base_path . '/Source/Plugins/Events/Source/EventsPlugin.php';
require_once $base_path . '/Source/Plugins/Extensions/Source/Base.php';
require_once $base_path . '/Source/Plugins/Extensions/Source/ExtensionInstances.php';
require_once $base_path . '/Source/Plugins/Extensions/Source/Extensions.php';
require_once $base_path . '/Source/Plugins/Extensions/Source/ExtensionsPlugin.php';
require_once $base_path . '/Source/Plugins/Feed/Source/FeedPlugin.php';
require_once $base_path . '/Source/Plugins/Fields/Source/FieldsPlugin.php';
require_once $base_path . '/Source/Plugins/Footer/Source/FooterPlugin.php';
require_once $base_path . '/Source/Plugins/Fullname/Source/FullnamePlugin.php';
require_once $base_path . '/Source/Plugins/Gravatar/Source/GravatarPlugin.php';
require_once $base_path . '/Source/Plugins/Head/Source/HeadPlugin.php';
require_once $base_path . '/Source/Plugins/Header/Source/HeaderPlugin.php';
require_once $base_path . '/Source/Plugins/Image/Source/Key.php';
require_once $base_path . '/Source/Plugins/Image/Source/Html.php';
require_once $base_path . '/Source/Plugins/Image/Source/ImagePlugin.php';
require_once $base_path . '/Source/Plugins/Item/Source/ItemPlugin.php';
require_once $base_path . '/Source/Plugins/Linebreaks/Source/LinebreaksPlugin.php';
require_once $base_path . '/Source/Plugins/Links/Source/LinksPlugin.php';
require_once $base_path . '/Source/Plugins/Login/Source/LoginPlugin.php';
require_once $base_path . '/Source/Plugins/Logout/Source/LogoutPlugin.php';
require_once $base_path . '/Source/Plugins/Menuitems/Source/MenuitemsPlugin.php';
require_once $base_path . '/Source/Plugins/Messages/Source/MessagesPlugin.php';
require_once $base_path . '/Source/Plugins/Navbar/Source/Menu.php';
require_once $base_path . '/Source/Plugins/Navbar/Source/NavbarPlugin.php';
require_once $base_path . '/Source/Plugins/Ordering/Source/OrderingPlugin.php';
require_once $base_path . '/Source/Plugins/Page/Source/PagePlugin.php';
require_once $base_path . '/Source/Plugins/Pagetypeapplication/Source/PagetypeapplicationPlugin.php';
require_once $base_path . '/Source/Plugins/Pagetypeconfiguration/Source/Base.php';
require_once $base_path . '/Source/Plugins/Pagetypeconfiguration/Source/GridMenuitem.php';
require_once $base_path . '/Source/Plugins/Pagetypeconfiguration/Source/Lists.php';
require_once $base_path . '/Source/Plugins/Pagetypeconfiguration/Source/PagetypeconfigurationPlugin.php';
require_once $base_path . '/Source/Plugins/Pagetypedashboard/Source/PagetypedashboardPlugin.php';
require_once $base_path . '/Source/Plugins/Pagetypeedit/Source/PagetypeeditPlugin.php';
require_once $base_path . '/Source/Plugins/Pagetypegrid/Source/Base.php';
require_once $base_path . '/Source/Plugins/Pagetypegrid/Source/GridQuery.php';
require_once $base_path . '/Source/Plugins/Pagetypegrid/Source/PagetypegridPlugin.php';
require_once $base_path . '/Source/Plugins/Pagetypeitem/Source/PagetypeitemPlugin.php';
require_once $base_path . '/Source/Plugins/Pagetypelist/Source/PagetypelistPlugin.php';
require_once $base_path . '/Source/Plugins/Pagetypenew/Source/PagetypenewPlugin.php';
require_once $base_path . '/Source/Plugins/Pagination/Source/PaginationPlugin.php';
require_once $base_path . '/Source/Plugins/Paging/Source/PagingPlugin.php';
require_once $base_path . '/Source/Plugins/Parse/Source/ParsePlugin.php';
require_once $base_path . '/Source/Plugins/Position/Source/PositionPlugin.php';
require_once $base_path . '/Source/Plugins/Queryauthorisation/Source/QueryauthorisationPlugin.php';
require_once $base_path . '/Source/Plugins/Readmore/Source/ReadmorePlugin.php';
require_once $base_path . '/Source/Plugins/Sites/Source/SitesPlugin.php';
require_once $base_path . '/Source/Plugins/Smilies/Source/SmiliesPlugin.php';
require_once $base_path . '/Source/Plugins/Snippet/Source/SnippetPlugin.php';
require_once $base_path . '/Source/Plugins/Spamprotection/Source/SpamprotectionPlugin.php';
require_once $base_path . '/Source/Plugins/Status/Source/StatusPlugin.php';
require_once $base_path . '/Source/Plugins/Template/Source/Data.php';
require_once $base_path . '/Source/Plugins/Template/Source/Model.php';
require_once $base_path . '/Source/Plugins/Template/Source/TemplatePlugin.php';
require_once $base_path . '/Source/Plugins/Theme/Source/ThemePlugin.php';
require_once $base_path . '/Source/Plugins/Toolbar/Source/ToolbarPlugin.php';
require_once $base_path . '/Source/Plugins/Wrap/Source/WrapPlugin.php';

require_once __DIR__ . '/ReadJsonFile.php';
require_once __DIR__ . '/ResourceMaps.php';
require_once __DIR__ . '/IoCC.php';
require_once __DIR__ . '/Files/Input/Requests.php';
require_once __DIR__ . '/Frontcontroller.php';
