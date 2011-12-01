<?php
/**
 * @package     Molajo
 * @subpackage  Load Other Libraries
 * @copyright   Copyright (C) 2012 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die;
/**
 *  File Helper
 */
$filehelper = new MolajoFileHelper();
$filehelper->requireClassFile(PLATFORM . '/jplatform/simplepie/simplepie.php', 'SimplePie');

/** Twig Autoload */
//$filehelper->requireClassFile(MOLAJO_BASE_FOLDER.'/libraries/Twig/Autoloader.php', 'Twig_Autoloader');
//Twig_Autoloader::register();

/** @var $loader  */
//        $loader = new Twig_Loader_Filesystem(MOLAJO_CMS_LAYOUTS.'/extensions');
//        $this->twig = new Twig_Environment($loader, array(
//          'cache' => MOLAJO_CMS_LAYOUTS.'/extensions/cache',
//       ));


//require LIBRARIES.'/Doctrine/Common/ClassLoader.php';
//$classLoader = new \Doctrine\Common\ClassLoader('Doctrine');
//var_dump($classLoader);
//$classLoader->register();