<?php
/**
 * @package     Molajo
 * @subpackage  Configuration Model
 * @copyright   Copyright (C) 2012 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die;

/**
 * Configuration Model
 *
 * @package     Molajo
 * @subpackage  Model
 * @since       1.0
 */
class MolajoModelConfiguration extends MolajoModel
{
    /**
     * @var string $_option
     *
     * @since 1.0
     */
    protected $_option;

    /**
     * @var int $_id
     *
     * @since 1.0
     */
    protected $_id;

    /**
     * @var array $_overrides
     *
     * @since 1.0
     */
    protected $_overrides = array();

    /**
     * __construct
     *
     * Constructor.
     *
     * @param  $config
     * @since  1.0
     */
    public function __construct($config = array())
    {
        $this->_name = get_class($this);

        parent::__construct($config = array());

        if (array_key_exists('option', $this->config)) {
            $this->_option = $this->config['option'];
        }
        $this->getID();
        $this->setOverridesArray();
    }

    /**
     * getOptionValue
     *
     * Retrieve a Single Configuration Value for Option ID
     *
     * @param  $option_id
     * 
     * @return  bool
     * @since   1.0
     */
    public function getOptionValue($option_id)
    {
        $query = $this->_db->getQuery(true);

        /** option or core **/
        $key = $this->getComponentOptionKey($option_id);

        /** validation query **/
        $query->select($this->_db->namequote('option_value'), $this->_db->namequote('option_value_literal'));
        $query->from($this->_db->namequote('#__extension_options'));
        $query->where($this->_db->namequote('option_id') . ' = ' . (int)$option_id);
        $query->where($this->_db->namequote('extension_instance_id') . ' = ' . (int)$key);
        $query->where($this->_db->namequote('ordering') . ' > 0 ');

        $this->_db->setQuery($query->__toString());

        if ($results = $this->_db->loadObjectList()) {
        } else {
            MolajoController::getApplication()->setMessage($this->_db->getErrorMsg(), 'error');
            return false;
        }

        if (count($results) > 0) {
            foreach ($results as $count => $item) {
                return $item->option_value;
            }
        }

        return false;
    }

    /**
     * getOptionLiteralValue
     *
     * For a specific option_id and option_value, return the associated option_value_literal
     *
     * @param   $option_id
     * @param   $option_value
     * @return  string or boolean option_value
     * @since   1.0
     */
    public function getOptionLiteralValue($option_id, $option_value)
    {
        $query = $this->_db->getQuery(true);

        /** option or core **/
        $key = $this->getComponentOptionKey($option_id);

        /** retrieve value **/
        $query = $this->_db->getQuery(true);

        $query->select($this->_db->namequote('option_value_literal'));
        $query->from($this->_db->namequote('#__extension_options'));
        $query->where($this->_db->namequote('option_id') . ' = ' . (int)$option_id);
        $query->where($this->_db->namequote('extension_instance_id') . ' = ' . (int)$key);
        $query->where($this->_db->namequote('option_value') . ' = ' . $this->_db->quote(trim($option_value)));
        $query->where($this->_db->namequote('ordering') . ' > 0 ');

        $this->_db->setQuery($query->__toString());

        if ($results = $this->_db->loadObjectList()) {
        } else {
            MolajoController::getApplication()->setMessage($this->_db->getErrorMsg(), 'error');
            return false;
        }

        if (count($results) > 0) {
            foreach ($results as $result) {
                return $result->option_value_literal;
            }
        }

        return false;
    }

