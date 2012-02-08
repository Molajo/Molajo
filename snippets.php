<?php
/*
 * Application Configuration Object
 *
 * Site configuration (located in sites/N/configuration.php file) and
 *  Application configuration (stored in the application table, parameter column)
 *  are combined in the Application Controller and can be accessed anywhere in Molajo
 */

Molajo::Application()->set('sef', 1);

echo Molajo::Application()->get('sef', 1);

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
Molajo::Application()
    ->setMessage(
        TextServices::_('Title required for article.'),
        MOLAJO_MESSAGE_TYPE_WARNING
    );

Molajo::Application()
    ->setMessage(
        $message = TextServices::_('ERROR_DATABASE_QUERY'),
        $type = MOLAJO_MESSAGE_TYPE_ERROR,
        $code = null,
        $debug_location = 'AssetHelper::get',
        $debug_object = $query->__toString()
    );

?>

<?php
/**
 *  User Object
 */
echo implode(',', Molajo::User()->get('view_groups'));

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

<?php
/**
 *  Security: output rendering
 */
echo Molajo::Display()->safeHTML($this->row->content_text);

echo Molajo::Display()->safeText($this->row->title);

echo Molajo::Display()->safeURL($this->row->url);

echo Molajo::Display()->safeInteger($this->row->version);

?>

<?php

/**
 * Static instance
 *
 * @var    object
 * @since  1.0
 */
protected static $instance;


/**
 * getInstance
 *
 * @static
 * @return  bool|object
 * @since   1.0
 */
public static function getInstance()
{
    if (empty(self::$instance)) {
        self::$instance = new MolajoXXXService();
    }
    return self::$instance;
}

/**
 * Class constructor
 *
 * @return  null
 * @since   1.0
 */
public function __construct()
{
    $this->connect();
}
