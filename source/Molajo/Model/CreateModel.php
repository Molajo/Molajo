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
 * Create
 *
 * @package     Molajo
 * @subpackage  Model
 * @since       1.0
 */
class CreateModel extends Model
{
	/**
	 * create - inserts a new row into a table
	 *
	 * @param  $data
	 *
	 * @return object
	 * @since  1.0
	 */
	public function create($data, $table_registry_name)
	{
		$table_name = Services::Registry()->get($table_registry_name, 'table');
		$primary_prefix = Services::Registry()->get($table_registry_name, 'primary_prefix');

		/** Prepare Data from Custom Field Groups */
		$customfieldgroups = Services::Registry()->get(
			$table_registry_name, 'customfieldgroups', array());

		if (is_array($customfieldgroups) && count($customfieldgroups) > 0) {

			foreach ($customfieldgroups as $customFieldName) {

				/** For this Custom Field Group (ex. Parameters, metadata, etc.) */
				$customFieldName = strtolower($customFieldName);

				/** Retrieve Field Definitions from Registry (XML) */
				$fields = Services::Registry()->get($table_registry_name, $customFieldName);

				$temp = $data->$customFieldName;

				/** Shared processing  */
				foreach ($fields as $field) {

					$name = $field['name'];
					$type = $field['type'];

					if (isset($field['identity'])) {
						$identity = $field['identity'];
					} else {
						$identity = 0;
					}
					if ($identity == 1) {
						$type = 'identity';
					}

					$value = $this->prepareFieldValues($type, $temp[$name]);
					if ($value === false) {
						$valid = false;
						break;
					}

					/** data element for SQL insert */
					$data->$customFieldName[$name] = $value;
				}
			}
		}

		/** Build Insert Statement */
		$fields = Services::Registry()->get($table_registry_name, 'fields');

		$insertSQL = 'INSERT INTO ' . $this->db->qn($table_name) . ' ( ';
		$valuesSQL = ' VALUES ( ';

		$first = true;

		foreach ($fields as $f) {

			if ($first === true) {
				$first = false;
			} else {
				$insertSQL .= ', ';
				$valuesSQL .= ', ';
			}

			$name = $f['name'];
			$type = strtolower($f['type']);

			$insertSQL .= $this->db->qn($name);

			$valuesSQL .= $this->prepareFieldValues($type, $data->$name);

		}

		$sql = $insertSQL . ') ' . $valuesSQL . ') ';

		$this->db->setQuery($sql);
		$this->db->execute();

		$id = $this->db->insertid();

		return $id;
	}

	/**
	 * prepareFieldValues prepares the values of each data element for insert into the database
	 *
	 * @param $name
	 * @param $type
	 * @param int $identity
	 * @param $data
	 *
	 * @return string - data element value
	 * @since  1.0
	 */
	protected function prepareFieldValues($type, $input)
	{
		$value = '';

		if ($type == 'identity') {
			$value = 'NULL';

		} elseif ($input === null) {
			$value = 'NULL';

		} elseif ($type == 'integer'
			|| $type == 'binary'
			|| $type == 'catalog_id'
			|| $type == 'boolean'
		) {

			$value = (int)$input;

		} elseif ($type == 'char'
			|| $type == 'datetime'
			|| $type == 'url'
			|| $type == 'email'
			|| $type == 'text'
			|| $type == 'ip_address'
		) {

			$value = $this->db->q($input);

		} elseif ($type == 'password') {

		} elseif ($type == 'customfield') {
			$value = $this->db->q(json_encode($input));

		} else {
			echo 'UNKNOWN TYPE ' . $type . ' in CreateModel::prepareFieldValues <br />';
		}

		return $value;
	}
}
