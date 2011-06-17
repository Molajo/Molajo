<?php
/**
 * @package     Molajo
 * @subpackage  Molajo System Plugin
 * @copyright   Copyright (C) 2010-2011 individual Molajo Contributors. All rights reserved. See http://Molajo.org/Copyright
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined( '_JEXEC' ) or die( 'Restricted access' );

JLoader::register('JComponentRouter', JPATH_PLUGINS.'/system/molajorouter/libraries/joomla/application/component/router.php');
/**
 * Molajo System Plugin
 */
class plgSystemMolajoRouter extends JPlugin
{
	function onAfterInitialise()
	{
		JLoader::register('JText', JPATH_PLUGINS.'/system/molajorouter/libraries/joomla/methods.php');
		JLoader::register('JRoute', JPATH_PLUGINS.'/system/molajorouter/libraries/joomla/methods.php');
		JLoader::import('joomla.application.router', JPATH_PLUGINS.'/system/molajorouter/libraries/');
		JLoader::register('JRouterSite', JPATH_PLUGINS.'/system/molajorouter/includes/router.php');
		JLoader::register('JRouterAdministrator', JPATH_PLUGINS.'/system/molajorouter/includes/router.php');
		
		$app = JFactory::getApplication();
		if($app->isSite()) {
			$router = $app->getRouter();
			$router->attachBuildRule(array('plgSystemMolajoRouter', 'processItemID'));
			if(in_array('force_ssl', $app->getCfg('sef_rules', array()))) {
				$router->attachParseRule(array('plgSystemMolajoRouter', 'forceSSL'));
			}
			$router->attachParseRule(array('plgSystemMolajoRouter', 'cleanupPath'));
			if(in_array('sef', $app->getCfg('sef_rules', array()))) {
				$router->attachBuildRule(array('plgSystemMolajoRouter', 'buildSEF'));
				$router->attachParseRule(array('plgSystemMolajoRouter', 'parseSEF'));
			}
			$router->attachParseRule(array('plgSystemMolajoRouter', 'parseRAW'));
		}
	}
	
	function onContentPrepareForm(JForm $form, $data)
	{
		if($form->getName() == 'com_config.application') {
			$this->loadLanguage();
			$form->addFieldPath(JPATH_PLUGINS.'/system/molajorouter/fields');
			$xml = '<form><fieldset name="seo">
				<field name="sef_rules" type="sefrules" default="0" label="COM_CONFIG_FIELD_SEF_RULES_LABEL" description="COM_CONFIG_FIELD_SEF_RULES_DESC"/>
				<field name="sef_component_rules" type="checkboxes" label="COM_CONFIG_FIELD_SEF_COMPONENT_RULES_LABEL" description="COM_CONFIG_FIELD_SEF_COMPONENT_RULES_DESC">';
			$app = JFactory::getApplication();
			$results = $app->triggerEvent('onComponentRouterRules');
			foreach($results as $rules) {
				foreach($rules as $rule) {
					$xml .= '<option value="'.$rule.'">COM_CONFIG_FIELD_SEF_COMPONENT_ROUTER_'.strtoupper($rule).'</option>';
				}
			}
			$xml .= '</field>
				<field type="hidden" name="sef" value="0" />
			</fieldset></form>';
			$form->load($xml);
			$form->removeField('sef');
			$form->removeField('sef_rewrite');
			$form->removeField('sef_suffix');
			$form->removeField('unicodeslugs');
		}
	}
	
	function onRouterRules()
	{
		$this->loadLanguage();
		return array('sef', 'force_ssl', 'sef_rewrite', 'sef_suffix', 'unicodeslugs');
	}
	
