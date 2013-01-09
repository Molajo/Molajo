<?php
/**
 * Asset Service
 *
 * @package      Niambie
 * @license      MIT
 * @copyright    2013 Amy Stephen. All rights reserved.
 */
namespace Molajo\Service\Services\Asset;

defined('NIAMBIE') or die;

/**
 * The Asset Service is used within the Theme Service, Plugins, and MVC classes to indicate
 * Asset files, such as CSS, JS, and Links to loaded during the Head and Defer Include Template
 * Rendering Process.
 *
 * @author       Amy Stephen
 * @license      MIT
 * @copyright    2013 Amy Stephen. All rights reserved.
 * @since        1.0
 */
Class AssetService
{
    /**
     * Direction
     *
     * @var    object
     * @since  1.0
     */
    protected $direction = null;

    /**
     * HTML5
     *
     * @var    object
     * @since  1.0
     */
    protected $html5 = null;

    /**
     * Line end
     *
     * @var    object
     * @since  1.0
     */
    protected $line_end = null;

    /**
     * Mimetype
     *
     * @var    object
     * @since  1.0
     */
    protected $mimetype = 'text/html';

    /**
     * Links
     *
     * @var    object
     * @since  1.0
     */
    protected $links = null;

    /**
     * Links Priorities
     *
     * @var    object
     * @since  1.0
     */
    protected $links_priorities = null;

    /**
     * Css
     *
     * @var    object
     * @since  1.0
     */
    protected $css = null;

    /**
     * CSS Priorities
     *
     * @var    object
     * @since  1.0
     */
    protected $css_priorities = null;

    /**
     * Css Declarations
     *
     * @var    object
     * @since  1.0
     */
    protected $css_declarations = null;

    /**
     * CSS Declarations Priorities
     *
     * @var    object
     * @since  1.0
     */
    protected $css_declarations_priorities = null;

    /**
     * Js
     *
     * @var    object
     * @since  1.0
     */
    protected $js = null;

    /**
     * Js Priorities
     *
     * @var    object
     * @since  1.0
     */
    protected $js_priorities = null;

    /**
     * Js Defer
     *
     * @var    object
     * @since  1.0
     */
    protected $js_defer = null;

    /**
     * Js Defer Priorities
     *
     * @var    object
     * @since  1.0
     */
    protected $js_defer_priorities = null;

    /**
     * JS Declarations
     *
     * @var    object
     * @since  1.0
     */
    protected $js_declarations = null;

    /**
     * JS Declarations Priorities
     *
     * @var    object
     * @since  1.0
     */
    protected $js_declarations_priorities = null;

    /**
     * JS Declarations Defer
     *
     * @var    object
     * @since  1.0
     */
    protected $js_declarations_defer = null;

    /**
     * JS Declarations Defer Priorities
     *
     * @var    object
     * @since  1.0
     */
    protected $js_declarations_defer_priorities = null;

    /**
     * List of Properties
     *
     * @var    object
     * @since  1.0
     */
    protected $property_array = array(
        'direction',
        'html5',
        'mimetype',
        'line_end',
        'links',
        'link_priorities',
        'css',
        'css_priorities',
        'css_declarations',
        'css_declarations_priorities',
        'js',
        'js_priorities',
        'js_defer',
        'js_defer_priorities',
        'js_declarations',
        'js_declarations_priorities',
        'js_declarations_defer',
        'js_declarations_defer_priorities'
    );

    /**
     * Initialise
     *
     * @return  null
     * @since   1.0
     */
    public function initialise()
    {
        $this->set('links', array());
        $this->set('link_priorities', array());

        $this->set('css', array());
        $this->set('css_priorities', array());

        $this->set('css_declarations', array());
        $this->set('css_declarations_priorities', array());

        $this->set('js', array());
        $this->set('js_priorities', array());

        $this->set('js_defer', array());
        $this->set('js_defer_priorities', array());

        $this->set('js_declarations', array());
        $this->set('js_declarations_priorities', array());

        $this->set('js_declarations_defer', array());
        $this->set('js_declarations_defer_priorities', array());

        return;
    }

    /**
     * Get the current value (or default) of the specified key
     *
     * @param   string  $key
     * @param   mixed   $default
     *
     * @return  mixed
     * @since   1.0
     * @throws  \OutOfRangeException
     */
    public function get($key = null, $default = null)
    {
        $key = strtolower($key);

        if (in_array($key, $this->property_array)) {
        } else {
            throw new \OutOfRangeException
            ('Asset Service: attempting to get value for unknown property: ' . $key);
        }

        $this->$key = $default;

        return $this->$key;
    }

    /**
     * Set the value of the specified key
     *
     * @param   string  $key
     * @param   mixed   $value
     *
     * @return  mixed
     * @since   1.0
     * @throws  \OutOfRangeException
     */
    public function set($key, $value = null)
    {
        $key = strtolower($key);

        if (in_array($key, $this->property_array)) {
        } else {
            throw new \OutOfRangeException
            ('Asset Service: attempting to set value for unknown key: ' . $key);
        }

        $this->$key = $value;

        return $this->$key;
    }

    /**
     * addLink - Adds <link> tags to the head of the document
     *
     * Usage:
     *
     * $this->assets->addLink(
     *   $url = EXTENSIONS_THEMES_URL
     *      . '/' . Services::Registry()->get('parameters', 'theme_path_node')
     *      . '/' . 'images/apple-touch-icon-114x114.png',
     *   $relation = 'apple-touch-icon-precomposed',
     *   $relation_type = 'rel',
     *   $attributes = array('sizes,114x114')
     *  );
     *
     * Produces:
     * <link rel="apple-touch-icon-precomposed" sizes="114x114" href="images/apple-touch-icon-114x114.png" />
     *
     * @param   string  $url
     * @param   string  $relation
     * @param   string  $relation_type
     * @param   array   $attributes
     * @param   int     $priority
     *
     * @return  object  AssetService
     * @since   1.0
     */
    public function addLink($url, $relation, $relation_type = 'rel', $attributes = array(), $priority = 500)
    {
        if (trim($url) == '') {
            return $this;
        }

        $temp_links = $this->get('links', array());

        $temp_row = new \stdClass();

        $temp_row->url           = $url;
        $temp_row->relation      = $relation;
        $temp_row->relation_type = $relation_type;
        $temp_row->attributes    = '';

        $temp = trim(implode(' ', $attributes));
        if (trim($temp) == '') {
        } elseif (count($temp) == 1) {
            $temp = array($temp);
        }
        if (is_array($temp) && count($temp) > 0) {
            foreach ($temp as $pair) {
                $split = explode(',', $pair);
                $temp_row->attributes .= ' ' . $split[0]
                    . '="'
                    . $split[1]
                    . '"';
            }
        }
        $temp_row->priority = $priority;

        $temp_links[] = $temp_row;

        $this->set('links', $temp_links);

        $priorities = $this->get('link_priorities');

        if (count($priorities) > 0) {
            if (in_array($priority, $priorities)) {
            } else {
                $priorities[] = $priority;
            }
        } else {
            $priorities[] = $priority;
        }

        $this->set('link_priorities', $priorities);

        return $this;
    }

    /**
     * addCssFolder - Loads the CS located within the folder, as specified by the file path
     *
     * Usage:
     * $this->assets->addCssFolder($file_path, $url_path, $priority);
     *
     * @param   string   $file_path
     * @param   string   $url_path
     * @param   integer  $priority
     *
     * @return  object
     * @since   1.0
     */
    public function addCssFolder($file_path, $url_path, $priority = 500)
    {
        if (is_dir($file_path . '/css')) {
        } else {
            return $this;
        }

        $files = \files($file_path);

        if (count($files) > 0) {

            foreach ($files as $file) {
                $add = 0;
                if (substr($file, 0, 4) == 'ltr_') {
                    if ($this->get('language', 'direction') == 'rtl') {
                    } else {
                        $add = 1;
                    }

                } elseif (substr($file, 0, 4) == 'rtl_') {

                    if ($this->get('language', 'direction') == 'rtl') {
                        $add = 1;
                    }

                } elseif (strtolower(substr($file, 0, 4)) == 'hold') {

                } else {
                    $add = 1;
                }

                if ($add == 1) {
                    $this->addCss($url_path . '/css/' . $file, $priority);
                }
            }
        }

        return $this;
    }

    /**
     * addCss - Adds a linked stylesheet to the page
     *
     * Usage:
     * $this->assets->addCss($url_path . '/template.css');
     *
     * @param   string  $url
     * @param   int     $priority
     * @param   string  $mimetype
     * @param   string  $media
     * @param   string  $conditional
     * @param   array   $attributes
     *
     * @return  mixed
     * @since   1.0
     */
    public function addCss(
        $url,
        $priority = 500,
        $mimetype = 'text/css',
        $media = '',
        $conditional = '',
        $attributes = array()
    ) {
        $css = $this->get('css', array());

        foreach ($css as $item) {

            if ($item->url == $url
                && $item->mimetype == $mimetype
                && $item->media == $media
                && $item->conditional == $conditional
            ) {
                return $this;
            }
        }

        $temp_row = new \stdClass();

        $temp_row->url         = $url;
        $temp_row->priority    = $priority;
        $temp_row->mimetype    = $mimetype;
        $temp_row->media       = $media;
        $temp_row->conditional = $conditional;
        $temp_row->attributes  = trim(implode(' ', $attributes));

        $css[] = $temp_row;

        $this->set('css', $css);

        $priorities = $this->get('css_priorities', array());

        if (in_array($priority, $priorities)) {
        } else {
            $priorities[] = $priority;
        }

        sort($priorities);

        $this->set('css_priorities', $priorities);

        return $this;
    }

    /**
     * addCssDeclaration - Adds a css declaration to the array for later rendering
     *
     * Usage:
     * $this->assets->addCssDeclaration($css_in_here, 'text/css');
     *
     * @param  string  $content
     * @param  string  $mimetype
     * @param  int     $priority
     *
     * @return  object
     * @since   1.0
     */
    public function addCssDeclaration($content, $mimetype = 'text/css', $priority = 500)
    {
        $css = $this->get('css_declarations');

        if (is_array($css) && count($css) > 0) {
            foreach ($css as $item) {
                if ($item->content == $content) {
                    return $this;
                }
            }
        }

        $temp_row = new \stdClass();

        $temp_row->mimetype = $mimetype;
        $temp_row->content  = $content;
        $temp_row->priority = $priority;

        $css[] = $temp_row;

        $this->set('css_declarations', $css);

        $priorities = $this->get('css_declarations_priorities', array());

        if (in_array($priority, $priorities)) {
        } else {
            $priorities[] = $priority;
        }

        sort($priorities);

        $this->set('css_declarations_priorities', $priorities);

        return $this;
    }

    /**
     * addJsFolder - Loads the JS Files located within the folder specified by the filepath
     *
     * Usage:
     * $this->assets->addJsFolder($file_path, $url_path, $priority, 0);
     *
     * @param   string  $file_path
     * @param   string  $url_path
     * @param   int     $priority
     * @param   int     $defer
     *
     * @return  void
     * @since   1.0
     */
    public function addJsFolder($file_path, $url_path, $priority = 500, $defer = 0)
    {
        if ($defer == 1) {
            $extra = '/js/defer';
        } else {
            $extra = '/js';
            $defer = 0;
        }

        if (is_dir($file_path . $extra)) {
        } else {
            return;
        }
        // .js
        $files = \files($file_path . $extra);

        if (count($files) > 0) {
            foreach ($files as $file) {
                if (strtolower(substr($file, 0, 4)) == 'hold') {
                } else {
                    $this->addJs(
                        $url_path . $extra . '/' . $file,
                        $priority,
                        $defer,
                        'text/javascript',
                        0
                    );
                }
            }
        }

        return;
    }

    /**
     * addJs - Adds a linked script to the page
     *
     * Usage:
     * $this->assets->addJs('http://example.com/test.js', 1000, 1);
     *
     * @param   string  $url
     * @param   int     $priority
     * @param   int     $defer
     * @param   string  $mimetype
     * @param   bool    $async
     *
     * @return  object  AssetService
     * @since   1.0
     */
    public function addJs($url, $priority = 500, $defer = 0, $mimetype = "text/javascript", $async = false)
    {
        if ($defer == 1) {
            $js = $this->get('js_defer', array());
        } else {
            $js = $this->get('js', array());
        }

        foreach ($js as $item) {
            if ($item->url == $url) {
                return $this;
            }
        }

        $temp_row = new \stdClass();

        $temp_row->url      = $url;
        $temp_row->priority = $priority;
        $temp_row->mimetype = $mimetype;
        $temp_row->async    = $async;
        $temp_row->defer    = $defer;

        $js[] = $temp_row;

        if ($defer == 1) {
            $this->set('js_defer', $js);
        } else {
            $this->set('js', $js);
        }

        if ($defer == 1) {
            $priorities = $this->get('js_defer_priorities', $js);
        } else {
            $priorities = $this->get('js_priorities', $js);
        }

        if (in_array($priority, $priorities)) {
        } else {
            $priorities[] = $priority;
        }

        sort($priorities);

        if ($defer == 1) {
            $this->set('js_defer_priorities', $priorities);
        } else {
            $this->set('js_priorities', $priorities);
        }

        return $this;
    }

    /**
     * addJSDeclarations - Adds a js declaration to an array for later rendering
     *
     * Usage:
     * $this->assets->addJSDeclarations($fallback, 'text/javascript', 1000);
     *
     * @param   string  $content
     * @param   int     $priority
     * @param   int     $defer
     * @param   string  $mimetype
     *
     * @return  object  AssetService
     * @since   1.0
     */
    public function addJSDeclarations($content, $priority = 500, $defer = 0, $mimetype = 'text/javascript')
    {
        if ($defer == 1) {
            $js = $this->get('js_declarations_defer', array());
        } else {
            $js = $this->get('js_declarations', array());
        }

        foreach ($js as $item) {
            if ($item->content == $content) {
                return $this;
            }
        }

        $temp_row = new \stdClass();

        $temp_row->content  = $content;
        $temp_row->mimetype = $mimetype;
        $temp_row->defer    = $defer;
        $temp_row->priority = $priority;

        $js[] = $temp_row;

        if ($defer == 1) {
            $this->set('js_declarations_defer', $js);
        } else {
            $this->set('js_declarations', $js);
        }

        if ($defer == 1) {
            $priorities = $this->get('js_declarations_defer_priorities', array());
        } else {
            $priorities = $this->get('js_declarations_priorities', array());
        }

        if (is_array($priorities)) {
        } else {
            $priorities = array();
        }

        if (in_array($priority, $priorities)) {
        } else {
            $priorities[] = $priority;
        }

        sort($priorities);

        if ($defer == 1) {
            $this->set('js_declarations_defer_priorities', $priorities);
        } else {
            $this->set('js_declarations_priorities', $priorities);
        }

        return $this;
    }

    /**
     * setPriority - use to override the priority of a specific file
     *
     * Usage:
     * $this->assets->setPriority('css', 'http://example.com/media/1236_grid.css', 1);
     *
     * @param   string  $type
     * @param   string  $url
     * @param   string  $priority
     *
     * @return  array
     * @since   1.0
     */
    public function setPriority($type, $url, $priority)
    {
        $temp = $this->get($type);

        if (is_array($temp) && count($temp) > 0) {
        } else {
            return array();
        }

        $update = false;

        $query_results = array();

        foreach ($temp as $temp_row) {

            if (isset($temp_row->url)) {

                if ($temp_row->url == $url) {
                    echo $priority;
                    $temp_row->priority = $priority;
                    $update             = true;
                }
            }
            $query_results[] = $temp_row;
        }

        if ($update === true) {
            $this->set($type, $query_results);

            $priorityType = $type . '_priorities';

            $priorities = array();
            foreach ($temp as $temp_row) {
                if (in_array($temp_row->priority, $priorities)) {
                } else {
                    $priorities[] = $temp_row->priority;
                }
            }

            sort($priorities);

            $this->set($priorityType, $priorities);
        }

        return $this;
    }

    /**
     * remove - use to remove a specific Asset
     *
     * Usage:
     * $this->assets->remove('css', 'http://example.com/media/1236_grid.css');
     *
     * @param   string  $type
     * @param   string  $url
     *
     * @return  array
     * @since   1.0
     */
    public function remove($type, $url)
    {
        $temp = $this->get($type);
        if (is_array($temp) && count($temp) > 0) {
        } else {
            return array();
        }

        $update        = false;
        $query_results = array();
        foreach ($temp as $temp_row) {
            if (isset($temp_row->url)) {
                if ($temp_row->url == $url) {
                    $update = true;
                } else {
                    $query_results[] = $temp_row;
                }
            }
        }

        if ($update === true) {
            $this->set($type, $query_results);

            $priorityType = $type . '_priorities';

            $priorities = array();
            foreach ($temp as $temp_row) {
                if (in_array($temp_row->priority, $priorities)) {
                } else {
                    $priorities[] = $temp_row->priority;
                }
            }

            sort($priorities);
            $this->set($priorityType, $priorities);
        }

        return $this;
    }

    /**
     * MVC Assets Query producing output for rendering the View
     *
     * @param   string  $type
     *
     * @return  array
     * @since   1.0
     */
    public function getAssets($type)
    {
        $priorityType = $type . 'Priorities';

        $temp = $this->get($type);

        if (is_array($temp) && count($temp) > 0) {
        } else {
            return array();
        }

        $priorities = $this->get($priorityType);
        sort($priorities);

        $query_results = array();

        foreach ($priorities as $priority) {

            foreach ($temp as $temp_row) {

                $include = false;

                if (isset($temp_row->priority)) {
                    if ($temp_row->priority == $priority) {
                        $include = true;
                    }
                }

                if ($include === false) {
                } else {
                    $temp_row->application_html5 = $this->html5;
                    $temp_row->end               = $this->line_end;
                    $temp_row->page_mimetype     = $this->mimetype;
                    $query_results[]             = $temp_row;
                }
            }
        }

        return $query_results;
    }
}
