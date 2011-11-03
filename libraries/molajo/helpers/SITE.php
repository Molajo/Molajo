<?php
/**
 * @package     Molajo
 * @subpackage  Site Helper
 * @copyright   Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @copyright   Copyright (C) 2011 Amy Stephen. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */
defined('MOLAJO') or die;

/**
 * MolajoSiteHelper
 *
 * @package     Molajo
 * @subpackage  Site Helper
 * @since       1.0
 */
class MolajoSiteHelper
{
	/**
     * @var null $_sites
     * 
     * @since 1.0
     */
	protected static $_sites = null;

	/**
	 * getSiteInfo
     *
     * Retrieves Site info from database
	 *
	 * This method will return a site information array if called
	 * with no arguments which can be used to add custom site information.
	 *
	 * @param   integer  $id		A site identifier, can be ID or Name
	 * @param   boolean  $byName	If True, find the site by its name
	 *
	 * @return  boolean  True if the information is added. False on error
	 * @since   1.0
	 */
	public static function getSiteInfo ($id = null, $byName = false)
	{
		if (self::$_sites === null) {
            
            $obj = new stdClass();

            $db = MolajoFactory::getDbo();
            $query = $db->getQuery(true);

            $query->select('id');
            $query->select('name');
            $query->select('description');            
            $query->select('path');
            $query->select('parameters');
            $query->select('custom_fields');
            $query->select('base_url');            
            $query->from($db->namequote('#__sites'));

            $db->setQuery($query->__toString());

            if ($results = $db->loadObjectList()) {
            } else {
                MolajoFactory::getSite()->enqueueMessage($db->getErrorMsg(), 'error');
                return false;
            }

            if ($db->getErrorNum()) {
                return new MolajoException($db->getErrorMsg());
            }

            foreach ($results as $result) {
                
                $obj->id	        = $result->id;
                $obj->name	        = $result->name;
                $obj->description	= $result->description;                
                $obj->path	        = $result->path;
                $obj->parameters	= $result->parameters;
                $obj->custom_fields = $result->custom_fields;
                $obj->base_url	    = $result->base_url;
                $obj->description	= $result->description;
                  
                self::$_sites[$result->id] = clone $obj;
            }
        }

		/** All sites requested */
		if (is_null($id)) {
			return self::$_sites;
		}

		/** Name lookup */
		if ($byName) {
			foreach (self::$_sites as $site) {
				if ($site->name == strtolower($id)) {
					return $site;
				}
			}

		} else {
			if (isset(self::$_sites[$id])){
				return self::$_sites[$id];
			}
		}

        /** Name and or ID lookup unsuccessful */
		return null;
	}

	/**
	 * getSiteApplications
     *
     * Retrieves Applications for which the site is authorized to see
	 *
	 * @param   integer  $id		A site id
	 *
	 * @return  array
	 * @since   1.0
	 */
	public static function getSiteApplications ($id = null)
	{
        $db = MolajoFactory::getDbo();
        $query = $db->getQuery(true);

        if ($id == null) {
            $id = MOLAJO_SITE_ID;
        }

        $query->select('application_id');
        $query->from($db->namequote('#__site_applications'));
        $query->where($db->namequote('site_id').' = '.(int) $id);

        $db->setQuery($query->__toString());

        if ($results = $db->loadObjectList()) {
        } else {
            MolajoFactory::getSite()->enqueueMessage($db->getErrorMsg(), 'error');
            return false;
        }

        if ($db->getErrorNum()) {
            return new MolajoException($db->getErrorMsg());
        }

        return $results;
	}

	/**
     * loadSiteClasses
     *
     * @param string $site_name
     *
     * @return bool
     *
	 * @since   1.0
     */
	public static function loadSiteClasses()
	{
        $filehelper = new MolajoFileHelper();
        $files = JFolder::files(MOLAJO_SITE_PATH.'/classes', '\.php$', false, false);
        foreach ($files as $file) {
            $filehelper->requireClassFile(MOLAJO_SITE_PATH.'/classes/'.$file, 'Molajo'.ucfirst(MOLAJO_SITE).ucfirst(substr($file, 0, strpos($file, '.'))));
        }
	}
}