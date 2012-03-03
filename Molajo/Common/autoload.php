<?php
/**
 * @package     Molajo
 * @copyright   Copyright (C) 2012 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
namespace Molajo\Common;

defined('MOLAJO') or die;

/** Molajo */
require_once __DIR__ . '/SplClassLoader.php';

$molajoLoader = new SplClassLoader();

$molajoLoader->register('Molajo\\Application', MOLAJO_BASE_FOLDER);
$molajoLoader->register('Molajo\\Application\\Helper', MOLAJO_BASE_FOLDER);
$molajoLoader->register('Molajo\\Application\\MVC\\Controller', MOLAJO_BASE_FOLDER);
$molajoLoader->register('Molajo\\Application\\MVC\\Model', MOLAJO_BASE_FOLDER);
$molajoLoader->register('Molajo\\Application\\MVC\\View', MOLAJO_BASE_FOLDER);
$molajoLoader->register('Molajo\\Common', MOLAJO_BASE_FOLDER);
$molajoLoader->register('Molajo\\Extension\\Component', MOLAJO_BASE_FOLDER);
$molajoLoader->register('Molajo\\Extension\\Formfield', MOLAJO_BASE_FOLDER);
$molajoLoader->register('Molajo\\Extension\\Helper', MOLAJO_BASE_FOLDER);
$molajoLoader->register('Molajo\\Extension\\Language', MOLAJO_BASE_FOLDER);
$molajoLoader->register('Molajo\\Extension\\Module', MOLAJO_BASE_FOLDER);
$molajoLoader->register('Molajo\\Extension\\Plugin', MOLAJO_BASE_FOLDER);
$molajoLoader->register('Molajo\\Extension\\Renderer', MOLAJO_BASE_FOLDER);
$molajoLoader->register('Molajo\\Extension\\Theme', MOLAJO_BASE_FOLDER);
$molajoLoader->register('Molajo\\Extension\\View', MOLAJO_BASE_FOLDER);

/** Symfony */
require_once MOLAJO_BASE_FOLDER . '/Vendor/Symfony/Component/ClassLoader/UniversalClassLoader.php';
use Symfony\Component\ClassLoader\UniversalClassLoader;
$symfonyLoader = new UniversalClassLoader();

$symfonyLoader->register();
$symfonyLoader->register('Symfony\\Component\\HttpFoundation', MOLAJO_BASE_FOLDER . '/Vendor');
$symfonyLoader->register('Symfony\\Component\\HttpFoundation\\Session', MOLAJO_BASE_FOLDER . '/Vendor');
if (interface_exists('SessionHandlerInterface')) {
} else {
    $symfonyLoader->registerPrefixFallback(MOLAJO_BASE_FOLDER . '/Vendor/Symfony/Component/HttpFoundation/Resources/stubs');
}

