<?php
/**
 * @package     Molajo
 * @subpackage  Controller
 * @copyright   Copyright (C) 2012 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die;

/**
 * Content
 *
 * @package      Molajo
 * @subpackage   Model
 * @since        1.0
 */
class MolajoContentModel extends MolajoDisplayModel
{
    /**
     * __construct
     *
     * @return  object
     * @since   1.0
     */
    function __construct()
    {
        $this->name = $this;
        $this->table = '#__content';

        parent::__construct();
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
            $this->setError(Services::Language()->_('MOLAJO_WARNING_PROVIDE_VALID_NAME'));
            return false;
        }

        /** alias **/
        $this->_getAlias();

        if (trim(str_replace('-', '', $this->alias)) == '') {
            $this->alias = Services::Date()
                ->format('Y-m-d-H-i-s');
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
        $date = Services::Date();
        $user = Services::User();

        if ($this->id) {
            $this->modified = $date->toSql();
            $this->modified_by = $user->get('id');

            if (intval($this->stop_publishing_datetime) > 0 && $this->stop_publishing_datetime < $this->start_publishing_datetime) {
                $temp = $this->start_publishing_datetime;
                $this->start_publishing_datetime = $this->stop_publishing_datetime;
                $this->stop_publishing_datetime = $temp;
            }

        } else {
            if (intval($this->created)) {
            } else {
                $this->created = $date->toSql();
            }
            if (empty($this->created_by)) {
                $this->created_by = $user->get('id');
            }
        }

        $this->_getAlias();

        if (trim(str_replace('-', '', $this->alias)) == '') {
            $this->alias = Services::Date()->getDate()->format('Y-m-d-H-i-s');
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
        $db = $this->getDb();

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

            $this->db->setQuery($query->__toString());

            if ($result = $this->db->loadResult()) {
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
