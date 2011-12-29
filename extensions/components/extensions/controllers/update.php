<?php
/**
 * @version        $Id: update.php 21440 2011-06-04 13:40:19Z dextercowley $
 * @package        Joomla.Administrator
 * @subpackage    installer
 * @copyright    Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license        GNU General Public License, see LICENSE.php
 */

// No direct access
defined('_JEXEC') or die;

/**
 * @package        Joomla.Administrator
 * @subpackage    installer
 */
class InstallerControllerUpdate extends JController
{

    /**
     * Update a set of extensions.
     *
     * @since    1.0
     */
    function update()
    {
        // Check for request forgeries
        JRequest::checkToken() or die;

        $model = $this->getModel('update');
        $uid = JRequest::getVar('cid', array(), '', 'array');

        JArrayHelper::toInteger($uid, array());
        if ($model->update($uid)) {
            $cache = MolajoFactory::getCache('menu');
            $cache->clean();
        }


        $redirect_url = MolajoFactory::getUser()->getUserState('installer.redirect_url');
        if (empty($redirect_url)) {
            $redirect_url = MolajoRouteHelper::_('index.php?option=installer&view=update', false);
        } else
        {
            // wipe out the user state when we're going to redirect
            MolajoFactory::getUser()->setUserState('installer.redirect_url', '');
            MolajoFactory::getUser()->setUserState('installer.message', '');
            MolajoFactory::getUser()->setUserState('installer.extension_message', '');
        }
        $this->setRedirect($redirect_url);
    }

    /**
     * Find new updates.
     *
     * @since    1.0
     */
    function find()
    {
        // Find updates
        // Check for request forgeries
        JRequest::checkToken() or die;
        $model = $this->getModel('update');
        $model->purge();
        $result = $model->findUpdates();
        $this->setRedirect(MolajoRouteHelper::_('index.php?option=installer&view=update', false));
        //$view->display();
    }

    /**
     * Purges updates.
     *
     * @since    1.0
     */
    function purge()
    {
        // Purge updates
        // Check for request forgeries
        JRequest::checkToken() or die;
        $model = $this->getModel('update');
        $model->purge();
        $model->enableSites();
        $this->setRedirect(MolajoRouteHelper::_('index.php?option=installer&view=update', false), $model->_message);
    }
}