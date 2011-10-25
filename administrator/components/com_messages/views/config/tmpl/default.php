<?php
/**
 * @version		$Id: default.php 20196 2011-01-09 02:40:25Z ian $
 * @package		Joomla.Administrator
 * @subpackage	com_messages
 * @copyright	Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access.
defined('_JEXEC') or die;

// Include the HTML helpers.
MolajoHTML::addIncludePath(JPATH_COMPONENT.'/helpers/html');
MolajoHTML::_('behavior.tooltip');
MolajoHTML::_('behavior.formvalidation');
MolajoHTML::_('behavior.keepalive');
?>
<script type="text/javascript">
	Joomla.submitbutton = function(task)
	{
		if (task == 'config.cancel' || document.formvalidator.isValid(document.id('config-form'))) {
			Joomla.submitform(task, document.getElementById('config-form'));
		}
	}
</script>
<form action="<?php echo MolajoRoute::_('index.php?option=com_messages'); ?>" method="post" name="adminForm" id="message-form" class="form-validate">
	<fieldset>
		<div class="fltrt">
			<button type="button" onclick="Joomla.submitform('config.save', this.form);window.top.setTimeout('window.parent.SqueezeBox.close()', 1400);">
				<?php echo MolajoText::_('JSAVE');?></button>
			<button type="button" onclick="window.parent.SqueezeBox.close();">
				<?php echo MolajoText::_('JCANCEL');?></button>
		</div>
		<div class="configuration" >
			<?php echo MolajoText::_('COM_MESSAGES_MY_SETTINGS') ?>
		</div>
	</fieldset>

	<fieldset class="adminform">
	<ul  class="adminformlist">
		<li><?php echo $this->form->getLabel('lock'); ?>
		<?php echo $this->form->getInput('lock'); ?></li>

		<li><?php echo $this->form->getLabel('mail_on_new'); ?>
		<?php echo $this->form->getInput('mail_on_new'); ?></li>

		<li><?php echo $this->form->getLabel('auto_purge'); ?>
		<?php echo $this->form->getInput('auto_purge'); ?></li>
	</ul>

		<input type="hidden" name="task" value="" />
		<?php echo MolajoHTML::_('form.token'); ?>
	</fieldset>
</form>
