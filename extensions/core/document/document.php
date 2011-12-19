<?php
/**
 * @package     Molajo
 * @subpackage  Document
 * @copyright   Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @copyright   Copyright (C) 2012 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die;

/**
 * MolajoDocument
 *
 * Document class, provides an easy interface to parse and display a document
 *
 * @package     Molajo
 * @subpackage  Document
 * @since       1.0
 */
class MolajoDocument extends JObject
{
    /**
     * Document title
     *
     * @var    string
     * @since  1.0
     */
    public $title = '';

    /**
     * Document description
     *
     * @var    string
     * @since  1.0
     */
    public $description = '';

    /**
     * Document full URL
     *
     * @var    string
     * @since   1.0
     */
    public $link = '';

    /**
     * Document base URL
     *
     * @var    string
     * @since  1.0
     */
    public $base = '';

    /**
     * Contains the document language setting
     *
     * @var    string
     * @since   1.0
     */
    public $language = 'en-gb';

    /**
     * Contains the document direction setting
     *
     * @var    string
     * @since  1.0
     */
    public $direction = 'ltr';

    /**
     * Document generator
     *
     * @var    string
     */
    public $_generator = 'Molajo 1.0 - Web Application Development Framework';

    /**
     * Document modified date
     *
     * @var    string
     * @since  1.0
     */
    public $_mdate = '';

    /**
     * Tab string
     *
     * @var        string
     */
    public $_tab = "\11";

    /**
     * Contains the line end string
     *
     * @var        string
     */
    public $_lineEnd = "\12";

    /**
     * Contains the character encoding string
     *
     * @var    string
     */
    public $_charset = 'utf-8';

    /**
     * Document mime type
     *
     * @var        string
     */
    public $_mime = '';

    /**
     * Document namespace
     *
     * @var        string
     */
    public $_namespace = '';

    /**
     * Document profile
     *
     * @var        string
     */
    public $_profile = '';

    /**
     * Array of linked scripts
     *
     * @var        array
     */
    public $_scripts = array();

    /**
     * Array of scripts placed in the header
     *
     * @var  array
     */
    public $_script = array();

    /**
     * Array of linked style sheets
     *
     * @var    array
     */
    public $_styleSheets = array();

    /**
     * Array of included style declarations
     *
     * @var    array
     */
    public $_style = array();

    /**
     * Array of meta tags
     *
     * @var    array
     */
    public $_metaTags = array();

    /**
     * The rendering engine
     *
     * @var    object
     */
    public $_engine = null;

    /**
     * The document type
     *
     * @var    string
     * @since  1.0
     */
    public $_type = null;

    /**
     * Array of buffered output
     *
     * @var    mixed (depends on the renderer)
     */
    public static $_buffer = null;

    /**
     * __construct
     *
     * Class constructor.
     *
     * @param   array  $options  Associative array of options
     *
     * @return  document
     *
     * @since   1.0
     */
    public function __construct($options = array())
    {
        parent::__construct();

        if (array_key_exists('lineend', $options)) {
            $this->setLineEnd($options['lineend']);
        }

        if (array_key_exists('charset', $options)) {
            $this->setCharset($options['charset']);
        }

        if (array_key_exists('language', $options)) {
            $this->setLanguage($options['language']);
        }

        if (array_key_exists('direction', $options)) {
            $this->setDirection($options['direction']);
        }

        if (array_key_exists('tab', $options)) {
            $this->setTab($options['tab']);
        }

        if (array_key_exists('link', $options)) {
            $this->setLink($options['link']);
        }

        if (array_key_exists('base', $options)) {
            $this->setBase($options['base']);
        }
    }

