<?php
/**
 * @package    Niambie
 * @copyright  2012 Amy Stephen. All rights reserved.
 * @license    GNU GPL v 2, or later and MIT, see License folder
 */
namespace Molajo\Service\Services\Asset;

use Molajo\Service\Services;

defined('NIAMBIE') or die;

/**
 * Asset
 *
 * @package     Niambie
 * @subpackage  Service
 * @since       1.0
 */
Class AssetService
{
    /**
     * Initialise
     *
     * @return  boolean
     * @since   1.0
     */
    public function Initialise()
    {
        Services::Registry()->createRegistry(ASSETS_LITERAL);

        Services::Registry()->set(ASSETS_LITERAL, LINKS_LITERAL, array());
        Services::Registry()->set(ASSETS_LITERAL, LINKS_LITERAL . 'Priorities', array());

        Services::Registry()->set(ASSETS_LITERAL, CSS_LITERAL, array());
        Services::Registry()->set(ASSETS_LITERAL, CSS_LITERAL . 'Priorities', array());

        Services::Registry()->set(ASSETS_LITERAL, CSS_DECLARATIONS_LITERAL, array());
        Services::Registry()->set(ASSETS_LITERAL, CSS_DECLARATIONS_LITERAL . 'Priorities', array());

        Services::Registry()->set(ASSETS_LITERAL, JS_LITERAL, array());
        Services::Registry()->set(ASSETS_LITERAL, JS_LITERAL . 'Priorities', array());

        Services::Registry()->set(ASSETS_LITERAL, JS_DEFER_LITERAL, array());
        Services::Registry()->set(ASSETS_LITERAL, JS_DEFER_LITERAL . 'Priorities', array());

        Services::Registry()->set(ASSETS_LITERAL, JS_DECLARATIONS_LITERAL, array());
        Services::Registry()->set(ASSETS_LITERAL, JS_DECLARATIONS_LITERAL . 'Priorities', array());

        Services::Registry()->set(ASSETS_LITERAL, JS_DECLARATIONS_DEFER_LITERAL, array());
        Services::Registry()->set(ASSETS_LITERAL, JS_DECLARATIONS_DEFER_LITERAL . 'Priorities', array());

        return;
    }

    /**
     * addLink - Adds <link> tags to the head of the document
     *
     * Usage:
     *
     * Services::Asset()->addLink(
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
     * @param   $url
     * @param   $relation
     * @param   $relation_type
     * @param   $attributes
     * @param   $prioritu
     *
     * @return  object
     * @since   1.0
     */
    public function addLink($url, $relation, $relation_type = 'rel', $attributes = array(), $priority = 500)
    {
        if (trim($url) == '') {
            return $this;
        }
        $temp_links = Services::Registry()->get(ASSETS_LITERAL, LINKS_LITERAL, array());

        $temp_row = new \stdClass();

        $temp_row->url = $url;
        $temp_row->relation = Services::Filter()->escape_text($relation);
        $temp_row->relation_type = Services::Filter()->escape_text($relation_type);
        $temp_row->attributes = '';

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
                    . Services::Filter()->escape_text($split[1])
                    . '"';
            }
        }
        $temp_row->priority = $priority;

        $temp_links[] = $temp_row;

        Services::Registry()->set(ASSETS_LITERAL, LINKS_LITERAL, $temp_links);

        $priorities = Services::Registry()->get(ASSETS_LITERAL, LINKS_LITERAL . 'Priorities');

        if (count($priorities) > 0) {
            if (in_array($priority, $priorities)) {
            } else {
                $priorities[] = $priority;
            }
        } else {
            $priorities[] = $priority;
        }

        Services::Registry()->set(ASSETS_LITERAL, LINKS_LITERAL . 'Priorities', $priorities);

        return $this;
    }

    /**
     * addCssFolder - Loads the CS located within the folder, as specified by the file path
     *
     * Usage:
     * Services::Asset()->addCssFolder($file_path, $url_path, $priority);
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
        if (Services::Filesystem()->folderExists($file_path . '/css')) {
        } else {
            return $this;
        }

        $files = Services::Filesystem()->folderFiles($file_path . '/css', '\.css$', false, false);

        if (count($files) > 0) {

            foreach ($files as $file) {
                $add = 0;
                if (substr($file, 0, 4) == 'ltr_') {
                    if (Services::Language()->get('direction') == 'rtl') {
                    } else {
                        $add = 1;
                    }

                } elseif (substr($file, 0, 4) == 'rtl_') {

                    if (Services::Language()->get('direction') == 'rtl') {
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
     * Services::Asset()->addCss($url_path . '/template.css');
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
        $css = Services::Registry()->get(ASSETS_LITERAL, 'Css', array());

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

        $temp_row->url = $url;
        $temp_row->priority = $priority;
        $temp_row->mimetype = $mimetype;
        $temp_row->media = $media;
        $temp_row->conditional = $conditional;
        $temp_row->attributes = trim(implode(' ', $attributes));

        $css[] = $temp_row;

        Services::Registry()->set(ASSETS_LITERAL, CSS_LITERAL, $css);

        $priorities = Services::Registry()->get(ASSETS_LITERAL, CSS_LITERAL . 'Priorities', array());

        if (in_array($priority, $priorities)) {
        } else {
            $priorities[] = $priority;
        }

        sort($priorities);

        Services::Registry()->set(ASSETS_LITERAL, CSS_LITERAL . 'Priorities', $priorities);

        return $this;
    }

    /**
     * addCssDeclaration - Adds a css declaration to the array for later rendering
     *
     * Usage:
     * Services::Asset()->addCssDeclaration($css_in_here, 'text/css');
     *
     * @param   $content
     * @param   string    $mimetype
     * @param   int       $priority
     *
     * @return  object
     * @since   1.0
     */
    public function addCssDeclaration($content, $mimetype = 'text/css', $priority = 500)
    {
        $css = Services::Registry()->get(ASSETS_LITERAL, CSS_DECLARATIONS_LITERAL);

        if (is_array($css) && count($css) > 0) {
            foreach ($css as $item) {
                if ($item->content == $content) {
                    return $this;
                }
            }
        }

        $temp_row = new \stdClass();

        $temp_row->mimetype = $mimetype;
        $temp_row->content = $content;
        $temp_row->priority = $priority;

        $css[] = $temp_row;

        Services::Registry()->set(ASSETS_LITERAL, CSS_DECLARATIONS_LITERAL, $css);

        $priorities = Services::Registry()->get(ASSETS_LITERAL, CSS_DECLARATIONS_LITERAL . 'Priorities', array());

        if (in_array($priority, $priorities)) {
        } else {
            $priorities[] = $priority;
        }

        sort($priorities);

        Services::Registry()->set(ASSETS_LITERAL, CSS_DECLARATIONS_LITERAL . 'Priorities', $priorities);

        return $this;
    }

    /**
     * addJsFolder - Loads the JS Files located within the folder specified by the filepath
     *
     * Usage:
     * Services::Asset()->addJsFolder($file_path, $url_path, $priority, 0);
     *
     * @param   $file_path
     * @param   $url_path
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

        if (Services::Filesystem()->folderExists($file_path . $extra)) {
        } else {
            return;
        }

        $files = Services::Filesystem()->folderFiles($file_path . $extra, '\.js$', false, false);

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
     * Services::Asset()->addJs('http://example.com/test.js', 1000, 1);
     *
     * @param   string  $url
     * @param   int     $priority
     * @param   bool    $defer
     * @param   string  $mimetype
     * @param   bool    $async
     *
     * @return  mixed
     * @since   1.0
     */
    public function addJs($url, $priority = 500, $defer = 0, $mimetype = "text/javascript", $async = false)
    {
        if ($defer == 1) {
            $js = Services::Registry()->get(ASSETS_LITERAL, JS_DEFER_LITERAL, array());
        } else {
            $js = Services::Registry()->get(ASSETS_LITERAL, JS_LITERAL, array());
        }

        foreach ($js as $item) {
            if ($item->url == $url) {
                return $this;
            }
        }

        $temp_row = new \stdClass();

        $temp_row->url = $url;
        $temp_row->priority = $priority;
        $temp_row->mimetype = $mimetype;
        $temp_row->async = $async;
        $temp_row->defer = $defer;

        $js[] = $temp_row;

        if ($defer == 1) {
            Services::Registry()->set(ASSETS_LITERAL, JS_DEFER_LITERAL, $js);
        } else {
            Services::Registry()->set(ASSETS_LITERAL, JS_LITERAL, $js);
        }

        if ($defer == 1) {
            $priorities = Services::Registry()->get(ASSETS_LITERAL, JS_DEFER_LITERAL . 'Priorities', $js);
        } else {
            $priorities = Services::Registry()->get(ASSETS_LITERAL, JS_LITERAL . 'Priorities', $js);
        }

        if (in_array($priority, $priorities)) {
        } else {
            $priorities[] = $priority;
        }

        sort($priorities);

        if ($defer == 1) {
            Services::Registry()->set(ASSETS_LITERAL, JS_DEFER_LITERAL . 'Priorities', $priorities);
        } else {
            Services::Registry()->set(ASSETS_LITERAL, JS_LITERAL . 'Priorities', $priorities);
        }

        return $this;
    }

    /**
     * addJSDeclarations - Adds a js declaration to an array for later rendering
     *
     * Usage:
     * Services::Asset()->addJSDeclarations($fallback, 'text/javascript', 1000);
     *
     * @param   string  $content
     * @param   string  $priority
     * @param   string  $defer
     * @param   string  $mimetype
     *
     * @return  object
     * @since   1.0
     */
    public function addJSDeclarations($content, $priority = 500, $defer = 0, $mimetype = 'text/javascript')
    {
        if ($defer == 1) {
            $js = Services::Registry()->get(ASSETS_LITERAL, JS_DECLARATIONS_DEFER_LITERAL, array());
        } else {
            $js = Services::Registry()->get(ASSETS_LITERAL, JS_DECLARATIONS, array());
        }

        foreach ($js as $item) {
            if ($item->content == $content) {
                return $this;
            }
        }

        $temp_row = new \stdClass();

        $temp_row->content = $content;
        $temp_row->mimetype = $mimetype;
        $temp_row->defer = $defer;
        $temp_row->priority = $priority;

        $js[] = $temp_row;

        if ($defer == 1) {
            Services::Registry()->set(ASSETS_LITERAL, JS_DECLARATIONS_DEFER_LITERAL, $js);
        } else {
            Services::Registry()->set(ASSETS_LITERAL, JS_DECLARATIONS, $js);
        }

        if ($defer == 1) {
            $priorities = Services::Registry()->get(
                ASSETS_LITERAL,
                JS_DECLARATIONS_DEFER_LITERAL . 'Priorities',
                array()
            );
        } else {
            $priorities = Services::Registry()->get(ASSETS_LITERAL, JS_DECLARATIONS_LITERAL . 'Priorities', array());
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
            Services::Registry()->set(ASSETS_LITERAL, JS_DECLARATIONS_DEFER_LITERAL . 'Priorities', $priorities);
        } else {
            Services::Registry()->set(ASSETS_LITERAL, JS_DECLARATIONS_LITERAL . 'Priorities', $priorities);
        }

        return $this;
    }

    /**
     * setPriority - use to override the priority of a specific file
     *
     * Usage:
     * Services::Asset()->setPriority('Css', 'http://example.com/media/1236_grid.css', 1);
     *
     * @param   $type
     * @param   $url
     * @param   $priority
     *
     * @return  array
     * @since   1.0
     */
    public function setPriority($type, $url, $priority)
    {
        $temp_rows = Services::Registry()->get(ASSETS_LITERAL, $type);

        if (is_array($temp_rows) && count($temp_rows) > 0) {
        } else {
            return array();
        }

        $update = false;

        $query_results = array();

        foreach ($temp_rows as $temp_row) {

            if (isset($temp_row->url)) {

                if ($temp_row->url == $url) {
                    echo $priority;
                    $temp_row->priority = $priority;
                    $update = true;
                }
            }
            $query_results[] = $temp_row;
        }

        if ($update === true) {
            Services::Registry()->set(ASSETS_LITERAL, $type, $query_results);

            $priorityType = $type . 'Priorities';

            $priorities = array();
            foreach ($temp_rows as $temp_row) {
                if (in_array($temp_row->priority, $priorities)) {
                } else {
                    $priorities[] = $temp_row->priority;
                }
            }

            sort($priorities);

            Services::Registry()->set(ASSETS_LITERAL, $priorityType, $priorities);
        }

        return $this;
    }

    /**
     * remove - use to remove a specific Asset
     *
     * Usage:
     * Services::Asset()->remove('Css', 'http://example.com/media/1236_grid.css');
     *
     * @param   $type
     * @param   $url
     * @param   $priority
     *
     * @return  array
     * @since   1.0
     */
    public function remove($type, $url)
    {
        $temp_rows = Services::Registry()->get(ASSETS_LITERAL, $type);
        if (is_array($temp_rows) && count($temp_rows) > 0) {
        } else {
            return array();
        }

        $update = false;
        $query_results = array();
        foreach ($temp_rows as $temp_row) {
            if (isset($temp_row->url)) {
                if ($temp_row->url == $url) {
                    $update = true;
                } else {
                    $query_results[] = $temp_row;
                }
            }
        }

        if ($update === true) {
            Services::Registry()->set(ASSETS_LITERAL, $type, $query_results);

            $priorityType = $type . 'Priorities';

            $priorities = array();
            foreach ($temp_rows as $temp_row) {
                if (in_array($temp_row->priority, $priorities)) {
                } else {
                    $priorities[] = $temp_row->priority;
                }
            }

            sort($priorities);
            Services::Registry()->set(ASSETS_LITERAL, $priorityType, $priorities);
        }

        return $this;
    }

    /**
     * Parsing locates include Asset statements and requests data to pass into MVC to render Document
     *
     * @param   $type
     *
     * @return  array
     * @since   1.0
     */
    public function getAssets($type)
    {
        $priorityType = $type . 'Priorities';

        $temp_rows = Services::Registry()->get(ASSETS_LITERAL, $type);

        if (is_array($temp_rows) && count($temp_rows) > 0) {
        } else {
            return array();
        }

        $application_html5 = Services::Registry()->get(CONFIGURATION_LITERAL, 'application_html5', 1);
        if ((int)Services::Registry()->get(CONFIGURATION_LITERAL, 'application_html5', 1) == 1) {
            $end = '>' . chr(10);
        } else {
            $end = '/>' . chr(10);
        }

        $priorities = Services::Registry()->get(ASSETS_LITERAL, $priorityType);
        sort($priorities);

        $query_results = array();

        foreach ($priorities as $priority) {

            foreach ($temp_rows as $temp_row) {

                $include = false;

                if (isset($temp_row->priority)) {
                    if ($temp_row->priority == $priority) {
                        $include = true;
                    }
                }

                if ($include === false) {
                } else {
                    $temp_row->application_html5 = $application_html5;
                    $temp_row->end = $end;
                    $temp_row->page_mime_type = Services::Registry()->get(METADATA_LITERAL, 'mimetype');
                    $query_results[] = $temp_row;
                }
            }
        }
        return $query_results;
    }
}
