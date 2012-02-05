<?php
/**
 *  Collect snippets for later posting as Gists
 */

/*
 * Errors and Messages
 *
 * define('MOLAJO_MESSAGE_TYPE_MESSAGE', 'message');
 * define('MOLAJO_MESSAGE_TYPE_NOTICE', 'notice');
 * define('MOLAJO_MESSAGE_TYPE_WARNING', 'warning');
 * define('MOLAJO_MESSAGE_TYPE_ERROR', 'error');
 */

MolajoController::getApplication()->setMessage('Test message', MOLAJO_MESSAGE_TYPE_WARNING);

MolajoController::getApplication()
    ->setMessage(
    $message = MolajoTextHelper::_('ERROR_DATABASE_QUERY'),
    $type = MOLAJO_MESSAGE_TYPE_ERROR,
    $code = null,
    $debug_location = 'MolajoAssetHelper::get',
    $debug_object = $query->__toString()
);

/**
 *  Application Object
 */
MolajoController::getApplication()->get('sef', 1);

/**
 *  User Object
 */
MolajoController::getUser()->view_groups;

$doc = JFactory::getDocument();
$doc->addScript('https://www.google.com/jsapi');
$doc->addScriptDeclaration('
google.load("jquery", "1.6.2", {uncompressed: true});
google.load("jqueryui", "1.8.15", {uncompressed:true});
');

/**
 *  Include Statements

    <include:head/>
    <div class="wrapper">
        <include:module name=page-header template=page-header wrap=header wrap_class=header />
        <section class="middle">
            <include:message />
                <div class="container">
                    <include:request wrap="div" wrap_class="content" />
                </div>
                <include:tag name=sidebar template=sidebar wrap=aside wrap_class=leftsidebar />
        </section>
        <include:module name=page-footer template=page-footer wrap=footer wrap_class="footer" />
    </div>
    <include:defer />
 */
