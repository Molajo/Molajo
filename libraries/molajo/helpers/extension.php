<?php
/**
 * @package     Molajo
 * @subpackage  Extension Helper
 * @copyright   Copyright (C) 2011 Amy Stephen. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */
defined('MOLAJO') or die;

/**
 * Extension Helper
 *
 * @package     Molajo
 * @subpackage  Extension Helper
 * @since       1.0
 */
abstract class MolajoExtensionHelper
{
	/**
	 * _load
     *
     * Loads the published plugins.
	 *
     * @static
     * @return bool|mixed
     */
	public function getExtensions($extension_type_id, $specificExtension = null)
	{
		$db		= MolajoFactory::getDbo();
		$query	= $db->getQuery(true);
        $date   = MolajoFactory::getDate();
        $now    = $date->toMySQL();
        $nullDate = $db->getNullDate();

		$query->select('a.'.$db->namequote('id'));
		$query->select('a.'.$db->namequote('parameters'));
        $query->select('b.'.$db->namequote('name'));
        $query->select('b.'.$db->namequote('folder'). ' as type');
//		$query->select('a.'.$db->namequote('enabled'));
        $query->select(' 1 as enabled');
		$query->select('a.'.$db->namequote('asset_id'));

		$query->from($db->namequote('#__extension_instances').' as a');
		$query->from($db->namequote('#__extensions').' as b');
		$query->from($db->namequote('#__application_extensions').' as c');

        $query->where('a.'.$db->namequote('extension_type_id').' = '.(int) $extension_type_id);

        $query->where('a.'.$db->namequote('status').' = '.MOLAJO_STATE_PUBLISHED);
        $query->where('(a.start_publishing_datetime = '.$db->Quote($nullDate).' OR a.start_publishing_datetime <= '.$db->Quote($now).')');
        $query->where('(a.stop_publishing_datetime = '.$db->Quote($nullDate).' OR a.stop_publishing_datetime >= '.$db->Quote($now).')');

		$query->where('b.'.$db->namequote('id').' = a.'.$db->namequote('extension_id'));

//		$query->where('c.'.$db->namequote('application_id').' = '.MOLAJO_APPLICATION_ID);
		$query->where('c.'.$db->namequote('extension_id').' = b.'.$db->namequote('id'));
		$query->where('c.'.$db->namequote('extension_instance_id').' = a.'.$db->namequote('id'));

        if ($specificExtension == null) {
        } else {
            $query->where('b.'.$db->namequote('name').' = '.$db->quote($specificExtension));
        }

		$query->where('b.'.$db->namequote('name').' != "sef"');
		$query->where('b.'.$db->namequote('name').' != "joomla"');
		$query->where('b.'.$db->namequote('name').' != "example"');
		$query->where('b.'.$db->namequote('name').' != "system"');
		$query->where('b.'.$db->namequote('name').' != "webservices"');
		$query->where('b.'.$db->namequote('name').' != "broadcast"');
		$query->where('b.'.$db->namequote('name').' != "content"');
		$query->where('b.'.$db->namequote('name').' != "links"');
		$query->where('b.'.$db->namequote('name').' != "media"');
		$query->where('b.'.$db->namequote('name').' != "protect"');
		$query->where('b.'.$db->namequote('name').' != "responses"');
		$query->where('b.'.$db->namequote('name').' != "broadcast"');

        $acl = new MolajoACL ();
        $acl->getQueryInformation ('', $query, 'viewaccess', array('table_prefix'=>'a'));

        $db->setQuery($query->__toString());
        $extensions = $db->loadObjectList();

        if ($error = $db->getErrorMsg()) {
            MolajoError::raiseWarning(500, $error);
            return false;
        }
 
		return $extensions;
	}
}