<?php
/**
 * @package   Molajo
 * @copyright 2012 Amy Stephen. All rights reserved.
 * @license   GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
namespace Molajo\Application\Service;

use Molajo\Application\Services;

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
Class DatabaseService extends BaseService
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
	 * @var    Joomla\database\JDatabaseDriver
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
		} catch (\Exception $e)	{
			throw new \RuntimeException(sprintf('Unable to connect to the Database: %s', $e->getMessage()));
		}

        $this->db->debug($site->debug);

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
	 * Get the current query object or a new JDatabaseQuery object.
	 *
	 * @return  JDatabaseQuery  The current query object or a new object extending the JDatabaseQuery class.
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
	 * Gets an exporter class object.
	 *
	 * @return  JDatabaseExporter  An exporter object.
	 *
	 * @since   12.1
	 * @throws  \RuntimeException
	 */
	public function getExporter()
	{
		$class = 'Joomla\\database\\exporter\\JDatabaseExporter' . ucfirst(strtolower($this->name));
		if (class_exists($class)) {
		} else {
			throw new \RuntimeException('Database Query class not found');
		}

		$exporter = new $class();
		$exporter->setDbo($this->db);
		return $exporter;
	}

	/**
	 * Gets an importer class object.
	 *
	 * @return  JDatabaseImporter  An importer object.
	 *
	 * @since   12.1
	 * @throws  \RuntimeException
	 */
	public function getImporter()
	{
		$class = 'Joomla\\database\\exporter\\JDatabaseImporter' . ucfirst(strtolower($this->name));
		if (class_exists($class)) {
		} else {
			throw new \RuntimeException('Database Query class not found');
		}

		$importer = new $class();
		$importer->setDbo($this->db);
		return $importer;
	}

	/**
	 * Get a new iterator on the current query.
	 *
	 * @param   string  $column  An option column to use as the iterator key.
	 * @param   string  $class   The class of object that is returned.
	 *
	 * @return  JDatabaseIterator  A new database iterator.
	 *
	 * @since   12.1
	 * @throws  \RuntimeException
	 */
	public function getIterator($column = null, $class = 'stdClass')
	{
		// Derive the class name from the driver.
		$iteratorClass = 'Joomla\\database\\iterator\\';
		$iteratorClass .= 'JDatabaseIterator' . ucfirst($this->name);

		// Make sure we have an iterator class for this driver.
		if (class_exists($iteratorClass)) {
		} else {
			throw new \RuntimeException(sprintf('class *%s* is not defined', $iteratorClass));
		}

		return new $iteratorClass($this->execute(), $column, $class);
	}
}
