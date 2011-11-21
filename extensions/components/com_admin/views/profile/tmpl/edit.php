<?php
/**
 * @version		$Id: edit.php 21672 2011-06-24 22:04:46Z chdemko $
 * @package		Joomla.Administrator
 * @subpackage	com_admin
 * @copyright	Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

// Include the component HTML helpers.
MolajoHTML::addIncludePath(JPATH_COMPONENT.'/helpers/html');

// Load the tooltip behavior.
MolajoHTML::_('behavior.tooltip');
MolajoHTML::_('behavior.formvalidation');

// Get the form fieldsets.
$fieldsets = $this->form->getFieldsets();
?>

<script type="text/javascript">
	Joomla.submitbutton = function(task)
	{
		if (task == 'profile.cancel' || document.formvalidator.isValid(document.id('profile-form'))) {
			Joomla.submitform(task, document.getElementById('profile-form'));
		}
	}
</script>

<form action="<?php echo MolajoRouteHelper::_('index.php?option=com_admin&view=profile&layout=edit&id='.$this->item->id); ?>" method="post" name="adminForm" id="profile-form" class="form-validate">
	<div class="width-60 fltlft">
		<fieldset class="adminform">
			<legend><?php echo MolajoText::_('COM_ADMIN_USER_ACCOUNT_DETAILS'); ?></legend>
			<ul class="adminformlist">
			<?php foreach($this->form->getFieldset('user_details') as $field) :?>
				<li><?php echo $field->label; ?>
				<?php echo $field->input; ?></li>
			<?php endforeach; ?>
			</ul>
		</fieldset>
	</div>

	<div class="width-40 fltrt">
		<?php
		echo MolajoHTML::_('sliders.start');
		foreach ($fieldsets as $fieldset) :
			if ($fieldset->name == 'user_details') :
				continue;
			endif;
			echo MolajoHTML::_('sliders.panel', MolajoText::_($fieldset->label), $fieldset->name);
		?>
		<fieldset class="panelform">
		<ul class="adminformlist">
		<?php foreach($this->form->getFieldset($fieldset->name) as $field): ?>
			<?php if ($field->hidden): ?>
				<?php echo $field->input; ?>
			<?php else: ?>
				<li><?php echo $field->label; ?>
				<?php echo $field->input; ?></li>
			<?php endif; ?>
		<?php endforeach; ?>
		</ul>
		</fieldset>
		<?php endforeach; ?>
		<?php echo MolajoHTML::_('sliders.end'); ?>

		<input type="hidden" name="task" value="" />
		<?php echo MolajoHTML::_('form.token'); ?>
	</div>
</form>
