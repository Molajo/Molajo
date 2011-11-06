<?php
/**
 * @package     Molajo
 * @subpackage  Load Other Libraries
 * @copyright   Copyright (C) 2011 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die;
/**
 *  File Helper
 */
$filehelper = new MolajoFileHelper();

$filehelper->requireClassFile(LIBRARIES.'/jplatform/simplepie/simplepie.php', 'SimplePie');

/** Additional Libraries (Add as configuration options later) */
$filehelper->requireClassFile(LIBRARIES.'/akismet/Akismet.class.php', 'Akismet');
require_once LIBRARIES.'/recaptcha/recaptchalib.php';

/** Twig Autoload */
$filehelper->requireClassFile(MOLAJO_BASE_FOLDER.'/libraries/Twig/Autoloader.php', 'Twig_Autoloader');
Twig_Autoloader::register();
 
/** @var $loader  */
//        $loader = new Twig_Loader_Filesystem(MOLAJO_EXTENSION_LAYOUTS.'/extensions');
//        $this->twig = new Twig_Environment($loader, array(
//          'cache' => MOLAJO_EXTENSION_LAYOUTS.'/extensions/cache',
//       ));


//require LIBRARIES.'/Doctrine/Common/ClassLoader.php';
//$classLoader = new \Doctrine\Common\ClassLoader('Doctrine');
//var_dump($classLoader);
//$classLoader->register();