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
 * JPane abstract class
 *
 * @package     Joomla.Platform
 * @subpackage  HTML
 * @since       11.1
 * @deprecated  12.1    Use MolajoHTML::_ static helpers
 */
abstract class MolajoPane extends JObject
{
    public $useCookies = false;

    /**
     * Returns a JPanel object.
     *
     * @param   string  $behavior  The behavior to use.
     * @param   array   $parameters    Associative array of values.
     *
     * @return  object
     *
     * @deprecated    12.1
     * @since   11.1
     *
     */
    public static function getInstance($behavior = 'Tabs', $parameters = array())
    {
        // Deprecation warning.
        JLog::add('JPane::getInstance is deprecated.', JLog::WARNING, 'deprecated');

        $classname = 'JPane' . $behavior;
        $instance = new $classname($parameters);

        return $instance;
    }

    /**
     * Creates a pane and creates the javascript object for it.
     *
     * @param   string  $id  The pane identifier.
     *
     * @return  string
     *
     * @since   11.1
     *
     * @deprecated    12.1
     */
    abstract public function startPane($id);

    /**
     * Ends the pane.
     *
     * @since   11.1
     *
     * @return  string
     *
     * @deprecated    12.1
     */
    abstract public function endPane();

    /**
     * Creates a panel with title text and starts that panel.
     *
     * @param   string  $text  The panel name and/or title.
     * @param   string  $id    The panel identifer.
     *
     * @return  string
     *
     * @deprecated  12.1
     * @since   11.1
     */
    abstract public function startPanel($text, $id);

    /**
     * Ends a panel.
     *
     * @return  string
     *
     * @since   11.1
     * @deprecated    12.1
     */
    abstract public function endPanel();

    /**
     * Load the javascript behavior and attach it to the document.
     *
     * @return  void
     *
     * @deprecated    12.1
     * @since   11.1
     */
    abstract protected function _loadBehavior();
}

/**
 * JPanelTabs class to to draw parameter panes.
 *
 * @package     Joomla.Platform
 * @subpackage  HTML
 * @since       11.1
 * @deprecated  Use MolajoHTML::_ static helpers
 */
class MolajoPaneTabs extends MolajoPane
{
    /**
     * Constructor.
     *
     * @param   array  $parameters  Associative array of values
     *
     * @return  void
     *
     * @since   11.1
     */
    function __construct($parameters = array())
    {
        // Deprecation warning.
        JLog::add('JPaneTabs is deprecated.', JLog::WARNING, 'deprecated');

        static $loaded = false;

        parent::__construct($parameters);

        if (!$loaded) {
            $this->_loadBehavior($parameters);
            $loaded = true;
        }
    }

    /**
     * Creates a pane and creates the javascript object for it.
     *
     * @param   string  $id  The pane identifier.
     *
     * @return  string  HTML to start the pane dl
     *
     * @since   11.1
     *
     * @deprecated    12.1
     */
    public function startPane($id)
    {

        // Deprecation warning.
        JLog::add('JPane::startPane is deprecated.', JLog::WARNING, 'deprecated');

        return '<dl class="tabs" id="' . $id . '">';
    }

    /**
     * Ends the pane.
     *
     * @return  string  HTML to end the pane dl
     *
     * @since   11.1
     *
     * @deprecated    12.1
     */
    public function endPane()
    {
        // Deprecation warning.
        JLog::add('JPaneTabs::endPane is deprecated.', JLog::WARNING, 'deprecated');

        return "</dl>";
    }

    /**
     * Creates a tab panel with title text and starts that panel.
     *
     * @param   string  $text  The name of the tab
     * @param   string  $id    The tab identifier
     *
     * @return  string  HTML for the dt tag.
     *
     * @since   11.1
     *
     * @deprecated    12.1
     */
    public function startPanel($text, $id)
    {
        // Deprecation warning.
        JLog::add('JPaneTabs::startPanel is deprecated.', JLog::WARNING, 'deprecated');

        return '<dt class="' . $id . '"><span>' . $text . '</span></dt><dd>';
    }

    /**
     * Ends a tab page.
     *
     * @return  string   HTML for the dd tag.
     *
     * @since   11.1
     *
     * @deprecated    12.1
     */
    public function endPanel()
    {
        // Deprecation warning.
        JLog::add('JPaneTabs::endPanel is deprecated.', JLog::WARNING, 'deprecated');

        return "</dd>";
    }

    /**
     * Load the javascript behavior and attach it to the document.
     *
     * @param   array  $parameters  Associative array of values
     *
     * @return  void
     *
     * @since   11.1
     * @deprecated    12.1
     */
    protected function _loadBehavior($parameters = array())
    {
        // Deprecation warning.
        JLog::add('JPaneTabs::_loadBehavior is deprecated.', JLog::WARNING, 'deprecated');

        // Include mootools framework
        MolajoHTML::_('behavior.framework', true);

        $document = MolajoFactory::getDocument();

        $options = '{';
        $opt['onActive'] = (isset($parameters['onActive'])) ? $parameters['onActive'] : null;
        $opt['onBackground'] = (isset($parameters['onBackground'])) ? $parameters['onBackground'] : null;
        $opt['display'] = (isset($parameters['startOffset'])) ? (int)$parameters['startOffset'] : null;
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

        $js = '	window.addEvent(\'domready\', function(){ $$(\'dl.tabs\').each(function(tabs){ new JTabs(tabs, ' . $options . '); }); });';

        $document->addScriptDeclaration($js);
        MolajoHTML::_('script', 'system/tabs.js', false, true);
    }
}

