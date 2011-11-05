<?php
/**
 * @package     Molajo
 * @subpackage  Site
 * @copyright   Copyright (C) 2011 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die;

/**
 * MolajoSite
 *
 * Acts as a Factory class for site specific functions and API options
 */
class MolajoSite extends JObject
{
    /**
     * The site identifier.
     *
     * @var    integer
     * @since  1.0
     */
    public $siteId = null;

    /**
     * The site name
     *
     * @var    string
     * @since  1.0
     */
    public $name = null;

    /**
     * The description name
     *
     * @var    string
     * @since  1.0
     */
    public $description = null;

    /**
     * The base url
     *
     * @var    string
     * @since  1.0
     */
    public $base_url = null;

    /**
     * Applications the site is authorized to access
     *
     * @var    string
     * @since  1.0
     */
    public $applications = null;

    /**
     * Parameters
     *
     * @var    date
     * @since  1.0
     */
    public $parameters = null;

    /**
     * Parameters
     *
     * @var    date
     * @since  1.0
     */
    public $custom_fields = null;

    /**
     * __construct
     *
     * Class constructor.
     *
     * @param   array  $config  A configuration array
     *
     * @since  1.0
     */
    public function __construct($config = array())
    {
        /** Site ID and Name */
        $config['siteId'] = MOLAJO_SITE_ID;
        $this->_siteId = MOLAJO_SITE_ID;
        $this->_name = MOLAJO_SITE;
    }

    /**
     * getInstance
     *
     * Returns the global site object, creating if not existing
     *
     * @param   mixed   $site         Site identifier or name.
     * @param   array   $config       Associative array of configuration settings.
     * @param   strong  $prefix       Prefix for class names
     *
     * @return  site object
     *
     * @since  1.0
     */
    public static function getInstance($site, $config = array(), $prefix = 'Molajo')
    {
        static $instances;

        if (isset($instances)) {
        } else {
            $instances = array();
        }

        if (empty($instances[$site])) {

            $info = MolajoSiteHelper::getSiteInfo($site, false);
            if ($info === false) {
                return false;
            }

            if (defined('MOLAJO_SITE_ID')) {
            } else {
                define('MOLAJO_SITE_ID', $info->id);
            }

            $results = MolajoSiteHelper::loadSiteClasses();
            if ($results === false) {
                return false;
            }

            $classname = $prefix . ucfirst($site) . 'Site';
            if (class_exists($classname)) {
                $instance = new $classname($config);
            } else {
                return MolajoError::raiseError(500, MolajoText::sprintf('MOLAJO_SITE_INSTANTIATION_ERROR', $classname));
            }
            $instances[$site] = &$instance;
        }

        return $instances[$site];
    }

    /**
     * initialise
     *
     * Retrieves the configuration information, loads language files, editor, triggers onAfterInitialise
     *
     * @param    array
     *
     * @since 1.0
     */
    public function initialise($options = array())
    {
        $info = MolajoSiteHelper::getSiteInfo(MOLAJO_SITE, false);
        if ($info === false) {
            return false;
        }

        $this->description = $info->description;
        $this->parameters = $info->parameters;
        $this->custom_fields = $info->custom_fields;
        $this->base_url = $info->base_url;
    }

    /**
     * authorise
     *
     * Check if the site is authorized for this application
     *
     * @param $application_id
     * @return boolean
     */
    public function authorise($application_id)
    {
        $this->applications = MolajoSiteHelper::getSiteApplications();
        if ($this->applications === false) {
            return false;
        }

        $found = false;
        foreach ($this->applications as $single) {
            if ($single->application_id == $application_id) {
                $found = true;
            }
        }
        if ($found === true) {
            return true;
        }

        MolajoError::raiseError(403, MolajoText::_('SITE_NOT_AUTHORIZED_FOR_APPLICATION'));
        return false;
    }
}