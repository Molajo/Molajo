<?php
/**
 * @package   Molajo
 * @copyright 2012 Amy Stephen. All rights reserved.
 * @license   GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
namespace Molajo\Service;

use Molajo\Services;

use Molajo\MVC\Model\ItemModel;

defined('MOLAJO') or die;

/**
 * User
 *
 * @package     Molajo
 * @subpackage  Service
 * @since       1.0
 */
Class UserService
{
	/**
	 * Instance
	 *
	 * @var    object
	 * @since  1.0
	 */
	protected static $instances = array();

	/**
	 * getInstance
	 *
	 * @param   string $identifier  Requested User (id or username or 0 for guest)
	 *
	 * @return  object  User
	 * @since   1.0
	 */
	public static function getInstance($id = 0)
	{
		$id = 42;
		if (empty(self::$instances[$id])) {
			$user = new UserService($id);
			self::$instances[$id] = $user;
		}
		return self::$instances[$id];
	}

	/**
	 * __construct
	 *
	 * @param   integer  $identifier
	 *
	 * @return  object
	 * @since   1.0
	 */
	protected function __construct($id = 0)
	{
		$this->id = (int)$id;
		return $this->load();
	}

	/**
	 * load
	 *
	 * Retrieve User Information (both authenticated and guest)
	 *
	 * @return  User
	 * @since   1.0
	 */
	protected function load()
	{

/**
 get the editor and language
 and session?
 		$user	 =& JFactory::getUser();
		$editor	 = $user->getParam('editor', $this->getCfg('editor'));
		$editor = JTriggerHelper::isEnabled('editors', $editor) ? $editor : $this->getCfg('editor');
		$config->setValue('config.editor', $editor);
getUserState , setUserState  , getUserStateFromRequest
login and logout
registration

*/
		$m = new ItemModel ('Users', $this->id);
		$results = $m->load();

		$first_name = '';
		$last_name = '';

		while (list($name, $value) = each($results)) {

			if ($name == 'parameters'
				|| $name == 'custom_fields'
				|| $name == 'metadata'
				|| substr($name, 0, 5) == 'Model'
			) {
			} else {
				Service::Registry()->set('User', '' . $name, $value);
				if ($name == 'first_name') {
					$first_name = $value;
				} elseif ($name == 'last_name') {
					$last_name = $value;
				}
			}
		}

		Service::Registry()->set('User', 'name', $first_name . ' ' . $last_name);
		Service::Registry()->set('User', 'administrator', 0);

		if ($this->id == 0) {
			Service::Registry()->set('User', 'public', 1);
			Service::Registry()->set('User', 'guest', 1);
			Service::Registry()->set('User', 'registered', 0);
		} else {
			Service::Registry()->set('User', 'public', 1);
			Service::Registry()->set('User', 'guest', 0);
			Service::Registry()->set('User', 'registered', 1);
		}

		$xml = simplexml_load_file(CONFIGURATION_FOLDER . '/Table/Users.xml');

		Service::Registry()->loadField('UserCustomfields', 'custom_fields', $results['custom_fields'], $xml->custom_fields);
		Service::Registry()->loadField('UserMetadata', 'meta', $results['metadata'], $xml->metadata);
		Service::Registry()->loadField('UserParameters', 'parameters', $results['parameters'], $xml->parameter);

		/** User Applications */
		$temp = array();
		$applications = $results['Model\\UserApplications'];

		while (list($name, $value) = each($applications)) {
			if ($name == 'application_id') {
				$temp[] = $value;
			}
		}

		Service::Registry()->set('User', 'Applications', $temp);

		/** User Groups */
		$temp = array();
		$groups = $results['Model\\UserGroups'];
		while (list($name, $value) = each($groups)) {
			if ($name == 'group_id') {
				$temp[] = $value;
			}
		}

		if (in_array(SYSTEM_GROUP_PUBLIC, $temp)) {
		} else {
			$temp[] = SYSTEM_GROUP_PUBLIC;
		}

		if ($this->id == 0) {
			$temp[] = SYSTEM_GROUP_GUEST;
		} else {
			if (in_array(SYSTEM_GROUP_REGISTERED, $temp)) {
			} else {
				$temp[] = SYSTEM_GROUP_REGISTERED;
			}
		}

		Service::Registry()->set('User', 'Groups', $temp);

		if (in_array(SYSTEM_GROUP_ADMINISTRATOR, $temp)) {
			Service::Registry()->set('User', 'administrator', 1);
		} else {
			Service::Registry()->set('User', 'administrator', 0);
		}

		/** User View Groups */
		$temp = array();
		$view_groups = $results['Model\\UserViewGroups'];
		while (list($name, $value) = each($view_groups)) {
			if ($name == 'view_group_id') {
				$temp[] = $value;
			}
		}

		if (count($temp) == 0) {
			$temp = array(SYSTEM_GROUP_PUBLIC, SYSTEM_GROUP_GUEST);
		}
		Service::Registry()->set('User', 'ViewGroups', $temp);

		/**      */
		$list = Service::Registry()->listRegistry(1);

		return $this;
	}
}
