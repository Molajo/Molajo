<?php
/**
 * @package     Molajo
 * @subpackage  Base
 * @copyright   Copyright (C) 2012 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die;

/**
 * MolajoSite
 *
 * @package     Molajo
 * @subpackage  Base
 * @since       1.0
 */
class MolajoSite
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
     * $applications
     *
     * Applications the site is authorized to access
     *
     * @var    array
     * @since  1.0
     */
    protected $applications = null;

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
            self::$instance = new MolajoSite ();
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
     * @since 1.0
     */
    public function load()
    {
        /** Instantiate Application */
        $app = Molajo::Application();
        $app->initialize();

        /** Set Site Paths */
        $this->_setPaths();

        /** Site Parameters */
        $info = SiteHelper::get();
        if ($info === false) {
            return false;
        }

        /** is site authorised for this Application? */
        $authorise = $this->_authorise();
        if ($authorise === false) {
            $message = '304: ' . MOLAJO_BASE_URL;
            echo $message;
            die;
        }

        $this->_custom_fields = new Registry;
        $this->_custom_fields->loadString($info->custom_fields);

        $this->_parameters = new Registry;
        $this->_parameters->loadString($info->parameters);

        $this->_metadata = new Registry;
        $this->_metadata->loadString($info->metadata);

        $this->_base_url = $info->base_url;

        /** Primary Application Logic Flow */
        $app->process();

        return;
    }

    /**
     * authorise
     *
     * Check if the site is authorized for this application
     *
     * @param $application_id
     * @return boolean
     */
    protected function _authorise()
    {
        $this->_applications = SiteHelper::getApplications();
        if ($this->_applications === false) {
            return false;
        }

        $found = false;
        foreach ($this->_applications as $single) {
            if ($single->application_id == MOLAJO_APPLICATION_ID) {
                $found = true;
            }
        }
        if ($found === true) {
            return true;
        }

        /** set header status, message and override theme/page, if needed */
        Molajo::Responder()->setHeader(
            'Status',
            '403 Not Authorised',
            'true'
        );
        Services::Message()
            ->set(
            Molajo::Application()->get(
                'error_403_message',
                'Not Authorised.'
            ),
            MOLAJO_MESSAGE_TYPE_ERROR,
            403
        );

        return false;
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
        if (defined('MOLAJO_SITE_NAME')) {
        } else {
            define('MOLAJO_SITE_NAME',
            Molajo::Application()
                ->get(
                'site_name',
                MOLAJO_SITE_ID
            )
            );
        }

        if (defined('MOLAJO_SITE_CACHE_FOLDER')) {
        } else {
            define('MOLAJO_SITE_CACHE_FOLDER',
            Molajo::Application()
                ->get(
                'cache_path',
                MOLAJO_SITE_FOLDER_PATH . '/cache'
            )
            );
        }

        if (defined('MOLAJO_SITE_LOGS_FOLDER')) {
        } else {
            define('MOLAJO_SITE_LOGS_FOLDER',
            Molajo::Application()
                ->get(
                'logs_path',
                MOLAJO_SITE_FOLDER_PATH . '/logs'
            )
            );
        }

        /** following must be within the web document folder */
        if (defined('MOLAJO_SITE_MEDIA_FOLDER')) {
        } else {
            define('MOLAJO_SITE_MEDIA_FOLDER',
            Molajo::Application()
                ->get(
                'media_path',
                MOLAJO_SITE_FOLDER_PATH . '/media'
            )
            );
        }

        if (defined('MOLAJO_SITE_MEDIA_URL')) {
        } else {
            define('MOLAJO_SITE_MEDIA_URL',
                MOLAJO_BASE_URL .
                Molajo::Application()
                    ->get(
                    'media_url',
                    MOLAJO_BASE_URL . 'sites/' . MOLAJO_SITE_ID . '/media'
                )
            );
        }

        if (defined('MOLAJO_SITE_TEMP_FOLDER')) {
        } else {
            define('MOLAJO_SITE_TEMP_FOLDER',
            Molajo::Application()
                ->get(
                'temp_path',
                MOLAJO_SITE_FOLDER_PATH . '/temp'
            )
            );
        }
        if (defined('MOLAJO_SITE_TEMP_URL')) {
        } else {
            define('MOLAJO_SITE_TEMP_URL',
                MOLAJO_BASE_URL .
                Molajo::Application()
                    ->get(
                    'temp_url',
                    MOLAJO_BASE_URL . 'sites/' . MOLAJO_SITE_ID . '/temp'
                )
            );
        }

        return;
    }
}
