<?php
/**
 * @package     Joomla.Platform
 * @subpackage  HTML
 *
 * @copyright   Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('MOLAJO') or die;

/**
 * Utility class for Sliders elements
 *
 * @package     Joomla.Platform
 * @subpackage  HTML
 * @since       11.1
 */
abstract class MolajoHtmlSliders
{
    /**
     * Creates a panes and loads the javascript behavior for it.
     *
     * @param   string  $group   The pane identifier.
     * @param   array   $parameters  An array of options.
     *
     * @return  string
     *
     * @since   11.1
     */
    public static function start($group = 'sliders', $parameters = array())
    {
        self::_loadBehavior($group, $parameters);

        return '<div id="' . $group . '" class="pane-sliders"><div style="display:none;"><div>';
    }

    /**
     * Close the current pane.
     *
     * @return  string  hTML to close the pane
     *
     * @since   11.1
     */
    public static function end()
    {
        return '</div></div></div>';
    }

    /**
     * Begins the display of a new panel.
     *
     * @param   string  $text  Text to display.
     * @param   string  $id    Identifier of the panel.
     *
     * @return  string  HTML to start a panel
     *
     * @since   11.1
     */
    public static function panel($text, $id)
    {
        return '</div></div><div class="panel"><h3 class="pane-toggler title" id="' . $id . '"><a href="javascript:void(0);"><span>' . $text
               . '</span></a></h3><div class="pane-slider content">';
    }

    /**
     * Load the JavaScript behavior.
     *
     * @param   string  $group   The pane identifier.
     * @param   array   $parameters  Array of options.
     *
     * @return  void
     *
     * @since   11.1
     */
    protected static function _loadBehavior($group, $parameters = array())
    {
        static $loaded = array();
        if (!array_key_exists($group, $loaded)) {
            $loaded[$group] = true;
            // Include mootools framework.
            MolajoHTML::_('behavior.framework', true);

            $document = MolajoFactory::getDocument();

            $display = (isset($parameters['startOffset']) && isset($parameters['startTransition']) && $parameters['startTransition'])
                    ? (int)$parameters['startOffset'] : null;
            $show = (isset($parameters['startOffset']) && !(isset($parameters['startTransition']) && $parameters['startTransition']))
                    ? (int)$parameters['startOffset'] : null;
            $options = '{';
            $opt['onActive'] = "function(toggler, i) {toggler.addClass('pane-toggler-down');' .
				'toggler.removeClass('pane-toggler');i.addClass('pane-down');i.removeClass('pane-hide');Cookie.write('jpanesliders_"
                               . $group . "',$$('div#" . $group . ".pane-sliders > .panel > h3').indexOf(toggler));}";
            $opt['onBackground'] = "function(toggler, i) {toggler.addClass('pane-toggler');' .
				'toggler.removeClass('pane-toggler-down');i.addClass('pane-hide');i.removeClass('pane-down');if($$('div#"
                                   . $group . ".pane-sliders > .panel > h3').length==$$('div#" . $group
                                   . ".pane-sliders > .panel > h3.pane-toggler').length) Cookie.write('jpanesliders_" . $group . "',-1);}";
            $opt['duration'] = (isset($parameters['duration'])) ? (int)$parameters['duration'] : 300;
            $opt['display'] = (isset($parameters['useCookie']) && $parameters['useCookie'])
                    ? JRequest::getInt('jpanesliders_' . $group, $display, 'cookie')
                    : $display;
            $opt['show'] = (isset($parameters['useCookie']) && $parameters['useCookie'])
                    ? JRequest::getInt('jpanesliders_' . $group, $show, 'cookie') : $show;
            $opt['opacity'] = (isset($parameters['opacityTransition']) && ($parameters['opacityTransition'])) ? 'true'
                    : 'false';
            $opt['alwaysHide'] = (isset($parameters['allowAllClose']) && (!$parameters['allowAllClose'])) ? 'false'
                    : 'true';
            foreach ($opt as $k => $v)
            {
                if ($v) {
                    $options .= $k . ': ' . $v . ',';
                }
            }
            if (substr($options, -1) == ',') {
                $options = substr($options, 0, -1);
            }
            $options .= '}';

            $js = "window.addEvent('domready', function(){ new Fx.Accordion($$('div#" . $group
                  . ".pane-sliders > .panel > h3.pane-toggler'), $$('div#" . $group . ".pane-sliders > .panel > div.pane-slider'), " . $options
                  . "); });";

            $document->addScriptDeclaration($js);
        }
    }
}
