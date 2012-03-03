<?php
/**
 * @package     Molajo
 * @subpackage  Base
 * @copyright   Copyright (C) 2012 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
namespace Molajo\Application;

defined('MOLAJO') or die;

use Molajo\Application\Application;

/**
 * Site
 *
 * @package     Molajo
 * @subpackage  Base
 * @since       1.0
 */
Class Site
{
    /**
     * $instance
     *
     * @var    object
     * @since  1.0
     */
    protected static $instance = null;

    /**
     * $config
     *
     * @var    integer
     * @since  1.0
     */
    protected $config = null;

    /**
     * $parameters
     *
     * @var    array
     * @since  1.0
     */
    protected $parameters = null;

    /**
     * $custom_fields
     *
     * @var    array
     * @since  1.0
     */
    protected $custom_fields = null;

    /**
     * getInstance
     *
     * Returns the global site object, creating if not existing
     *
     * @return  site  object
     * @since   1.0
     */
    public static function getInstance()
    {
        if (self::$instance) {
        } else {
            self::$instance = new Site ();
        }
        return self::$instance;
    }

    /**
     * __construct
     *
     * Class constructor.
     *
     * @since  1.0
     */
    public function __construct()
    {
        $this->load();
    }

    /**
     * load
     *
     * Retrieves the configuration information, loads language files, editor, triggers onAfterInitialise
     *
     * @param    array
     *
     * @return null
     * @since 1.0
     */
    public function load()
    {
        /** Instantiate Application */
        Molajo::Application()->initialize();

        /** Set Site Paths */
        $this->_setPaths();

        /** Site Parameters */
        $m = new MolajoSitesModel ();
        $m->query->where($m->db->qn('id') . ' = ' . (int)SITE_ID);
        $results = $m->runQuery();
        foreach ($results as $info)
        {
        }
        if ($info === false) {
            return;
        }

        /** is site authorised for this Application? */
        $authorise = Service::Access()->authoriseSiteApplication();
        if ($authorise === false) {
            $message = '304: ' . MOLAJO_BASE_URL;
            echo $message;
            die;
        }

        $this->custom_fields = new Registry;
        $this->custom_fields->loadString($info->custom_fields);

        $this->parameters = new Registry;
        $this->parameters->loadString($info->parameters);

        $this->metadata = new Registry;
        $this->metadata->loadString($info->metadata);

        $this->base_url = $info->base_url;

        /** Primary Application Logic Flow */
        $results = Molajo::Application()->process();

        /** Application Complete */
        if (Service::Configuration()->get('debug', 0) == 1) {
            debug('MolajoSite::load End');
        }

        exit(0);
    }

    /**
     * _setPaths
     *
     * Retrieves site configuration information
     * and sets paths for site file locations
     *
     * @results  null
     * @since    1.0
     */
    protected function _setPaths()
    {
        if (defined('SITE_NAME')) {
        } else {
            define('SITE_NAME', Service::Configuration()->get('site_name', SITE_ID));
        }

        if (defined('SITE_CACHE_FOLDER')) {
        } else {
            define('SITE_CACHE_FOLDER', Service::Configuration()->get('cache_path', SITE_FOLDER_PATH . '/cache'));
        }

        if (defined('SITE_LOGS_FOLDER')) {
        } else {
            define('SITE_LOGS_FOLDER', Service::Configuration()->get('logs_path', SITE_FOLDER_PATH . '/logs'));
        }

        /** following must be within the web document folder */
        if (defined('SITE_MEDIA_FOLDER')) {
        } else {
            define('SITE_MEDIA_FOLDER', Service::Configuration()->get('media_path', SITE_FOLDER_PATH . '/media'));
        }

        if (defined('SITE_MEDIA_URL')) {
        } else {
            define('SITE_MEDIA_URL', MOLAJO_BASE_URL . Service::Configuration()->get('media_url', MOLAJO_BASE_URL . 'sites/' . SITE_ID . '/media'));
        }

        if (defined('SITE_TEMP_FOLDER')) {
        } else {
            define('SITE_TEMP_FOLDER', Service::Configuration()->get('temp_path', SITE_FOLDER_PATH . '/temp'));
        }
        if (defined('SITE_TEMP_URL')) {
        } else {
            define('SITE_TEMP_URL', MOLAJO_BASE_URL . Service::Configuration()->get('temp_url', MOLAJO_BASE_URL . 'sites/' . SITE_ID . '/temp'));
        }

        return;
    }
}
