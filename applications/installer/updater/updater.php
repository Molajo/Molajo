<?php
/**
 * @package     Molajo
 * @subpackage  Application
 * @copyright   Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @copyright   Copyright (C) 2012 Individual Molajo Contributors. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die;

/**
 * Updater Class
 *
 * @package     Joomla.Platform
 * @subpackage  Updater
 * @since       11.1
 */
class MolajoUpdater extends MolajoAdapter
{
    /**
     * Constructor
     *
     * @return  MolajoUpdater
     *
     * @since   1.0
     */
    public function __construct()
    {
        // Adapter base path, class prefix
        parent::__construct(dirname(__FILE__), 'MolajoUpdater');
    }

    /**
     * Returns a reference to the global Installer object, only creating it
     * if it doesn't already exist.
     *
     * @return  object  An installer object
     *
     * @since   1.0
     */
    public static function &getInstance()
    {
        static $instance;

        if (!isset($instance)) {
            $instance = new MolajoUpdater;
        }
        return $instance;
    }

    /**
     * Finds an update for an extension
     *
     * @param   integer  $eid  Extension Identifier; if zero use all sites
     *
     * @return  boolean True if there are updates
     *
     * @since   1.0
     */
    public function findUpdates($eid = 0)
    {
        // Check if fopen is allowed
        $result = ini_get('allow_url_fopen');
        if (empty($result)) {
            MolajoError::raiseWarning('101', MolajoTextHelper::_('JLIB_UPDATER_ERROR_COLLECTION_FOPEN'));
            return false;
        }

        $dbo = $this->getDbo();
        $retval = false;
        // Push it into an array
        if (!is_array($eid)) {
            $query = 'SELECT DISTINCT extension_site_id, type, location FROM #__extension_sites WHERE enabled = 1';
        }
        else
        {
            $query = 'SELECT DISTINCT extension_site_id, type, location FROM #__extension_sites' .
                     ' WHERE extension_site_id IN' .
                     '  (SELECT extension_site_id FROM #__extension_sites_extensions WHERE extension_id IN (' . implode(',', $eid) . '))';
        }
        $dbo->setQuery($query);
        $results = $dbo->loadAssocList();
        $result_count = count($results);
        for ($i = 0; $i < $result_count; $i++)
        {
            $result = &$results[$i];
            $this->setAdapter($result['type']);
            if (!isset($this->_adapters[$result['type']])) {
                // Ignore update sites requiring adapters we don't have installed
                continue;
            }
            $update_result = $this->_adapters[$result['type']]->findUpdate($result);
            if (is_array($update_result)) {
                if (array_key_exists('extension_sites', $update_result) && count($update_result['extension_sites'])) {
                    $results = JArrayHelper::arrayUnique(array_merge($results, $update_result['extension_sites']));
                    $result_count = count($results);
                }
                if (array_key_exists('updates', $update_result) && count($update_result['updates'])) {
                    for ($k = 0, $count = count($update_result['updates']); $k < $count; $k++)
                    {
                        $current_update = &$update_result['updates'][$k];
                        $update = MolajoTable::getInstance('update');
                        $extension = MolajoTable::getInstance('extension');
                        $uid = $update
                                ->find(
                            array(
                                 'element' => strtolower($current_update->get('element')), 'type' => strtolower($current_update->get('type')),
                                 'application_id' => strtolower($current_update->get('application_id')),
                                 'folder' => strtolower($current_update->get('folder'))
                            )
                        );

                        $eid = $extension
                                ->find(
                            array(
                                 'element' => strtolower($current_update->get('element')), 'type' => strtolower($current_update->get('type')),
                                 'application_id' => strtolower($current_update->get('application_id')),
                                 'folder' => strtolower($current_update->get('folder'))
                            )
                        );
                        if (!$uid) {
                            // Set the extension id
                            if ($eid) {
                                // We have an installed extension, check the update is actually newer
                                $extension->load($eid);
                                $data = json_decode($extension->manifest_cache, true);
                                if (version_compare($current_update->version, $data['version'], '>') == 1) {
                                    $current_update->extension_id = $eid;
                                    $current_update->store();
                                }
                            }
                            else
                            {
                                // A potentially new extension to be installed
                                $current_update->store();
                            }
                        }
                        else
                        {
                            $update->load($uid);
                            // if there is an update, check that the version is newer then replaces
                            if (version_compare($current_update->version, $update->version, '>') == 1) {
                                $current_update->store();
                            }
                        }
                    }
                }
                $update_result = true;
            }
            elseif ($retval)
            {
                $update_result = true;
            }
        }
        return $retval;
    }

    /**
     * Multidimensional array safe unique test
     *
     * @param   array  $myArray  The source array.
     *
     * @return  array
     *
     * @deprecated    12.1
     * @note    Use JArrayHelper::arrayUnique() instead.
     * @note    Borrowed from PHP.net
     * @see     http://au2.php.net/manual/en/function.array-unique.php
     * @since   1.0
     *
     */
    public function arrayUnique($myArray)
    {
        JLog::add('MolajoUpdater::arrayUnique() is deprecated. See JArrayHelper::arrayUnique().', JLog::WARNING, 'deprecated');
        return JArrayHelper::arrayUnique($myArray);
    }

    /**
     * Finds an update for an extension
     *
     * @param   integer  $id  Id of the extension
     *
     * @return  mixed
     *
     * @since   1.0
     */
    public function update($id)
    {
        $updaterow = MolajoTable::getInstance('update');
        $updaterow->load($id);
        $update = new MolajoUpdate;
        if ($update->loadFromXML($updaterow->details_url)) {
            return $update->install();
        }
        return false;
    }
}