    /**
     * getInstance
     *
     * Returns the global document object, creating it if not existing
     *
     * @param   string  $format     The document type to instantiate
     * @param   array   $attribues  Array of attributes
     *
     * @return  object  The document object.
     * @since   1.0
     */
    public static function getInstance($format = 'html', $attributes = array())
    {
        static $instances;

        if (!isset($instances)) {
            $instances = array();
        }

        $signature = serialize(array($format, $attributes));

        if (empty($instances[$signature])) {
            $format = preg_replace('/[^A-Z0-9_\.-]/i', '', $format);
            $path = dirname(__FILE__) . '/' . $format . '/' . $format . '.php';
            $holdFormat = null;

            if (file_exists($path)) {
            } else {
                $holdFormat = $format;
                $format = 'raw';
            }

            // Determine the path and class
            $class = 'MolajoDocument' . ucfirst($format);
            if (class_exists($class)) {

            } else {
                $path = dirname(__FILE__) . '/' . $format . '/' . $format . '.php';
                if (file_exists($path)) {
                    require_once $path;

                } else {
                    MolajoError::raiseError(500, MolajoTextHelper::_('JLIB_DOCUMENT_ERROR_UNABLE_LOAD_DOC_CLASS'));
                }
            }

            $instance = new $class($attributes);
            $instances[$signature] = &$instance;

            if (is_null($holdFormat)) {
            } else {
                $instance->setType($holdFormat);
            }
        }

        return $instances[$signature];
    }

    /**
     * setType
     *
     * Set the document type
     *
     * @param   string  $format
     *
     * @return
     * @since   1.0
     */
    public function setType($format)
    {
        $this->_type = $format;
    }

    /**
     * getType
     *
     * Returns the document type
     *
     * @return  string
     * @since   1.0
     */
    public function getType()
    {
        return $this->_type;
    }

    /**
     * getBuffer
     *
     * Get the contents of the document buffer
     *
     * @return  The contents of the document buffer
     * @since   1.0
     */
    public function getBuffer()
    {
        return self::$_buffer;
    }

    /**
     * setBuffer
     *
     * Set the contents of the document buffer
     *
     * @param   string  $content  The content to be set in the buffer.
     * @param   array   $options  Array of optional elements.
     *
     * @return  void
     * @since   1.0
     */
    public function setBuffer($content, $options = array())
    {
        self::$_buffer = $content;
    }

    /**
     * getMetaData
     *
     * Gets a meta tag.
     *
     * @param   string  $name        Value of name or http-equiv tag
     * @param   bool    $http_equiv  META type "http-equiv" defaults to null
     *
     * @return  string
     * @since   1.0
     */
    public function getMetaData($name, $http_equiv = false)
    {
        $result = '';
        $name = strtolower($name);
        if ($name == 'generator') {
            $result = $this->getGenerator();
        }
        else if ($name == 'description') {
            $result = $this->getDescription();

        } else {
            if ($http_equiv == true) {
                $result = @$this->_metaTags['http-equiv'][$name];
            } else {

                $result = @$this->_metaTags['standard'][$name];
            }
        }

        return $result;
    }

    /**
     * setMetaData
     *
     * Sets or alters a meta tag.
     *
     * @param   string   $name        Value of name or http-equiv tag
     * @param   string   $content     Value of the content tag
     * @param   bool     $http_equiv  META type "http-equiv" defaults to null
     * @param   bool     $sync        Should http-equiv="content-type" by synced with HTTP-header?
     *
     * @return  void
     * @since   1.0
     */
    public function setMetaData($name, $content, $http_equiv = false, $sync = true)
    {
        $name = strtolower($name);

        if ($name == 'generator') {
            $this->setGenerator($content);

        } else if ($name == 'description') {
            $this->setDescription($content);

        } else {
            if ($http_equiv == true) {
                $this->_metaTags['http-equiv'][$name] = $content;

                // Syncing with HTTP-header
                if ($sync && strtolower($name) == 'content-type') {
                    $this->setMimeEncoding($content, false);
                }

            } else {
                $this->_metaTags['standard'][$name] = $content;
            }
        }
    }

    /**
     * addScript
     *
     * Adds a linked script to the page
     *
     * @param   string  $url        URL to the linked script
     * @param   string  $format     Type of script. Defaults to 'text/javascript'
     * @param   bool    $defer      Adds the defer attribute.
     * @param   bool    $async      Adds the async attribute.
     * @return
     * @since    1.0
     */
    public function addScript($url, $format = "text/javascript", $defer = false, $async = false)
    {
        $this->_scripts[$url]['mime'] = $format;
        $this->_scripts[$url]['defer'] = $defer;
        $this->_scripts[$url]['async'] = $async;
    }

