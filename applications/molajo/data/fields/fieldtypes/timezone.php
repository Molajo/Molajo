<?php
/**
 * @package     Molajo
 * @subpackage  Form
 * @copyright   Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @copyright   Copyright (C) 2012 Cristina Solano. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die;

/**
 * Form Field class for the Joomla Framework.
 *
 * @package    Molajo
 * @subpackage  Form
 * @since       1.0
 */
class MolajoFormFieldTimezone extends MolajoFormFieldGroupedList
{
    /**
     * The form field type.
     *
     * @var    string
     * @since  1.0
     */
    protected $type = 'Timezone';

    /**
     * The list of available timezone groups to use.
     *
     * @var    array
     * @since  1.0
     */
    protected static $zones = array(
        'Africa', 'America', 'Antarctica', 'Arctic', 'Asia',
        'Atlantic', 'Australia', 'Europe', 'Indian', 'Pacific'
    );

    /**
     * Method to get the field option groups.
     *
     * @return  array  The field option objects as a nested array in groups.
     * @since   1.0
     */
    protected function getGroups()
    {
        // Initialize variables.
        $groups = array();

        // If the timezone is not set use the server setting.
        if (strlen($this->value) == 0) {
            $value = MolajoFactory::getConfig()->get('offset');
        }

        // Get the list of time zones from the server.
        $zones = DateTimeZone::listIdentifiers();

        // Build the group lists.
        foreach ($zones as $zone) {

            // Time zones not in a group we will ignore.
            if (strpos($zone, '/') === false) {
                continue;
            }

            // Get the group/locale from the timezone.
            list ($group, $locale) = explode('/', $zone, 2);

            // Only use known groups.
            if (in_array($group, self::$zones)) {

                // Initialize the group if necessary.
                if (!isset($groups[$group])) {
                    $groups[$group] = array();
                }

                // Only add options where a locale exists.
                if (!empty($locale)) {
                    $groups[$group][$zone] = MolajoHTML::_('select.option',
                                                           $zone,
                                                           str_replace('_', ' ', $locale), 'value', 'text', false);
                }
            }
        }

        // Sort the group lists.
        ksort($groups);
        foreach ($groups as $zone => & $location) {
            sort($location);
        }

        // Merge any additional groups in the XML definition.
        $groups = array_merge(parent::getGroups(), $groups);

        return $groups;
    }
}