<?php
/**
 * @version		$Id: module.php 21032 2011-03-29 16:38:31Z dextercowley $
 * @copyright	Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access.
defined('_JEXEC') or die;

jimport('joomla.application.component.controllerform');

/**
 * Module controller class.
 *
 * @package		Joomla.Administrator
 * @subpackage	com_modules
 * @version		1.6
 */
class ModulesControllerModule extends JControllerForm
{
	/**
	 * Override parent add method.
	 */
	public function add()
	{
		// Initialise variables.
		$app = MolajoFactory::getApplication();

		// Get the result of the parent method. If an error, just return it.
		$result = parent::add();
		if (MolajoError::isError($result)) {
			return $result;
		}

		// Look for the Extension ID.
		$extensionId = JRequest::getInt('eid');
		if (empty($extensionId)) {
			$this->setRedirect(MolajoRouteHelper::_('index.php?option='.$this->option.'&view='.$this->view_item.'&layout=edit', false));
			return MolajoError::raiseWarning(500, MolajoText::_('COM_MODULES_ERROR_INVALID_EXTENSION'));
		}

		$app->setUserState('com_modules.add.module.extension_id', $extensionId);
	}

	/**
	 * Override parent cancel method to reset the add module state.
	 */
	public function cancel($key = null)
	{
		// Initialise variables.
		$app = MolajoFactory::getApplication();

		parent::cancel();

		$app->setUserState('com_modules.add.module.extension_id', null);
	}

	/**
	 * Override parent allowSave method.
	 */
	protected function allowSave($data, $key = 'id')
	{
		// use custom position if selected
		if (empty($data['position'])) {
			$data['position'] = $data['custom_position'];
		}

		unset($data['custom_position']);

		return parent::allowSave($data, $key);
	}

	/**
	 * Function that allows child controller access to model data after the data has been saved.
	 *
	 * @param	JModel	$model	The data model object.
	 *
	 * @return	void
	 * @since	1.0
	 */
	protected function postSaveHook(JModel &$model, $validData = array())
	{
		// Initialise variables.
		$app = MolajoFactory::getApplication();
		$task = $this->getTask();

		switch ($task)
		{
			case 'save2new':
				$app->setUserState('com_modules.add.module.extension_id', $model->getState('module.extension_id'));
				break;

			default:
				$app->setUserState('com_modules.add.module.extension_id', null);
				break;
		}
	}
}