    /**
     * addScriptDeclaration
     *
     * Adds a script to the page
     *
     * @param   string  $content    Script
     * @param   string  $format     Scripting mime (defaults to 'text/javascript')
     *
     * @return  void
     * @since    1.0
     */
    public function addScriptDeclaration($content, $format = 'text/javascript')
    {
        if (!isset($this->_script[strtolower($format)])) {
            $this->_script[strtolower($format)] = $content;

        } else {
            $this->_script[strtolower($format)] .= chr(13) . $content;
        }
    }

    /**
     * addStyleSheet
     *
     * Adds a linked stylesheet to the page
     *
     * @param   string  $url      URL to the linked style sheet
     * @param   string  $format   Mime encoding type
     * @param   string  $media    Media type that this stylesheet applies to
     * @param   array   $attribs  Array of attributes
     *
     * @return  void
     * @since    1.0
     */
    public function addStyleSheet($url, $format = 'text/css', $media = null, $attribs = array())
    {
        $this->_styleSheets[$url]['mime'] = $format;
        $this->_styleSheets[$url]['media'] = $media;
        $this->_styleSheets[$url]['attribs'] = $attribs;
    }

    /**
     * addStyleDeclaration
     *
     * Adds a stylesheet declaration to the page
     *
     * @param   string  $content  Style declarations
     * @param   string  $format   Type of stylesheet (defaults to 'text/css')
     *
     * @return  void
     */
    public function addStyleDeclaration($content, $format = 'text/css')
    {
        if (!isset($this->_style[strtolower($format)])) {
            $this->_style[strtolower($format)] = $content;

        } else {
            $this->_style[strtolower($format)] .= chr(13) . $content;
        }
    }

    /**
     * setCharset
     *
     * Sets the document charset
     *
     * @param   string  $format  Charset encoding string
     *
     * @return  void
     */
    public function setCharset($format = 'utf-8')
    {
        $this->_charset = $format;
    }

    /**
     * getCharset
     *
     * Returns the document charset encoding.
     *
     * @return string
     */
    public function getCharset()
    {
        return $this->_charset;
    }

    /**
     * setLanguage
     *
     * Sets the global document language declaration. Default is English (en-gb).
     *
     * @param   string    $lang
     *
     * @return  void
     */
    public function setLanguage($lang = "en-gb")
    {
        $this->language = strtolower($lang);
    }

    /**
     * getLanguage
     *
     * Returns the document language.
     *
     * @return string
     */
    public function getLanguage()
    {
        return $this->language;
    }

    /**
     * setDirection
     *
     * Sets the global document direction declaration. Default is left-to-right (ltr).
     *
     * @param   string  $lang
     *
     * @return  void
     */
    public function setDirection($dir = "ltr")
    {
        $this->direction = strtolower($dir);
    }

    /**
     * getDirection
     *
     * Returns the document direction declaration.
     *
     * @return string
     */
    public function getDirection()
    {
        return $this->direction;
    }

    /**
     * setTitle
     *
     * Sets the title of the document
     *
     * @param   string    $title
     *
     * @return  void
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

    /**
     * getTitle
     *
     * Return the title of the document.
     *
     * @return  string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * setBase
     *
     * Sets the base URI of the document
     *
     * @param  string  $base
     *
     * @return void
     */
    public function setBase($base)
    {
        $this->base = $base;
    }

    /**
     * getBase
     *
     * Return the base URI of the document.
     *
     * @return  string
     */
    public function getBase()
    {
        return $this->base;
    }

    /**
     * setDescription
     *
     * Sets the description of the document
     *
     * @param  string  $title
     *
     * @return void
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * getDescription
     *
     * Return the title of the page.
     *
     * @return  string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * setLink
     *
     * Sets the document link
     *
     * @param  string  $url  A url
     *
     * @return  void
     */
    public function setLink($url)
    {
        $this->link = $url;
    }

    /**
     * getLink
     *
     * Returns the document base url
     *
     * @return string
     */
    public function getLink()
    {
        return $this->link;
    }

    /**
     * setGenerator
     *
     * Sets the document generator
     *
     * @param  string
     *
     * @return  void
     */
    public function setGenerator($generator)
    {
        $this->_generator = $generator;
    }

    /**
     * getGenerator
     *
     * Returns the document generator
     *
     * @return string
     */
    public function getGenerator()
    {
        return $this->_generator;
    }

