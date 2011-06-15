<?php
/**
 * @version     $id: file.script.php
 * @package     Molajo
 * @subpackage  Component
 * @copyright   Copyright (C) 2011 Amy Stephen. All rights reserved. See http://Molajo.org/Copyright
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
jimport( 'joomla.html.toolbar.button.popup' );

class com_molajosamplesInstallerScript {

	function install($parent) {
		echo '<p>'. JText::_('MOLAJOSAMPLES_16_CUSTOM_INSTALL_SCRIPT') . '</p>';
	}

	function uninstall($parent) {

        $response = JButtonConfirm::fetchButton($type='Confirm', $msg=JText::_('MOLAJOSAMPLES_DO_YOU_WANT_TO_REMOVE_COMPONENT_TABLE'), $name = JText::_('MOLAJOSAMPLES'), $text = '', $task = '', $list = true, $hideMenu = false);
		echo '<p>'. JText::_('MOLAJOSAMPLES_16_CUSTOM_UNINSTALL_SCRIPT') .'</p>';
	}

	function update($parent) {
		echo '<p>'. JText::_('MOLAJOSAMPLES_16_CUSTOM_UPDATE_SCRIPT') .'</p>';
	}

	function preflight($type, $parent) {
		echo '<p>'. JText::sprintf('MOLAJOSAMPLES_16_CUSTOM_PREFLIGHT', $type) .'</p>';
	}

	function postflight($type, $parent) {
		echo '<p>'. JText::sprintf('MOLAJOSAMPLES_16_CUSTOM_POSTFLIGHT', $type) .'</p>';
		// An example of setting a redirect to a new location after the install is completed
		//$parent->getParent()->set('redirect_url', 'http://www.google.com');
	}
}