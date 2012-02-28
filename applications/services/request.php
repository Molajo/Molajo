<?php
/**
 * @package     Molajo
 * @subpackage  Services
 * @copyright   Copyright (C) 2012 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die;

use Symfony\Component\HttpFoundation\Request;

/**
 * Request
 *
 * @package     Molajo
 * @subpackage  Services
 * @since       1.0
 */
class MolajoRequestService extends Request
{
    /**
     * Static instance
     *
     * @var    object
     * @since  1.0
     */
    protected static $instance;

    /**
     * $request
     *
     * @var    object
     * @since  1.0
     */
    public $request;

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
            self::$instance = new MolajoRequestService();
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
    public function __construct($query = null, $request = null, $attributes = null, $cookies = null, $files = null, $server = null, $content = null)
    {
        parent::__construct();
    }

    public function connect()
    {
        $this->request = $this->createFromGlobals();
    }
}
