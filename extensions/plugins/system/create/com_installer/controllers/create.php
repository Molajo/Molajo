<?php
/**
 * @version     $id: create.php
 * @package     Molajo
 * @subpackage  Create Extensions
 * @copyright   Copyright (C) 2011 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die;

/**
 * Create Controller
 *
 * @package	Molajo
 * @subpackage	Controller
 * @since	1.6
 */
class InstallerControllerCreate extends JController {

    /**
     * Create a set of extensions.
     *
     * @since	1.6
     */
    function create()
    {
        JRequest::checkToken() or die;
        $model	= $this->getModel('create');
        $model->create();
        $this->setRedirect(JRoute::_('index.php?option=com_installer&view=create',false));
    }
}