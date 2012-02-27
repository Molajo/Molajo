<?php
/**
 * @package     Molajo
 * @subpackage  Services
 * @copyright   Copyright (C) 2012 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die;

/**
 * File
 *
 * @package     Molajo
 * @subpackage  Services
 * @since       1.0
 */
class MolajoFileService
{

    /**
     * Static instance
     *
     * @var    object
     * @since  1.0
     */
    protected static $instance;

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
            self::$instance = new MolajoFileService();
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

    }

    /**
     * setHTMLFilter
     *
     * Returns false if there is one group that the user belongs to
     *  authorized to save content without an HTML filter, otherwise
     *  it returns true
     *
     * @return bool
     * @since  1.0
     */
    public function setHTMLFilter ()
    {
        $groups = Services::Configuration()->get('disable_filter_for_groups');
        $groupArray = explode(',', $groups);
        $userGroups = Services::User()->get('groups');

        foreach ($groupArray as $single) {

            if (in_array($single, $userGroups)) {
                return false;
                break;
            }

        }
        return true;
    }
}
