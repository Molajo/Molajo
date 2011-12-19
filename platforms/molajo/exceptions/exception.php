<?php
/**
 * @package     Molajo
 * @subpackage  Application
 * @copyright   Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @copyright   Copyright (C) 2012 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die;

/**
 * Exception object.
 *
 * @package     Molajo
 * @subpackage  Exception
 * @since       1.0
 */
class MolajoException extends Exception
{
    /**
     * @var    string  Error level.
     * @since 1.0
     */
    protected $level = null;

    /**
     * @var    string  Error code.
     * @since 1.0
     */
    protected $code = null;

    /**
     * @var    string  Error message.
     * @since 1.0
     */
    protected $message = null;

    /**
     * Additional info about the error relevant to the developer,
     * for example, if a database connect fails, the dsn used
     *
     * @var    string
     * @since 1.0
     */
    protected $info = '';

    /**
     * Name of the file the error occurred in
     *
     * @var    string
     * @since 1.0
     */
    protected $file = null;

    /**
     * Line number the error occurred in  
     *
     * @var    int
     * @since 1.0
     */
    protected $line = 0;

    /**
     * Name of the method the error occurred in  
     *
     * @var    string
     * @since 1.0
     */
    protected $function = null;

    /**
     * Name of the class the error occurred in [Available if backtrace is enabled]
     *
     * @var    string
     * @since 1.0
     */
    protected $class = null;

    /**
     * @var    string  Error type.
     * @since 1.0
     */
    protected $type = null;

    /**
     * Arguments received by the method the error occurred in [Available if backtrace is enabled]
     *
     * @var    array
     * @since 1.0
     */
    protected $args = array();

    /**
     * @var    mixed  Backtrace information.
     * @since 1.0
     */
    protected $backtrace = null;

    /**
     * Constructor
     * - used to set up the error with all needed error details.
     *
     * @param   string   $message    The error message
     * @param   string   $code       The error code from the application
     * @param   integer  $level      The error level (use the PHP constants E_ALL, E_NOTICE etc.).
     * @param   string   $info       Optional: The additional error information.
     * @param   boolean  $backtrace  True if backtrace information is to be collected
     *
     * @since   1.0
     */
    public function __construct($message, $code = 0, $level = null, $info = null, $backtrace = false)
    {
        $this->level = $level;
        $this->code = $code;
        $this->message = $message;

        if ($info == null) {
        } else {
            $this->info = $info;
        }

        if ($backtrace && function_exists('debug_backtrace')) {
            $this->backtrace = debug_backtrace();

            for ($i = count($this->backtrace) - 1; $i >= 0; --$i)
            {
                ++$i;
                if (isset($this->backtrace[$i]['file'])) {
                    $this->file = $this->backtrace[$i]['file'];
                }
                if (isset($this->backtrace[$i]['line'])) {
                    $this->line = $this->backtrace[$i]['line'];
                }
                if (isset($this->backtrace[$i]['class'])) {
                    $this->class = $this->backtrace[$i]['class'];
                }
                if (isset($this->backtrace[$i]['function'])) {
                    $this->function = $this->backtrace[$i]['function'];
                }
                if (isset($this->backtrace[$i]['type'])) {
                    $this->type = $this->backtrace[$i]['type'];
                }

                $this->args = false;
                if (isset($this->backtrace[$i]['args'])) {
                    $this->args = $this->backtrace[$i]['args'];
                }
                break;
            }
        }

        // Store exception for debugging purposes
        MolajoError::addToStack($this);

        parent::__construct($message, (int)$code);
    }

    /**
     * Returns error message
     *
     * @return  string  Error message
     *
     * @since   11.0
     */
    public function __toString()
    {
        return $this->message;
    }

    /**
     * Returns to error message
     *
     * @return  string   Error message
     *
     * @since   1.0
     * @deprecated    12.1
     */
    public function toString()
    {
        return (string)$this;
    }

    /**
     * Returns a property of the object or the default value if the property is not set.
     *
     * @param   string  $property  The name of the property
     * @param   mixed   $default   The default value
     *
     * @return  mixed  The value of the property or null
     *
     * @since       1.0
     */
    public function get($property, $default = null)
    {
        if (isset($this->$property)) {
            return $this->$property;
        }
        return $default;
    }

    /**
     * Returns an associative array of object properties
     *
     * @param   boolean  $public  If true, returns only the public properties
     *
     * @return  array  Object properties
     *
     * @since   1.0
     */
    public function getProperties($public = true)
    {
        $vars = get_object_vars($this);
        if ($public) {
            foreach ($vars as $key => $value)
            {
                if ('_' == substr($key, 0, 1)) {
                    unset($vars[$key]);
                }
            }
        }
        return $vars;
    }

    /**
     * Get the most recent error message
     *
     * @param   integer  $i         Option error index
     * @param   boolean  $toString  Indicates if MolajoError objects should return their error message
     *
     * @return  string  Error message
     *
     * @since   1.0
     *
     * @deprecated  12.1
     */
    public function getError($i = null, $toString = true)
    {
        if ($i === null) {
            // Default, return the last message
            $error = end($this->_errors);
        }
        elseif (!array_key_exists($i, $this->_errors))
        {
            // If $i has been specified but does not exist, return false
            return false;
        }
        else
        {
            $error = $this->_errors[$i];
        }

        // Check if only the string is requested
        if (MolajoError::isError($error) && $toString) {
            return (string)$error;
        }

        return $error;
    }

    /**
     * Return all errors, if any
     *
     * @return  array  Array of error messages or MolajoErrors
     *
     * @since   1.0
     *
     * @deprecated  12.1
     */
    public function getErrors()
    {
        return $this->_errors;
    }

    /**
     * Modifies a property of the object, creating it if it does not already exist.
     *
     * @param   string  $property  The name of the property
     * @param   mixed   $value     The value of the property to set
     *
     * @return  mixed  Previous value of the property
     *
     * @deprecated  12.1
     * @see         setProperties()
     * @since       11.1
     */
    public function set($property, $value = null)
    {
        $previous = isset($this->$property) ? $this->$property : null;
        $this->$property = $value;
        return $previous;
    }

    /**
     * Set the object properties based on a named array/hash
     *
     * @param   mixed  $properties  Either and associative array or another object
     *
     * @return  boolean
     *
     * @deprecated  12.1
     * @see         set()
     * @since       11.1
     */
    public function setProperties($properties)
    {
        // Cast to an array
        $properties = (array)$properties;

        if (is_array($properties)) {
            foreach ($properties as $k => $v)
            {
                $this->$k = $v;
            }

            return true;
        }

        return false;
    }

    /**
     * Add an error message
     *
     * @param   string  $error  Error message
     *
     * @return  void
     *
     * @since   1.0
     *
     * @deprecated  12.1
     */
    public function setError($error)
    {
        array_push($this->_errors, $error);
    }
}


class ApplicationException extends Exception
{
}
class DatabaseException extends Exception
{
}
class JException extends Exception
{
}
class JApplicationException extends ApplicationException
{
}
class JDatabaseException extends DatabaseException
{
}