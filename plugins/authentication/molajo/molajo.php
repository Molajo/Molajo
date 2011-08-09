<?php
/**
 * @package     Molajo
 * @subpackage  Authentication
 * @copyright   Copyright (C) 2011 Molajo. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die;

/**
 * Authentication
 *
 * @package		Molajo
 * @subpackage	Authentication
 * @since       1.0
 */
class plgAuthenticationMolajo extends JPlugin
{
	/**
	 * This method should handle any authentication and report back to the subject
	 *
	 * @access	public
	 * @param	array	Array holding the user credentials
	 * @param	array	Array of extra options
	 * @param	object	Authentication response object
	 * @return	boolean
	 * @since   1.0
	 */
	function onUserAuthenticate($credentials, $options, &$response)
	{
		$response->type = 'Joomla';

		// Joomla does not like blank passwords
		if (empty($credentials['password'])) {
			$response->status = JAUTHENTICATE_STATUS_FAILURE;
			$response->error_message = JText::_('JGLOBAL_AUTH_EMPTY_PASS_NOT_ALLOWED');
			return false;
		}

		// Initialise variables.
		$conditions = '';

		// Get a database object
		$db		= MolajoFactory::getDbo();
		$query	= $db->getQuery(true);

		$query->select('id, password');
		$query->from('#__users');
		$query->where('username=' . $db->Quote($credentials['username']));

		$db->setQuery($query);
		$result = $db->loadObject();

		if ($result) {
		} else {
			$response->status = JAUTHENTICATE_STATUS_FAILURE;
			$response->error_message = JText::_('JGLOBAL_AUTH_NO_USER');
            return;
        }

        $parts	= explode(':', $result->password);
        $crypt	= $parts[0];
        $salt	= @$parts[1];
        $testcrypt = MolajoUserHelper::getCryptedPassword($credentials['password'], $salt);

        if ($crypt == $testcrypt) {
        } else {
            $response->status = JAUTHENTICATE_STATUS_FAILURE;
            $response->error_message = JText::_('JGLOBAL_AUTH_INVALID_PASS');
            return;
        }

        $user = MolajoUser::getInstance($result->id);
        $response->email = $user->email;
        $response->fullname = $user->name;
        if (MolajoFactory::getApplication()->isAdmin()) {
            $response->language = $user->getParam('admin_language');
        }
        else {
            $response->language = $user->getParam('language');
        }
        $response->status = JAUTHENTICATE_STATUS_SUCCESS;
        $response->error_message = '';
	}
}
