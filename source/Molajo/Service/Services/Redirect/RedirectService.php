<?php
/**
 * @package   Molajo
 * @copyright 2012 Amy Stephen. All rights reserved.
 * @license   GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
namespace Molajo\Service\Services\Redirect;

defined('MOLAJO') or die;

use Molajo\Service\Services;

use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * Redirect
 *
 * http://api.symfony.com/2.0/Symfony/Component/HttpFoundation/RedirectResponse.html
 *
 * @package   Molajo
 * @subpackage  Services
 * @since           1.0
 */
Class RedirectService
{
	/**
	 * Instance
	 *
	 * @var    object
	 * @since  1.0
	 */
	protected static $instance;

	/**
	 * $url
	 *
	 * @var    string
	 * @since  1.0
	 */
	public $url = null;

	/**
	 * $code
	 *
	 * @var    integer
	 * @since  1.0
	 */
	public $code = 0;

	/**
	 * getInstance
	 *
	 * @static
	 * @return object
	 *
	 * @since  1.0
	 */
	public static function getInstance($content = '', $status = 200, $headers = array())
	{
		if (empty(self::$instance)) {
			self::$instance = new RedirectService($content, $status, $headers);
		}
		return self::$instance;
	}

	/**
	 * Set the Redirect URL and Code
	 *
	 * @param  null $url
	 *
	 * @param  $code
	 *
	 * @return mixed
	 *
	 * @since  1.0
	 */
	public function set($url = null, $code = 302)
	{
		/** Installation redirect */
		if ($code == 999) {
			$code = 302;
			$this->url = $url;
			$this->code = $code;
			return;
		}

		/** Configuration Service is available */
		if (Services::Registry()->get('Configuration', 'sef', 1) == 1) {

			if (Services::Registry()->get('Configuration', 'sef_rewrite', 0) == 0) {
				$url = BASE_URL . APPLICATION_URL_PATH . 'index.php/' . $url;
			} else {
				$url = BASE_URL . APPLICATION_URL_PATH . $url;
			}

			if ((int)Services::Registry()->get('Configuration', 'sef_suffix', 0) == 1) {
				$url .= '.html';
			}
		}

		Services::Debug()->set('RedirectServices::set URL: ' . $this->url . ' Status Code: ' . $this->code);

		return;
	}

	/**
	 * redirect
	 *
	 * @return \Symfony\Component\HttpFoundation\RedirectResponse
	 * @since  1.0
	 */
	public function redirect()
	{
		Services::Debug()->set('RedirectServices::redirect to: ' . $this->url . ' Status Code: ' . $this->code);

		return new RedirectResponse($this->url, $this->code);
	}
}
