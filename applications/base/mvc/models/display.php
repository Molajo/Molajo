<?php
/**
 * @package     Molajo
 * @subpackage  Model
 * @copyright   Copyright (C) 2012 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die;

/**
 * Display
 *
 * Abstracted class used as the parent class to most display views
 *
 * @package     Molajo
 * @subpackage  Model
 * @since       1.0
 */
class MolajoDisplayModel extends MolajoModel
{

    /**
     * $selectStatements
     *
     * Array of select elements
     *
     * @var    array
     * @since  1.0
     */
    protected $selectStatements;

    /**
     * $selectionCriteria
     *
     * Array of query criteria
     *
     * @var    array
     * @since  1.0
     */
    protected $selectionCriteria;

    /**
     *  $limitResults
     *
     *  Number of resultsets to return
     *  @va
     */
    /**
     * _setCriteria
     *
     * Method to set the criteria needed for a query
     *
     * @return  object
     * @since   1.0
     */
    protected function _setCriteria()
    {
        /** Asset Type */
        $asset_type_id = $this->task_request->get('source_asset_type_id', 0);

        /** Model Helper */
        $extensionName = ucfirst($this->get('extension_instance_name', ''));
        $extensionName = str_replace(array('-', '_'), '', $extensionName);

        $helperClass = 'Molajo' . $extensionName . 'ModelHelper';

        if (class_exists($helperClass)) {
            $h = new $helperClass();
        } else {
            $h = new MolajoModelHelper();
        }

        /**
         *  Select Statements
         */
        $this->selectStatements = array();

        $fields = $this->getFieldnames();
        if (count($fields) > 0) {
            foreach ($fields as $field) {
                $this->selectStatements[] = 'a.'.$field;
            }
        }
/**
        echo '<pre>';
        var_dump($this->selectStatements);
        echo '</pre>';
*/
        /**
         *  Selection Criteria
         */
        $this->selectionCriteria = array();

        $xmlfile = MOLAJO_EXTENSIONS_COMPONENTS . '/articles/options/grid.xml';
        if (file_exists($xmlfile)) {
            $configuration = simplexml_load_file($xmlfile);
        } else {
            $configuration = array();
        }

        if (count($configuration) > 0) {

            foreach ($configuration->filters->children() as $child) {
                $field = (string)$child['name'];
                $requestAssetID = Molajo::Request()->get('request_asset_id');

                $filterName = 'select.' . $field;
                $storedAsName = $requestAssetID . '.' . $filterName;

                $filterValue = Services::User()->get($storedAsName, $filterName);
                $this->set($filterName, $filterValue);
            }
        }

        return;
    }

    /**
     * _setQuery
     *
     * Method to create a query object in preparation of running a query
     *
     * @return  object
     * @since   1.0
     */
    protected function _setQuery()
    {
        $this->query = $this->db->getQuery(true);

        /* remove below when set criteria done */
        $asset_type_id = $this->task_request->get('source_asset_type_id');
        /* remove above when set criteria done */

        /**
         *  Select Statements
         */
        if (count($this->selectStatements) > 0) {
            foreach ($this->selectStatements as $select) {
                $this->query->select(
                    $this->db->namequote($select)
                );
            }
        }

        $this->query->from($this->table . ' as a ');

        $this->db->setQuery($this->query);

        return;
    }

    protected function _hold ()
    {

        /** Status and Dates */
        $this->query->where('a.' . $this->db->namequote('status') .
            ' = ' . MOLAJO_STATUS_PUBLISHED);

        $this->query->where('(a.start_publishing_datetime = ' .
                $this->db->quote($this->nullDate) .
                ' OR a.start_publishing_datetime <= ' .
                $this->db->quote($this->now) . ')'
        );
        $this->query->where('(a.stop_publishing_datetime = ' .
                $this->db->quote($this->nullDate) .
                ' OR a.stop_publishing_datetime >= ' .
                $this->db->quote($this->now) . ')'
        );

        /** ordering */
        $this->query->order('a.start_publishing_datetime DESC');

        /** Assets Join and View Access Check */
        $this->query->where('b_assets.asset_type_id = ' .
            $this->db->quote($asset_type_id));

        MolajoAccessService::setQueryViewAccess(
            $this->query,
            array('join_to_prefix' => 'a',
                'join_to_primary_key' => 'id',
                'asset_prefix' => 'b_assets',
                'select' => true
            )
        );

    }

    /**
     * _runQuery
     *
     * Method to execute a prepared and set query statement,
     * returning the results
     *
     * @return  object
     * @since   1.0
     */
    protected function _runQuery()
    {
        $data = $this->db->loadObjectList();

        if ($this->db->getErrorNum() == 0) {

        } else {
            Services::Message()
                ->set(
                $message = Services::Language()->_('ERROR_DATABASE_QUERY') . ' ' .
                    $this->db->getErrorNum() . ' ' .
                    $this->db->getErrorMsg(),
                $type = MOLAJO_MESSAGE_TYPE_ERROR,
                $code = 500,
                $debug_location = $this->name . ':' . 'getData',
                $debug_object = $this->db
            );
            return $this->request->set('status_found', false);
        }

        if (count($data) == 0) {
            return array();
        }

        return $data;
    }
}
