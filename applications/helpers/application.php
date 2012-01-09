<?php
/**
 * @package     Molajo
 * @subpackage  Helper
 * @copyright   Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @copyright   Copyright (C) 2012 Amy Stephen. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */
defined('MOLAJO') or die;

/**
 * MolajoApplicationHelper
 *
 * @package     Molajo
 * @subpackage  Application Helper
 * @since       1.0
 */
class MolajoApplicationHelper
{
    /**
     * @var null $_applications
     *
     * @since 1.0
     */
    protected static $_applications = null;

    /**
     * getApplicationInfo
     *
     * Retrieves Application info from database
     *
     * This method will return a application information array if called
     * with no arguments which can be used to add custom application information.
     *
     * @param   integer  $id        A application identifier, can be ID or Name
     * @param   boolean  $byName    If True, find the application by its name
     *
     * @return  boolean  True if the information is added. False on error
     * @since   1.0
     */
    public static function getApplicationInfo($id = null, $byName = false)
    {
        if (self::$_applications === null) {

            $obj = new stdClass();

            if ($id == 'installation') {
                $obj->id = 0;
                $obj->name = 'installation';
                $obj->path = 'installation';

                self::$_applications[0] = clone $obj;

            } else {

                $db = MolajoController::getDbo();

                $query = $db->getQuery(true);

                $query->select('id');
                $query->select('name');
                $query->select('path');
                $query->from($db->namequote('#__applications'));

                $db->setQuery($query->__toString());

                if ($results = $db->loadObjectList()) {
                } else {
                    MolajoController::getApplication()->setMessage($db->getErrorMsg(), 'error');
                    return false;
                }

                if ($db->getErrorNum()) {
                    return new MolajoException($db->getErrorMsg());
                }

                foreach ($results as $result) {
                    $obj->id = $result->id;
                    $obj->name = $result->name;
                    $obj->path = $result->path;

                    self::$_applications[$result->id] = clone $obj;
                }
            }
        }

        /** All applications requested */
        if (is_null($id)) {
            return self::$_applications;
        }

        /** Name lookup */
        if ($byName) {
            foreach (self::$_applications as $application) {
                if ($application->name == strtolower($id)) {
                    return $application;
                }
            }

        } else {
            if (isset(self::$_applications[$id])) {
                return self::$_applications[$id];
            }
        }

        /** Name and or ID lookup unsuccessful */
        return null;
    }
}