<?php
$base = substr(__DIR__, 0, strlen(__DIR__) - 12);

require $base . '/vendor/phpunit/php-code-coverage/PHP/CodeCoverage/Autoload.php';

$coverage = new PHP_CodeCoverage;
$coverage->start('Molajo');

// ...

$coverage->stop();

$writer = new PHP_CodeCoverage_Report_Clover;
$writer->process($coverage, __DIR__ . '/clover.xml');

$writer = new PHP_CodeCoverage_Report_HTML;
$writer->process($coverage, __DIR__ . '/code-coverage-report');
