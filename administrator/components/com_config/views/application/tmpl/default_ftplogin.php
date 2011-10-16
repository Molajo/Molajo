<?php
/**
 * @version		$Id: default_ftplogin.php 20196 2011-01-09 02:40:25Z ian $
 * @package		Joomla.Administrator
 * @subpackage	com_config
 * @copyright	Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access
defined('_JEXEC') or die;
?>
<div class="width-100">
	<fieldset title="<?php echo MolajoText::_('COM_CONFIG_FTP_DETAILS'); ?>" class="adminform">
		<legend><?php echo MolajoText::_('COM_CONFIG_FTP_DETAILS'); ?></legend>
		<?php echo MolajoText::_('COM_CONFIG_FTP_DETAILS_TIP'); ?>

		<?php if (JError::isError($this->ftp)): ?>
			<p><?php echo MolajoText::_($this->ftp->message); ?></p>
		<?php endif; ?>
		<ul class="adminformlist">
		<li><label for="username"><?php echo MolajoText::_('JGLOBAL_USERNAME'); ?></label>
		<input type="text" id="username" name="username" class="input_box" size="70" value="" /></li>

		<li><label for="password"><?php echo MolajoText::_('JGLOBAL_PASSWORD'); ?></label>
		<input type="password" id="password" name="password" class="input_box" size="70" value="" /></li>
		</ul>
	</fieldset>
</div>