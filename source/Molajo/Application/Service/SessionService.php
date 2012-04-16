<?php
/**
 * @package   Molajo
 * @copyright 2012 Amy Stephen. All rights reserved.
 * @license   GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
namespace Molajo\Application\Service;

defined('MOLAJO') or die;

use Molajo\Application\Services;
use Symfony\Component\HttpFoundation\Session;

/**
 * Redirect
 *
 * http://api.symfony.com/2.0/Symfony/Component/HttpFoundation/Session.html
 *
 * @package   Molajo
 * @subpackage  Services
 * @since           1.0
 */
Class SessionService
{
    /**
     * Response instance
     *
     * @var    object
     * @since  1.0
     */
    protected static $instance;

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
     * @return object
     * @since  1.0
     */
    public static function getInstance()
    {
        if (empty(self::$instance)) {
            self::$instance = new SessionService();
        }
        return self::$instance;
    }


	/**
	 * getSession
	 *
	 */
	public function getSession()
	{
		/** Session */
		if (parent::hasPreviousSession() === false) {
			$this->session = parent::setSession($this->setSessionStorageData());
		} else {
			$this->session = $this->request->getSession()->start();
		}
	}


	/**
	 * setSessionStorageData
	 *
	 * @return NativeFileSessionStorage
	 */
	public function setSessionStorageData()
	{
		$save_path = Services::Registry()->get('Configuration\\cache_path', SITE_FOLDER_PATH . '/cache');
		$options = array();
		$options['cookie_lifetime'] = Services::Registry()->get('Configuration\\lifetime', 15);
		$options['cookie_domain'] = $cookie_domain = Services::Registry()->get('Configuration\\cookie_domain', '');
		$options['cookie_path'] = $cookie_path = Services::Registry()->get('Configuration\\cookie_path', '');

		$sessionStorage = new NativeFileSessionStorage ($save_path, $options);
		return $sessionStorage;
	}
}
