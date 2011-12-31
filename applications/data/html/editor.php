<?php
/**
 * @package     Molajo
 * @subpackage  Attributes
 * @copyright   Copyright (C) 2012 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die;

/**
 * MolajoEditor class to handle WYSIWYG editors
 *
 * @package    Molajo
 * @subpackage  HTML
 * @since       1.0
 */
class MolajoEditor extends JObservable
{
    /**
     * Editor Plugin object
     *
     * @var    object
     */
    protected $_editor = null;

    /**
     * Editor Plugin name
     *
     * @var string
     */
    protected $_name = null;

    /**
     * Object asset
     *
     * @var string
     */
    protected $asset = null;

    /**
     * Object author
     *
     * @var string
     */
    protected $author = null;

    /**
     * Constructor
     *
     * @param   string  The editor name
     */
    public function __construct($editor = 'none')
    {
        $this->_name = $editor;
    }

    /**
     * Returns the global Editor object, only creating it
     * if it doesn't already exist.
     *
     * @param   string  $editor  The editor to use.
     * @return  object  MolajoEditor    The Editor object.
     */
    public static function getInstance($editor = 'none')
    {
        static $instances;

        if (!isset ($instances)) {
            $instances = array();
        }

        $signature = serialize($editor);

        if (empty ($instances[$signature])) {
            $instances[$signature] = new MolajoEditor($editor);
        }

        return $instances[$signature];
    }

    /**
     * Initialise the editor
     */
    public function initialise()
    {
        //check if editor is already loaded
        if (is_null(($this->_editor))) {
            return;
        }

        $args['event'] = 'onInit';

        $return = '';
        $results[] = $this->_editor->update($args);

        foreach ($results as $result)
        {
            if (trim($result)) {
                //$return .= $result;
                $return = $result;
            }
        }

        MolajoController::MolajoControllerApplication()->addCustomTag($return);
    }

    /**
     * Display the editor area.
     *
     * @param   string   $name        The control name.
     * @param   string   $html        The contents of the text area.
     * @param   string   $width        The width of the text area (px or %).
     * @param   string   $height    The height of the text area (px or %).
     * @param   integer  $col        The number of columns for the textarea.
     * @param   integer  $row        The number of rows for the textarea.
     * @param   boolean  $buttons    True and the editor buttons will be displayed.
     * @param   string   $id        An optional ID for the textarea (note: since 1.6). If not supplied the name is used.
     * @param   string   $asset
     * @param   object   $author
     * @param   array    $parameters    Associative array of editor parameters.
     */
    public function display($name, $html, $width, $height, $col, $row, $buttons = true, $id = null, $asset = null, $author = null, $parameters = array())
    {
        $this->asset = $asset;
        $this->author = $author;
        $this->_loadEditor($parameters);

        // Check whether editor is already loaded
        if (is_null(($this->_editor))) {
            return;
        }

        // Backwards compatibility. Width and height should be passed without a semicolon from now on.
        // If editor plugins need a unit like "px" for CSS styling, they need to take care of that
        $width = str_replace(';', '', $width);
        $height = str_replace(';', '', $height);

        // Initialise variables.
        $return = null;

        $args['name'] = $name;
        $args['content'] = $html;
        $args['width'] = $width;
        $args['height'] = $height;
        $args['col'] = $col;
        $args['row'] = $row;
        $args['buttons'] = $buttons;
        $args['id'] = $id ? $id : $name;
        $args['event'] = 'onDisplay';

        $results[] = $this->_editor->update($args);

        foreach ($results as $result)
        {
            if (trim($result)) {
                $return .= $result;
            }
        }
        return $return;
    }

