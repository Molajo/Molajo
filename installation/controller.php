<?php
/**
 * @package     Molajo
 * @subpackage  index.php
 * @copyright	Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @copyright   Copyright (C) 2011 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die;

/**
 * Base controller class for the Installer.
 *
 * @package		Joomla
 * @subpackage  Molajo
 * @since		1.0
 */
class MolajoInstallationController extends JController
{
	/**
     * display
     *
	 * Method to display a view.
	 *
	 * @param	boolean	$cachable	If true, the view output will be cached
	 * @param	array	$urlparams	An array of safe url parameters and their variable types, for valid values see {@link JFilterInput::clean()}.
	 *
	 * @return	JController	This object to support chaining.
	 * @since	1.5
	 */
	public function display($cachable = false, $urlparams = false)
	{
		// Get the current URI to redirect to.
		$uri		= JURI::getInstance();
		$redirect	= base64_encode($uri);

		// Get the document object.
		$document	= MolajoFactory::getDocument();

		// Set the default view name and format from the Request.
		if (file_exists(MOLAJO_PATH_CONFIGURATION.'/configuration.php')
            && (filesize(MOLAJO_PATH_CONFIGURATION.'/configuration.php') > 10)
            && file_exists(MOLAJO_PATH_INSTALLATION.'/index.php')) {
			$DefaultView	= 'remove';
		} else {
			$DefaultView	= 'language';
		}

		$vName		= JRequest::getWord('view', $DefaultView);
		$vFormat	= $document->getType();
		$lName		= JRequest::getWord('layout', 'default');

		if (strcmp($vName, $DefaultView) == 0) {
			JRequest::setVar('view', $DefaultView);
		}

		if ($view = $this->getView($vName, $vFormat)) {

			switch ($vName) {
				default:
					$model = $this->getModel('Setup', 'MolajoInstallationModel', array('dbo' => null));
					break;
			}

			// Push the model into the view (as default).
			$view->setModel($model, true);
			$view->setLayout($lName);

			// Push document object into the view.
			$view->assignRef('document', $document);

			$view->display();
		}

		return $this;
	}
}
