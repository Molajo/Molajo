<?php
/**
 * @version		$Id: application.php 21518 2011-06-10 21:38:12Z chdemko $
 * @package		Joomla.Administrator
 * @subpackage	com_config
 * @copyright	Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

/**
 * @package		Joomla.Administrator
 * @subpackage	com_config
 */
class ConfigControllerApplication extends JController
{
	/**
	 * Class Constructor
	 *
	 * @param	array	$config		An optional associative array of configuration settings.
	 * @return	void
	 * @since	1.0
	 */
	function __construct($config = array())
	{
		parent::__construct($config);

		// Map the apply task to the save method.
		$this->registerTask('apply', 'save');
	}

	/**
	 * Method to save the configuration.
	 *
	 * @return	bool	True on success, false on failure.
	 * @since	1.0
	 */
	public function save()
	{
		// Check for request forgeries.
		JRequest::checkToken() or die;

		// Check if the user is authorized to do this.
		if (!MolajoFactory::getUser()->authorise('core.admin'))
		{
			MolajoFactory::getApplication()->redirect('index.php', MolajoText::_('JERROR_ALERTNOAUTHOR'));
			return;
		}

		// Set FTP credentials, if given.
		JClientHelper::setCredentialsFromRequest('ftp');

		// Initialise variables.
		$app	= MolajoFactory::getApplication();
		$model	= $this->getModel('Application');
		$form	= $model->getForm();
		$data	= JRequest::getVar('jform', array(), 'post', 'array');

		// Validate the posted data.
		$return = $model->validate($form, $data);

		// Check for validation errors.
		if ($return === false)
		{
			// Get the validation messages.
			$errors	= $model->getErrors();

			// Push up to three validation messages out to the user.
			for ($i = 0, $n = count($errors); $i < $n && $i < 3; $i++) {
				if (MolajoError::isError($errors[$i])) {
					$app->enqueueMessage($errors[$i]->getMessage(), 'warning');
				} else {
					$app->enqueueMessage($errors[$i], 'warning');
				}
			}

			// Save the data in the session.
			$app->setUserState('com_config.config.global.data', $data);

			// Redirect back to the edit screen.
			$this->setRedirect(MolajoRoute::_('index.php?option=com_config&view=application', false));
			return false;
		}

		// Attempt to save the configuration.
		$data	= $return;
		$return = $model->save($data);

		// Check the return value.
		if ($return === false)
		{
			// Save the data in the session.
			$app->setUserState('com_config.config.global.data', $data);

			// Save failed, go back to the screen and display a notice.
			$message = MolajoText::sprintf('JERROR_SAVE_FAILED', $model->getError());
			$this->setRedirect('index.php?option=com_config&view=application', $message, 'error');
			return false;
		}

		// Set the success message.
		$message = MolajoText::_('COM_CONFIG_SAVE_SUCCESS');

		// Set the redirect based on the task.
		switch ($this->getTask())
		{
			case 'apply':
				$this->setRedirect('index.php?option=com_config', $message);
				break;

			case 'save':
			default:
				$this->setRedirect('index.php', $message);
				break;
		}

		return true;
	}

	/**
	 * Cancel operation
	 */
	function cancel()
	{
		// Check if the user is authorized to do this.
		if (!MolajoFactory::getUser()->authorise('core.admin', 'com_config'))
		{
			MolajoFactory::getApplication()->redirect('index.php', MolajoText::_('JERROR_ALERTNOAUTHOR'));
			return;
		}

		// Set FTP credentials, if given
		JClientHelper::setCredentialsFromRequest('ftp');

		// Clean the session data.
		$app = MolajoFactory::getApplication();
		$app->setUserState('com_config.config.global.data',	null);

		$this->setRedirect('index.php');
	}

	function refreshHelp()
	{
		// Set FTP credentials, if given
		JClientHelper::setCredentialsFromRequest('ftp');

		if (($data = file_get_contents('http://help.joomla.org/helpsites.xml')) === false) {
			$this->setRedirect('index.php?option=com_config', MolajoText::_('COM_CONFIG_ERROR_HELPREFRESH_FETCH'), 'error');
		} else if (!JFile::write(JPATH_BASE.'/help/helpsites.xml', $data)) {
			$this->setRedirect('index.php?option=com_config', MolajoText::_('COM_CONFIG_ERROR_HELPREFRESH_ERROR_STORE'), 'error');
		} else {
			$this->setRedirect('index.php?option=com_config', MolajoText::_('COM_CONFIG_HELPREFRESH_SUCCESS'));
		}
	}

	/**
     * Molajo Hack - remove removeroot
     *
	 * Method to remove the root property from the configuration.
	 *
	 * @return	bool	True on success, false on failure.
	 * @since	1.0
	 */
	public function removeroot() {}
}