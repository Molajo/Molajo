<?php
/**
 * @package    Molajo
 * @copyright  2013 Amy Stephen. All rights reserved.
 * @license    MIT
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
 * @package         Molajo
 * @subpackage      Services
 * @since           1.0
 */
Class RedirectService
{
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
     * Set the Redirect URL and Code
     *
     * @param null $url
     * @param      $code
     *
     * @return mixed
     * @since  1.0
     */
    public function set($url = null, $code = 302)
    {
        /** Installation redirect */
        if ($code == 999) {
            $code       = 302;
            $this->url  = $url;
            $this->code = $code;

            return;
        }

        /** Configuration Service is available */
        if (Services::Application()->get('url_sef', 1) == 1) {
            $url = BASE_URL . APPLICATION_URL_PATH . $url;
        }

        Services::Profiler()->set('message',
            'Redirect Services Set URL: ' . $this->url
                . ' Status Code: ' . $this->code,
            'Application'
        );

        return;
    }

    /**
     * redirect
     *
     * @return  \Symfony\Component\HttpFoundation\RedirectResponse
     * @since   1.0
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
        Services::Profiler()->set('message',
            'RedirectServices::redirect to: ' . $this->url
                . ' Status Code: ' . $this->code,
            'Application'
        );

        return new RedirectResponse($this->url, $this->code);
    }
}
