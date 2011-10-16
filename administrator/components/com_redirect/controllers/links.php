<?php
/**
 * @version		$Id: links.php 20230 2011-01-10 01:43:49Z eddieajau $
 * @package		Joomla.Administrator
 * @subpackage	com_redirect
 * @copyright	Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

jimport('joomla.application.component.controlleradmin');

/**
 * Redirect link list controller class.
 *
 * @package		Joomla.Administrator
 * @subpackage	com_redirect
 * * * @since		1.0
 */
class RedirectControllerLinks extends JControllerAdmin
{
	/**
	 * Method to update a record.
	 * @since	1.0
	 */
	public function activate()
	{
		// Check for request forgeries.
		JRequest::checkToken() or die;

		// Initialise variables.
		$ids		= JRequest::getVar('cid', array(), '', 'array');
		$newUrl		= JRequest::getString('new_url');
		$comment	= JRequest::getString('comment');

		if (empty($ids)) {
			JError::raiseWarning(500, MolajoText::_('COM_REDIRECT_NO_ITEM_SELECTED'));
		}
		else {
			// Get the model.
			$model = $this->getModel();

			JArrayHelper::toInteger($ids);

			// Remove the items.
			if (!$model->activate($ids, $newUrl, $comment)) {
				JError::raiseWarning(500, $model->getError());
			}
			else {
				$this->setMessage(MolajoText::plural('COM_REDIRECT_N_LINKS_UPDATED', count($ids)));
			}
		}

		$this->setRedirect('index.php?option=com_redirect&view=links');
	}

	/**
	 * Proxy for getModel.
	 * @since	1.0
	 */
	public function getModel($name = 'Link', $prefix = 'RedirectModel', $config = array('ignore_request' => true))
	{
		$model = parent::getModel($name, $prefix, $config);

		return $model;
	}
}