	function onComponentRouterRules($router = false)
	{
		if(!$router) {
			return array('joomla', 'joomla_advanced');
		}
		$cfg = JFactory::getApplication()->getCfg('sef_component_rules', array());
		if(in_array('joomla', $cfg) && !in_array('joomla_advanced', $cfg)) {
			$router->attachBuildRule(array('plgSystemMolajoRouter', 'buildComponentSEF'));
			$router->attachParseRule(array('plgSystemMolajoRouter', 'parseComponentSEF'));
		}
		if(!in_array('joomla', $cfg) && in_array('joomla_advanced', $cfg)) {
			$router->attachBuildRule(array('plgSystemMolajoRouter', 'buildComponentSEFAdvanced'));
			$router->attachParseRule(array('plgSystemMolajoRouter', 'parseComponentSEFAdvanced'));
		}
	}
	
	/**
	 * Function to attach the correct ItemID to the URL 
	 * and do some general processing
	 * 
	 * @param JRouter calling JRouter object
	 * @param JURI URL to be processed
	 */
	public static function processItemID(&$router, &$uri)
	{
		// Set URI defaults
		$app	= JFactory::getApplication();
		$menu	= $app->getMenu();

		// Get the itemid form the URI
		$itemid = $uri->getVar('Itemid');

		if(!$itemid && !$uri->getVar('option') && is_null($uri->getPath())) {
			$uri->setQuery(array_merge($router->getVars(),$uri->getQuery(true)));
		}
		
		if (!$uri->getVar('option')) {
			if ($item = $menu->getItem($itemid)) {
					$uri->setVar('option', $item->component);
			}
		}
	}

	/**
	 * Function to build the SEF URL 
	 * 
	 * @param JRouter calling JRouter object
	 * @param JURI URL to be processed
	 */
	public static function buildSEF(&$router, &$uri)
	{
		$app	= JFactory::getApplication();
		$menu	= $app->getMenu();
	
		// Make sure any menu vars are used if no others are specified
		if ($uri->getVar('Itemid') && count($uri->getQuery(true)) == 2) {

			// Get the active menu item
			$itemid = $uri->getVar('Itemid');
			$item = $menu->getItem($itemid);

			if ($item) {
				$uri->setQuery($item->query);
			}
			$uri->setVar('Itemid', $itemid);
		}
		
		$option = $uri->getVar('option');
		if (!$option) {
			return;
		}
		
		$query = $uri->getQuery(true);
		
		/*
		 * Build the component route
		 */
		$component	= preg_replace('/[^A-Z0-9_\.-]/i', '', $option);
		$tmp		= '';
		$function	= $app->getRouter()->getComponentRouter($component);
		$parts		= call_user_func_array($function, array(&$query));

		// encode the route segments
		$parts = self::_encodeSegments($parts);

		$result = implode('/', $parts);
		if ($router->getOptions('sef_suffix', 0) && !(substr($result, -9) == 'index.php' || substr($result, -1) == '/')) {
			if ($format = $uri->getVar('format', 'html')) {
				$result .= '.'.$format;
				$uri->delVar('format');
			}
		}

		$tmp	= ($result != "") ? $result : '';

		/*
		 * Build the application route
		 */
		$built = false;
		if (isset($query['Itemid']) && !empty($query['Itemid'])) {
			$item = $menu->getItem($query['Itemid']);
			if (is_object($item) && $query['option'] == $item->component) {
				if (!$item->home || $item->language!='*') {
					$tmp = !empty($tmp) ? $item->route.'/'.$tmp : $item->route;
				}
				$built = true;
			}
		}

		if (!$built) {
			$tmp = 'component/'.substr($query['option'], 4).'/'.$tmp;
		}
		
		if (!$router->getOptions('sef_rewrite', 0)) {
			//Transform the route
			$result = 'index.php/'.$result;
		}
		//$route .= '/'.$tmp;

		// Unset unneeded query information
		if (isset($item) && $query['option'] == $item->component) {
			unset($query['Itemid']);
		}
		unset($query['option']);

		//Set query again in the URI
		$uri->setQuery($query);
		$uri->setPath($tmp);
				
		if ($limitstart = $uri->getVar('limitstart')) {
			$uri->setVar('start', (int) $limitstart);
			$uri->delVar('limitstart');
		}
	}
	
