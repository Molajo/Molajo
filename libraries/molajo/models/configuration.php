<?php
/**
 * @version     $id: configuration.php
 * @package     Molajo
 * @subpackage  Configuration Model
 * @copyright   Copyright (C) 2011 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die();

/**
 * Molajo Configuration Model
 *
 * @package	Molajo
 * @subpackage	Model
 * @since 1.6
 */
class MolajoModelConfiguration extends JModel
{
   /**
     * $table
     * @var string
     */
    public $table = '#__configuration';

    /**
     * Build Array of Valid Views
     *
     * @param	None
     * @return	void
     */
    public function validateView ($view, $option_id)
    {

        return $this->validationQuery ($view, $option_id);
    }

    /**
     *  Build Array of Valid Layouts
     *
     *  @param  None
     *  @return value or false
     *  @since	1.6
     */
    public function validateLayout ($layout, $option_id)
    {
        return $this->validationQuery ($layout, $option_id);
    }

    /**
     *  Build Array of Valid Format
     *
     *  @param  None
     *  @return value or false
     *  @since	1.6
     */
    public function validateFormat ($layout, $option_id)
    {
        return $this->validationQuery ($layout, $option_id);
    }

    /**
     *  Validate Tasks
     *
     *  @param      None
     *  @return     void
     *  @since	1.6
     */
    public function validateTask ($task, $option_id)
    {
        return $this->validationQuery ($task, $option_id);
    }

    /**
     * getSingleConfigurationValue
     *
     * Retrieve a Single Configuration Value for Option ID
     *
     * @param int $option_id
     * @return string
     *
     */
    public function getSingleConfigurationValue ($option_id)
    {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);
        
        /** check for override **/
        $component_option = $this->getOverrideKey ($option_id);
        if ($component_option === false) {
            $component_option = 'core';
        }

        /** validation query **/
        $query->select($db->namequote('option_value'), $db->namequote('option_value_literal'));
        $query->from($db->namequote('#__configuration'));
        $query->where($db->namequote('option_id').' = '.(int) $option_id);
        $query->where($db->namequote('component_option').' = '. $db->quote(trim($component_option)));
        $query->where($db->namequote('ordering').' > 0 ');

        $db->setQuery($query->__toString());

        if ($results = $db->loadObjectList()) {
        } else {
            JFactory::getApplication()->enqueueMessage($db->getErrorMsg(), 'error');
            return false;
        }

        if (count($results) > 0) {
            foreach ($results as $count => $item) {
                $singleView = $item->option_value;
            }
            return $singleView;
        }

