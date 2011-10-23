<?php
/**
 * @package     Molajo
 * @subpackage  Other
 * @copyright   Copyright (C) 2011 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die;
/**
 *  File Helper
 */
$filehelper = new MolajoFileHelper();

/** Additional Libraries (Add as configuration options later) */
$filehelper->requireClassFile(LIBRARIES.'/akismet/Akismet.class.php', 'Akismet');
$filehelper->requireClassFile(LIBRARIES.'/mollom/mollom.php', 'Mollom');
require_once LIBRARIES.'/recaptcha/recaptchalib.php';
$filehelper->requireClassFile(LIBRARIES.'/securimage/securimage.php', 'Securimage');

/** Twig Autoload */
$filehelper->requireClassFile(MOLAJO_PATH_ROOT.'/libraries/Twig/Autoloader.php', 'Twig_Autoloader');
Twig_Autoloader::register();
 
/** @var $loader  */
//        $loader = new Twig_Loader_Filesystem(MOLAJO_LAYOUTS_EXTENSIONS);
//        $this->twig = new Twig_Environment($loader, array(
//          'cache' => MOLAJO_LAYOUTS_EXTENSIONS.'/cache',
//       ));