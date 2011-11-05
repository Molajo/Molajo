<?php
/**
 * @package     Molajo
 * @subpackage  Router
 *
 * @copyright   Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @copyright   Copyright (C) 2011 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die;

/**
 * Set the available masks for the routing mode
 */
define('JROUTER_MODE_RAW', 0);
define('JROUTER_MODE_SEF', 1);

/**
 * Class to create and parse routes
 *
 * @package    Molajo
 * @subpackage  Application
 * @since       1.0
 */
class MolajoRouter extends JObject
{
    /**
     * The rewrite mode
     *
     * @var integer
     */
    protected $_mode = null;

    /**
     * An array of variables
     *
     * @var array
     */
    protected $_vars = array();

    /**
     * An array of rules
     *
     * @var array
     */
    protected $_rules = array(
        'build' => array(),
        'parse' => array()
    );

    /**
     * Class constructor
     */
    public function __construct($options = array())
    {
        if (array_key_exists('mode', $options)) {
            $this->_mode = $options['mode'];
        } else {
            $this->_mode = JROUTER_MODE_RAW;
        }
    }

    /**
     * Returns the global router object, only creating it if it
     * doesn't already exist.
     *
     * @param   string  $application  The name of the application
     * @param   array   $options An associative array of options
     *
     * @return  router object.
     * @since   1.0
     */
    public static function getInstance($application, $options = array())
    {
        static $instances;

        if (isset($instances)) {
        } else {
            $instances = array();
        }

        if (empty($instances[$application])) {

            $classname = 'Molajo' . ucfirst($application) . 'Router';
            if (class_exists($classname)) {
                $instance = new $classname($options);

            } else {
                $error = MolajoError::raiseError(500, MolajoText::sprintf('MOLAJO_APPLICATION_ERROR_ROUTER_LOAD', $application));
                return $error;
            }

            $instances[$application] = &$instance;
        }

        return $instances[$application];
    }

    /**
     *  Function to convert a route to an internal URI
     *
     * @param   string   $uri
     *
     * @return  array
     * @since   11.1
     */
    public function parse(&$uri)
    {
        $vars = array();

        // Process the parsed variables based on custom defined rules
        $vars = $this->_processParseRules($uri);

        // Parse RAW URL
        if ($this->_mode == JROUTER_MODE_RAW) {
            $vars += $this->_parseRawRoute($uri);
        }

        // Parse SEF URL
        if ($this->_mode == JROUTER_MODE_SEF) {
            $vars += $this->_parseSefRoute($uri);
        }

        return array_merge($this->getVars(), $vars);
    }

    /**
     * Function to convert an internal URI to a route
     *
     * @param   string  The internal URL
     *
     * @return  string  The absolute search engine friendly URL
     * @since   1.0
     */
    public function build($url)
    {
        // Create the URI object
        $uri = $this->_createURI($url);

        // Process the uri information based on custom defined rules
        $this->_processBuildRules($uri);

        // Build RAW URL
        if ($this->_mode == JROUTER_MODE_RAW) {
            $this->_buildRawRoute($uri);
        }

        // Build SEF URL : mysite/route/index.php?var=x
        if ($this->_mode == JROUTER_MODE_SEF) {
            $this->_buildSefRoute($uri);
        }

        return $uri;
    }

    /**
     * Get the router mode
     *
     * @return
     * @since   1.0
     */
    public function getMode()
    {
        return $this->_mode;
    }

    /**
     * Get the router mode
     *
     * @return
     * @since   1.0
     */
    public function setMode($mode)
    {
        $this->_mode = $mode;
    }

    /**
     * Set a router variable, creating it if it doesn't exist
     *
     * @param   string   $key     The name of the variable
     * @param   mixed    $value   The value of the variable
     * @param   boolean  $create  If True, the variable will be created if it doesn't exist yet
     *
     * @return
     * @since   1.0
     */
    public function setVar($key, $value, $create = true)
    {
        if ($create || array_key_exists($key, $this->_vars)) {
            $this->_vars[$key] = $value;
        }
    }

    /**
     * Set the router variable array
     *
     * @param   array    $vars    An associative array with variables
     * @param   boolean  $merge   If True, the array will be merged instead of overwritten
     *
     * @return
     * @since   1.0
     */
    public function setVars($vars = array(), $merge = true)
    {
        if ($merge) {
            $this->_vars = array_merge($this->_vars, $vars);
        } else {
            $this->_vars = $vars;
        }
    }

