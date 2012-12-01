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
     * @return  object
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
     * @param   string  $key
     * @param   mixed   $default
     *
     * @return  mixed
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
     * @param   string  $key
     * @param   mixed   $value
     *
     * @return  mixed
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
     * Pass input to Filter Service which handles each data element according to datatype
     *
     * @param   string  $name
     * @param   string  $field_value
     * @param   string  $dataType
     * @param   int     $null        0 or 1 - is null allowed
     * @param   string  $default     Default value, optional
     *
     * @return  mixed
     * @since   1.0
     */
    protected function filterInput($name, $value, $dataType, $null = null, $default = null)
    {
        try {
            $value = Services::Filter()->filter($value, $dataType, $null, $default);

        } catch (\Exception $e) {
            //todo: errors
            echo $e->getMessage() . ' ' . $name;
        }

        return $value;
    }

    /**
     * Single Value Result returned in $this->query_results
     *
     * @param   $primary_prefix
     * @param   $table_name
     *
     * @return  object
     * @since   1.0
     */
    protected function loadResult($primary_prefix, $table_name)
    {
        if ($this->query->select == null) {
            $this->query->select(
                $this->db->qn($primary_prefix . '.' . $this->primary_key)
            );
        }

        if ($this->query->from == null) {
            $this->query->from(
                $this->db->qn($table_name) . ' as ' . $this->db->qn($primary_prefix)
            );
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
     * insertLanguageString
     *
     * todo: move this into normal CRUD operations
     *
     * @param   array $translated
     *
     * @return  bool
     * @since   1.0
     */
    public function insertLanguageString($translated = array())
    {
        if (count($translated) === 0) {
            return true;
        }

        if (Services::Registry()->get(USER_LITERAL, 'username') == 'admin') {
        } else {
            return true;
        }

        if (Services::Registry()->get(CONFIGURATION_LITERAL, 'profiler_collect_missing_language_strings') == '1') {
        } else {
            return true;
        }

        /** Add Missing Language Strings to Base */
        foreach ($translated as $key => $value) {

            $sql = "
             SELECT id
					FROM `molajo_language_strings`
					WHERE language = 'string'
					AND title = "
                . $this->db->q($value);

            $this->db->setQuery($sql);
            $results = $this->db->loadResult();

            if ((int)$results == 0) {

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

                VALUES (null, 0, 6250, 6250, "
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
        }

        /** Add to English Language */
        $en_GB = "SELECT DISTINCT id
					FROM `molajo_language_strings`
					WHERE language = 'string'
					  AND title NOT IN (SELECT title
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

                    SELECT null as `id`, `site_id`, `extension_instance_id`, `catalog_type_id`,
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
                    WHERE id = " . (int)$row->id;

                    $this->db->setQuery($sql);
                    $this->db->execute();
                }
            }
        }

        /** Add to Catalog */
        $catalog = "SELECT DISTINCT id
					FROM `molajo_language_strings`
					WHERE CONCAT(path, '/', alias) NOT IN (SELECT DISTINCT sef_request
					  FROM  `molajo_catalog`
					  WHERE catalog_type_id = " . (INT)CATALOG_TYPE_LANGUAGE_STRING . ")";

        $this->db->setQuery($catalog);
        $results = $this->db->loadObjectList();

        if ($results === false || count($results) === 0) {
        } else {
            foreach ($results as $row) {

                $sql = "

				INSERT INTO `molajo_catalog`(`id`, `application_id`, `catalog_type_id`,
						`source_id`, `enabled`, `redirect_to_id`, `sef_request`, `page_type`,
						`extension_instance_id`, `view_group_id`, `primary_category_id`)

				SELECT null as `id`, `b`.`id`, `a`.`catalog_type_id`,
					`a`.`id`, 1, 0, CONCAT(`a`.`path`, '/', `a`.`alias`),
					'item', `a`.`extension_instance_id`, 1, 12

				FROM `molajo_language_strings` as `a`,
					`molajo_applications` as `b`

				WHERE a.id = " . (int)$row->id;

                $this->db->setQuery($sql);
                $this->db->execute();
            }
        }
        return true;
    }
}
