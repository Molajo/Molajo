<?php
/**
 * @version     $id: com_installer
 * @package     Molajo
 * @subpackage  Controller
 * @copyright   Copyright (C) 2011 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 *
 * 1. Had to override this Controller so that I could comment out the Helper File require statement to override the Helper File
 * 2. Moved the create method in here as the form action create.create could not find the Controller
 *
 */
defined('MOLAJO') or die;
jimport('joomla.application.component.controller');

/**
 * Installer Controller
 *
 * @package		Joomla.Administrator
 * @subpackage	com_installer
 * @since		1.5
 */
class InstallerController extends JController
{
    /**
     * Method to display a view.
     *
     * @param	boolean			If true, the view output will be cached
     * @param	array			An array of safe url parameters and their variable types, for valid values see {@link JFilterInput::clean()}.
     *
     * @return	JController		This object to support chaining.
     * @since	1.5
     */
    public function display($cachable = false, $urlparams = false)
    {
//		require_once JPATH_COMPONENT.'/helpers/installer.php';

        $document = JFactory::getDocument();

        // Set the default view name and format from the Request.
        $vName		= JRequest::getCmd('view', 'install');
        $vFormat	= $document->getType();
        $lName		= JRequest::getCmd('layout', 'default');

        // Get and render the view.
        if ($view = $this->getView($vName, $vFormat)) {
            $ftp	= JClientHelper::setCredentialsFromRequest('ftp');
            $view->assignRef('ftp', $ftp);

            // Get the model for the view.
            $model = $this->getModel($vName);

            // Push the model into the view (as default).
            $view->setModel($model, true);
            $view->setLayout($lName);

            // Push document object into the view.
            $view->assignRef('document', $document);
            // Load the submenu.
            InstallerHelper::addSubmenu($vName);
            $view->display();
        }
        return $this;
    }

    /**
     * Create a set of extensions.
     *
     * @since	1.6
     */
    function create()
    {
        JRequest::checkToken() or jexit(JText::_('JINVALID_TOKEN'));
        $model	= $this->getModel('create');
        $results = $model->create();
        $this->setRedirect(JRoute::_('index.php?option=com_installer&view=create', false));
        return $results;
    }
}