    /**
     * getOptionList
     *
     * Retrieve Single Configuration Value for this Component and Option
     *
     * @param  string $option
     * @param  int    $option_id
     * @return string
     * @since  1.0
     */
    public function getOptionList($option_id)
    {
        /** check for overrides **/
        $query = $this->_db->getQuery(true);

        /** option or core **/
        $component_option = $this->getComponentOptionKey($option_id);

        /** validation query **/
        $query = $this->_db->getQuery(true);

        $query->select('DISTINCT ' . $this->_db->namequote('option_value') . ' AS value');
        $query->select($this->_db->namequote('option_value_literal') . ' as text');
        $query->from($this->_db->namequote('#__extension_options'));
        $query->where($this->_db->namequote('option_id') . ' = ' . (int)$option_id);
        $query->where($this->_db->namequote('extension_instance_id') . ' = ' . (int)$this->_id);
        $query->where($this->_db->namequote('ordering') . ' > 0 ');
        $query->order($this->_db->namequote('ordering'));

        $this->_db->setQuery($query->__toString());

        if ($results = $this->_db->loadObjectList()) {
        } else {
            MolajoController::getApplication()->setMessage($this->_db->getErrorMsg(), 'error');
            return false;
        }
        return $results;
    }

    /**
     * getComponentOptionKey
     *
     * Retrieve component_option key for option_id
     *
     * @return string option value
     * 
     * @since  1.0
     */
    private function getComponentOptionKey($option_id)
    {
        foreach ($this->_overrides as $configurationOverrides => $override) {
            if ($configurationOverrides == $option_id) {
                if ($override == $this->_id) {
                    return $override;
                    break;
                }
            }
            continue;
        }
        /** core is 1 */
        return 1;
    }

    /**
     * setOverridesArray
     *
     * To override the default Molajo configuration values, add a record for the
     * option_id with an ordering of 0.
     * This method creates an array for all option_id types and associated value
     * The core is used when there is no override for that option_id set
     *
     * @return string
     * @since  1.0
     */
    private function setOverridesArray()
    {
        $query = $this->_db->getQuery(true);

        /** retrieve all option_id values **/
        $query->select('DISTINCT ' . $this->_db->namequote('option_id'));
        $query->from($this->_db->namequote('#__extension_options'));
        $query->where($this->_db->namequote('ordering') . ' = 0');
        $query->where($this->_db->namequote('option_id') . ' > 0');
        $query->order($this->_db->namequote('option_id'));

        $this->_db->setQuery($query->__toString());

        if ($results = $this->_db->loadObjectList()) {
        } else {
            MolajoController::getApplication()->setMessage($this->_db->getErrorMsg(), 'error');
            return false;
        }

        /** for each option_id, determine if there are overrides for the extension */
        $optionArray = array();
        if (count($results) > 0) {

            foreach ($results as $count => $item) {

                /** retrieve override component_option, if existing **/
                $query = $this->_db->getQuery(true);

                $query->select($this->_db->namequote('extension_instance_id'));
                $query->from($this->_db->namequote('#__extension_options'));
                $query->where($this->_db->namequote('ordering') . ' = 0');
                $query->where($this->_db->namequote('option_id') . ' = ' . (int)$item->option_id);
                $query->where($this->_db->namequote('extension_instance_id') . ' = ' . (int)$this->_id);

                $this->_db->setQuery($query->__toString());

                if ($extensionResults = $this->_db->loadResult()) {
                    $optionArray[$item->option_id] = $extensionResults;
                } else {
                    /** core is 1 */
                    $optionArray[$item->option_id] = 1;
                }
            }
        }

        $this->_overrides = $optionArray;

        return;
    }

    /**
     * getID
     *
     * Retrieve the primary key for the extension instance for the $option
     *
     * @return null
     * @since  1.0
     */
    private function getID()
    {
        $query = $this->_db->getQuery(true);

        $query->select('DISTINCT a.' . $this->_db->namequote('id') . ' AS extension_instance_id ');
        $query->from($this->_db->namequote('#__extension_instances') . ' as a');
        $query->from($this->_db->namequote('#__extensions') . ' as b');
        $query->where('a.' . $this->_db->namequote('extension_id') . ' = b.' . $this->_db->namequote('id'));
        $query->where($this->_db->namequote('name') . ' = ' . $this->_db->quote($this->_option));

        $this->_db->setQuery($query->__toString());

        if ($this->_id = $this->_db->loadResult()) {
        } else {
            MolajoController::getApplication()->setMessage($this->_db->getErrorMsg(), 'error');
            return false;
        }

        return;
    }
}
