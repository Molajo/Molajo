<?php
/**
 * @package     Molajo
 * @subpackage  Install
 * @copyright   Copyright (C) 2011 Chris Rault. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die;

include_once dirname(__FILE__).'/browser.php';

if ($lcbrowser == 'internetexplorer') {
	include_once dirname(__FILE__).'/PIE.php';
}

$browser 	= new MBrowser();
$thebrowser = preg_replace("/[^A-Za-z]/i", "", $browser->getBrowser());
$ver 		= $browser->getVersion();
$dots 		= ".";
$dashes 	= "";
$mod_chrome	= "";
$ver 		= str_replace($dots , $dashes , $ver);
$lcbrowser 	= strtolower($thebrowser);

$layout = JRequest::getCmd('layout', 'installer_step1');        
if ($layout == 'installer_step1') {
    $stepNumber = 1;
    
} elseif ($layout == 'installer_step2') {
    $stepNumber = 2;
    
} elseif ($layout == 'installer_step3') {
    $stepNumber = 3;
    
} elseif ($layout == 'installer_step4') {
    $stepNumber = 4;
}  

$version = '1.0';