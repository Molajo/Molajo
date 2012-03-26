<?php
/**
 * @package   Molajo
 * @copyright 2012 Amy Stephen. All rights reserved.
 * @license   GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
namespace Molajo\Application\Service;

defined('MOLAJO') or die;

/**
 * Document
 *
 * @package   Molajo
 * @subpackage  Service
 * @since       1.0
 */
Class DocumentService
{
    /**
     * Static instance
     *
     * @var    object
     * @since  1.0
     */
    protected static $instance;

    /**
     * Links
     *
     * @var    string
     * @since  1.0
     */
    protected $links;

    /**
     * Metadata
     *
     * @var    array
     * @since  1.0
     */
    protected $metadata = array();

    /**
     * CSS
     *
     * @var    array
     * @since  1.0
     */
    protected $css = array();

    /**
     * CSS Declarations
     *
     * @var    array
     * @since  1.0
     */
    protected $css_declarations = array();

    /**
     * JS
     *
     * @var    string
     * @since  1.0
     */
    protected $js = array();

    /**
     * JS Declarations
     *
     * @var    array
     * @since  1.0
     */
    protected $js_declarations = array();

    /**
     * Mime encoding
     *
     * @var    string
     * @since  1.0
     */
    protected $mime_encoding;

    /**
     * Last Modified Date
     *
     * @var    string
     * @since  1.0
     */
    protected $last_modified;

    /**
     * getInstance
     *
     * @static
     * @return bool|object
     * @since  1.0
     */
    public static function getInstance()
    {
        if (empty(self::$instance)) {
            self::$instance = new DocumentService();
        }
        return self::$instance;
    }

    /**
     * __construct
     *
     * Class constructor.
     *
     * @return boolean
     * @since  1.0
     */
    public function __construct()
    {
        $this->set_mime_encoding();
    }

    /**
     * set_last_modified
     *
     * Last modified date
     *
     * @return null
     * @since  1.0
     */
    public function set_last_modified($value)
    {
        $this->last_modified = $value;
    }

    /**
     * get_last_modified
     *
     * Last modified date
     *
     * @return null
     * @since  1.0
     */
    public function get_last_modified()
    {
        return $this->last_modified;
    }

    /**
     * set_mime_encoding
     *
     * Sets the document MIME encoding that is sent to the browser.
     *
    \  	 * @param   string   $type
     *
     * @return void
     * @since   1.0
     */
    public function set_mime_encoding($type = 'text/html')
    {
        $this->mime_encoding = strtolower($type);
    }

    /**
     * get_mime_encoding
     *
     * Sets the document MIME encoding that is sent to the browser.
     *
    \  	 * @param   string   $type
     *
     * @return string
     * @since   1.0
     */
    public function get_mime_encoding()
    {
        return $this->mime_encoding;
    }

    /**
     * set_metadata
     *
     * Defines an entry of metadata to be displayed within the document head
     *
     * Example usage:
     * Services::Document()->set_metadata
    ('viewport', 'width=device-width, initial-scale=1, maximum-scale=1')
     *
     * @param   string  $name
     * @param   string  $content
     * @param   string  $context  True: http-equiv; False: standard; Otherise, provided
     *
     * @return  void
     * @since   1.0
     */
    public function set_metadata($name, $content, $context = false)
    {
        $name = strtolower($name);

        if (is_bool($context) && ($context === true)) {
            $this->metadata['http-equiv'][$name] = $content;

        } else if (is_string($context)) {
            $result = $this->metadata[$context][$name];

        } else {
            $this->metadata['standard'][$name] = $content;
        }
    }

    /**
     * get_metadata
     *
     * Retrieves an array of metadata defined for the document
     *
     * Example usage:
     * $metadata = Services::Document()->get_metadata();
     *
     * @return  string
     * @since   1.0
     */
    public function get_metadata()
    {
        return $this->metadata;
    }

    /**
     * add_link
     *
     * Adds <link> tags to the head of the document
     *
     * Example usage:
     * Services::Document()->add_link(
     *   $url = MOLAJO_EXTENSIONS_THEMES_URL
     *      . '/' . Services::Registry()->get('request\\theme_name')
     *      . '/' . 'images/apple-touch-icon-114x114.png',
     *   $relation = 'apple-touch-icon-precomposed',
     *   $relation_type = 'rel',
     *   $attributes = array('sizes,114x114')
     *  );
     *
     * Produces:
     * <link rel="apple-touch-icon-precomposed" sizes="114x114" href="images/apple-touch-icon-114x114.png" />
     *
     * @param  $url
     * @param  $relation
     * @param  $relation_type
     * @param  $attributes
     *
     * @return mixed
     */
    public function add_link($url, $relation, $relation_type = 'rel', $attributes = array())
    {
        $count = count($this->links);
        if ($count > 0) {
            foreach ($this->links as $link) {
                if ($link['url'] == $url) {
                    return;
                }
            }
        }
        $this->links[$count]['url'] = $url;
        $this->links[$count]['relation'] = $relation;
        $this->links[$count]['relation_type'] = $relation_type;
        $this->links[$count]['attributes'] = trim(implode(' ', $attributes));
    }

    /**
     * get_links
     *
     * Retrieves and returns the entire array of links
     *
     * Example usage:
     * $list = Services::Document()->get_links();
     *
     * @return array
     * @since  1.0
     */
    public function get_links()
    {
        return $this->links;
    }

    /**
     * add_css_folder
     *
     * Loads the CS located within the folder, as specified by the filepath
     *
     * Example usage:
     * Services::Document()->add_css_folder($file_path, $url_path, $priority);
     *
     * @param  string  $file_path
     * @param  string  $url_path
     * @param  integer $priority
     *
     * @return void
     * @since  1.0
     */
    public function add_css_folder($file_path, $url_path, $priority = 500)
    {
        if (Services::Filesystem()->folderExists($file_path . '/css')) {
        } else {
            return;
        }

        $files = Services::Filesystem()->folderFiles($file_path . '/css', '\.css$', false, false);

        if (count($files) > 0) {
            foreach ($files as $file) {
                if (substr($file, 0, 4) == 'rtl_') {
                    if (Services::Language()->get('direction') == 'rtl') {
                        $this->add_css($url_path . '/css/' . $file, $priority);
                    }
                } else {
                    $this->add_css($url_path . '/css/' . $file, $priority);
                }
            }
        }
    }

    /**
     * add_css
     *
     * Adds a linked stylesheet to the page
     *
     * Example usage:
     * Services::Document()->add_css($url_path . '/template.css');
     *
     * @param  string $url
     * @param  int    $priority
     * @param  string $mimetype
     * @param  null   $media
     * @param  array  $attributes
     *
     * @return mixed
     * @since  1.0
     */
    public function add_css($url, $priority = 500, $mimetype = 'text/css', $media = null, $attributes = array())
    {
        $count = count($this->css);
        if ($count > 0) {
            foreach ($this->css as $item) {
                if ($item['url'] == $url) {
                    return;
                }
            }
        }
        $this->css[$count]['url'] = $url;
        $this->css[$count]['mimetype'] = $mimetype;
        $this->css[$count]['media'] = $media;
        $this->css[$count]['attributes'] = trim(implode(' ', $attributes));
        $this->css[$count]['priority'] = $priority;
    }

    /**
     * get_css
     *
     * Retrieves array of css links
     *
     * Example usage:
     * $list = Services::Document()->get_css();
     *
     * @return array
     * @since  1.0
     */
    public function get_css()
    {
        return $this->css;
    }

    /**
     * add_css_declaration
     *
     * Adds a css declaration to the array for later rendering
     *
     * Example usage:
     * Services::Document()->add_css_declaration
    ($css_in_here, 'text/css');
     *
     * @param   string  $content
     * @param   string  $format
     *
     * @return  void
     * @since   1.0
     */
    public function add_css_declaration($content, $mimetype = 'text/css')
    {
        $count = count($this->css_declarations);
        if ($count > 0) {
            foreach ($this->css_declarations as $item) {
                if ($item['content'] == $content) {
                    return;
                }
            }
        }
        $this->css_declarations[$count]['mimetype'] = $mimetype;
        $this->css_declarations[$count]['content'] = $content;
    }

    /**
     * get_css_declarations
     *
     * Retrieves the array of CSS declarations
     *
     * Example usage:
     * $list = Services::Document()->get_css_declarations();
     *
     * @return array
     * @since  1.0
     */
    public function get_css_declarations()
    {
        return $this->css_declarations;
    }

    /**
     * add_js_folder
     *
     * Loads the JS Files located within the folder specified by the filepath
     *
     * Example usage:
     * Services::Document()->add_js_folder($file_path, $url_path, $priority, 0);
     *
     * @param  $file_path
     * @param  $url_path
     * @return void
     * @since  1.0
     */
    public function add_js_folder($file_path, $url_path, $priority = 500, $defer = 0)
    {
        if ($defer == 1) {
            $extra = '/js/defer';
        } else {
            $extra = '/js';
            $defer = 0;
        }
        if (Services::Filesystem()->folderExists($file_path . $extra)) {
        } else {
            return;
        }

        $files = Services::Filesystem()->folderFiles($file_path . $extra, '\.js$', false, false);

        if (count($files) > 0) {
            foreach ($files as $file) {
                $this->add_js($url_path . $extra . '/' . $file, $priority, $defer, 'text/javascript');
            }
        }
    }

    /**
     * add_js
     *
     * Adds a linked script to the page
     *
     * Example usage:
     * Services::Document()->add_js
     *   ('http://html5shim.googlecode.com/svn/trunk/html5.js', 1000);
     *
     * @param  $url
     * @param  int $priority
     * @param  bool $defer
     * @param  string $mimetype
     * @param  bool $async
     *
     * @return mixed
     * @since  1.0
     */
    public function add_js($url, $priority = 500, $defer = 0, $mimetype = "text/javascript", $async = false)
    {
        if ($defer == 1) {
        } else {
            $defer = 0;
        }

        $count = count($this->js);

        if ($count > 0) {
            foreach ($this->js as $item) {
                if ($item['url'] == $url) {
                    return;
                }
            }
        }
        $this->js[$count]['url'] = $url;
        $this->js[$count]['mimetype'] = $mimetype;
        $this->js[$count]['defer'] = $defer;
        $this->js[$count]['async'] = $async;
        $this->js[$count]['priority'] = $priority;
    }

    /**
     * get_js
     *
     * Retrieves an array of JS links for presentation at the top (defer = 0)
     * or bottom (defer = 1) of the page
     *
     * Example usage:
     * $list = Services::Document()->get_js($defer);
     *
     * @return array
     */
    public function get_js($defer = 0)
    {
        if ($defer == 1) {
        } else {
            $defer = 0;
        }

        $results = array();

        $count = count($this->js);

        if ($count > 0) {
            foreach ($this->js as $item) {
                if ($item['defer'] == $defer) {
                    $results[] = $item;
                }
            }
        }
        return $results;
    }

    /**
     * add_js_declaration
     *
     * Adds a js declaration to an array for later rendering
     *
     * Example usage:
     * Services::Document()->add_js_declaration
     *    ($fallback, 'text/javascript', 1000);
     *
     * @param  string  $content
     * @param  string  $format
     * @param  string  $defer
     *
     * @return  void
     * @since    1.0
     */
    public function add_js_declaration($content, $mimetype = 'text/javascript', $defer = 0)
    {
        if ($defer == 1) {
        } else {
            $defer = 0;
        }

        $count = count($this->js_declarations);
        if ($count > 0) {
            foreach ($this->js_declarations as $script) {
                if ($script['content'] == $script) {
                    return;
                }
            }
        }
        $this->js_declarations[$count]['mimetype'] = $mimetype;
        $this->js_declarations[$count]['content'] = $content;
        $this->js_declarations[$count]['defer'] = $defer;
    }

    /**
     * get_js_declarations
     *
     * Retrieves an array of JS declarations
     *
     * Example usage:
     * $list = Services::Document()->get_js_declarations($defer);
     *
     * @param bool $defer
     *
     * @return array
     * @since  1.0
     */
    public function get_js_declarations($defer = 0)
    {
        if ($defer == 1) {
        } else {
            $defer = 0;
        }

        $results = array();

        $count = count($this->js_declarations);

        if ($count > 0) {
            foreach ($this->js_declarations as $item) {
                if ($item['defer'] == $defer) {
                    $results[] = $item;
                }
            }
        }
        return $results;
    }
}