    /**
     * Get a router variable
     *
     * @param   string   The name of the variable
     *
     * @return  mixed    Value of the variable
     * @since   1.0
     */
    public function getVar($key)
    {
        $result = null;
        if (isset($this->_vars[$key])) {
            $result = $this->_vars[$key];
        }
        return $result;
    }

    /**
     * Get the router variable array
     *
     * @return  array    An associative array of router variables
     * @since   1.0
     */
    public function getVars()
    {
        return $this->_vars;
    }

    /**
     * Attach a build rule
     *
     * @param  string  callback  The function to be called
     *
     * @return
     * @since   1.0.
     */
    public function attachBuildRule($callback)
    {
        $this->_rules['build'][] = $callback;
    }

    /**
     * Attach a parse rule
     *
     * @param   string  $callback   The function to be called.
     *
     * @return
     * @since   1.0
     */
    public function attachParseRule($callback)
    {
        $this->_rules['parse'][] = $callback;
    }

    /**
     * Function to convert a raw route to an internal URI
     *
     * @param   string   The raw route
     *
     * @return
     * @since   1.0
     */
    protected function _parseRawRoute(&$uri)
    {
        return false;
    }

    /**
     *  Function to convert a sef route to an internal URI
     *
     * @param   string   The sef URI
     *
     * @return  string   Internal URI
     * @since   1.0
     */
    protected function _parseSefRoute(&$uri)
    {
        return false;
    }

    /**
     * Function to build a raw route
     *
     * @param   string   The internal URL
     *
     * @return           Raw Route
     * @since   1.0
     */
    protected function _buildRawRoute(&$uri)
    {
    }

    /**
     * Function to build a sef route
     *
     * @param   string   The uri
     *
     * @return  string   The SEF route
     * @since   1.0
     */
    protected function _buildSefRoute(&$uri)
    {
    }

    /**
     * Process the parsed router variables based on custom defined rules
     *
     * @param   string   The URI to parse
     *
     * @return  array    The array of processed URI variables
     * @since   1.0
     */
    protected function _processParseRules(&$uri)
    {
        $vars = array();

        foreach ($this->_rules['parse'] as $rule) {
            $vars += call_user_func_array($rule, array(&$this, &$uri));
        }

        return $vars;
    }

    /**
     * Process the build uri query data based on custom defined rules
     *
     * @param   string   The URI
     * @return
     *
     * @since   1.0
     */
    protected function _processBuildRules(&$uri)
    {
        foreach ($this->_rules['build'] as $rule) {
            call_user_func_array($rule, array(&$this, &$uri));
        }
    }

    /**
     * Create a uri based on a full or partial url string
     *
     * @param   string   $url  The URI
     *
     * @return  JURI           A JURI object
     * @since   1.0
     */
    protected function _createURI($url)
    {
        // Create full URL if we are only appending variables to it
        if (substr($url, 0, 1) == '&') {
            $vars = array();
            if (strpos($url, '&amp;') !== false) {
                $url = str_replace('&amp;', '&', $url);
            }

            parse_str($url, $vars);

            $vars = array_merge($this->getVars(), $vars);

            foreach ($vars as $key => $var) {
                if ($var == "") {
                    unset($vars[$key]);
                }
            }

            $url = 'index.php?' . JURI::buildQuery($vars);
        }

        // Decompose link into url component parts
        return new JURI($url);
    }

    /**
     * Encode route segments
     *
     * @param   array    An array of route segments
     *
     * @return  array    Array of encoded route segments
     * @since   1.0
     */
    protected function _encodeSegments($segments)
    {
        $total = count($segments);
        for ($i = 0; $i < $total; $i++) {
            $segments[$i] = str_replace(':', '-', $segments[$i]);
        }

        return $segments;
    }

    /**
     * Decode route segments
     *
     * @param   array    $segments  An array of route segments
     *
     * @return  array    Array of decoded route segments
     * @since 11,1
     */
    protected function _decodeSegments($segments)
    {
        $total = count($segments);
        for ($i = 0; $i < $total; $i++) {
            $segments[$i] = preg_replace('/-/', ':', $segments[$i], 1);
        }

        return $segments;
    }
}