        return false;
    }

   /**
     *  Validate Tasks
     *
     *  @param      None
     *  @return     void
     *  @since	1.6
     */
    public function validationQuery ($value, $option_id)
    {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);

        $component_option = $this->getOverrideKey ($option_id);
        if ($component_option === false) {
            $component_option = 'core';
        }

        /** retrieve value **/
        $query = $db->getQuery(true);
        $query->select($db->namequote('option_id'), $db->namequote('option_value_literal'));
        $query->from($db->namequote('#__configuration'));
        $query->where($db->namequote('option_id').' = '.(int) $option_id);
        $query->where($db->namequote('component_option').' = '. $db->quote(trim($component_option)));
        $query->where($db->namequote('option_value').' = '. $db->quote(trim($value)));
        $query->where($db->namequote('ordering').' > 0 ');

        $db->setQuery($query->__toString());

        if (!$results = $db->loadObjectList()) {
            JFactory::getApplication()->enqueueMessage($db->getErrorMsg(), 'error');
            return false;
        }

        if (count($results) > 0) {
            foreach ($results as $count => $item) {
                $validated = $item->option_id;
            }
            return $validated;
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
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);

        $component_option = $this->getOverrideKey ($option_id);
        if ($component_option === false) {
            $component_option = 'core';
        }

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
            JFactory::getApplication()->enqueueMessage($db->getErrorMsg(), 'error');
            return false;
        }
        return $results;
    }

    /**
     * getOptionValueLiteral
     *
     * Retrieve Single Configuration Value for this Component and Option
     *
     * @param string $option
     * @param int $option_id
     * @return string
     */
    public function getOptionValueLiteral ($option_id, $option_value)
    {
        /** check for overrides **/
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);

        $component_option = $this->getOverrideKey ($option_id);
        if ($component_option === false) {
            $component_option = 'core';
        }

        /** verify value **/
        $query = $db->getQuery(true);

        $query->select('DISTINCT '.$db->namequote('option_value_literal').' as text');
        $query->from($db->namequote('#__configuration'));
        $query->where($db->namequote('option_id').' = '.(int) $option_id);
        $query->where($db->namequote('option_value').' = '. $db->quote(trim($option_value)));
        $query->where($db->namequote('component_option').' = '. $db->quote(trim($component_option)));
        $query->where($db->namequote('ordering').' > 0 ');

        $db->setQuery($query->__toString());

        if (!$results = $db->loadObjectList()) {
            return false;
        }

        if (count($results) > 0) {
            foreach ($results as $count => $item) {
                $option_value = $item->text;
            }
            return $option_value;
        }
        return false;
    }

    /**
     * getDefaultView
     *
     * Retrieve Default View
     *
     * @param int $option_id
     * @return string
     */
    public function getDefaultView ($option_id)
    {
        /** check for overrides **/
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);

        $component_option = $this->getOverrideKey ($option_id);
        if ($component_option === false) {
            $component_option = 'core';
        }

        $ordering = $this->getDefaultViewOrdering ($option_id);
        if ($ordering === false) {
            return false;
        }

        /** for lowest ordering, retrieve default view **/
        $query = $db->getQuery(true);

        $query->select($db->namequote('option_value_literal').' as default_view');
        $query->from($db->namequote('#__configuration'));
        $query->where($db->namequote('option_id').' = '.(int) $option_id);
        $query->where($db->namequote('ordering').' = '. $ordering);
        $query->where($db->namequote('component_option').' = '. $db->quote(trim($component_option)));

        $db->setQuery($query->__toString());

        if (!$results = $db->loadObjectList()) {
        } else {
            foreach ($results as $count => $item) {
                $value = $item->default_view;
            }
            return $value;
        }
        return false;
    }

    /**
     * getDefaultViewOrdering
     *
     * Retrieve Default View
     *
     * @param int $option_id
     * @return string
     */
    public function getDefaultViewOrdering ($option_id)
    {
        /** check for overrides **/
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);

        $component_option = $this->getOverrideKey ($option_id);
        if ($component_option === false) {
            $component_option = 'core';
        }

        /** retrieve lowest ordering value for default view **/
        $query = $db->getQuery(true);

        $query->select('MIN(ordering) as ordering');
        $query->from($db->namequote('#__configuration'));
        $query->where($db->namequote('option_id').' = '.(int) $option_id);
        $query->where($db->namequote('component_option').' = '. $db->quote(trim($component_option)));
        $query->where($db->namequote('ordering').' > 0 ');

        $db->setQuery($query->__toString());

        if (!$results = $db->loadObjectList()) {
            return false;
        } else {
            foreach ($results as $count => $item) {
                $ordering = $item->ordering;
            }
            return $ordering;
        }
    }

    /**
     * getViewType
     *
     * Retrieve Default View
     *
     * @param int $option_id
     * @return string
     */
    public function getViewType ($option_id, $value)
    {
         /** check for overrides **/
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);

        $component_option = $this->getOverrideKey ($option_id);
        if ($component_option === false) {
            $component_option = 'core';
        }

        $query = $db->getQuery(true);

        $query->select($db->namequote('option_value').' as single_view');
        $query->select($db->namequote('option_value_literal').' as default_view');
        $query->from($db->namequote('#__configuration'));
        $query->where($db->namequote('option_id').' = '.(int) $option_id);
        $query->where('('.$db->namequote('option_value').' = '. $db->quote(trim($value)).' or '.$db->namequote('option_value_literal').' = '. $db->quote(trim($value)).')');
        $query->where($db->namequote('component_option').' = '. $db->quote(trim($component_option)));

        $db->setQuery($query->__toString());

        if (!$results = $db->loadObjectList()) {
        } else {
            foreach ($results as $count => $item) {
                if ($value == $item->default_view) {
                    $return = 'default';
                } else {
                    $return = 'single';
                }
            }
            return $return;
        }
        return false;
    }


    /**
     * getViewType
     *
     * Retrieve Default View
     *
     * @param  $option_id
     * @param  $value
     * @param  $type
     * @return bool|string
     */
    public function getViewMatch ($option_id, $value, $type)
    {
        /** check for overrides **/
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);

        $component_option = $this->getOverrideKey ($option_id);
        if ($component_option === false) {
            $component_option = 'core';
        }

        $query = $db->getQuery(true);

        $query->select($db->namequote('option_value').' as single_view');
        $query->select($db->namequote('option_value_literal').' as default_view');
        $query->from($db->namequote('#__configuration'));
        $query->where($db->namequote('option_id').' = '.(int) $option_id);
        $query->where('('.$db->namequote('option_value').' = '. $db->quote(trim($value)).' or '.$db->namequote('option_value_literal').' = '. $db->quote(trim($value)).')');
        $query->where($db->namequote('component_option').' = '. $db->quote(trim($component_option)));

        $db->setQuery($query->__toString());

        if (!$results = $db->loadObjectList()) {
        } else {
            foreach ($results as $count => $item) {
                if ($type == 'default') {
                    $value = $item->single_view;
                } else {
                    $value = $item->default_view;
                }
            }
 
            return $value;
        }
        return false;
    }

    /**
     * getOverrideKey
     *
     * Retrieve component_option key for option_id
     *
     * @param string $option
     * @param int $option_id
     * @return string
     */
    public function getOverrideKey ($option_id)
    {
        $configurationArray = JRequest::getVar('configurationArray', array());

        foreach ($configurationArray
                    as $confOption_id => $confComponent_option) {

            if ($confOption_id == $option_id) {
                return $confComponent_option;
            }
        }

        return false;
    }

    /**
     * getMolajoComponentList
     *
     * Retrieve Single Configuration Value for this Component and Option
     *
     * @param string $option
     * @param int $option_id
     * @return string
     */
    public function getMolajoComponentList ()
    {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);

        /** validation query **/
        $query = $db->getQuery(true);
        $option_id = 0;

        $query->select('DISTINCT '.$db->namequote('option_value').' AS value, '.$db->namequote('option_value_literal').' as text');
        $query->from($db->namequote('#__configuration'));
        $query->where($db->namequote('option_id').' = '.(int) $option_id);
        $query->where($db->namequote('ordering').' > 0 ');
        $query->order($db->namequote('ordering'));

        $db->setQuery($query->__toString());

        if (!$results = $db->loadObjectList()) {
            JFactory::getApplication()->enqueueMessage($db->getErrorMsg(), 'error');
            return false;
        }
        return $results;
    }

    /**
     * getOptionOverrides
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
    public function getOptionOverrides ($component_option)
    {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);

        /** retrieve all option_id values **/
        $query->select('DISTINCT '.$db->namequote('option_id'));
        $query->from($db->namequote('#__configuration'));
        $query->where($db->namequote('ordering').' = 0');
        $query->where($db->namequote('option_id').' > 0');

        $db->setQuery($query->__toString());

        if (!$results = $db->loadObjectList()) {
            JFactory::getApplication()->enqueueMessage($db->getErrorMsg(), 'error');
            return false;
        }

        $optionArray = array();
        if (count($results) > 0) {
            foreach ($results as $count => $item) {

                /** retrieve override component_option, if existing **/
                $query = $db->getQuery(true);
                $query->select($db->namequote('component_option'));
                $query->from($db->namequote('#__configuration'));
                $query->where($db->namequote('ordering').' = 0');
                $query->where($db->namequote('option_id').' = '. (int) $item->option_id);
                $query->where($db->namequote('component_option').' = '. $db->quote($component_option));

                $db->setQuery($query->__toString());

                if ($componentResults = $db->loadObjectList()) {
                    $optionArray[$item->option_id] = $component_option;
                } else {
                    $optionArray[$item->option_id] = 'core';
                }
            }
        }

        JRequest::setVar('configurationArray', $optionArray);

        return;
    }
}