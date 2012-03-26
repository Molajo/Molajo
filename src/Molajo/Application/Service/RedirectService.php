<?php
/**
 * @package   Molajo
 * @copyright 2012 Amy Stephen. All rights reserved.
 * @license   GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
namespace Molajo\Application\Service;

defined('MOLAJO') or die;

use Molajo\Application\Services;
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
     * Response instance
     *
     * @var    object
     * @since  1.0
     */
    protected static $instance;

    /**
     * $url
     *
     * @var        string
     * @since      1.0
     */
    public $url = null;

    /**
     * $code
     *
     * @var        integer
     * @since      1.0
     */
    public $code = 0;

    /**
     * getInstance
     *
     * @static
     * @return object
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
     * set
     *
     * @param null $url
     * @param $code
     * @return mixed
     * @since 1.0
     */
    public function set($url = null, $code = 302)
    {
        if (Services::Configuration()->get('sef', 1) == 1) {

            if (Services::Configuration()->get('sef_rewrite', 0) == 0) {
                $url = MOLAJO_BASE_URL
                    . MOLAJO_APPLICATION_URL_PATH
                    . 'index.php/' . $url;
            } else {
                $url = MOLAJO_BASE_URL
                    . MOLAJO_APPLICATION_URL_PATH
                    . $url;
            }

            if ((int)Services::Configuration()->get('sef_suffix', 0) == 1) {
                $url .= '.html';
            }
        }

        $this->url = $url;
        $this->code = $code;

        Services::Debug()->set('RedirectService::set URL: ' . $this->url . ' Status Code: ' . $this->code);

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
        Services::Debug()->set('RedirectService::redirect to: ' . $this->url . ' Status Code: ' . $this->code);

        return new RedirectResponse($this->url, $this->code);
    }
}
