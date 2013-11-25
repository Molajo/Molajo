<?php
/**
 * Foundation
 *
 * @package    Molajo
 * @copyright  2013 Amy Stephen. All rights reserved.
 * @license    MIT
 */
if (substr($_SERVER['DOCUMENT_ROOT'], - 1) == '/') {
    define('ROOT_FOLDER', $_SERVER['DOCUMENT_ROOT']);
} else {
    define('ROOT_FOLDER', $_SERVER['DOCUMENT_ROOT'] . '/');
}

$base = substr(__DIR__, 0, strlen(__DIR__) - 5);

define('BASE_FOLDER', $base);

$classMap = array(
    'Molajo\\Application'                                      => BASE_FOLDER . '/Application.php',
    'Molajo\\Controller'                                       => BASE_FOLDER . '/Frontcontroller.php',
    'Molajo\\Site'                                             => BASE_FOLDER . '/Site.php',
    'Molajo\\CommonApi\\ApplicationInterface'                        => BASE_FOLDER . '/Api/ApplicationInterface.php',
    'Molajo\\CommonApi\\ExceptionInterface'                          => BASE_FOLDER . '/Api/ExceptionInterface.php',
    'Molajo\\CommonApi\\FrontcontrollerInterface'                    => BASE_FOLDER . '/Api/FrontcontrollerInterface.php',
    'Molajo\\CommonApi\\SiteInterface'                               => BASE_FOLDER . '/Api/SiteInterface.php',
    'Molajo\\Application\\Exception\\ApplicationException'     => BASE_FOLDER . '/Exception/ApplicationException.php',
    'Molajo\\Application\\Exception\\ErrorThrownAsException'   => BASE_FOLDER . '/Exception/ErrorThrownAsException.php',
    'Molajo\\Application\\Exception\\Exceptions'               => BASE_FOLDER . '/Exception/Exceptions.php',
    'Molajo\\Application\\Exception\\FrontcontrollerException' => BASE_FOLDER . '/Exception/FrontcontrollerException.php',
    'Molajo\\Application\\Exception\\SiteException'            => BASE_FOLDER . '/Exception/SiteException.php'
);

spl_autoload_register(
    function ($class) use ($classMap) {
        if (array_key_exists($class, $classMap)) {
            require_once $classMap[$class];
        }
    }
);
