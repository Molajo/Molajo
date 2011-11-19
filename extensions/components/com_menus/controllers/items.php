<?php
/**
 * @version		$Id: items.php 20228 2011-01-10 00:52:54Z eddieajau $
 * @copyright	Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

jimport( 'joomla.application.component.controlleradmin' );

/**
 * The Menu Item Controller
 *
 * @package		Joomla.Administrator
 * @subpackage	com_menus
 * * * @since		1.0
 */
class MenusControllerItems extends JControllerAdmin
{
	public function __construct($config = array())
	{
		parent::__construct($config);
		$this->registerTask('unsetDefault',	'setDefault');
	}

	/**
	 * Proxy for getModel
	 * @since	1.0
	 */
	function getModel($name = 'Item', $prefix = 'MenusModel', $config = array())
	{
		return parent::getModel($name, $prefix, array('ignore_request' => true));
	}

	/**
	 * Rebuild the nested set tree.
	 *
	 * @return	bool	False on failure or error, true on success.
	 * @since	1.0
	 */
	public function rebuild()
	{
		JRequest::checkToken() or die;

		$this->setRedirect('index.php?option=com_menus&view=items');

		// Initialise variables.
		$model = $this->getModel();

		if ($model->rebuild()) {
			// Reorder succeeded.
			$this->setMessage(MolajoText::_('MENU_ITEMS_REBUILD_SUCCESS'));
			return true;
		} else {
			// Rebuild failed.
			$this->setMessage(MolajoText::sprintf('MENU_ITEMS_REBUILD_FAILED'));
			return false;
		}
	}

	public function saveorder()
	{
		JRequest::checkToken() or die;

		// Get the arrays from the Request
		$order	= JRequest::getVar('order',	null,	'post',	'array');
		$originalOrder = explode(',', JRequest::getString('original_order_values'));

		// Make sure something has changed
		if (!($order === $originalOrder))
		{
			parent::saveorder();
		}
		else
		{
			// Nothing to reorder
			$this->setRedirect(MolajoRoute::_('index.php?option='.$this->option.'&view='.$this->view_list, false));
			return true;
		}
	}

	/**
	 * Method to set the home property for a list of items
	 *
	 * @since	1.0
	 */
	function setDefault()
	{
		// Check for request forgeries
		JRequest::checkToken('default') or die(MolajoText::_('JINVALID_TOKEN'));

		// Get items to publish from the request.
		$cid	= JRequest::getVar('cid', array(), '', 'array');
		$data	= array('setDefault' => 1, 'unsetDefault' => 0);
		$task 	= $this->getTask();
		$value	= JArrayHelper::getValue($data, $task, 0, 'int');

		if (empty($cid)) {
			MolajoError::raiseWarning(500, MolajoText::_($this->text_prefix.'_NO_ITEM_SELECTED'));
		} else {
			// Get the model.
			$model = $this->getModel();

			// Make sure the item ids are integers
			JArrayHelper::toInteger($cid);

			// Publish the items.
			if (!$model->setHome($cid, $value)) {
				MolajoError::raiseWarning(500, $model->getError());
			} else {
				if ($value == 1) {
					$ntext = 'MENU_ITEMS_SET_HOME';
				}
				else {
					$ntext = 'MENU_ITEMS_UNSET_HOME';
				}
				$this->setMessage(MolajoText::plural($ntext, count($cid)));
			}
		}

		$this->setRedirect(MolajoRoute::_('index.php?option='.$this->option.'&view='.$this->view_list, false));
	}
}
