<?php
/**
 * @package     Molajo
 * @subpackage  Helper
 * @copyright   Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @copyright   Copyright (C) 2011 Amy Stephen. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */
defined('MOLAJO') or die;

/**
 * Module Helper
 *
 * @package     Molajo
 * @subpackage  Helper
 * @since       1.0
 */
abstract class MolajoModuleHelper
{
	/**
     * getModule
     *
	 * Get module by name (real, eg 'Breadcrumbs' or folder, eg 'mod_breadcrumbs')
	 *
	 * @param   string  The name of the module
	 * @param   string  The title of the module, optional
	 *
	 * @return  object  The Module object
	 */
	public static function &getModule($name, $title = null)
	{
		$result		= null;
		$modules	= self::_load();

		$total		= count($modules);
		for ($i = 0; $i < $total; $i++)
		{
			// Match the name of the module
			if ($modules[$i]->name == $name)
			{
				// Match the title if we're looking for a specific instance of the module
				if (!$title || $modules[$i]->title == $title)
				{
					$result = &$modules[$i];
					break;	// Found it
				}
			}
		}

		// If we didn't find it, and the name is mod_something, create a dummy object
		if (is_null($result) && substr($name, 0, 4) == 'mod_')
		{
			$result				    = new stdClass;
			$result->id			    = 0;
			$result->title		    = '';
			$result->subtitle	    = '';
			$result->module		    = $name;
			$result->position	    = '';
			$result->content	    = '';
			$result->showtitle	    = 0;
			$result->showsubtitle	= 0;
			$result->control	    = '';
			$result->params		    = '';
			$result->user		    = 0;
		}

		return $result;
	}

	/**
	 * Get modules by position
	 *
	 * @param   string  $position	The position of the module
	 *
	 * @return  array  An array of module objects
	 */
	public static function &getModules($position)
	{
		$app		= MolajoFactory::getApplication();
		$position	= strtolower($position);
		$result		= array();

		$modules    = self::_load();

		$total = count($modules);
		for ($i = 0; $i < $total; $i++)
		{
			if ($modules[$i]->position == $position) {
				$result[] = &$modules[$i];
			}
		}
		if (count($result) == 0) {

			if (JRequest::getBool('tp')
                && MolajoComponentHelper::getParams('com_templates')->get('template_positions_display')) {

				$result[0] = self::getModule('mod_'.$position);
				$result[0]->title = $position;
				$result[0]->content = $position;
				$result[0]->position = $position;
			}
		}

		return $result;
	}

	/**
	 * Checks if a module is enabled
	 *
	 * @param   string  The module name
	 *
	 * @return  boolean
	 */
	public static function isEnabled($module)
	{
		$result = self::getModule($module);
		return (!is_null($result));
	}

	/**
	 * renderModule
     *
     * Render the module.
	 *
	 * @param   object  A module object.
	 * @param   array   An array of attributes for the module (probably from the XML).
	 *
	 * @return  string  The HTML content of the module output.
	 */
	public static function renderModule($module, $attribs = array())
	{
		static $chrome;
		$app	= MolajoFactory::getApplication();

		// Record the scope.
		$scope	= $app->scope;

		// Set scope to module name
		$app->scope = $module->module;

		// Get parameters
		$params = new JRegistry;
		$params->loadJSON($module->params);

		// Get module path
		$module->module = preg_replace('/[^A-Z0-9_\.-]/i', '', $module->module);
		$path = MOLAJO_PATH_BASE.'/modules/'.$module->module.'/'.$module->module.'.php';

		// Load the module
		if (!$module->user && file_exists($path))
		{
			$lang = MolajoFactory::getLanguage();
			// 1.5 or Core then 1.6 3PD
				$lang->load($module->module, MOLAJO_PATH_BASE, null, false, false)
			||	$lang->load($module->module, dirname($path), null, false, false)
			||	$lang->load($module->module, MOLAJO_PATH_BASE, $lang->getDefault(), false, false)
			||	$lang->load($module->module, dirname($path), $lang->getDefault(), false, false);

			$content = '';
			ob_start();
			require $path;
			$module->content = ob_get_contents().$content;
			ob_end_clean();

		}

		// Load the module chrome functions
		if (!$chrome) {
			$chrome = array();
		}


// layouts chrome
//		require_once MOLAJO_PATH_THEMES.'/system/html/modules.php';
//		$chromePath = MOLAJO_PATH_THEMES.'/'.$app->getTemplate().'/html/modules.php';

//		if (!isset($chrome[$chromePath]))
//		{
//			if (file_exists($chromePath)) {
//				require_once $chromePath;
//			}
//			$chrome[$chromePath] = true;
//		}

//		// Make sure a style is set
//		if (!isset($attribs['style'])) {
//			$attribs['style'] = 'none';
//		}

		// Dynamically add outline style
//		if (JRequest::getBool('tp')
//          && MolajoComponentHelper::getParams('com_templates')->get('template_positions_display')) {
//		$attribs['style'] .= ' outline';
//		}

//		foreach(explode(' ', $attribs['style']) as $style)
//		{
//			$chromeMethod = 'modChrome_'.$style;

			// Apply chrome and render module
//			if (function_exists($chromeMethod))
//			{
//				$module->style = $attribs['style'];
//
//				ob_start();
//				$chromeMethod($module, $params, $attribs);
//				$module->content = ob_get_contents();
//				ob_end_clean();
//			}
//		}
//remove above

		$app->scope = $scope; //revert the scope

		return $module->content;
	}

