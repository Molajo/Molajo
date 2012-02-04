<?php
/**
 *  Collect snippets for later posting as Gists
 */

/*
 *  Errors and Messages
 */
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
