<?php

/**
 * File			incoming-data.php
 * Created		12/2/11 7:41 PM
 * Author		Matt Thomas matt@betweenbrain.com
 * Copyright	Copyright (C) 2011 betweenbrain llc.
 * Description	Mock incoming data for testing and development purposes.
 */

// Gotta define Mo'
define('MOLAJO', 'Long Live Molajo!');

// Mock data
$userGroup['public'] = array(
	"login"  => 1,
	"create" => 0,
	"view"   => 1,
	"edit"   => 0,
	"delete" => 0,
	"admin"  => 0,
);

$userGroup['guest'] = array(
	"login"  => 0,
	"create" => 0,
	"view"   => 1,
	"edit"   => 0,
	"delete" => 0,
	"admin"  => 0
);

$userGroup['registered'] = array(
	"login"  => 1,
	"create" => 1,
	"view"   => 1,
	"edit"   => 1,
	"delete" => 0,
	"admin"  => 0
);

$userGroup['administrator'] = array(
	"login"  => 1,
	"create" => 1,
	"view"   => 1,
	"edit"   => 1,
	"delete" => 1,
	"admin"  => 1
);