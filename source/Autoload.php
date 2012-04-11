<?php
/**
 * @package   Molajo
 * @copyright 2012 Amy Stephen. All rights reserved.
 * @license   GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die;

if (file_exists(MOLAJO_BASE_FOLDER . '/autoloadoverride.php')) {
    include_once MOLAJO_BASE_FOLDER . '/autoloadoverride.php';
    return;
}

/** used in this script */
if (defined('MOLAJO_APPLICATIONS')) {
} else {
    define('MOLAJO_APPLICATIONS', MOLAJO_BASE_FOLDER . '/Molajo/Application');
}
if (defined('VENDOR')) {
} else {
    define('VENDOR', MOLAJO_BASE_FOLDER . '/Vendor');
}

if (defined('JPATH_SITE')) {
} else {
    define('JPATH_SITE', MOLAJO_BASE_FOLDER);
}
if (defined('JPATH_PLATFORM')) {
} else {
    define('JPATH_PLATFORM', VENDOR . '/Joomla');
}
if (defined('JPATH_LIBRARIES')) {
} else {
    define('JPATH_LIBRARIES', VENDOR . '/Joomla');
}

/** Use Symfony ClassLoader for Autoload */
require_once MOLAJO_BASE_FOLDER . '/Vendor/Symfony/Component/ClassLoader/UniversalClassLoader.php';
use Symfony\Component\ClassLoader\UniversalClassLoader;
$s = new UniversalClassLoader();

/** register the loader */
$s->register();

/** Molajo namespaces */
$s->registerNamespace('Molajo', MOLAJO_BASE_FOLDER);
$s->registerNamespace('Molajo\\Application', MOLAJO_BASE_FOLDER);
$s->registerNamespace('Molajo\\Application\\MVC', MOLAJO_BASE_FOLDER);
$s->registerNamespace('Molajo\\Application\\MVC\\Controller', MOLAJO_BASE_FOLDER);
$s->registerNamespace('Molajo\\Application\\MVC\\Model', MOLAJO_BASE_FOLDER);
$s->registerNamespace('Molajo\\Application\\MVC\\Model\\Trigger', MOLAJO_BASE_FOLDER);
$s->registerNamespace('Molajo\\Application\\MVC\\View', MOLAJO_BASE_FOLDER);
$s->registerNamespace('Molajo\\Application\\Service', MOLAJO_BASE_FOLDER);
$s->registerNamespace('Molajo\\Common', MOLAJO_BASE_FOLDER);
$s->registerNamespace('Molajo\\Extension', MOLAJO_BASE_FOLDER);
$s->registerNamespace('Molajo\\Extension\\Includer', MOLAJO_BASE_FOLDER);
$s->registerNamespace('Molajo\\Extension\\Component', MOLAJO_BASE_FOLDER);
$s->registerNamespace('Molajo\\Extension\\Formfield', MOLAJO_BASE_FOLDER);
$s->registerNamespace('Molajo\\Extension\\Helper', MOLAJO_BASE_FOLDER);
$s->registerNamespace('Molajo\\Extension\\Language', MOLAJO_BASE_FOLDER);
$s->registerNamespace('Molajo\\Extension\\Module', MOLAJO_BASE_FOLDER);
$s->registerNamespace('Molajo\\Extension\\Plugin', MOLAJO_BASE_FOLDER);
$s->registerNamespace('Molajo\\Extension\\Theme', MOLAJO_BASE_FOLDER);
$s->registerNamespace('Molajo\\Extension\\View', MOLAJO_BASE_FOLDER);

/** Symfony namespaces */
$s->registerNamespace('Symfony\\Component\\HttpFoundation', VENDOR);
$s->registerNamespace('Symfony\\Component\\HttpFoundation\\Session', VENDOR);
if (interface_exists('SessionHandlerInterface')) {
} else {
    $s->registerPrefixFallback(VENDOR . '/Symfony/Component/HttpFoundation/Resources/stubs');
}

/** Joomla namespaces */
$s->registerNamespace('Joomla', VENDOR);
$s->registerNamespace('Joomla\\crypt', VENDOR);
$s->registerNamespace('Joomla\\database', VENDOR);
$s->registerNamespace('Joomla\\database\\driver', VENDOR);
$s->registerNamespace('Joomla\\database\\exporter', VENDOR);
$s->registerNamespace('Joomla\\database\\importer', VENDOR);
$s->registerNamespace('Joomla\\database\\iterator', VENDOR);
$s->registerNamespace('Joomla\\database\\query', VENDOR);
$s->registerNamespace('Joomla\\filesystem', VENDOR);
$s->registerNamespace('Joomla\\filesystem\streams', VENDOR);
$s->registerNamespace('Joomla\\filesystem\support', VENDOR);
$s->registerNamespace('Joomla\\log', VENDOR);
$s->registerNamespace('Joomla\\object', VENDOR);
$s->registerNamespace('Joomla\\registry', VENDOR);
$s->registerNamespace('Joomla\\registry\\format', VENDOR);
$s->registerNamespace('Joomla\\string', VENDOR);
$s->registerNamespace('Joomla\\utilities', VENDOR);

/** Other */
$s->registerNamespace('Mustache', VENDOR);
$s->registerNamespace('PhpConsole', VENDOR);
$s->registerNamespace('HTMLPurifier', VENDOR);
$s->registerNamespace('HTMLPurifier\\filters', VENDOR);
$s->registerNamespace('phpmailer', VENDOR);
$s->registerNamespace('phputf8', VENDOR);
$s->registerNamespace('Simplepie', VENDOR);
if (defined('HTMPURIFIER_FILTERS')) {
} else {
	define('HTMPURIFIER_FILTERS', VENDOR.'/HTMLPurifier/filters');
}

//use PhpConsole\PhpConsole;
//PhpConsole::start(true, true, VENDOR . '/PhpConsole');
/** Joomla */
//require_once MOLAJO_BASE_FOLDER . '/Molajo/Common/platforms-joomla.php';
