<?php

include_once 'MySQLWorkbenchXML.class.php';
include_once 'MySQLWorkbench.class.php';

// Download my sample mwb-file from http://www.query4u.de/showcase/tests/Workbench/user.mwb
// or create your own sample by using mysql workbench.
// The mwb-file is not part of this sample, because phpclasses does allow to upload mwb-files.


$user = new MySQLWorkbench("user", dirname(__FILE__));
$user->run();

echo $user->render();
die();