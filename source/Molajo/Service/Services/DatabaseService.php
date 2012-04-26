<?php
/**
 * @package   Molajo
 * @copyright 2012 Amy Stephen. All rights reserved.
 * @license   GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
namespace Molajo\Service\Services;

use Molajo\Service\Services;
use Joomla\date\JDate;

defined('MOLAJO') or die;

/**
 * Database
 *
 * Used by the model
 *
 * @package     Molajo
 * @subpackage  Service
 * @since       1.0
 */
Class DatabaseService
{
	/**
	 * Static instance
	 *
	 * @var    object
	 * @since  1.0
	 */
	protected static $instance;

	/**
	 * Name
	 *
	 * @var    string
	 * @since  1.0
	 */
	protected $name;

	/**
	 * Options
	 *
	 * @var    array
	 * @since  1.0
	 */
	protected $options;

	/**
	 * DB Connection
	 *
	 * @var    object
	 * @since  1.0
	 */
	protected $db;

	/**
	 * getInstance
	 *
	 * @static
	 * @param   null  $configuration_file
	 * @return  string
	 * @throws  \Exception
	 */
	public static function getInstance($configuration_file = null)
	{
		return self::$instance ? self::$instance : new DatabaseService($configuration_file);
	}

	/**
	 * Class constructor.
	 *
	 * @param   string  $name     Name of the database driver you'd like to instantiate
	 * @param   array   $options  Parameters to be passed to the database driver
	 *
	 * @since   11.3
	 */
	public function __construct($configuration_file = null)
	{
		if ($configuration_file === null) {
			$configuration_file = SITE_FOLDER_PATH . '/configuration.php';
		}
		if (file_exists($configuration_file)) {
			require_once $configuration_file;
		} else {
			throw new \Exception('Fatal error - Application-Site Configuration File does not exist');
		}

		$site = new \SiteConfiguration();

		/** set connection options */
		$this->options = array(
			'driver' => preg_replace('/[^A-Z0-9_\.-]/i', '', $site->jdatabase_dbtype),
			'host' => $site->jdatabase_host,
			'user' => $site->jdatabase_user,
			'password' => $site->jdatabase_password,
			'database' => $site->jdatabase_db,
			'prefix' => $site->jdatabase_dbprefix,
			'select' => true
		);

		$this->name = $site->jdatabase_dbtype;

		/** connect */
		$class = 'Joomla\\database\\driver\\JDatabaseDriver' . ucfirst(strtolower($this->options['driver']));
		if (class_exists($class)) {
		} else {
			throw new \RuntimeException(sprintf('Unable to load Database Driver: %s', $this->options['driver']));
		}

		try {
			$this->db = new $class($this->options);
		} catch (\Exception $e) {
			throw new \RuntimeException(sprintf('Unable to connect to the Database: %s', $e->getMessage()));
		}

		$this->db->debug($site->jdatabase_debug);

		$date = null;
		$dateFromJDate = new JDate($date ? $date : 'now');
		$dateFromJDate = $dateFromJDate->toSql(false, $this->db);

		return $this;
	}

	/**
	 * get
	 *
	 * @param   $value
	 *
	 * @return  mixed
	 * @since   1.0
	 */
	public function get($value)
	{
		return $this->$value;
	}

	/**
	 * Get the current query object for the current database connection
	 *
	 * @return  Joomla\database\JDatabaseQuery  Database query object
	 *
	 * @since   1.0
	 * @throws  \RuntimeException
	 */
	public function getQuery()
	{
		$class = 'Joomla\\database\\query\\JDatabaseQuery' . ucfirst(strtolower($this->name));
		if (class_exists($class)) {
		} else {
			throw new \RuntimeException('Database Query class not found');
		}

		return new $class($this->db);
	}

	/**
	 * Get an exporter object for the current database connection.
	 *
	 * @return   Joomla\database\JDatabaseExporter  Exporter object.
	 *
	 * @since   12.1
	 * @throws  \RuntimeException
	 */
	public function getExporter()
	{
		$c = 'Joomla\\database\\exporter\\JDatabaseExporter' . ucfirst(strtolower($this->name));
		if (class_exists($c)) {
		} else {
			throw new \RuntimeException('Database Query class not found');
		}

		$exporter = new $c();
		$exporter->setDbo($this->db);
		return $exporter;
	}

	/**
	 * Get an importer object for the current database connection.
	 *
	 * @return   Joomla\database\JDatabaseImporter  Importer object.
	 *
	 * @since   12.1
	 * @throws  \RuntimeException
	 */
	public function getImporter()
	{
		$c = 'Joomla\\database\\exporter\\JDatabaseImporter' . ucfirst(strtolower($this->name));
		if (class_exists($c)) {
		} else {
			throw new \RuntimeException('Database Query class not found');
		}

		$importer = new $c();
		$importer->setDbo($this->db);
		return $importer;
	}

	/**
	 * Get a new iterator on current query.
	 *
	 * @param   string  $column  Iterator key, optional
	 * @param   string  $class   Class of object returned
	 *
	 * @return  Joomla\database\JDatabaseIterator  A new database iterator.
	 *
	 * @since   12.1
	 * @throws  \RuntimeException
	 */
	public function getIterator($column = null, $class = 'stdClass')
	{
		$c = 'Joomla\\database\\iterator\\JDatabaseIterator' . ucfirst(strtolower($this->name));
		if (class_exists($c)) {
		} else {
			throw new \RuntimeException(sprintf('class *%s* is not defined', $c));
		}

		return new $c($this->execute(), $column, $class);
	}
}
