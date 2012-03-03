<?php
/**
 * @package     Molajo
 * @subpackage  Helper
 * @copyright   Copyright (C) 2012 Amy Stephen. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */
namespace Molajo\Extension\Helper;

defined('MOLAJO') or die;

/**
 * Theme
 *
 * @package     Molajo
 * @subpackage  Helper
 * @since       1.0
 */
class ThemeHelper extends Mustache
{
    /**
     * $data
     *
     * Allows collection of any set of data for a single $item
     *
     * @var    array
     * @since  1.0
     */
    public $data = array();

    /**
     * $rows
     *
     * Retains pointer to current row contained within the $data array
     *
     * @var    int
     * @since  1.0
     */
    protected $rows = 0;

    /**
     * __construct
     *
     * Class constructor.
     *
     * @since  1.0
     */
    public function __construct()
    {
    }

    /**
     * items
     *
     * Returns a single row of information to mustache
     * around the {# item } {/ item } controlbreak
     *
     * tracks row number in #this->rows so that rowset can be exploited
     *
     * @return ArrayIterator
     * @since  1.0
     */
    public function items()
    {
        $this->rows++;
        return new ArrayIterator($this->data);
    }

    /**
     * gravatar
     *
     * Using the $this->row value, the data element introtext can be
     * printed for this specific article.
     *
     * @return string
     * @since  1.0
     */
    public function gravatar()
    {
        $this->analytics();
        $ret = new UrlService();
        return $ret->getGravatar('AmyStephen@gmail.com');
    }

    /**
     * analytics
     *
     * Google Analytics
     *
     * @return mixed
     */
    public function analytics()
    {
        $code = Service::Configuration()->get('google_analytics_code','UA-1682054-15');
        if (trim($code) == '') {
            return;
        }
        $analytics = "
var _gaq = _gaq || [];
_gaq.push(['_setAccount', '".$code."']);
_gaq.push(['_trackPageview']);

(function() {
var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
})();
        ";
        Service::Document()->add_js_declaration($analytics, 'text/javascript', 1);
    }

    /**
     * placeholder
     *
     * @return mixed
     */
    public function placeholder()
    {
        return Service::Text()->getPlaceHolderText(55, array('html', 'lorem'));
    }

    /**
     * intro
     *
     * Using the $this->row value, the data element introtext can be
     * printed for this specific article.
     *
     * @return string
     * @since  1.0
     */
    public function intro()
    {
        $result = Service::Text()->smilies($this->data[$this->rows - 1]->introtext);
        return $this->data[$this->rows - 1]->introtext;
    }

    /**
     * hello
     *
     * Returns hello for {{ hello }}
     * Template example overrides for different result
     *
     * @return string
     * @since  1.0
     */
    public function hello()
    {
        return 'Hello!';
    }

    /**
     * profile
     *
     * Renders the Author Profile Module for this article
     *
     * $results  text
     * $since    1.0
     */
    public function profile()
    {
        $rc = new MolajoModuleRenderer ('profile', '');
        $attributes = array();
        $attributes['name'] = 'dashboard';
        $attributes['template'] = 'dashboard';
        $attributes['wrap'] = 'section';
        $attributes['id'] = $this->data[$this->rows - 1]->id;

        return $rc->process($attributes);
    }

    /**
     * get
     *
     * Get requested theme data
     *
     * @static
     * @return  array
     * @since   1.0
     */
    public static function get($theme)
    {
        $rows = ExtensionHelper::get(
            MOLAJO_ASSET_TYPE_EXTENSION_THEME,
            $theme
        );

        if (count($rows) == 0) {
            return array();
        }
        $row = null;
        foreach ($rows as $row) {
        }

        return $row;
    }

    /**
     * getPath
     *
     * Return path for selected Theme
     *
     * @static
     * @param $theme_name
     * @return bool|string
     */
    public static function getPath($theme_name)
    {
        if (file_exists(MOLAJO_EXTENSIONS_THEMES . '/' . $theme_name . '/' . 'index.php')) {
            return MOLAJO_EXTENSIONS_THEMES . '/' . $theme_name;
        }
        return false;
    }

    /**
     * getPath
     *
     * Return path for selected Theme
     *
     * @static
     * @return bool|string
     * @since 1.0
     */
    public static function getPathURL($theme_name)
    {
        if (file_exists(MOLAJO_EXTENSIONS_THEMES . '/' . $theme_name . '/' . 'index.php')) {
            return MOLAJO_EXTENSIONS_THEMES_URL . '/' . $theme_name;
        }
        return false;
    }

    /**
     * getFavicon
     *
     * Retrieve Favicon Path
     *
     * Can be located in:
     *  - Themes/images/ folder (priority 1)
     *  - Root of the website (priority 2)
     *
     * @static
     * @return  mixed
     * @since   1.0
     */
    public static function getFavicon($theme_name)
    {
        $path = MOLAJO_EXTENSIONS_THEMES . '/' . $theme_name . '/images/';
        if (file_exists($path . 'favicon.ico')) {
            return MOLAJO_EXTENSIONS_THEMES_URL . '/' . $theme_name . '/images/favicon.ico';
        }
        $path = MOLAJO_BASE_FOLDER;
        if (file_exists($path . 'favicon.ico')) {
            return MOLAJO_BASE_URL . '/' . $theme_name . '/images/favicon.ico';
        }

        return false;
    }
}
