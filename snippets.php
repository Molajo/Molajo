Four Application areas: processes, services, helpers, and installers.

Any method can be extended (and possibly overridden) - by placing code xyz.

PROCESSES

Molajo - base

Molajo::Site
Molajo::Application
Molajo::Request
Molajo::Parser
MolajoRenderer
...MolajoComponentRenderer, formfield, head, message, module, tag, theme
- MolajoController
- MolajoModel
- MolajoView
Molajo::Responder

SERVICES

Services - base

Live connection to a data source

Services::Access
Services::Authentication
Services::Configuration
Services::Date
Services::Dispatcher
Services::Filter
Services::Image
Services::DB
Services::Language
Services::Mail
Services::Message()
Services::Security
Services::Session
Services::Text
Services::Url
Services::User


HELPERS

Molajo::Helpers

Specific to process support, primarily implements extension layer

Helper::Application
Helper::Asset
Helper::Extension
Helper::Site

INSTALLERS

Molajo::Installer

$user = Services::Connect('User');
$db = Services::Connect('jdb');

<?php
/*
 *  Debugging: Send message to Chrome Console
 *
 *  To use, must use Chrome and install:
 *  https://chrome.google.com/webstore/detail/nfhmhhlpfleoednkpnnnkolmclajemef
 *
 */

if (Services::Configuration()->get('debug', 0) == 1) {
    debug('stuff goes here'.$includingVariables);
}

/*
 * Application Configuration Object
 *
 * Site configuration (located in sites/N/configuration.php file) and
 *  Application configuration (stored in the application table, parameter column)
 *  are combined in the Application Controller and can be accessed anywhere in Molajo
 */

Services::Configuration()->set('sef', 1);

echo Services::Configuration()->get('sef', 1);

/** List all parameters and values */

?>
<?php
/*
 * Meta Data
 */

Molajo::Responder()->set('metadata_title', 1);

echo Molajo::Responder()->get('sef', 1);

/** List all parameters and values */

?>

<?php
/*
 * Errors and Messages
 *
 * Both errors and messages use the same application message object.
 *
 * define('MOLAJO_MESSAGE_TYPE_MESSAGE', 'message');
 * define('MOLAJO_MESSAGE_TYPE_NOTICE', 'notice');
 * define('MOLAJO_MESSAGE_TYPE_WARNING', 'warning');
 * define('MOLAJO_MESSAGE_TYPE_ERROR', 'error');
 */
?>
<?php
/** Basic Message, for example: "Article saved." or "Title required."  */
Services::Message()
    ->set(
        Services::Language()->_('Title required for article.'),
        MOLAJO_MESSAGE_TYPE_WARNING
    );

Services::Message()
    ->set(
        $message = Services::Language()->_('ERROR_DATABASE_QUERY'),
        $type = MOLAJO_MESSAGE_TYPE_ERROR,
        $code = null,
        $debug_location = 'AssetHelper::get',
        $debug_object = $query->__toString()
    );
$config = $this
?>
Service::Message()->get('x')
Service::Session
Service::User
<?php
/**
 *  User Object
 */
echo implode(',', Services::User()->get('view_groups'));

?>


<?php
/**
 *  Include Renderer Statements
 *
 *  Molajo Renderers are used to process Molajo extensions and, in conjunction with
 *  the MVC, execute the display task and render the template and wrap views.
 *  These statements can be used in any Page, Template or Wrap View.
 *
 *  The <include:request /> renderer processes the primary component for the page.
 *  Secondary component displays can be requested using the <include:component /> option
 */
?>
<include:head/>
<div class="wrapper">
    <include:module name=page-header template=page-header wrap=header wrap_class=header />
    <section class="middle">
        <include:message />
        <include:request wrap="div" wrap_class="container" />
        <include:tag name=sidebar template=sidebar wrap=aside wrap_class=leftsidebar />
    </section>
    <include:module name=page-footer template=page-footer wrap=footer wrap_class="footer" />
</div>
<include:defer />

<?php
/**
 *  Working with Media
 */
$doc = JFactory::getDocument();
$doc->addScript('https://www.google.com/jsapi');
$doc->addScriptDeclaration('
google.load("jquery", "1.6.2", {uncompressed: true});
google.load("jqueryui", "1.8.15", {uncompressed:true});
');


/**
 *  Security: output rendering
 */
echo Services::Security()->safeHTML($this->row->content_text);

echo Services::Security()->safeText($this->row->title);

echo Services::Security()->safeURL($this->row->url);

echo Services::Security()->safeInteger($this->row->version);

?>

