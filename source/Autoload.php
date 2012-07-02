<?php
/**
 * @package    Molajo
 * @copyright  2012 Amy Stephen. All rights reserved.
 * @license    GNU GPL v 2, or later and MIT, see license folder
 */
defined('MOLAJO') or die;

/** used in this script */
if (defined('APPLICATIONS')) {
} else {
	define('APPLICATIONS', BASE_FOLDER . '/Molajo');
}
if (defined('VENDOR')) {
} else {
	define('VENDOR', BASE_FOLDER . '/Vendor');
}

/** Use Symfony ClassLoader for Autoload */
require_once VENDOR . '/Symfony/Component/ClassLoader/UniversalClassLoader.php';
use Symfony\Component\ClassLoader\UniversalClassLoader;

$s = new UniversalClassLoader();

/** register the loader */
$s->register();

/** Molajo namespaces */
$s->registerNamespace('Molajo', BASE_FOLDER);

$s->registerNamespace('Molajo\\Controller', BASE_FOLDER);

$s->registerNamespace('Molajo\\Extension', BASE_FOLDER);
$s->registerNamespace('Molajo\\Extension\\Component', BASE_FOLDER);
$s->registerNamespace('Molajo\\Extension\\Formfield', BASE_FOLDER);
$s->registerNamespace('Molajo\\Extension\\Helper', BASE_FOLDER);
$s->registerNamespace('Molajo\\Extension\\Includer', BASE_FOLDER);
$s->registerNamespace('Molajo\\Extension\\Language', BASE_FOLDER);
$s->registerNamespace('Molajo\\Extension\\Menuitems', BASE_FOLDER);
$s->registerNamespace('Molajo\\Extension\\Module', BASE_FOLDER);
$s->registerNamespace('Molajo\\Extension\\Theme', BASE_FOLDER);
$s->registerNamespace('Molajo\\Extension\\Trigger', BASE_FOLDER);
$s->registerNamespace('Molajo\\Extension\\View', BASE_FOLDER);

$s->registerNamespace('Molajo\\Model', BASE_FOLDER);

$s->registerNamespace('Molajo\\Service', BASE_FOLDER);
$s->registerNamespace('Molajo\\Service\\Services', BASE_FOLDER);

/** Symfony namespaces */
$s->registerNamespace('Symfony\\Component\\HttpFoundation', VENDOR);
$s->registerNamespace('Symfony\\Component\\HttpFoundation\\Session', VENDOR);
if (interface_exists('SessionHandlerInterface')) {
} else {
	$s->registerPrefixFallback(VENDOR . '/Symfony/Component/HttpFoundation/Resources/stubs');
}

/** Joomla namespaces */
if (defined('JPATH_SITE')) {
} else {
	define('JPATH_SITE', BASE_FOLDER);
}
if (defined('JPATH_PLATFORM')) {
} else {
	define('JPATH_PLATFORM', VENDOR . '/Joomla');
}
if (defined('JPATH_LIBRARIES')) {
} else {
	define('JPATH_LIBRARIES', VENDOR . '/Joomla');
}
$s->registerNamespace('Joomla', VENDOR);
$s->registerNamespace('Joomla\\client', VENDOR);
$s->registerNamespace('Joomla\\crypt', VENDOR);
$s->registerNamespace('Joomla\\crypt\\cipher', VENDOR);
$s->registerNamespace('Joomla\\database', VENDOR);
$s->registerNamespace('Joomla\\database\\driver', VENDOR);
$s->registerNamespace('Joomla\\database\\exporter', VENDOR);
$s->registerNamespace('Joomla\\database\\importer', VENDOR);
$s->registerNamespace('Joomla\\database\\iterator', VENDOR);
$s->registerNamespace('Joomla\\database\\query', VENDOR);
$s->registerNamespace('Joomla\\date', VENDOR);
$s->registerNamespace('Joomla\\filesystem', VENDOR);
$s->registerNamespace('Joomla\\filesystem\streams', VENDOR);
$s->registerNamespace('Joomla\\filesystem\support', VENDOR);
$s->registerNamespace('Joomla\\image', VENDOR);
$s->registerNamespace('Joomla\\image\filters', VENDOR);
$s->registerNamespace('Joomla\\log', VENDOR);
$s->registerNamespace('Joomla\\log\\loggers', VENDOR);
$s->registerNamespace('Joomla\\object', VENDOR);
$s->registerNamespace('Joomla\\registry', VENDOR);
$s->registerNamespace('Joomla\\registry\\format', VENDOR);
$s->registerNamespace('Joomla\\string', VENDOR);
$s->registerNamespace('Joomla\\utilities', VENDOR);

/** Other */
$s->registerNamespace('TwitterOAuth', VENDOR);
$s->registerNamespace('Mustache', VENDOR);
$s->registerNamespace('LoremIpsumGenerator', VENDOR);
$s->registerNamespace('ChromePHP', VENDOR);
$s->registerNamespace('FirePHP', VENDOR);
$s->registerNamespace('PhpConsole', VENDOR);
$s->registerNamespace('HTMLPurifier', VENDOR);
$s->registerNamespace('HTMLPurifier\\filters', VENDOR);
$s->registerNamespace('phpmailer', VENDOR);
$s->registerNamespace('phputf8', VENDOR);
$s->registerNamespace('phputf8\\mbstring', VENDOR);
$s->registerNamespace('phputf8\\native', VENDOR);
$s->registerNamespace('phputf8\\utils', VENDOR);
$s->registerNamespace('Simplepie', VENDOR);

if (defined('HTMPURIFIER_FILTERS')) {
} else {
	define('HTMPURIFIER_FILTERS', VENDOR . '/HTMLPurifier/filters');
}