	/**
	 * Get the path to a layout for a module
	 *
	 * @param   string  $module	The name of the module
	 * @param   string  $layout	The name of the module layout. If alternative layout, in the form template:filename.
	 * @return  string  The path to the module layout
	 * @since   1.0
	 */
	public static function getLayoutPath($module, $layout = 'default')
	{
		$template = MolajoFactory::getApplication()->getTemplate();
		$defaultLayout = $layout;
		if (strpos($layout, ':') !== false )
		{
			$temp = explode(':', $layout);
			$template = ($temp[0] == '_') ? $template : $temp[0];
			$layout = $temp[1];
			$defaultLayout = ($temp[1]) ? $temp[1] : 'default';
		}

		// Build the template and base path for the layout
		$tPath = MOLAJO_PATH_THEMES.'/'.$template.'/html/'.$module.'/'.$layout.'.php';
		$bPath = MOLAJO_PATH_BASE.'/modules/'.$module.'/tmpl/'.$defaultLayout.'.php';

		// If the template has a layout override use it
		if (file_exists($tPath)) {
			return $tPath;
		}
		else {
			return $bPath;
		}
	}

	/**
	 * Load published modules
	 *
	 * @return  array
	 */
	protected function &_load()
	{
		static $clean;

		if (isset($clean)) {
			return $clean;
		}

		$Itemid 	= JRequest::getInt('Itemid');
		$app		= MolajoFactory::getApplication();
		$user		= MolajoFactory::getUser();
		$lang 		= MolajoFactory::getLanguage()->getTag();
		$applicationId 	= (int) $app->getApplicationId();

		$cache 		= MolajoFactory::getCache ('com_modules', '');
		$cacheid 	= md5(serialize(array($Itemid, $applicationId, $lang)));

		if (!($clean = $cache->get($cacheid))) {
			$db	= MolajoFactory::getDbo();

			$query = $db->getQuery(true);
			$query->select('id, title, module, position, content, showtitle, params, mm.menuid');
			$query->from('#__modules AS m');
			$query->join('LEFT','#__modules_menu AS mm ON mm.moduleid = m.id');
			$query->where('m.published = 1');

			$date = MolajoFactory::getDate();
			$now = $date->toMySQL();
			$nullDate = $db->getNullDate();
			$query->where('(m.publish_up = '.$db->Quote($nullDate).' OR m.publish_up <= '.$db->Quote($now).')');
			$query->where('(m.publish_down = '.$db->Quote($nullDate).' OR m.publish_down >= '.$db->Quote($now).')');

            $acl = new MolajoACL ();
            $acl->getQueryInformation ('', &$query, 'viewaccess', array('table_prefix'=>'m'));

			$query->where('m.application_id = '. $applicationId);
			$query->where('(mm.menuid = '. (int) $Itemid .' OR mm.menuid <= 0)');

			// Filter by language
			if ($app->isSite() && $app->getLanguageFilter()) {
				$query->where('m.language IN (' . $db->Quote($lang) . ',' . $db->Quote('*') . ')');
			}

			$query->order('position, ordering');

			// Set the query
            $db->setQuery($query->__toString());
            
			$modules = $db->loadObjectList();
			$clean	= array();

			if($db->getErrorNum()){
				JError::raiseWarning(500, JText::sprintf('MOLAJO_APPLICATION_ERROR_MODULE_LOAD', $db->getErrorMsg()));
				return $clean;
			}

			// Apply negative selections and eliminate duplicates
			$negId	= $Itemid ? -(int)$Itemid : false;
			$dupes	= array();
			for ($i = 0, $n = count($modules); $i < $n; $i++)
			{
				$module = &$modules[$i];

				// The module is excluded if there is an explicit prohibition or if
				// the Itemid is missing or zero and the module is in exclude mode.
				$negHit	= ($negId === (int) $module->menuid)
						|| (!$negId && (int)$module->menuid < 0);

				if (isset($dupes[$module->id]))
				{
					// If this item has been excluded, keep the duplicate flag set,
					// but remove any item from the cleaned array.
					if ($negHit) {
						unset($clean[$module->id]);
					}
					continue;
				}
				$dupes[$module->id] = true;

				// Only accept modules without explicit exclusions.
				if (!$negHit)
				{
					//determine if this is a custom module
					$file				= $module->module;
					$custom				= substr($file, 0, 4) == 'mod_' ?  0 : 1;
					$module->user		= $custom;
					// Custom module name is given by the title field, otherwise strip off "mod_"
					$module->name		= $custom ? $module->title : substr($file, 4);
					$module->style		= null;
					$module->position	= strtolower($module->position);
					$clean[$module->id]	= $module;
				}
			}
			unset($dupes);
			// Return to simple indexing that matches the query order.
			$clean = array_values($clean);

			$cache->store($clean, $cacheid);
		}

		return $clean;
	}

