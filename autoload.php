<?php
require_once __DIR__ . '/platforms/symfony/class-loader/Symfony/Component/ClassLoader/UniversalClassLoader.php';
use Symfony\Component\ClassLoader\UniversalClassLoader;
$loader = new UniversalClassLoader();
$loader->register();
$loader->registerNamespace('Symfony\\Component\\HttpFoundation', __DIR__ . '/platforms/symfony/http-foundation');
$loader->registerNamespace('Symfony\\Component\\HttpFoundation\\Session', __DIR__ . '/platforms/symfony/http-foundation');
if (interface_exists('SessionHandlerInterface')) {
} else {
    $loader->registerPrefixFallback(__DIR__ . '/platforms/Symfony/http-foundation/Symfony/Component/HttpFoundation/Resources/stubs');
}
