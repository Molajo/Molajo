<?php
require_once __DIR__.'/platforms/symfony/class-loader/Symfony/Component/ClassLoader/UniversalClassLoader.php';

use Symfony\Component\ClassLoader\UniversalClassLoader;

$loader = new UniversalClassLoader();
$loader->register();

$loader->registerNamespace('Symfony\\Component\\HttpFoundation', __DIR__.'/platforms/symfony/http-foundation');