    /**
     * setModifiedDate
     *
     * Sets the document modified date
     *
     * @param  string
     *
     * @return  void
     */
    public function setModifiedDate($date)
    {
        $this->_mdate = $date;
    }

    /**
     * getModifiedDate
     *
     * Returns the document modified date
     *
     * @return string
     */
    public function getModifiedDate()
    {
        return $this->_mdate;
    }

    /**
     * setMimeEncoding
     *
     * Sets the document MIME encoding that is sent to the browser.
     *
     * This usually will be text/html because most browsers cannot yet
     * accept the proper mime settings for XHTML: application/xhtml+xml
     * and to a lesser extent application/xml and text/xml. See the W3C note
     * ({@link http://www.w3.org/TR/xhtml-media-types/
     * http://www.w3.org/TR/xhtml-media-types/}) for more details.
     *
     * @param   string  $format
     * @param   bool    $sync  Should the type be synced with HTML?
     *
     * @return  void
     */
    public function setMimeEncoding($format = 'text/html', $sync = true)
    {
        $this->_mime = strtolower($format);

        if ($sync) {
            $this->setMetaData('content-type', $format, true, false);
        }
    }

    /**
     * getMimeEncoding
     *
     * Return the document MIME encoding that is sent to the browser.
     *
     * @return  string
     */
    public function getMimeEncoding()
    {
        return $this->_mime;
    }

    /**
     * setLineEnd
     *
     * Sets the line end style to Windows, Mac, Unix or a custom string.
     *
     * @param   string  $style  "win", "mac", "unix" or custom string.
     *
     * @return  void
     */
    public function setLineEnd($style)
    {
        switch ($style)
        {
            case 'win':
                $this->_lineEnd = "\15\12";
                break;
            case 'unix':
                $this->_lineEnd = "\12";
                break;
            case 'mac':
                $this->_lineEnd = "\15";
                break;
            default:
                $this->_lineEnd = $style;
        }
    }

    /**
     * _getLineEnd
     *
     * Returns the lineEnd
     *
     * @return  string
     */
    public function _getLineEnd()
    {
        return $this->_lineEnd;
    }

    /**
     * setTab
     *
     * Sets the string used to indent HTML
     *
     * @param   string  $string  String used to indent ("\11", "\t", '  ', etc.).
     *
     * @return  void
     */
    public function setTab($string)
    {
        $this->_tab = $string;
    }

    /**
     * _getTab
     *
     * Returns a string containing the unit for indenting HTML
     *
     * @return  string
     */
    public function _getTab()
    {
        return $this->_tab;
    }

    /**
     * loadRenderer
     *
     * Load a renderer
     *
     * @param   string  $format  The renderer type
     *
     * @return  mixed  Object or null if class does not exist
     * @since   1.0
     */
    public function loadRenderer($format)
    {
        $class = 'MolajoDocumentRenderer' . $format;

        if (class_exists($class)) {
        } else {
            $path = dirname(__FILE__) . '/' . $this->_type . '/renderer/' . $format . '.php';

            if (file_exists($path)) {
                require_once $path;
            } else {
                MolajoError::raiseError(500, MolajoTextHelper::_('Unable to load renderer class'));
            }
        }

        if (class_exists($class)) {
        } else {
            return null;
        }

        $instance = new $class($this);
        return $instance;
    }

    /**
     * parse
     *
     * Parses the document and prepares the buffers
     *
     * @return null
     */
    public function parse($parameters = array())
    {
        return null;
    }

    /**
     * render
     *
     * Outputs the document
     *
     * @param   boolean  $cache     If true, cache the output
     * @param   boolean  $compress  If true, compress the output
     * @param   array    $parameters    Associative array of attributes
     *
     * @return  The rendered data
     */
    public function render($cache = false, $parameters = array())
    {
        if ($mdate = $this->getModifiedDate()) {
            MolajoFactory::getApplication()->setHeader('Last-Modified', $mdate /* gmdate('D, d M Y H:i:s', time() + 900).' GMT' */);
        }

        MolajoFactory::getApplication()->setHeader('Content-Type', $this->_mime . '; charset=' . $this->_charset);
    }
}
