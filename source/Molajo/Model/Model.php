<?php
/**
 * @package    Molajo
 * @copyright  2012 Amy Stephen. All rights reserved.
 * @license    GNU GPL v 2, or later and MIT, see license folder
 */
namespace Molajo\Model;

use Molajo\Service\Services;

defined('MOLAJO') or die;

/**
 * Model
 *
 * Base Molajo Model
 *
 * @package       Molajo
 * @subpackage    Model
 * @since         1.0
 */
class Model
{
	/**
	 * Database connection
	 *
	 * Public to access db quoting on query parts
	 *
	 * @var    string
	 * @since  1.0
	 */
	public $db;

	/**
	 * Database query object
	 *
	 * Public to allow setting of partial query values
	 *
	 * @var    object
	 * @since  1.0
	 */
	public $query;

	/**
	 * Used in queries to determine date validity
	 *
	 * Public to access property during query development
	 *
	 * @var    object
	 * @since  1.0
	 */
	public $null_date;

	/**
	 * Today's CCYY-MM-DD 00:00:00 Used in queries to determine date validity
	 *
	 * Public to access property during query development
	 *
	 * @var    object
	 * @since  1.0
	 */
	public $now;

	/**
	 * Results from queries
	 *
	 * @var    object
	 * @since  1.0
	 */
	protected $query_results;

	/**
	 * Pagination object from display query
	 *
	 * @var    object
	 * @since  1.0
	 */
	protected $pagination;

	/**
	 * Parameters
	 *
	 * @var    object
	 * @since  1.0
	 */
	public $parameters = array();

	/**
	 * @return object
	 * @since   1.0
	 */
	public function __construct()
	{
		$this->parameters = array();
		$this->query_results = array();
		$this->pagination = array();
	}

	/**
	 * Get the current value (or default) of the specified Model property
	 *
	 * @param string $key     Property
	 * @param mixed  $default Value
	 *
	 * @return mixed
	 * @since   1.0
	 */
	public function get($key, $default = null)
	{
		$value = null;

		if (in_array($key, array('db', 'query', 'null_date', 'now', 'query_results', 'pagination'))) {
			$value = $this->$key;

		} else {
			if (isset($this->parameters[$key])) {
				$value = $this->parameters[$key];
			}
		}

		if ($value === null) {
			return $default;
		}

		return $value;
	}

	/**
	 * Set the value of a Model property
	 *
	 * @param string $key   Property
	 * @param mixed  $value Value
	 *
	 * @return mixed
	 * @since   1.0
	 */
	public function set($key, $value = null)
	{
		if (in_array($key, array('db', 'query', 'null_date', 'now', 'query_results', 'pagination'))) {
			$this->$key = $value;
		} else {
			$this->parameters[$key] = $value;
		}

		return;
	}

	/**
	 * retrieves messages from Messages dbo
	 *
	 * @param null $model_type
	 *
	 * @return mixed Array or String or Null
	 * @since   1.0
	 */
	public function getMessages($model_type = null)
	{
		return $this->db->getMessages();
	}

	/**
	 * retrieves parameters from Registry DBO
	 *
	 * @param null $model_type
	 *
	 * @return mixed Array or String or Null
	 * @since   1.0
	 */
	public function getParameters($model_type = null)
	{
		return $this->db->getData('Parameters', $model_type);
	}

	/**
	 * retrieves result (single element) from Trigger Registry
	 *
	 * @param null $model_type
	 *
	 * @return mixed Array or String or Null
	 * @since   1.0
	 */
	public function getTriggerdata($model_type = null)
	{
		if ($model_type == '*' || strpos($model_type, '*')) {
			return $this->db->getData('Triggerdata', $model_type, false);
		}

		return $this->db->getData('Triggerdata', $model_type, true);
	}

	/**
	 * retrieves lists of non-table based data from Text Service DB
	 *
	 * @param null $model_type
	 *
	 * @return mixed Array or String or Null
	 * @since   1.0
	 */
	public function getListdata($model_type = null)
	{
		return $this->db->getData($model_type);
	}

	/**
	 * retrieves head and metadata from Metadata registry
	 *
	 * @param null $model_type
	 *
	 * @return mixed Array or String or Null
	 * @since   1.0
	 */
	public function getMetadata($model_type = null)
	{
		return $this->db->getMetadata($model_type);
	}

	/**
	 * retrieves JS and CSS assets, metadata for head from Asset Registry
	 *
	 * @param null $model_type
	 *
	 * @return mixed Array or String or Null
	 * @since   1.0
	 */
	public function getAssets($model_type = null)
	{
		return $this->db->getAssets($model_type);
	}

	/**
	 * filterInput
	 *
	 * @param string $name        Name of input field
	 * @param string $field_value Value of input field
	 * @param string $dataType    Datatype of input field
	 * @param int    $null        0 or 1 - is null allowed
	 * @param string $default     Default value, optional
	 *
	 * @return mixed
	 * @since   1.0
	 */
	protected function filterInput(
		$name, $value, $dataType, $null = null, $default = null)
	{

		try {
			$value = Services::Filter()
				->filter(
				$value,
				$dataType,
				$null,
				$default
			);

		} catch (\Exception $e) {
			//todo: errors
			echo $e->getMessage() . ' ' . $name;
		}

		return $value;
	}

	/**
	 * loadResult
	 *
	 * Single Value Result
	 *
	 * Access by referencing the query results field, directly
	 *
	 * For example, in this method, the result is in $this->query_results.
	 *
	 * @return object
	 * @since   1.0
	 */
	public function loadResult($primary_prefix, $table_name)
	{
		if ($this->query->select == null) {
			$this->query->select($this->db->qn($primary_prefix . '.' . $this->primary_key));
		}

		if ($this->query->from == null) {
			$this->query->from($this->db->qn($table_name) . ' as ' . $this->db->qn($primary_prefix));
		}

		$this->db->setQuery($this->query->__toString());

		$this->query_results = $this->db->loadResult();

		if (empty($this->query_results)) {
			return false;
		}

		$this->processQueryResults('loadResult');

		return $this->query_results;

	}

	/**
	 * getPagination
	 *
	 * @return array
	 * @since    1.0
	 */
	public function getPagination()
	{
		return $this->pagination;
	}
}
