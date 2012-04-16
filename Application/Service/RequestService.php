<?php
/**
 * @package   Molajo
 * @copyright 2012 Amy Stephen. All rights reserved.
 * @license   GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
namespace Molajo\Application\Service;

defined('MOLAJO') or die;

/**
 * Request
 *
 * @url http://symfony.com/doc/current/components/http_foundation.html#accessing-request-data
 *
 * @package   Molajo
 * @subpackage  Services
 * @since       1.0
 */
Class RequestService
{
    /**
     * Static instance
     *
     * @var    object
     * @since  1.0
     */
    protected static $instance;

	/**
	 * Request Connection
	 *
	 * @var    object
	 * @since  1.0
	 */
	public $connection;

	/**
     * Request
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
    public function __construct()
    {
		$class = 'Symfony\\Component\\HttpFoundation\\Request';
		$this->connection = new $class();

		$this->request = $this->connection->createFromGlobals();

		return $this;
    }

	/**
	 * Used to connect to services
	 *
	 * @static
	 * @param $name
	 * @param $arguments
	 */
	public function __call($name, $arguments)
	{
echo 'Nmae '.$name.'<br />';
		echo '<pre>';
		var_dump($arguments);
echo '</pre>';
		return $this->connection->$name($arguments);
	}
}
