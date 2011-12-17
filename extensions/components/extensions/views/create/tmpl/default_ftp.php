<?php
/**
 * @version        $Id: default_ftp.php 20196 2011-01-09 02:40:25Z ian $
 * @package        Joomla.Administrator
 * @subpackage    installer
 * @copyright    Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license        GNU General Public License version 2 or later; see LICENSE.txt
 * @since        1.5
 */

// no direct access
defined('_JEXEC') or die;
?>
<fieldset title="<?php echo MolajoTextHelper::_('INSTALLER_MSG_DESCFTPTITLE'); ?>">
    <legend><?php echo MolajoTextHelper::_('INSTALLER_MSG_DESCFTPTITLE'); ?></legend>

    <?php echo MolajoTextHelper::_('INSTALLER_MSG_DESCFTP'); ?>

    <?php if (MolajoError::isError($this->ftp)): ?>
    <p><?php echo MolajoTextHelper::_($this->ftp->getMessage()); ?></p>
    <?php endif; ?>

    <table class="adminform">
        <tbody>
        <tr>
            <td width="120">
                <label for="username"><?php echo MolajoTextHelper::_('JGLOBAL_USERNAME'); ?></label>
            </td>
            <td>
                <input type="text" id="username" name="username" class="input_box" size="70" value=""/>
            </td>
        </tr>
        <tr>
            <td width="120">
                <label for="password"><?php echo MolajoTextHelper::_('JGLOBAL_PASSWORD'); ?></label>
            </td>
            <td>
                <input type="password" id="password" name="password" class="input_box" size="70" value=""/>
            </td>
        </tr>
        </tbody>
    </table>

</fieldset>