	public static function forceSSL($router, $uri)
	{
		if (strtolower($uri->getScheme()) != 'https') {
			//forward to https
			$uri->setScheme('https');
			JFactory::getApplication()->redirect((string)$uri);
		}
		return array();
	}
	
	public static function cleanupPath($router, $uri)
	{
		// Get the path
		$path = $uri->getPath();

		//Remove basepath
		$path = substr_replace($path, '', 0, strlen(JURI::base(true)));

		//Remove prefix
		$path = str_replace('index.php', '', $path);

		//Set the route
		$uri->setPath(trim($path , '/'));
	}
	
	public static function parseSEF($router, $uri)
	{
		$app	= JFactory::getApplication();
		$menu	= $app->getMenu();
		$route	= $uri->getPath();

		// Get the variables from the uri
		$vars = $uri->getQuery(true);

		// Handle an empty URL (special case)
		if (empty($route)) {
			return true;
		}
		
		/*
		 * Parse the application route
		 */
		$segments	= explode('/', $route);
		if (count($segments) > 1 && $segments[0] == 'component') {
			$uri->setvar('option','com_'.$segments[1]);
			$uri->setvar('Itemid', null);
			$route = implode('/', array_slice($segments, 2));
		} else {
			//Need to reverse the array (highest sublevels first)
			$items = array_reverse($menu->getMenu());

			$found = false;
			$route_lowercase = JString::strtolower($route);

			foreach ($items as $item) {
				$length = strlen($item->route); //get the lenght of the route

				if ($length > 0 && strpos($route.'/', $item->route.'/') === 0 && $item->type != 'menulink') {
					$route = substr($route, $length);

					$uri->setVar('Itemid', $item->id);
					$uri->setVar('option',$item->component);
					$menu->setActive($item->id);
					$found = true;
					break;
				}
			}
			if (!$found)
			{
				$item = $menu->getDefault(JFactory::getLanguage()->getTag());
			}
			$vars['Itemid'] = $item->id;
			$vars['option'] = $item->component;
		}

		/*
		 * Parse the component route
		 */
		if (!empty($route) && $uri->getVar('option')) {
			$segments = explode('/', $route);
			if (empty($segments[0])) {
				array_shift($segments);
			}

			// Handle component	route
			$component = preg_replace('/[^A-Z0-9_\.-]/i', '', $uri->getVar('option'));
			$router = JFactory::getApplication()->getRouter();
			$function = $router->getComponentRouter($component, 'Parse');

			if(is_string($function)) {
				//decode the route segments
				$segments = self::_decodeSegments($segments);
			}

			$uri->setQuery(array_merge($uri->getQuery(true),call_user_func($function, $segments)));
		}

		$uri->setQuery(array_merge($uri->getQuery(true), $vars));

		if ($start = $uri->getVar('start')) {
			$uri->delVar('start');
			$uri->setVar('limitstart', $start);
		}

		// Get the path
		$path = $uri->getPath();

		//Remove the suffix
		/**
			if ($app->getCfg('sef_suffix') && !(substr($path, -9) == 'index.php' || substr($path, -1) == '/')) {
				if ($suffix = pathinfo($path, PATHINFO_EXTENSION)) {
					$path = str_replace('.'.$suffix, '', $path);
					$vars['format'] = $suffix;
				}
			}
		**/
		
		JRequest::set($uri->getQuery(true));

		return true;
	}
	
	public static function parseRAW($router, $uri)
	{
		$vars	= array();
		$app	= JFactory::getApplication();
		$menu	= $app->getMenu();

		//Handle an empty URL (special case)
		if (!$uri->getVar('Itemid')) {
			$item = $menu->getDefault(JFactory::getLanguage()->getTag());
			if (!is_object($item)) {
				// No default item set
				return true;
			}
			$uri->setVar('Itemid', $item->id);		
		} else {
			$item = $menu->getItem($uri->getVar('Itemid'));
		}

		//Set the information in the request
		$uri->setQuery(array_merge($item->query, $uri->getQuery(true)));

		// Set the active menu item
		$menu->setActive($item->id);

		return true;
	}
	
