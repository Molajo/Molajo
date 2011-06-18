<?php
/**
 * @version		$Id: router.php 20196 2011-01-09 02:40:25Z ian $
 * @package		Joomla.Site
 * @subpackage	Application
 * @copyright	Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access
defined('JPATH_BASE') or die;

/**
 * Class to create and parse routes for the site application
 *
 * @package		Joomla.Site
 * @subpackage	Application
 * @since		1.5
 */
class JRouterSite extends JRouter
{	
	protected $componentRouters = array();
	
	protected $routerMethod;
	
	protected $routerClass;

	public function getComponentRouter($component, $functionName = 'build')
	{
		if(isset($this->componentRouters[$component])) {
			if(is_object($this->componentRouters[$component])) {
				return array($this->componentRouters[$component], $functionName);
			} elseif(is_string($this->componentRouters[$component])) {
				return $this->componentRouters[$component].$functionName.'Route';
			} else {
				return 'JRouterDummyRouter';
			}
		}
		$compname = ucfirst(substr($component, 4));
		if(!class_exists($compname.'Router')) {
			// Use the component routing handler if it exists
			$path = JPATH_SITE.'/components/'.$component.'/router.php';

			// Use the custom routing handler if it exists
			if (file_exists($path)) {
				require_once $path;
				if(!class_exists($compname.'Router')) {
					$this->componentRouters[$component] = $compname; 
				}
			} else {
				$this->componentRouters[$component] = false;
			}
		}
		if(class_exists($compname.'Router')) {
			$name = $compname.'Router';
			$this->componentRouters[$component] = new $name();
		}
		if(is_object($this->componentRouters[$component])) {
			return array($this->componentRouters[$component], $functionName);
		} elseif(is_string($this->componentRouters[$component])) {
			return $this->componentRouters[$component].$functionName.'Route';
		} else {
			return 'JRouterDummyRouter';
		}
	}
	
	public function setComponentRouter($component, $router)
	{
		$this->componentRouters[$component] = $router;
	}
}

function JRouterDummyRouter(&$query)
{
	return array();
}

/**
 * Class to create and parse routes
 *
 * @package		Joomla
 * @since		1.5
 */
class JRouterAdministrator extends JRouter
{
}