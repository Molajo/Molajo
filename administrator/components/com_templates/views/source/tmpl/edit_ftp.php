<?php
/**
 * @version		$Id: edit_ftp.php 20196 2011-01-09 02:40:25Z ian $
 * @package		Joomla.Administrator
 * @subpackage	com_templates
 * @copyright	Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access.
defined('_JEXEC') or die;
?>
<fieldset class="adminform" title="<?php echo MolajoText::_('COM_TEMPLATES_FTP_TITLE'); ?>">
	<legend><?php echo MolajoText::_('COM_TEMPLATES_FTP_TITLE'); ?></legend>

	<?php echo MolajoText::_('COM_TEMPLATES_FTP_DESC'); ?>

	<?php if (JError::isError($this->ftp)): ?>
		<p class="error"><?php echo MolajoText::_($this->ftp->message); ?></p>
	<?php endif; ?>

	<table class="adminform">
		<tbody>
			<tr>
				<td width="120">
					<label for="username"><?php echo MolajoText::_('JGLOBAL_USERNAME'); ?></label>
				</td>
				<td>
					<input type="text" id="username" name="username" class="inputbox" size="70" value="" />
				</td>
			</tr>
			<tr>
				<td width="120">
					<label for="password"><?php echo MolajoText::_('JGLOBAL_PASSWORD'); ?></label>
				</td>
				<td>
					<input type="password" id="password" name="password" class="inputbox" size="70" value="" />
				</td>
			</tr>
		</tbody>
	</table>
</fieldset>


