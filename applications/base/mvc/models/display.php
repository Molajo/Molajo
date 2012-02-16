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
     * _setCriteria
     *
     * Method to set the criteria needed for a query
     *
     * @return  object
     * @since   1.0
     */
    protected function _setCriteria()
    {
        /** Set State for Selection Criteria */
        $asset_type_id = $this->task_request->get('source_asset_type_id');

        $extensionName = ucfirst($this->get('extension_instance_name'));
        $extensionName = str_replace(array('-', '_'), '', $extensionName);

        $helperClass = 'Molajo' . $extensionName . 'ModelHelper';

        if (class_exists($helperClass)) {
            $h = new $helperClass();
        } else {
            $h = new MolajoModelHelper();
        }

        /** Retrieve xml for this view */
        $xmlfile = MOLAJO_EXTENSIONS_COMPONENTS . '/articles/options/grid.xml';
        $configuration = simplexml_load_file($xmlfile);
        if (count($configuration) == 0) {
            return true;
        }

        $filterArray = array();
        foreach ($configuration->filters->children() as $child) {
            $field = (string)$child['name'];
            $requestAssetID = Molajo::Request()->get('request_asset_id');

            $filterName = 'select.' . $field;
            $storedAsName = $requestAssetID . '.' . $filterName;

            $filterValue = Services::User()->get($storedAsName, $filterName);
            $this->set($filterName, $filterValue);
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

        $this->query->select('a.' . $this->db->namequote('id'));
        $this->query->select('a.' . $this->db->namequote('extension_instance_id'));
        $this->query->select('a.' . $this->db->namequote('asset_type_id'));
        $this->query->select('a.' . $this->db->namequote('title'));
        $this->query->select('a.' . $this->db->namequote('subtitle'));
        $this->query->select('a.' . $this->db->namequote('path'));
        $this->query->select('a.' . $this->db->namequote('alias'));
        $this->query->select('a.' . $this->db->namequote('content_text'));
        $this->query->select('a.' . $this->db->namequote('protected'));
        $this->query->select('a.' . $this->db->namequote('featured'));
        $this->query->select('a.' . $this->db->namequote('stickied'));
        $this->query->select('a.' . $this->db->namequote('status'));
        $this->query->select('a.' . $this->db->namequote('start_publishing_datetime'));
        $this->query->select('a.' . $this->db->namequote('stop_publishing_datetime'));
        $this->query->select('a.' . $this->db->namequote('version'));
        $this->query->select('a.' . $this->db->namequote('version_of_id'));
        $this->query->select('a.' . $this->db->namequote('status_prior_to_version'));
        $this->query->select('a.' . $this->db->namequote('created_datetime'));
        $this->query->select('a.' . $this->db->namequote('created_by'));
        $this->query->select('a.' . $this->db->namequote('modified_datetime'));
        $this->query->select('a.' . $this->db->namequote('modified_by'));
        $this->query->select('a.' . $this->db->namequote('checked_out_datetime'));
        $this->query->select('a.' . $this->db->namequote('checked_out_by'));

        $this->query->select('a.' . $this->db->namequote('root'));
        $this->query->select('a.' . $this->db->namequote('parent_id'));
        $this->query->select('a.' . $this->db->namequote('lft'));
        $this->query->select('a.' . $this->db->namequote('rgt'));
        $this->query->select('a.' . $this->db->namequote('lvl'));
        $this->query->select('a.' . $this->db->namequote('home'));

        $this->query->select('a.' . $this->db->namequote('custom_fields'));
        $this->query->select('a.' . $this->db->namequote('parameters'));
        $this->query->select('a.' . $this->db->namequote('metadata'));
        $this->query->select('a.' . $this->db->namequote('language'));
        $this->query->select('a.' . $this->db->namequote('translation_of_id'));
        $this->query->select('a.' . $this->db->namequote('ordering'));

        $this->query->from(Services::Configuration()->get('dbprefix') . 'content' . ' as a ');

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

        $this->db->setQuery($this->query);

        return;
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
