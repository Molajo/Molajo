<?php
/**
 * Bootstrap for Testing
 *
 * @package    Molajo
 * @copyright  2014 Amy Stephen. All rights reserved.
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 */
$base = substr(__DIR__, 0, strlen(__DIR__) - 5);
if (function_exists('CreateClassMap')) {
} else {
    include_once __DIR__ . '/CreateClassMap.php';
}
include_once $base . '/vendor/autoload.php';

$classmap = array();
$results  = createClassMap($base . '/Source/Plugins/Author', 'Molajo\\Plugins\\Author\\');
$classmap = array_merge($classmap, $results);
$results  = createClassMap($base . '/Source/Plugins/Blockquote', 'Molajo\\Plugins\\Blockquote\\');
$classmap = array_merge($classmap, $results);
$results  = createClassMap($base . '/Source/Plugins/Comments', 'Molajo\\Plugins\\Comments\\');
$classmap = array_merge($classmap, $results);
$results  = createClassMap($base . '/Source/Plugins/Copyright', 'Molajo\\Plugins\\Copyright\\');
$classmap = array_merge($classmap, $results);
$results  = createClassMap($base . '/Source/Plugins/Dateformats', 'Molajo\\Plugins\\Dateformats\\');
$classmap = array_merge($classmap, $results);
$results  = createClassMap($base . '/Source/Plugins/Email', 'Molajo\\Plugins\\Email\\');
$classmap = array_merge($classmap, $results);
$results  = createClassMap($base . '/Source/Plugins/Fullname', 'Molajo\\Plugins\\Fullname\\');
$classmap = array_merge($classmap, $results);
$results  = createClassMap($base . '/Source/Plugins/Linebreaks', 'Molajo\\Plugins\\Linebreaks\\');
$classmap = array_merge($classmap, $results);
$results  = createClassMap($base . '/Source/Plugins/Links', 'Molajo\\Plugins\\Links\\');
$classmap = array_merge($classmap, $results);
$results  = createClassMap($base . '/Source/Plugins/Messages', 'Molajo\\Plugins\\Messages\\');
$classmap = array_merge($classmap, $results);
$results  = createClassMap($base . '/Source/Plugins/Readmore', 'Molajo\\Plugins\\Readmore\\');
$classmap = array_merge($classmap, $results);
$results  = createClassMap($base . '/Source/Plugins/Smilies', 'Molajo\\Plugins\\Smilies\\');
$classmap = array_merge($classmap, $results);
$results  = createClassMap($base . '/Source/Plugins/Snippet', 'Molajo\\Plugins\\Snippet\\');
$classmap = array_merge($classmap, $results);
ksort($classmap);

spl_autoload_register(
    function ($class) use ($classmap) {
        if (array_key_exists($class, $classmap)) {
            require_once $classmap[$class];
        }
    }
);

