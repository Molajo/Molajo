<?php
include_once('browser.php');
if ($lcbrowser == 'internetexplorer'){ 
	include_once('PIE.php');
}
$browser 	= new MBrowser();
$thebrowser = preg_replace("/[^A-Za-z]/i", "", $browser->getBrowser());
$ver 		= $browser->getVersion();
$dots 		= ".";
$dashes 	= "";
$mod_chrome	= "";
$ver 		= str_replace($dots , $dashes , $ver);
$lcbrowser 	= strtolower($thebrowser);
