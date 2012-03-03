<?php
/**
 * @package     Molajo
 * @copyright   Copyright (C) 2012 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die;

require_once MOLAJO_BASE_FOLDER . '/Vendor/Symfony/Component/ClassLoader/UniversalClassLoader.php';
use Symfony\Component\ClassLoader\UniversalClassLoader;
$s = new UniversalClassLoader();

/** register the loader */
$s->register();

/** Molajo namespaces */
$s->registerNamespace('Molajo', MOLAJO_BASE_FOLDER);
$s->registerNamespace('Molajo\\Application', MOLAJO_BASE_FOLDER);
$s->registerNamespace('Molajo\\Application\\Helper', MOLAJO_BASE_FOLDER);
$s->registerNamespace('Molajo\\Application\\MVC\\Controller', MOLAJO_BASE_FOLDER);
$s->registerNamespace('Molajo\\Application\\MVC\\Model', MOLAJO_BASE_FOLDER);
$s->registerNamespace('Molajo\\Application\\MVC\\View', MOLAJO_BASE_FOLDER);
$s->registerNamespace('Molajo\\Application\\Service', MOLAJO_BASE_FOLDER);
$s->registerNamespace('Molajo\\Common', MOLAJO_BASE_FOLDER);
$s->registerNamespace('Molajo\\Extension\\Component', MOLAJO_BASE_FOLDER);
$s->registerNamespace('Molajo\\Extension\\Formfield', MOLAJO_BASE_FOLDER);
$s->registerNamespace('Molajo\\Extension\\Helper', MOLAJO_BASE_FOLDER);
$s->registerNamespace('Molajo\\Extension\\Language', MOLAJO_BASE_FOLDER);
$s->registerNamespace('Molajo\\Extension\\Module', MOLAJO_BASE_FOLDER);
$s->registerNamespace('Molajo\\Extension\\Plugin', MOLAJO_BASE_FOLDER);
$s->registerNamespace('Molajo\\Extension\\Renderer', MOLAJO_BASE_FOLDER);
$s->registerNamespace('Molajo\\Extension\\Theme', MOLAJO_BASE_FOLDER);
$s->registerNamespace('Molajo\\Extension\\View', MOLAJO_BASE_FOLDER);

/** Symfony namespaces */
$s->registerNamespace('Symfony\\Component\\HttpFoundation', MOLAJO_BASE_FOLDER . '/Vendor');
$s->registerNamespace('Symfony\\Component\\HttpFoundation\\Session', MOLAJO_BASE_FOLDER . '/Vendor');
if (interface_exists('SessionHandlerInterface')) {
} else {
    $s->registerPrefixFallback(MOLAJO_BASE_FOLDER . '/Vendor/Symfony/Component/HttpFoundation/Resources/stubs');
}

if (defined('VENDOR')) {
} else {
    define('VENDOR', MOLAJO_BASE_FOLDER . '/Vendor');
}

/** Not namedspaced */
//require_once __DIR__ . '/platforms-joomla.php';
require_once VENDOR . '/HTMLPurifier/HTMLPurifier.standalone.php';
require_once VENDOR . '/Mustache/Mustache.php';
require_once VENDOR . '/Simplepie/simplepie.php';
require_once VENDOR . '/PhpConsole/PhpConsole.php';
//PhpConsole::start(true, true, VENDOR . '/PhpConsole');

return;

abstract class JFactory extends Base
{
}

abstract class JError
{
    static $legacy = false;
}

class Registry extends JRegistry
{
}