	/**
	 * Encode route segments
	 *
	 * @param	array	An array of route segments
	 * @return  array
	 */
	protected static function _encodeSegments($segments)
	{
		$total = count($segments);
		for ($i=0; $i<$total; $i++) {
			$segments[$i] = str_replace(':', '-', $segments[$i]);
		}

		return $segments;
	}

	/**
	 * Decode route segments
	 *
	 * @param	array	An array of route segments
	 * @return  array
	 */
	protected static function _decodeSegments($segments)
	{
		$total = count($segments);
		for ($i=0; $i<$total; $i++)  {
			$segments[$i] = preg_replace('/-/', ':', $segments[$i], 1);
		}

		return $segments;
	}
	
	public static function buildComponentSEF($crouter, $query, $segments)
	{
		if(!isset($query['Itemid'])) {
			$segments[] = $query['view'];
			$views = $crouter->getViews();
			if(isset($views[$query['view']]->id)) {
				$segments[] = $query[$views[$query['view']]->id];
				unset($query[$views[$query['view']]->id]);
			}
			unset($query['view']);
			unset($query['ts']);
			return;
		}
		$menu = JFactory::getApplication()->getMenu();
		$item = $menu->getItem($query['Itemid']);
		
		$views = $crouter->getViews();
		
		if(isset($item->query['view']) && $item->query['view'] == $query['view']) {
			$view = $views[$query['view']];
			if(isset($item->query[$view->id]) && $item->query[$view->id] == (int) $query[$view->id]) {
				unset($query[$view->id]);
				$view = $view->parent;
				while($view) {
					unset($query[$view->child_id]);
					$view = $view->parent;
				}
				unset($query['view']);
				unset($query['ts']);
				unset($query['layout']);
				return array();
			}
		} 
		
		$path = array_reverse($crouter->getPath($query));
		$found = false;
		$found2 = true;
		for($i = 0, $j = count($path); $i < $j; $i++) {
			reset($path);
			$view = key($path);
			if($found) {
				$ids = array_shift($path);
				if($views[$view]->nestable) {
					foreach(array_reverse($ids) as $id) {
						if($found2) {
							$segments[] = $id;
						} else {
							if($item->query[$views[$view]->id] == (int) $id) {
								$found2 = true;
							}
						}
					}
				} else {
					if(is_bool($ids)) {
						$segments[] = $views[$view]->title; 
					} else {
						$segments[] = $ids[0];
					}
				}
			} else {
				if($item->query['view'] != $view) {
					array_shift($path);
				} else {
					if(!$views[$view]->nestable) {
						array_shift($path);
					} else {
						$i--;
						$found2 = false;
					}
					$found = true;
				}
			}
			unset($query[$views[$view]->child_id]);
		}
		if(isset($query['layout']) && isset($views[$view]->layouts[$query['layout']])) {
			$segments[] = $views[$view]->layouts[$query['layout']];
		}
		unset($query['layout']);
		unset($query['view']);
		unset($query['ts']);
		unset($query[$views[$view]->id]);
		return;
	}
	
