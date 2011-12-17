<?php
/**
 * @version        $Id: install.php 20196 2011-01-09 02:40:25Z ian $
 * @package        Joomla.Administrator
 * @subpackage    installer
 * @copyright    Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license        GNU General Public License, see LICENSE.php
 */

// No direct access.
defined('_JEXEC') or die;

/**
 * @package        Joomla.Administrator
 * @subpackage    installer
 */
class InstallerControllerInstall extends JController
{
    /**
     * Install an extension.
     *
     * @return    void
     * @since    1.0
     */
    public function install()
    {
        // Check for request forgeries
        JRequest::checkToken() or die;

        $model = $this->getModel('install');
        if ($model->install()) {
            $cache = MolajoFactory::getCache('menu');
            $cache->clean();
            // TODO: Reset the users acl here as well to kill off any missing bits
        }

        $app = MolajoFactory::getApplication();
        $redirect_url = $app->getUserState('installer.redirect_url');
        if (empty($redirect_url)) {
            $redirect_url = MolajoRouteHelper::_('index.php?option=installer&view=install', false);
        } else
        {
            // wipe out the user state when we're going to redirect
            $app->setUserState('installer.redirect_url', '');
            $app->setUserState('installer.message', '');
            $app->setUserState('installer.extension_message', '');
        }
        $this->setRedirect($redirect_url);
    }
}
