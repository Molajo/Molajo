<?php
/**
 * @package    Molajo
 * @copyright  2012 Individual Molajo Contributors. All rights reserved.
 * @license    GNU GPL v 2, or later and MIT, see License folder
 */
namespace Molajo\MVC\Model;

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

		if (in_array($key, array('db', 'query', 'null_date', 'now', 'query_results'))) {
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
		if (in_array($key, array('db', 'query', 'null_date', 'now', 'query_results'))) {
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
	 * retrieves result (single element) from Plugin Registry
	 *
	 * @param null $model_type
	 *
	 * @return mixed Array or String or Null
	 * @since   1.0
	 */
	public function getPlugindata($model_type = null)
	{
		if ($model_type == '*' || strpos($model_type, '*')) {
			return $this->db->getData('Plugindata', $model_type, false);
		}

		return $this->db->getData('Plugindata', $model_type, true);
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
	 * retrieves Profiler Console messages
	 *
	 * @param null $model_type
	 *
	 * @return mixed Array or String or Null
	 * @since   1.0
	 */
	public function getProfiler($model_type = null)
	{
		return $this->db->getProfiler();
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
	protected function loadResult($primary_prefix, $table_name)
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
	public function insertLanguageString($translated = array())
	{
		if (count($translated) === 0) {
			return true;
		}

		/** Add Missing Language Strings to Base */
		foreach ($translated as $key => $value) {

			$sql = "

			INSERT INTO `#__language_strings`
				(`id`, `site_id`, `extension_instance_id`, `catalog_type_id`,
					`title`, `subtitle`, `path`, `alias`, `content_text`,
					`protected`, `featured`, `stickied`, `status`,
					`start_publishing_datetime`, `stop_publishing_datetime`,
					`version`, `version_of_id`, `status_prior_to_version`,
					`created_datetime`, `created_by`,
					`modified_datetime`, `modified_by`,
					`checked_out_datetime`, `checked_out_by`,
					`root`, `parent_id`, `lft`, `rgt`, `lvl`, `home`,
					`customfields`, `parameters`, `metadata`,
					`language`, `translation_of_id`, `ordering`)

			VALUES (NULL, 0, 6250, 6250, "
				. $this->db->q($value) . ",
			'', 'languagestrings',
			LOWER(REPLACE("
				. $this->db->q($value) . ", ' ', '_')), '',
			1, 0, 0, 1, '2012-09-13 12:00:00', '0000-00-00 00:00:00', 1, 0, 0,
			'2012-09-13 12:00:00', 1, '2012-09-13 12:00:00', 1,
			'2012-09-13 12:00:00', 0, 5, 0, 1, 0, 1, 0, '{}', '{}', '{}', 'string', 0, 0);";

			$this->db->setQuery($sql);
			$this->db->execute();
		}

		/** Add to English Language */
		$en_GB = "SELECT DISTINCT id
					FROM `molajo_language_strings`
					WHERE language = 'string'
					  AND id NOT IN (SELECT parent_id
					  FROM  `molajo_language_strings`
					  WHERE language = 'en-gb')
					    AND id <> 5";

		$this->db->setQuery($en_GB);
		$results = $this->db->loadObjectList();

		if ($results === false || count($results) === 0) {
		} else {
			foreach ($results as $row) {


				if ($row->id == 5) {
				} else {
				$sql = "

				INSERT INTO `#__language_strings`
					(`id`, `site_id`, `extension_instance_id`, `catalog_type_id`,
						`title`, `subtitle`, `path`, `alias`, `content_text`,
						`protected`, `featured`, `stickied`, `status`,
						`start_publishing_datetime`, `stop_publishing_datetime`,
						`version`, `version_of_id`, `status_prior_to_version`,
						`created_datetime`, `created_by`,
						`modified_datetime`, `modified_by`,
						`checked_out_datetime`, `checked_out_by`,
						`root`, `parent_id`, `lft`, `rgt`, `lvl`, `home`,
						`customfields`, `parameters`, `metadata`,
						`language`, `translation_of_id`, `ordering`)

				SELECT NULL as `id`, `site_id`, `extension_instance_id`, `catalog_type_id`,
						`title`, `subtitle`, 'languagestrings/en-gb', `alias`, `content_text`,
						`protected`, `featured`, `stickied`, `status`,
						`start_publishing_datetime`, `stop_publishing_datetime`,
						`version`, `version_of_id`, `status_prior_to_version`,
						`created_datetime`, `created_by`,
						`modified_datetime`, `modified_by`,
						`checked_out_datetime`, `checked_out_by`,
						`root`, id as `parent_id`, `lft`, `rgt`, `lvl`, `home`,
						`customfields`, `parameters`, `metadata`,
						'en-gb', `translation_of_id`, `ordering`
				FROM #__language_strings
				WHERE id = " . (int) $row->id;

				$this->db->setQuery($sql);
				$this->db->execute();
				}
			}
		}

		/** Add to Catalog */
		$catalog = "SELECT DISTINCT id
					FROM `molajo_language_strings`
					WHERE id NOT IN (SELECT DISTINCT source_id
					  FROM  `molajo_catalog`
					  WHERE catalog_type_id = " . (INT) CATALOG_TYPE_LANGUAGE_STRING . ")";

		$this->db->setQuery($catalog);
		$results = $this->db->loadObjectList();

		if ($results === false || count($results) === 0) {
		} else {
			foreach ($results as $row) {

				$sql = "

				INSERT INTO `molajo_catalog`(`id`, `application_id`, `catalog_type_id`,
						`source_id`, `enabled`, `redirect_to_id`, `sef_request`, `page_type`,
						`extension_instance_id`, `view_group_id`, `primary_category_id`)

				SELECT NULL as `id`, `b`.`id`, `a`.`catalog_type_id`,
					`a`.`id`, 1, 0, CONCAT(`a`.`path`, '/', `a`.`alias`),
					'item', `a`.`extension_instance_id`, 1, 12

				FROM `molajo_language_strings` as `a`,
					`molajo_applications` as `b`

				WHERE a.id = " . (int) $row->id;

				$this->db->setQuery($sql);
				$this->db->execute();
			}
		}
		return true;
	}
}
