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
$fileHelper = new MolajoFileHelper();
$fileHelper->requireClassFile(PLATFORM . '/jplatform/simplepie/simplepie.php', 'SimplePie');

/**
 *	Twig
 */
require_once TWIG . '/Autoloader.php';
Twig_Autoloader::register();

//$loader = new Twig_Loader_String();
//$twig = new Twig_Environment($loader);

/**
 *  Github 
 */
require_once GITHUB . '/Autoloader.php';
Github_Autoloader::register();

/** Twig Autoload */
//$fileHelper->requireClassFile(MOLAJO_BASE_FOLDER.'/libraries/Twig/Autoloader.php', 'Twig_Autoloader');
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

//$github = new Github_Client();
//Search for users by username

//$users = $github->getUserApi()->search('amy');
//echo '<pre>';var_dump($users);echo '</pre>';
//die;
//  Get information about a user

//$user = $github->getUserApi()->show('ornicar');

//Update user informations

//    $github->getUserApi()->update('ornicar', array('location' => 'France', 'blog' => 'http://diem-project.org/blog'));

// Returns an array of information about the user.
//Get users that a specific user is following

//    $users = $github->getUserApi()->getFollowing('ornicar');
//    $users = $github->getUserApi()->getFollowers('ornicar');

//Make the authenticated user follow a user. Requires authentication.

//    $github->getUserApi()->follow('symfony');
/**
Returns an array of followed users.
Unfollow a user

Make the authenticated user unfollow a user. Requires authentication.

    $github->getUserApi()->unFollow('symfony');

Returns an array of followed users.
Get repos that a specific user is watching

    $users = $github->getUserApi()->getWatchedRepos('ornicar');

Returns an array of watched repos.
Get the authenticated user emails

    $emails = $github->getUserApi()->getEmails();

Returns an array of the authenticated user emails. Requires authentication.
Add an email to the authenticated user

    $github->getUserApi()->addEmail('my-email@provider.org');

Returns an array of the authenticated user emails. Requires authentication.
Remove an email from the authenticated user

    $github->getUserApi()->removeEmail('my-email@provider.org');
 */

