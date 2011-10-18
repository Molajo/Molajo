<?php
/**
 * @version		$Id: component.php 21320 2011-05-11 01:01:37Z dextercowley $
 * @package		Joomla.Administrator
 * @subpackage	com_config
 * @copyright	Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;

/**
 * Note: this view is intended only to be opened in a popup
 * @package		Joomla.Administrator
 * @subpackage	com_config
 */
class ConfigControllerComponent extends JController
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
	 * Save the configuration
	 */
	function save()
	{
		// Check for request forgeries.
		JRequest::checkToken() or die;

		// Set FTP credentials, if given.
		JClientHelper::setCredentialsFromRequest('ftp');

		// Initialise variables.
		$app	= MolajoFactory::getApplication();
		$model	= $this->getModel('Component');
		$form	= $model->getForm();
		$data	= JRequest::getVar('jform', array(), 'post', 'array');
		$id		= JRequest::getInt('id');
		$option	= JRequest::getCmd('component');

		// Check if the user is authorized to do this.
		if (!MolajoFactory::getUser()->authorise('core.admin', $option))
		{
			MolajoFactory::getApplication()->redirect('index.php', MolajoText::_('JERROR_ALERTNOAUTHOR'));
			return;
		}

		// Validate the posted data.
		$return = $model->validate($form, $data);

		// Check for validation errors.
		if ($return === false) {
			// Get the validation messages.
			$errors	= $model->getErrors();

			// Push up to three validation messages out to the user.
			for ($i = 0, $n = count($errors); $i < $n && $i < 3; $i++) {
				if (JError::isError($errors[$i])) {
					$app->enqueueMessage($errors[$i]->getMessage(), 'warning');
				} else {
					$app->enqueueMessage($errors[$i], 'warning');
				}
			}

			// Save the data in the session.
			$app->setUserState('com_config.config.global.data', $data);

			// Redirect back to the edit screen.
			$this->setRedirect(MolajoRoute::_('index.php?option=com_config&view=component&component='.$option.'&tmpl=component', false));
			return false;
		}

		// Attempt to save the configuration.
		$data	= array(
					'params'	=> $return,
					'id'		=> $id,
					'option'	=> $option
					);
		$return = $model->save($data);

		// Check the return value.
		if ($return === false)
		{
			// Save the data in the session.
			$app->setUserState('com_config.config.global.data', $data);

			// Save failed, go back to the screen and display a notice.
			$message = MolajoText::sprintf('JERROR_SAVE_FAILED', $model->getError());
			$this->setRedirect('index.php?option=com_config&view=component&component='.$option.'&tmpl=component', $message, 'error');
			return false;
		}

		// Set the redirect based on the task.
		switch ($this->getTask())
		{
			case 'apply':
				$message = MolajoText::_('COM_CONFIG_SAVE_SUCCESS');
				$this->setRedirect('index.php?option=com_config&view=component&component='.$option.'&tmpl=component', $message);
				break;

			case 'save':
			default:
				$this->setRedirect('index.php?option=com_config&view=close&tmpl=component');
				break;
		}

		return true;
	}
}