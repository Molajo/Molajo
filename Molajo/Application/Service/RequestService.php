<?php
/**
 * @package     Molajo
 * @copyright   Copyright (C) 2012 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
namespace Molajo\Application\Service;

defined('MOLAJO') or die;

use Symfony\Component\HttpFoundation\Request;

/**
 * Request
 *
 * @package     Molajo
 * @subpackage  Services
 * @since       1.0
 */
Class RequestService extends Request
{
    /**
     * Static instance
     *
     * @var    object
     * @since  1.0
     */
    protected static $instance;

    /**
     * Request
     *
     * @var    object
     * @since  1.0
     */
    public $request;

    /**
     * Session
     *
     * @var    object
     * @since  1.0
     */
    public $session;

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
            self::$instance = new RequestService();
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

        $this->request = Request::createFromGlobals();
/**
        $this->baseURL = $this->request->getScheme()
            .'://'.
            $this->request->getHttpHost()
            .$this->request->getBaseUrl();

        $this->pathinfo = $this->request->getPathInfo();
        $this->querystring = $this->request->getQueryString() .'<br />';
*/
    }

    public function getSession()
    {
        /** Session */
        if (parent::hasPreviousSession() === false) {
            echo 'false';
            $this->session = parent::setSession($this->setSessionStorageData());
        } else {
            $this->session = $this->request->getSession()->start();
        }
    }


    public function setSessionStorageData()
    {
        $save_path = Services::Configuration()->get('cache_path', SITE_FOLDER_PATH . '/cache');
        $options = array();
        $options['cookie_lifetime'] = Services::Configuration()->get('lifetime', 15);
        $options['cookie_domain'] = $cookie_domain = Services::Configuration()->get('cookie_domain', '');
        $options['cookie_path'] = $cookie_path = Services::Configuration()->get('cookie_path', '');

        $sessionStorage = new NativeFileSessionStorage ($save_path, $options);
        return $sessionStorage;
    }
}
