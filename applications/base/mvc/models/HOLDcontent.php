<?php
/**
 * @package     Molajo
 * @subpackage  Model
 * @copyright   Copyright (C) 2012 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die;

/**
 * Content Table Class
 *
 * @package     Molajo
 * @subpackage  Model
 * @since       1.0
 * @link
 */
class MolajoContentModel extends MolajoModel
{
    /**
     * __construct
     *
     * @param database A database connector object
     *
     * @since 1.0
     */
    function __construct($db)
    {
     //   parent::__construct('#' . JRequest::getCmd('ComponentTable'), 'id', $db);
        parent::__construct('#__content', 'id', $db);
    }

    /**
     * _getAssetTitle
     *
     * Method to return the title to use for the asset table.
     *
     * @return    string
     * @since    1.6
     */
    protected function _getAssetTitle()
    {
        return $this->title;
    }

    /**
     * _getAssetParentId
     *
     * Get the parent asset id for the record
     *
     * @return    int
     * @since    1.6
     */
    protected function _getAssetParentId($table = null, $id = null)
    {
        /** initialise **/
        $assetId = null;
        $db = $this->getJdbo();

        /** retrieve parent category asset **/
        if ($this->category_id) {
            $query = $db->getQuery(true);
            $query->select('asset_id');
            $query->from('#__categories');
            $query->where('id = ' . (int)$this->category_id);

            $this->_db->setQuery($query->__toString());
            if ($result = $this->_db->loadResult()) {
                $assetId = (int)$result;
            }
        }

        /** return results **/
        if ($assetId) {
            return $assetId;
        } else {
            return parent::_getAssetParentId($table, $id);
        }
    }

    /**
     * bind
     *
     * Overloaded bind function
     *
     * @param    array $hash named array
     *
     * @return    null|string null is operation was satisfactory, otherwise returns an error
     * @see    MolajoModel:bind
     * @since    1.6
     */
    public function bind($array, $ignore = '')
    {
        $jsonModel = JModel::getInstance('ModelConfiguration', 'Molajo', array('ignore_request' => true));
        $results = $jsonModel->getOptionList(MOLAJO_EXTENSION_OPTION_ID_JSON_FIELDS);

        foreach ($results as $count => $result) {
            if (isset($array[$result->value]) && is_array($array[$result->value])) {
                $registry = new Registry();
                $registry->loadArray($array[$result->value]);
                $array[$result->value] = (string)$registry;
            }
        }

        /** bind rules **/
        if (isset($array['rules']) && is_array($array['rules'])) {
            $rules = new JRules($array['rules']);
            $this->setRules($rules);
        }

        /** parent **/
        return parent::bind($array, $ignore);
    }

    /**
     * check
     *
     * Overloaded check function
     *
     * @return    boolean
     * @see    MolajoModel::check
     * @since    1.6
     */
    public function check()
    {
        /** title **/
        if (trim($this->title) == '') {
            $this->setError(TextServices::_('MOLAJO_WARNING_PROVIDE_VALID_NAME'));
            return false;
        }

        /** alias **/
        $this->_getAlias();

        if (trim(str_replace('-', '', $this->alias)) == '') {
            $this->alias = Molajo::Date()->format('Y-m-d-H-i-s');
        }

        /** text fields **/
        if (trim(str_replace('&nbsp;', '', $this->content_text)) == '') {
            $this->content_text = '';
        }

        /** publish up and down dates **/
        if (intval($this->stop_publishing_datetime) > 0 && $this->stop_publishing_datetime < $this->start_publishing_datetime) {
            $temp = $this->start_publishing_datetime;
            $this->start_publishing_datetime = $this->stop_publishing_datetime;
            $this->stop_publishing_datetime = $temp;
        }

        /** standard cleanup: eliminate extra spaces between phrases, remove cr (\r) and lf (\n) characters from string **/
        if (!empty($this->metakey)) {

            $bad_characters = array("\n", "\r", "\"", "<", ">");
            $after_clean = JString::str_ireplace($bad_characters, "", $this->metakey);
            $keys = explode(',', $after_clean);
            $clean_keys = array();

            foreach ($keys as $key) {
                if (trim($key)) {
                    $clean_keys[] = trim($key);
                }
            }
            $this->metakey = implode(", ", $clean_keys);
        }
        return true;
    }

    /**
     * store
     *
     * Overriden MolajoModel::store to set modified data and user id.
     *
     * @param    boolean    True to update fields even if they are null.
     *
     * @return    boolean    True on success.
     * @since    1.6
     */
    public function store($updateNulls = false)
    {
        $date = Molajo::Date();
        $user = Molajo::User();

        if ($this->id) {
            $this->modified = $date->toMySQL();
            $this->modified_by = $user->get('id');

            if (intval($this->stop_publishing_datetime) > 0 && $this->stop_publishing_datetime < $this->start_publishing_datetime) {
                $temp = $this->start_publishing_datetime;
                $this->start_publishing_datetime = $this->stop_publishing_datetime;
                $this->stop_publishing_datetime = $temp;
            }

        } else {
            if (intval($this->created)) {
            } else {
                $this->created = $date->toMySQL();
            }
            if (empty($this->created_by)) {
                $this->created_by = $user->get('id');
            }
        }

        $this->_getAlias();

        if (trim(str_replace('-', '', $this->alias)) == '') {
            $this->alias = Molajo::Date()->format('Y-m-d-H-i-s');
        }

        return parent::store($updateNulls);
    }

    /**
     * _getAlias
     *
     * Verify, or create and then verify, a unique value for Alias
     *
     * @return    boolean
     * @since    1.6
     */
    protected function _getAlias()
    {
        /** initialise **/
        $aliasFound = false;
        $db = $this->getJdbo();

        /** alias **/
        if (trim($this->alias) == '') {
            $this->alias = $this->title;
        }
        $this->alias = Molajo::Application()->stringURLSafe($this->alias);

        /** do not check alias for version saves  **/
        if ($this->state == MOLAJO_STATUS_VERSION) {
            return true;
        }

        /** check if unique, if not increment until unique value found **/
        $i = 1;
        $tempAlias = $this->alias;

        do {
            $query = $db->getQuery(true);

            $query->select($db->namequote('alias'));
            $query->from($db->namequote('#' . JRequest::getCmd('ComponentTable')));
            $query->where($db->namequote('alias') . ' = ' . $db->quote($this->alias));
            $query->where($db->namequote('id') . ' <> ' . (int)$this->id);
            $query->where($db->namequote('state') . ' <> ' . (int)MOLAJO_STATUS_VERSION);

            $this->_db->setQuery($query->__toString());

            if ($result = $this->_db->loadResult()) {
                $aliasFound = false;
            } else {
                $aliasFound = true;
            }

            if ($aliasFound === false) {
                $tempAlias = $this->alias . '-' . $i;
            }
            $i++;

        } while ($aliasFound === false);

        $this->alias = $tempAlias;

        return;
    }
}
