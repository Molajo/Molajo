<?php
/**
 * @package     Molajo
 * @subpackage  Login Controller
 * @copyright   Copyright (C) 2011 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die;

/**
 * Login Controller
 *
 * Handles the standard single-item save, delete, and cancel tasks
 *
 * Cancel: cancel and close
 * Save: apply, create, save, save2copy, save2new, restore
 * Delete: delete
 *
 * Called from the Multiple Controller for batch (copy, move) and delete
 *
 * @package	Molajo
 * @subpackage	Controller
 * @since	1.0
 */
class MolajoControllerLogin extends MolajoController
{

	/**
	 * Method to log in a user.
	 *
	 * @return	void
	 */
	public function login()
	{
        /** security token **/
        JRequest::checkToken() or die;

        /** initialisation */
        parent::initialise('login');

        /** @var $app */
		$app = MolajoFactory::getApplication();

		$model = $this->getModel('login');
		$credentials = $model->getState('credentials');
		$return = $model->getState('return');

		$result = $app->login($credentials, array('action' => 'core.login.admin'));
        /** success message **/
        $this->redirectClass->setRedirectMessage(JText::_('MOLAJO_LOGIN_SUCCESSFUL'));
        $this->redirectClass->setSuccessIndicator(true);

		if (!JError::isError($result)) {
			$app->redirect($return);
		}

		parent::display();
	}

	/**
	 * Method to log out a user.
	 *
	 * @return	void
	 */
	public function logout()
	{
		JRequest::checkToken('default') or jexit(JText::_('JInvalid_Token'));

		$app = MolajoFactory::getApplication();

		$userid = JRequest::getInt('uid', null);

		$options = array(
			'applicationid' => ($userid) ? 0 : 1
		);

		$result = $app->logout($userid, $options);

		if (!JError::isError($result)) {
			$model 	= $this->getModel('login');
			$return = $model->getState('return');
			$app->redirect($return);
		}

		parent::display();
	}
}