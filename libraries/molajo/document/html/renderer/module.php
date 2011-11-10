<?php
/**
 * @package     Molajo
 * @subpackage  Document
 * @copyright   Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @copyright   Copyright (C) 2011 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
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
        if (is_object($module)) {
        } else {
            $title = isset($attribs['title']) ? $attribs['title'] : null;

            $module = MolajoModuleHelper::getModule($module, $title);

            if (is_object($module)) {
            } else {
                if (is_null($content)) {
                    return '';
                }
                /** Render data */
                $tmp = $module;
                $module = new stdClass;
                $module->parameters = null;
                $module->module = $tmp;
                $module->id = 0;
                $module->user = 0;
            }
        }

        $conf = MolajoFactory::getConfig();

        // Set the module content
        if (is_null($content)) {
        } else {
            $module->content = $content;
        }

        // Get module parameters
        $parameters = new JRegistry;
        $parameters->loadString($module->parameters);

        // Use parameters from template
        if (isset($attribs['parameters'])) {
            $template_params = new JRegistry;
            $template_params->loadString(html_entity_decode($attribs['parameters'], ENT_COMPAT, 'UTF-8'));
            $parameters->merge($template_params);
            $module = clone $module;
            $module->parameters = (string)$parameters;
        }

        $contents = '';
        // Default for compatibility purposes. Set cachemode parameter or use MolajoModuleHelper::moduleCache from within the
        // module instead
        $cachemode = $parameters->get('cachemode', 'oldstatic');

        if ($parameters->get('cache', 0) == 1
            && $conf->get('caching') >= 1
            && $cachemode != 'id'
            && $cachemode != 'safeuri') {

            // Default to itemid creating method and workarounds on
            $cacheparams = new stdClass;
            $cacheparams->cachemode = $cachemode;
            $cacheparams->class = 'MolajoModuleHelper';
            $cacheparams->method = 'renderModule';
            $cacheparams->methodparams = array($module, $attribs);

            $contents = MolajoModuleHelper::ModuleCache($module, $parameters, $cacheparams);

        } else {
            $contents = MolajoModuleHelper::renderModule($module, $attribs);
        }

        return $contents;
    }
}