	/**
	* Module cache helper
	*
	* Caching modes:
	* To be set in XML:
	* 'static'		one cache file for all pages with the same module parameters
	* 'oldstatic'	1.5. definition of module caching, one cache file for all pages with the same module id and user aid,
	* 'itemid'		changes on itemid change,
	* To be called from inside the module:
	* 'safeuri'		id created from $cacheparams->modeparams array,
	* 'id'			module sets own cache id's
	*
	* @param   object  $module	Module object
	* @param   object  $moduleparams module parameters
	* @param   object  $cacheparams module cache parameters - id or url parameters, depending on the module cache mode
	* @param   array   $params - parameters for given mode - calculated id or an array of safe url parameters and their
	* 					variable types, for valid values see {@link JFilterInput::clean()}.
	*
	* @since   11.1
	*/
	public static function moduleCache($module, $moduleparams, $cacheparams)
	{
		if(!isset ($cacheparams->modeparams)) {
			$cacheparams->modeparams=null;
		}

		if(!isset ($cacheparams->cachegroup)) {
			$cacheparams->cachegroup = $module->module;
		}

		$user = MolajoFactory::getUser();
		$cache = MolajoFactory::getCache($cacheparams->cachegroup, 'callback');
		$conf = MolajoFactory::getConfig();

		// Turn cache off for internal callers if parameters are set to off and for all logged in users
		if($moduleparams->get('owncache', null) === 0  || $conf->get('caching') == 0 || $user->get('id')) {
			$cache->setCaching(false);
		}

		$cache->setLifeTime($moduleparams->get('cache_time', $conf->get('cachetime') * 60));

		$wrkaroundoptions = array (
			'nopathway' 	=> 1,
			'nohead' 		=> 0,
			'nomodules' 	=> 1,
			'modulemode' 	=> 1,
			'mergehead' 	=> 1
		);

		$wrkarounds = true;

//        $acl = new MolajoACL();
//		$view_levels = md5(serialize ($acl->getList('viewaccess')));
$view_levels='';
		switch ($cacheparams->cachemode) {

			case 'id':
				$ret = $cache->get(array($cacheparams->class, $cacheparams->method), $cacheparams->methodparams, $cacheparams->modeparams, $wrkarounds, $wrkaroundoptions);
				break;

			case 'safeuri':
				$secureid=null;
				if (is_array($cacheparams->modeparams)) {
					$uri = JRequest::get();
					$safeuri = new stdClass();
					foreach ($cacheparams->modeparams AS $key => $value) {
						// Use int filter for id/catid to clean out spamy slugs
						if (isset($uri[$key])) {
							$safeuri->$key = JRequest::_cleanVar($uri[$key], 0,$value);
						}
					} }
				$secureid = md5(serialize(array($safeuri, $cacheparams->method, $moduleparams)));
				$ret = $cache->get(array($cacheparams->class, $cacheparams->method), $cacheparams->methodparams, $module->id. $view_levels.$secureid, $wrkarounds, $wrkaroundoptions);
				break;

			case 'static':
				$ret = $cache->get(array($cacheparams->class, $cacheparams->method), $cacheparams->methodparams, $module->module.md5(serialize($cacheparams->methodparams)), $wrkarounds, $wrkaroundoptions);
				break;

			case 'oldstatic':  // provided for backward compatibility, not really usefull
				$ret = $cache->get(array($cacheparams->class, $cacheparams->method), $cacheparams->methodparams, $module->id. $view_levels, $wrkarounds, $wrkaroundoptions);
				break;

			case 'itemid':
			default:
				$ret = $cache->get(array($cacheparams->class, $cacheparams->method), $cacheparams->methodparams, $module->id. $view_levels.JRequest::getVar('Itemid',null,'default','INT'), $wrkarounds, $wrkaroundoptions);
				break;
		}

		return $ret;
	}
}
