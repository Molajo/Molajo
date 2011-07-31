<?php
/**
 * @package     Molajo
 * @subpackage  Table
 * @copyright   Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @copyright   Copyright (C) 2011 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die;

/**
 * Content Table Class
 *
 * @package     Molajo
 * @subpackage  Table
 * @since       1.0
 * @link
 */
class MolajoTableContent extends MolajoTable
{
    /**
     * __construct
     *
     * @param database A database connector object
     *
     * @since 1.6
     */
    function __construct($db)
    {
        parent::__construct('#'.JRequest::getCmd('ComponentTable'), 'id', $db);
    }

    /**
     * _getAssetTitle
     *
     * Method to return the title to use for the asset table.
     *
     * @return	string
     * @since	1.6
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
     * @return	int
     * @since	1.6
     */
    protected function _getAssetParentId ($table = null, $id = null)
    {
        /** initialize **/
        $assetId = null;
        $db = $this->getDbo();

        /** retrieve parent category asset **/
        if ($this->catid) {
            $query	= $db->getQuery(true);
            $query->select('asset_id');
            $query->from('#__categories');
            $query->where('id = '.(int) $this->catid);

            $this->_db->setQuery($query);
            if ($result = $this->_db->loadResult()) {
                $assetId = (int) $result;
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
     * @param	array $hash named array
     *
     * @return	null|string null is operation was satisfactory, otherwise returns an error
     * @see	MolajoTable:bind
     * @since	1.6
     */
    public function bind($array, $ignore = '')
    {
        $jsonModel = JModel::getInstance('ModelConfiguration', 'Molajo', array('ignore_request' => true));
        $results = $jsonModel->getOptionList (MOLAJO_CONFIG_OPTION_ID_JSON_FIELDS);

        foreach ($results as $count => $result) {
            if (isset($array[$result->value]) && is_array($array[$result->value])) {
                $registry = new JRegistry();
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
     * @return	boolean
     * @see	MolajoTable::check
     * @since	1.6
     */
    public function check()
    {
        /** title **/
        if (trim($this->title) == '') {
            $this->setError(JText::_('MOLAJO_WARNING_PROVIDE_VALID_NAME'));
            return false;
        }

        /** alias **/
        $this->_getAlias();

        if (trim(str_replace('-','',$this->alias)) == '') {
            $this->alias = MolajoFactory::getDate()->format('Y-m-d-H-i-s');
        }

        /** text fields **/
        if (trim(str_replace('&nbsp;', '', $this->content_text)) == '') {
            $this->content_text = '';
        }

        /** publish up and down dates **/
        if (intval($this->publish_down) > 0 && $this->publish_down < $this->publish_up) {
            $temp = $this->publish_up;
            $this->publish_up = $this->publish_down;
            $this->publish_down = $temp;
        }

        /** standard cleanup: eliminate extra spaces between phrases, remove cr (\r) and lf (\n) characters from string **/
        if (!empty($this->metakey)) {

            $bad_characters = array("\n", "\r", "\"", "<", ">");
            $after_clean = JString::str_ireplace($bad_characters, "", $this->metakey);
            $keys = explode(',', $after_clean);
            $clean_keys = array();

            foreach($keys as $key) {
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
     * Overriden MolajoTable::store to set modified data and user id.
     *
     * @param	boolean	True to update fields even if they are null.
     *
     * @return	boolean	True on success.
     * @since	1.6
     */
    public function store($updateNulls = false)
    {
        $date = MolajoFactory::getDate();
        $user = MolajoFactory::getUser();

        if ($this->id) {
            $this->modified	= $date->toMySQL();
            $this->modified_by	= $user->get('id');

            if (intval($this->publish_down) > 0 && $this->publish_down < $this->publish_up) {
                $temp = $this->publish_up;
                $this->publish_up = $this->publish_down;
                $this->publish_down = $temp;
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

        if (trim(str_replace('-','',$this->alias)) == '') {
            $this->alias = MolajoFactory::getDate()->format('Y-m-d-H-i-s');
        }

        return parent::store($updateNulls);
    }

    /**
     * _getAlias
     *
     * Verify, or create and then verify, a unique value for Alias
     *
     * @return	boolean
     * @since	1.6
     */
    protected function _getAlias ()
    {
        /** initialize **/
        $aliasFound = false;
        $db = $this->getDbo();

        /** alias **/
        if (trim($this->alias) == '') {
            $this->alias = $this->title;
        }
        $this->alias = JApplication::stringURLSafe($this->alias);

        /** do not check alias for version saves  **/
        if ($this->state == MOLAJO_STATE_VERSION) {
            return true;
        }

        /** check if unique, if not increment until unique value found **/
        $i = 1;
        $tempAlias = $this->alias;

        do {
                $query	= $db->getQuery(true);

                $query->select($db->namequote('alias'));
                $query->from($db->namequote('#'.JRequest::getCmd('ComponentTable')));
                $query->where($db->namequote('alias').' = '.$db->quote($this->alias));
                $query->where($db->namequote('id').' <> '. (int) $this->id);
                $query->where($db->namequote('state').' <> '.(int) MOLAJO_STATE_VERSION);

                $this->_db->setQuery($query);

                if ($result = $this->_db->loadResult()) {
                    $aliasFound = false;
                } else {
                    $aliasFound = true;
                }

            if ($aliasFound === false) {
                $tempAlias = $this->alias.'-'.$i;
            }
            $i++;

        } while ($aliasFound === false);

        $this->alias = $tempAlias;

        return;
    }
}