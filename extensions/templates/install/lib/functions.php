<?php
/**
 * @package     Molajo
 * @subpackage  Install
 * @copyright   Copyright (C) 2012 Chris Rault. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die;

include_once dirname(__FILE__) . '/browser.php';

$browser = new MBrowser();
$thebrowser = preg_replace("/[^A-Za-z]/i", "", $browser->getBrowser());
$ver = $browser->getVersion();
$dots = ".";
$dashes = "";
$chrome = "";
$ver = str_replace($dots, $dashes, $ver);
$lcbrowser = strtolower($thebrowser);

if ($lcbrowser == 'internetexplorer') {
    include_once dirname(__FILE__) . '/PIE.php';
}

$view = JRequest::getCmd('view', 'step1');
if ($view == 'step1') {
    $stepNumber = 1;

} elseif ($view == 'step2') {
    $stepNumber = 2;

} elseif ($view == 'step3') {
    $stepNumber = 3;

} elseif ($view == 'step4') {
    $stepNumber = 4;
}

$version = '1.0';
$configHTML = true;