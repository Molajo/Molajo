<?php
/**
 * @package     Molajo
 * @subpackage  Symfony
 * @copyright   Copyright (C) 2012 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 *
 * The Symfony Event Dispatcher is part of the symfony framework and released under the MIT license.
 * https://github.com/fabpot/event-dispatcher
 */
defined('MOLAJO') or die;
require_once PLATFORMS . '/sfEvent' . '/sfEventDispatcher.php';
require_once PLATFORMS.'/ClassLoader/UniversalClassLoader.php';
use Symfony\Component\ClassLoader\UniversalClassLoader;

$loader = new UniversalClassLoader();
$loader->registerNamespaces(array(
    'Symfony\\Component\\HttpFoundation' =>__DIR__.'/platforms/HttpFoundation'
));

if (!interface_exists('SessionHandlerInterface', false)) {
    $loader->registerPrefix('SessionHandlerInterface', __DIR__.'/platforms/HttpFoundation/Resources/stubs');
}
$loader->register();

require_once PLATFORMS .'/HttpFoundation/Request.php';
require_once PLATFORMS .'/HttpFoundation/ParameterBag.php';
require_once PLATFORMS .'/HttpFoundation/Response.php';
require_once PLATFORMS .'/HttpFoundation/Cookie.php';
require_once PLATFORMS .'/HttpFoundation/FileBag.php';
require_once PLATFORMS .'/HttpFoundation/HeaderBag.php';
require_once PLATFORMS .'/HttpFoundation/RedirectResponse.php';
require_once PLATFORMS .'/HttpFoundation/RequestMatcherInterface.php';
require_once PLATFORMS .'/HttpFoundation/RequestMatcher.php';
require_once PLATFORMS .'/HttpFoundation/ResponseHeaderBag.php';
require_once PLATFORMS .'/HttpFoundation/ServerBag.php';
require_once PLATFORMS .'/HttpFoundation/StreamedResponse.php';
require_once PLATFORMS .'/HttpFoundation/ApacheRequest.php';
