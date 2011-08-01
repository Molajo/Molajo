<?php
/**
 * @package     Molajo
 * @subpackage  Configuration Model
 * @copyright   Copyright (C) 2011 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die;

/**
 * Configuration Model
 *
 * @package	    Molajo
 * @subpackage	Model
 * @since       1.0
 */
class MolajoModelConfiguration extends JModel
{
    /**
     * @var string $option
     * @since 1.0
     */
    protected $option;

    /**
     * @var array $overrides
     * @since 1.0 
     */
    protected $overrides = array();

    /**
     * __construct
     *
     * Constructor.
     *
     * @param $option
     * @since	1.0
     */
    public function __construct($option)
    {
        $this->option = $option;
        $this->setOverridesArray();
    }

    /**
     * getOptionValue
     *
     * Retrieve a Single Configuration Value for Option ID
     *
     * @param $option_id
     * @return bool
     * @since   1.0
     */
    public function getOptionValue ($option_id)
    {
        $db = MolajoFactory::getDbo();
        $query = $db->getQuery(true);
        
        /** option or core **/
        $component_option = $this->getComponentOptionKey ($option_id);

        /** validation query **/
        $query->select($db->namequote('option_value'), $db->namequote('option_value_literal'));
        $query->from($db->namequote('#__configuration'));
        $query->where($db->namequote('option_id').' = '.(int) $option_id);
        $query->where($db->namequote('component_option').' = '. $db->quote(trim($component_option)));
        $query->where($db->namequote('ordering').' > 0 ');

        $db->setQuery($query->__toString());

        if ($results = $db->loadObjectList()) {
        } else {
            MolajoFactory::getApplication()->enqueueMessage($db->getErrorMsg(), 'error');
            return false;
        }

        if (count($results) > 0) {
            foreach ($results as $count => $item) {
                $editView = $item->option_value;
            }
            return $editView;
        }

        return false;
    }

    /**
     * getOptionLiteralValue
     *
     * For a specific option_id and option_value, return the associated option_value_literal
     *
     * @param $option_id
     * @param $option_value
     * @return string or boolean option_value
     * @since 1.0
     */
    public function getOptionLiteralValue ($option_id, $option_value)
    {
        $db = MolajoFactory::getDbo();
        $query = $db->getQuery(true);

        /** option or core **/
        $component_option = $this->getComponentOptionKey ($option_id);

        /** retrieve value **/
        $query = $db->getQuery(true);

        $query->select($db->namequote('option_value_literal'));
        $query->from($db->namequote('#__configuration'));
        $query->where($db->namequote('option_id').' = '.(int) $option_id);
        $query->where($db->namequote('component_option').' = '. $db->quote(trim($component_option)));
        $query->where($db->namequote('option_value').' = '. $db->quote(trim($option_value)));
        $query->where($db->namequote('ordering').' > 0 ');

        $db->setQuery($query->__toString());

        if ($results = $db->loadObjectList()) {
        } else {
            MolajoFactory::getApplication()->enqueueMessage($db->getErrorMsg(), 'error');
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
     * @param string $option
     * @param int $option_id
     * @return string
     */
    public function getOptionList ($option_id)
    {
        /** check for overrides **/
        $db = MolajoFactory::getDbo();
        $query = $db->getQuery(true);

        /** option or core **/
        $component_option = $this->getComponentOptionKey ($option_id);

        /** validation query **/
        $query = $db->getQuery(true);

        $query->select('DISTINCT '.$db->namequote('option_value').' AS value, '.$db->namequote('option_value_literal').' as text');
        $query->from($db->namequote('#__configuration'));
        $query->where($db->namequote('option_id').' = '.(int) $option_id);
        $query->where($db->namequote('component_option').' = '. $db->quote(trim($component_option)));
        $query->where($db->namequote('ordering').' > 0 ');
        $query->order($db->namequote('ordering'));

        $db->setQuery($query->__toString());

        if (!$results = $db->loadObjectList()) {
            MolajoFactory::getApplication()->enqueueMessage($db->getErrorMsg(), 'error');
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
     * @since 1.0
     */
    public function getComponentOptionKey ()
    {
        foreach ($this->overrides as $configurationOverrides => $override) {
            if ($configurationOverrides == $this->option) {
                return $override;
            }
        }
        return 'core';
    }

    /**
     * setOverridesArray
     *
     * To override the default Molajo configuration values, add a record for the option_id with an ordering of 0
     * This method creates an array for all option_id types and the associated component_option value
     * The component_option core is used when there is no override for that option_id set
     * The component option is used for overrides
     *
     * @param string $option
     * @param int $option_id
     * @return string
     */
    public function setOverridesArray ()
    {
        $db = MolajoFactory::getDbo();
        $query = $db->getQuery(true);

        /** retrieve all option_id values **/
        $query->select('DISTINCT '.$db->namequote('option_id'));
        $query->from($db->namequote('#__configuration'));
        $query->where($db->namequote('ordering').' = 0');
        $query->where($db->namequote('option_id').' > 0');
        $query->order($db->namequote('option_id'));
        
        $db->setQuery($query->__toString());

        if ($results = $db->loadObjectList()) {
        } else {
            MolajoFactory::getApplication()->enqueueMessage($db->getErrorMsg(), 'error');
            return false;
        }

        /** for each option_id, determine if there are overrides for the option */
        $optionArray = array();
        if (count($results) > 0) {
            
            foreach ($results as $count => $item) {

                /** retrieve override component_option, if existing **/
                $query = $db->getQuery(true);
                
                $query->select($db->namequote('component_option'));
                $query->from($db->namequote('#__configuration'));
                $query->where($db->namequote('ordering').' = 0');
                $query->where($db->namequote('option_id').' = '.(int) $item->option_id);
                $query->where($db->namequote('component_option').' = '.$db->quote($this->option));

                $db->setQuery($query->__toString());

                if ($componentResults = $db->loadObjectList()) {
                    $optionArray[$item->option_id] = $this->option;
                } else {
                    $optionArray[$item->option_id] = 'core';
                }
            }
        }
        return;
    }
}