/**
 * JPanelSliders class to to draw parameter panes.
 *
 * @package     Joomla.Platform
 * @subpackage  HTML
 * @since       11.1
 *
 * @deprecated  Use MolajoHTML::_ static helpers
 */
class MolajoPaneSliders extends MolajoPane
{
    /**
     * Constructor.
     *
     * @param   array  $parameters  Associative array of values.
     *
     * @since   11.1
     *
     * @deprecated    12.1
     */
    function __construct($parameters = array())
    {
        // Deprecation warning.
        JLog::add('JPanelSliders::__construct is deprecated.', JLog::WARNING, 'deprecated');

        static $loaded = false;

        parent::__construct($parameters);

        if (!$loaded) {
            $this->_loadBehavior($parameters);
            $loaded = true;
        }
    }

    /**
     * Creates a pane and creates the javascript object for it.
     *
     * @param   string  $id  The pane identifier.
     *
     * @return  string  HTML to start the slider div.
     *
     * @since   11.1
     *
     * @deprecated    12.1
     */
    public function startPane($id)
    {
        // Deprecation warning.
        JLog::add('JPaneSliders::startPane is deprecated.', JLog::WARNING, 'deprecated');

        return '<div id="' . $id . '" class="pane-sliders">';
    }

    /**
     * Ends the pane.
     *
     * @return  string  HTML to end the slider div.
     *
     * @since   11.1
     *
     * @deprecated    12.1
     */
    public function endPane()
    {
        // Deprecation warning.
        JLog::add('JPaneSliders::endPane is deprecated.', JLog::WARNING, 'deprecated');

        return '</div>';
    }

    /**
     * Creates a tab panel with title text and starts that panel.
     *
     * @param   string  $text  The name of the tab.
     * @param   string  $id    The tab identifier.
     *
     * @return  string  HTML to start the tab panel div.
     *
     * @since   11.1
     *
     * @deprecated    12.1
     */
    public function startPanel($text, $id)
    {
        // Deprecation warning.
        JLog::add('JPaneSliders::startPanel is deprecated.', JLog::WARNING, 'deprecated');

        return '<div class="panel">' . '<h3 class="pane-toggler title" id="' . $id . '"><a href="javascript:void(0);"><span>' . $text
               . '</span></a></h3>' . '<div class="pane-slider content">';
    }

    /**
     * Ends a tab page.
     *
     * @return  string  HTML to end the tab divs.
     *
     * @since   11.1
     *
     * @deprecated    12.1
     */
    public function endPanel()
    {
        // Deprecation warning.
        JLog::add('JPaneSliders::endPanel is deprecated.', JLog::WARNING, 'deprecated');

        return '</div></div>';
    }

    /**
     * Load the javascript behavior and attach it to the document.
     *
     * @param   array  $parameters  Associative array of values.
     *
     * @return  void
     *
     * @since 11.1
     *
     * @deprecated    12.1
     */
    protected function _loadBehavior($parameters = array())
    {
        // Deprecation warning.
        JLog::add('JPaneSliders::_loadBehavior is deprecated.', JLog::WARNING, 'deprecated');

        // Include mootools framework.
        MolajoHTML::_('behavior.framework', true);

        $document = MolajoFactory::getDocument();

        $options = '{';
        $opt['onActive'] = 'function(toggler, i) { toggler.addClass(\'pane-toggler-down\');' .
                           ' toggler.removeClass(\'pane-toggler\');i.addClass(\'pane-down\');i.removeClass(\'pane-hide\'); }';
        $opt['onBackground'] = 'function(toggler, i) { toggler.addClass(\'pane-toggler\');' .
                               ' toggler.removeClass(\'pane-toggler-down\');i.addClass(\'pane-hide\');i.removeClass(\'pane-down\'); }';
        $opt['duration'] = (isset($parameters['duration'])) ? (int)$parameters['duration'] : 300;
        $opt['display'] = (isset($parameters['startOffset']) && ($parameters['startTransition']))
                ? (int)$parameters['startOffset']
                : null;
        $opt['show'] = (isset($parameters['startOffset']) && (!$parameters['startTransition']))
                ? (int)$parameters['startOffset']
                : null;
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

        $js = '	window.addEvent(\'domready\', function(){ new Fx.Accordion($$(\'.panel h3.pane-toggler\'), $$(\'.panel div.pane-slider\'), '
              . $options . '); });';

        $document->addScriptDeclaration($js);
    }
}
