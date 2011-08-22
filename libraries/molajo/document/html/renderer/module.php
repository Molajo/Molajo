<?php
/**
 * @package    Molajo
 * @subpackage  Document
 *
 * @copyright   Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('MOLAJO') or die;

/**
 * MolajoDocument Module renderer
 *
 * @package    Molajo
 * @subpackage  Document
 * @since       1.0
 */
class MolajoDocumentRendererModule extends MolajoDocumentRenderer
{
	/**
	 * Renders a module script and returns the results as a string
	 *
	 * @param   string  $name      The name of the module to render
	 * @param   array   $attribs   Associative array of values
	 * @param   string  $content   If present, module information from the buffer will be used
	 *
	 * @return  string  The output of the script
	 *
	 * @since   11.1
	 */
	public function render($module, $attribs = array(), $content = null)
	{
		if (!is_object($module))
		{
			$title	= isset($attribs['title']) ? $attribs['title'] : null;

			$module = MolajoModuleHelper::getModule($module, $title);

			if (!is_object($module))
			{
				if (is_null($content)) {
					return '';
				}
				else {
					/**
					 * If module isn't found in the database but data has been pushed in the buffer
					 * we want to render it
					 */
					$tmp = $module;
					$module = new stdClass;
					$module->params = null;
					$module->module = $tmp;
					$module->id = 0;
					$module->user = 0;
				}
			}
		}

		// Get the user and configuration object
		// $user = MolajoFactory::getUser();
		$conf = MolajoFactory::getConfig();

		// Set the module content
		if (!is_null($content)) {
			$module->content = $content;
		}

		// Get module parameters
		$params = new JRegistry;
		$params->loadString($module->params);

		// Use parameters from template
		if (isset($attribs['params'])) {
			$template_params = new JRegistry;
			$template_params->loadString(html_entity_decode($attribs['params'], ENT_COMPAT, 'UTF-8'));
			$params->merge($template_params);
			$module = clone $module;
			$module->params = (string) $params;
		}

		$contents = '';
		// Default for compatibility purposes. Set cachemode parameter or use MolajoModuleHelper::moduleCache from within the
		// module instead
		$cachemode = $params->get('cachemode', 'oldstatic');

		if ($params->get('cache', 0) == 1  && $conf->get('caching') >= 1 && $cachemode != 'id' && $cachemode != 'safeuri')
		{

			// Default to itemid creating method and workarounds on
			$cacheparams = new stdClass;
			$cacheparams->cachemode = $cachemode;
			$cacheparams->class = 'MolajoModuleHelper';
			$cacheparams->method = 'renderModule';
			$cacheparams->methodparams = array($module, $attribs);

			$contents = MolajoModuleHelper::ModuleCache($module, $params, $cacheparams);

		}
		else {
			$contents = MolajoModuleHelper::renderModule($module, $attribs);
		}

		return $contents;
	}
}