	public static function buildComponentSEFAdvanced($crouter, $query, $segments)
	{
		if(!isset($query['Itemid'])) {
			return;
		}
		$menu = JFactory::getApplication()->getMenu();
		$item = $menu->getItem($query['Itemid']);
		
		$path = array_reverse($crouter->getPath($query));
		$views = $crouter->getViews();
		$found = false;
		$found2 = true;
		for($i = 0, $j = count($path); $i < $j; $i++) {
			reset($path);
			$view = key($path);
			if($found) {
				$ids = array_shift($path);
				if($views[$view]->nestable) {
					foreach(array_reverse($ids) as $id) {
						if($found2) {
							list($tmp, $segments[]) = explode(':', $id, 2);
						} else {
							if($item->query[$views[$view]->id] == (int) $id) {
								$found2 = true;
							}
						}
					}
				} else {
					if(is_bool($ids)) {
						$segments[] = $views[$view]->title; 
					} else {
						list($tmp, $segments[]) = explode(':', $ids[0], 2);
					}
				}
			} else {
				if($item->query['view'] != $view) {
					array_shift($path);
				} else {
					if(!$views[$view]->nestable) {
						array_shift($path);
					} else {
						$i--;
						$found2 = false;
					}
					$found = true;
				}
			}
			unset($query[$views[$view]->child_id]);
		}
		if(isset($query['layout']) && isset($views[$view]->layouts[$query['layout']])) {
			$segments[] = $views[$view]->layouts[$query['layout']];
		}
		unset($query['layout']);
		unset($query['view']);
		unset($query['ts']);
		unset($query[$views[$view]->id]);
		return;
	}
	
	public static function parseComponentSEF($router, $segments, $vars)
	{
		$views = $router->getViews();
		$menus = JFactory::getApplication()->getMenu();
		$active = $menus->getActive();
		$cview = $views[$active->query['view']]->children;
		if(isset($views[$active->query['view']]->layouts)) {
			$layouts = $views[$active->query['view']]->layouts;
		} else {
			$layouts = false;
		}
		$nestable = false;
		foreach ($segments as $segment) {
			$found = false;
			if(is_array($layouts)) {
				foreach($layouts as $layout => $title) {
					if($title == $segment) {
						$vars['layout'] = $layout;
						$found = true;
						break;
					}
				}
				if($found) {
					continue;
				}
			}
			list($id, $alias) = explode('-', $segment, 2);
			for($i = 0; $i < count($cview); $i++) {
				$view = $cview[$i];
				if(isset($view->id) && (int) $id > 0) {
					$found = true;
					if($view->nestable) {
						$item = call_user_func(array($router, 'get'.ucfirst($view->name)), $id);
						if($item->alias != $alias) {
							$found = false;
							$cview = array_merge($cview, $view->children);
							continue;
						}
						$nestable = true;
					}
					$vars['view'] = $view->name;
					if(isset($view->parent->id) && isset($vars[$view->parent->id])) {
						$vars[$view->parent_id] = $vars[$view->parent->id];
					}
					$vars[$view->id] = $id;
					
				} elseif($view->title == $segment) {
					$vars['view'] = $view->name;
					$found = true;
					break;
				}
			}
			if($found) {
				if(!$nestable) {
					if(!isset($cview->children)) {
						break;
					}
					$cview = $cview->children;
				}
				$nestable = false;
			} else {
				break;
			}			
		}
	}
}

class ContentRouter extends JComponentRouter
{
	function __construct()
	{
		$this->register('categories', 'categories');
		$this->register('category', 'category', 'id', 'categories', '', true);
		$this->register('article', 'article', 'id', 'category', 'catid');
		$this->register('archive', 'archive');
		$this->register('featured', 'featured');
		parent::__construct();
	}
	
	function getArticle($id)
	{
		/** some code missing here **/
	}
}

class ContactRouter extends JComponentRouter
{
	function __construct()
	{
		$this->register('categories', 'categories');
		$this->register('category', 'category', 'id', 'categories', '', true);
		$this->register('contact', 'contact', 'id', 'category', 'catid');
		$this->register('featured', 'featured');
		parent::__construct();
	}
}

class NewsfeedRouter extends JComponentRouter
{
	function __construct()
	{
		$this->register('categories', 'categories');
		$this->register('category', 'category', 'id', 'categories', '', true);
		$this->register('newsfeed', 'newsfeed', 'id', 'category', 'catid');
		parent::__construct();
	}
}

class WeblinksRouter extends JComponentRouter
{
	function __construct()
	{
		$this->register('categories', 'categories');
		$this->register('category', 'category', 'id', 'categories', '', true);
		$this->register('weblink', 'weblink', 'id', 'category', 'catid');
		parent::__construct();
	}
}