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
class MolajoModelConfiguration
{
    /**
     * @var string $option
     *
     * @since 1.0
     */
    protected $option;

    /**
     * @var int $extension_instance_id
     *
     * @since 1.0
     */
    protected $extension_instance_id;

    /**
     * @var array $overrides
     *
     * @since 1.0
     */
    protected $overrides = array();

    /**
     * __construct
     *
     * Constructor.
     *
     * @param $option
     * @since    1.0
     */
    public function __construct($option)
    {
        $this->option = $option;
        $this->retrieveExtensionInstanceID();
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
    public function getOptionValue($option_id)
    {
        $db = MolajoController::getDbo();
        $query = $db->getQuery(true);

        /** option or core **/
        $key = $this->getComponentOptionKey($option_id);

        /** validation query **/
        $query->select($db->namequote('option_value'), $db->namequote('option_value_literal'));
        $query->from($db->namequote('#__extension_options'));
        $query->where($db->namequote('option_id') . ' = ' . (int)$option_id);
        $query->where($db->namequote('extension_instance_id') . ' = ' . (int)$key);
        $query->where($db->namequote('ordering') . ' > 0 ');

        $db->setQuery($query->__toString());

        if ($results = $db->loadObjectList()) {
        } else {
            MolajoController::getApplication()->setMessage($db->getErrorMsg(), 'error');
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
     * @param $option_id
     * @param $option_value
     * @return string or boolean option_value
     * @since 1.0
     */
    public function getOptionLiteralValue($option_id, $option_value)
    {

        $db = MolajoController::getDbo();
        $query = $db->getQuery(true);

        /** option or core **/
        $key = $this->getComponentOptionKey($option_id);

        /** retrieve value **/
        $query = $db->getQuery(true);

        $query->select($db->namequote('option_value_literal'));
        $query->from($db->namequote('#__extension_options'));
        $query->where($db->namequote('option_id') . ' = ' . (int)$option_id);
        $query->where($db->namequote('extension_instance_id') . ' = ' . (int)$key);
        $query->where($db->namequote('option_value') . ' = ' . $db->quote(trim($option_value)));
        $query->where($db->namequote('ordering') . ' > 0 ');

        $db->setQuery($query->__toString());

        if ($results = $db->loadObjectList()) {
        } else {
            MolajoController::getApplication()->setMessage($db->getErrorMsg(), 'error');
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
    public function getOptionList($option_id)
    {
        /** check for overrides **/
        $db = MolajoController::getDbo();
        $query = $db->getQuery(true);

        /** option or core **/
        $component_option = $this->getComponentOptionKey($option_id);

        /** validation query **/
        $query = $db->getQuery(true);

        $query->select('DISTINCT ' . $db->namequote('option_value') . ' AS value');
        $query->select($db->namequote('option_value_literal') . ' as text');
        $query->from($db->namequote('#__extension_options'));
        $query->where($db->namequote('option_id') . ' = ' . (int)$option_id);
        $query->where($db->namequote('extension_instance_id') . ' = ' . (int)$this->extension_instance_id);
        $query->where($db->namequote('ordering') . ' > 0 ');
        $query->order($db->namequote('ordering'));

        $db->setQuery($query->__toString());

        if ($results = $db->loadObjectList()) {
        } else {
            MolajoController::getApplication()->setMessage($db->getErrorMsg(), 'error');
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
    private function getComponentOptionKey($option_id)
    {
        foreach ($this->overrides as $configurationOverrides => $override) {
            if ($configurationOverrides == $option_id) {
                if ($override == $this->extension_instance_id) {
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
     */
    private function setOverridesArray()
    {
        $db = MolajoController::getDbo();
        $query = $db->getQuery(true);

        /** retrieve all option_id values **/
        $query->select('DISTINCT ' . $db->namequote('option_id'));
        $query->from($db->namequote('#__extension_options'));
        $query->where($db->namequote('ordering') . ' = 0');
        $query->where($db->namequote('option_id') . ' > 0');
        $query->order($db->namequote('option_id'));

        $db->setQuery($query->__toString());

        if ($results = $db->loadObjectList()) {
        } else {
            MolajoController::getApplication()->setMessage($db->getErrorMsg(), 'error');
            return false;
        }

        /** for each option_id, determine if there are overrides for the extension */
        $optionArray = array();
        if (count($results) > 0) {

            foreach ($results as $count => $item) {

                /** retrieve override component_option, if existing **/
                $query = $db->getQuery(true);

                $query->select($db->namequote('extension_instance_id'));
                $query->from($db->namequote('#__extension_options'));
                $query->where($db->namequote('ordering') . ' = 0');
                $query->where($db->namequote('option_id') . ' = ' . (int)$item->option_id);
                $query->where($db->namequote('extension_instance_id') . ' = ' . (int)$this->extension_instance_id);

                $db->setQuery($query->__toString());

                if ($extensionResults = $db->loadResult()) {
                    $optionArray[$item->option_id] = $extensionResults;
                } else {
                    /** core is 1 */
                    $optionArray[$item->option_id] = 1;
                }
            }
        }

        $this->overrides = $optionArray;

        return;
    }

    /**
     * retrieveExtensionInstaneID
     *
     * Retrieve the primary key for the extension instance for the $option
     *
     * @return null
     */
    private function retrieveExtensionInstanceID()
    {
        $db = MolajoController::getDbo();
        $query = $db->getQuery(true);

        $query->select('DISTINCT a.' . $db->namequote('id') . ' AS extension_instance_id ');
        $query->from($db->namequote('#__extension_instances') . ' as a');
        $query->from($db->namequote('#__extensions') . ' as b');
        $query->where('a.' . $db->namequote('extension_id') . ' = b.' . $db->namequote('id'));
        $query->where($db->namequote('name') . ' = ' . $db->quote($this->option));

        $db->setQuery($query->__toString());

        if ($this->extension_instance_id = $db->loadResult()) {
        } else {
            MolajoController::getApplication()->setMessage($db->getErrorMsg(), 'error');
            return false;
        }

        return;
    }
}