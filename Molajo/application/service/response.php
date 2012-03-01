<?php
/**
 * @package     Molajo
 * @subpackage  Service
 * @copyright   Copyright (C) 2012 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
namespace Molajo\Application\Service;

defined('MOLAJO') or die;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * Response
 *
 * http://api.symfony.com/2.0/Symfony/Component/HttpFoundation/Response.html
 *
 * @package     Molajo
 * @subpackage  Services
 * @since       1.0
 */
Class ResponseService extends Response
{
    /**
     * Response instance
     *
     * @var    object
     * @since  1.0
     */
    protected static $instance;

    /**
     * $response
     *
     * @var    object
     * @since  1.0
     */
    protected $response;

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
            self::$instance = new ResponseService($content, $status, $headers);
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
    public function __construct($content, $status, $headers)
    {
        $this->response = new Response();
        parent::__construct($content, $status, $headers);
    }

    /**
     * isRedirect
     *
     * Redirect to the URL for a specified pageRequest value
     *
     * @since   1.0
     */
    public function isRedirect($location = null)
    {
        if (Services::Configuration()->get('sef', 1) == 1) {

            if (Services::Configuration()->get('sef_rewrite', 0) == 0) {
                $location = MOLAJO_BASE_URL
                    . MOLAJO_APPLICATION_URL_PATH
                    . 'index.php/' . $location;
            } else {
                $location = MOLAJO_BASE_URL
                    . MOLAJO_APPLICATION_URL_PATH
                    . $location;
            }

            if ((int)Services::Configuration()->get('sef_suffix', 0) == 1) {
                $location .= '.html';
            }
        }
        if (Services::Configuration()->get('debug', 0) == 1) {
            debug('Responseer::isRedirect redirect to: ' . $location);
        }

        parent::isRedirect($location);

        exit(0);
    }
}
