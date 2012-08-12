<?php
/**
 * @package    Molajo
 * @copyright  2012 Individual Molajo Contributors. All rights reserved.
 * @license    GNU GPL v 2, or later and MIT, see License folder
 */
namespace Molajo\Service\Services\Redirect;

defined('MOLAJO') or die;

use Molajo\Service\Services;
use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * Redirect
 *
 * http://api.symfony.com/2.0/Symfony/Resource/HttpFoundation/RedirectResponse.html
 *
 * @package    Molajo
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
     * @param null $url
     * @param  $code
     *
     * @return mixed
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
        if (Services::Registry()->get('Configuration', 'url_sef', 1) == 1) {
            $url = BASE_URL . APPLICATION_URL_PATH . $url;
        }

        Services::Profiler()->set('Redirect Services Set URL: ' . $this->url
            . ' Status Code: ' . $this->code, LOG_OUTPUT_APPLICATION
        );

        return;
    }

    /**
     * redirect
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     * @since  1.0
     */
    public function redirect($url = null, $code = null)
    {
        if ($url == null) {
        } else {
            $this->url = $url;
        }
        if ($code == null) {
        } else {
            $this->code = $code;
        }
        Services::Profiler()->set('RedirectServices::redirect to: ' . $this->url
            . ' Status Code: ' . $this->code, LOG_OUTPUT_APPLICATION);

        return new RedirectResponse($this->url, $this->code);
    }
}
