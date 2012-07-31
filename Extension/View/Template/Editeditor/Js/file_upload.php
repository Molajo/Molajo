<?php

// This is a simplified example, which doesn't cover security of uploaded files.
// This example just demonstrate the logic behind the process.

echo SITES_MEDIA_URL.$_FILES['file']['name'];
die;
copy($_FILES['file']['tmp_name'], SITES_MEDIA_URL.$_FILES['file']['name']);

$array = array(
    'filelink' => SITES_MEDIA_URL.$_FILES['file']['name'],
    'filename' => $_FILES['file']['name']
);

echo stripslashes(json_encode($array));