    /**
     * Save the editor content
     *
     * @param   string  The name of the editor control
     */
    public function save($editor)
    {
        $this->_loadEditor();

        // Check whether editor is already loaded
        if (is_null(($this->_editor))) {
            return;
        }

        $args[] = $editor;
        $args['event'] = 'onSave';

        $return = '';
        $results[] = $this->_editor->update($args);

        foreach ($results as $result)
        {
            if (trim($result)) {
                $return .= $result;
            }
        }

        return $return;
    }

    /**
     * Get the editor contents
     *
     * @param   string  $editor    The name of the editor control
     *
     * @return  string
     */
    public function getContent($editor)
    {
        $this->_loadEditor();

        $args['name'] = $editor;
        $args['event'] = 'onGetContent';

        $return = '';
        $results[] = $this->_editor->update($args);

        foreach ($results as $result)
        {
            if (trim($result)) {
                $return .= $result;
            }
        }

        return $return;
    }

    /**
     * Set the editor contents
     *
     * @param   string  $editor    The name of the editor control
     * @param   string  $html    The contents of the text area
     *
     * @return  string
     */
    public function setContent($editor, $html)
    {
        $this->_loadEditor();

        $args['name'] = $editor;
        $args['html'] = $html;
        $args['event'] = 'onSetContent';

        $return = '';
        $results[] = $this->_editor->update($args);

        foreach ($results as $result)
        {
            if (trim($result)) {
                $return .= $result;
            }
        }

        return $return;
    }

    /**
     * Get the editor buttons
     *
     * @param   string  $editor        The name of the editor.
     * @param   mixed   $buttons    Can be boolean or array, if boolean defines if the buttons are
     *                                 displayed, if array defines a list of buttons not to show.
     *
     * @since   1.0
     */
    public function getButtons($editor, $buttons = true)
    {
        $result = array();

        if (is_bool($buttons) && !$buttons) {
            return $result;
        }

        // Get plugins
        $plugins = MolajoPluginHelper::getPlugin('editors-xtd');

        foreach ($plugins as $plugin)
        {
            if (is_array($buttons) && in_array($plugin->name, $buttons)) {
                continue;
            }

            $isLoaded = MolajoPluginHelper::importPlugin('editors-xtd', $plugin->name, false);
            $className = 'plgButton' . $plugin->name;

            if (class_exists($className)) {
                $plugin = new $className($this, (array)$plugin);
            }

            // Try to authenticate
            if ($temp = $plugin->onDisplay($editor, $this->asset, $this->author)) {
                $result[] = $temp;
            }
        }

        return $result;
    }

    /**
     * Load the editor
     *
     * @param   array  $config    Associative array of editor config paramaters
     *
     * @return  mixed
     * @since   1.0
     */
    protected function _loadEditor($config = array())
    {
        // Check whether editor is already loaded
        if (!is_null(($this->_editor))) {
            return;
        }

        // Build the path to the needed editor plugin
        $name = JFilterInput::getInstance()->clean($this->_name, 'cmd');
        $path = MOLAJO_EXTENSIONS_PLUGINS . '/editors/' . $name . '.php';

        if (!JFile::exists($path)) {
            $path = MOLAJO_EXTENSIONS_PLUGINS . '/editors/' . $name . '/' . $name . '.php';
            if (!JFile::exists($path)) {
                $message = MolajoTextHelper::_('MOLAJO_HTML_EDITOR_CANNOT_LOAD');
                MolajoError::raiseWarning(500, $message);
                return false;
            }
        }

        // Require plugin file
        require_once $path;

        // Get the plugin
        $plugin = MolajoPluginHelper::getPlugin('editors', $this->_name);
        $parameters = new JRegistry;
        $parameters->loadJSON($plugin->parameters);
        $parameters->loadArray($config);
        $plugin->parameters = $parameters;

        // Build editor plugin classname
        $name = 'plgEditor' . $this->_name;

        if ($this->_editor = new $name ($this, (array)$plugin)) {
            // Load plugin parameters
            $this->initialise();
            MolajoPluginHelper::importPlugin('editors-xtd');
        }
    }
}
