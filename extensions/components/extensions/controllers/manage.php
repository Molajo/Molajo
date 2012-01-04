<?php
/**
 * @version        $Id: manage.php 20196 2011-01-09 02:40:25Z ian $
 * @package        Joomla.Administrator
 * @subpackage    installer
 * @copyright    Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license        GNU General Public License, see LICENSE.php
 */

// No direct access.
defined('_JEXEC') or die;

/**
 * @package        Joomla.Administrator
 * @subpackage    installer
 */
class InstallerControllerManage extends JController
{
    /**
     * Constructor.
     *
     * @param    array An optional associative array of configuration settings.
     * @see        JController
     * @since    1.0
     */
    public function __construct($config = array())
    {
        parent::__construct($config);

        $this->registerTask('unpublish', 'publish');
        $this->registerTask('publish', 'publish');
    }

    /**
     * Enable/Disable an extension (if supported).
     *
     * @since    1.0
     */
    public function publish()
    {
        // Check for request forgeries.
        JRequest::checkToken() or die;

        // Initialise variables.
        $user = MolajoController::getUser();
        $ids = JRequest::getVar('cid', array(), '', 'array');
        $values = array('publish' => 1, 'unpublish' => 0);
        $task = $this->getTask();
        $value = JArrayHelper::getValue($values, $task, 0, 'int');

        if (empty($ids)) {
            MolajoError::raiseWarning(500, MolajoTextHelper::_('INSTALLER_ERROR_NO_EXTENSIONS_SELECTED'));
        } else {
            // Get the model.
            $model = $this->getModel('manage');

            // Change the state of the records.
            if (!$model->publish($ids, $value)) {
                MolajoError::raiseWarning(500, implode('<br />', $model->getErrors()));
            } else {
                if ($value == 1) {
                    $ntext = 'INSTALLER_N_EXTENSIONS_PUBLISHED';
                } else if ($value == 0) {
                    $ntext = 'INSTALLER_N_EXTENSIONS_UNPUBLISHED';
                }
                $this->setMessage(MolajoTextHelper::plural($ntext, count($ids)));
            }
        }

        $this->setRedirect(MolajoRouteHelper::_('index.php?option=installer&view=manage', false));
    }

    /**
     * Remove an extension (Uninstall).
     *
     * @return    void
     * @since    1.0
     */
    public function remove()
    {
        // Check for request forgeries
        JRequest::checkToken() or die;

        $eid = JRequest::getVar('cid', array(), '', 'array');
        $model = $this->getModel('manage');

        JArrayHelper::toInteger($eid, array());
        $result = $model->remove($eid);
        $this->setRedirect(MolajoRouteHelper::_('index.php?option=installer&view=manage', false));
    }

    /**
     * Refreshes the cached metadata about an extension.
     *
     * Useful for debugging and testing purposes when the XML file might change.
     *
     * @since    1.0
     */
    function refresh()
    {
        // Check for request forgeries
        JRequest::checkToken() or die;

        $uid = JRequest::getVar('cid', array(), '', 'array');
        $model = $this->getModel('manage');

        JArrayHelper::toInteger($uid, array());
        $result = $model->refresh($uid);
        $this->setRedirect(MolajoRouteHelper::_('index.php?option=installer&view=manage', false));
    }
}