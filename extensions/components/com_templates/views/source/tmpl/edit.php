<?php
/**
 * @version		$Id: edit.php 20196 2011-01-09 02:40:25Z ian $
 * @package		Joomla.Administrator
 * @subpackage	com_templates
 * @copyright	Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access.
defined('_JEXEC') or die;

MolajoHTML::addIncludePath(JPATH_COMPONENT.'/helpers/html');
MolajoHTML::_('behavior.tooltip');
MolajoHTML::_('behavior.formvalidation');
MolajoHTML::_('behavior.keepalive');
?>
<script type="text/javascript">
	Joomla.submitbutton = function(task)
	{
		if (task == 'source.cancel' || document.formvalidator.isValid(document.id('source-form'))) {
			<?php echo $this->form->getField('source')->save(); ?>
			Joomla.submitform(task, document.getElementById('source-form'));
		} else {
			alert('<?php echo $this->escape(MolajoText::_('JGLOBAL_VALIDATION_FORM_FAILED'));?>');
		}
	}
</script>

<form action="<?php echo MolajoRoute::_('index.php?option=com_templates&layout=edit'); ?>" method="post" name="adminForm" id="source-form" class="form-validate">
	<?php if ($this->ftp) : ?>
		<?php echo $this->loadTemplate('ftp'); ?>
	<?php endif; ?>
	<fieldset class="adminform">
		<legend><?php echo MolajoText::sprintf('COM_TEMPLATES_TEMPLATE_FILENAME', $this->source->filename, $this->template->element); ?></legend>

		<?php echo $this->form->getLabel('source'); ?>
		<div class="clr"></div>
		<div class="editor-border">
		<?php echo $this->form->getInput('source'); ?>
		</div>
		<input type="hidden" name="task" value="" />
		<?php echo MolajoHTML::_('form.token'); ?>
	</fieldset>

	<?php echo $this->form->getInput('extension_id'); ?>
	<?php echo $this->form->getInput('filename'); ?>

</form>
