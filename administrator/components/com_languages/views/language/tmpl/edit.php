<?php
/**
 * @version		$Id: edit.php 21529 2011-06-11 22:17:15Z chdemko $
 * @package		Joomla.Administrator
 * @subpackage	com_languages
 * @copyright	Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;

JHtml::addIncludePath(JPATH_COMPONENT.'/helpers/html');
JHtml::_('behavior.tooltip');
JHtml::_('behavior.formvalidation');
$canDo = LanguagesHelper::getActions();
?>
<script type="text/javascript">
	Joomla.submitbutton = function(task)
	{
		if (task == 'language.cancel' || document.formvalidator.isValid(document.id('language-form'))) {
			Joomla.submitform(task, document.getElementById('language-form'));
		}
	}
</script>

<form action="<?php echo MolajoRoute::_('index.php?option=com_languages&layout=edit&lang_id='.(int) $this->item->lang_id); ?>" method="post" name="adminForm" id="language-form" class="form-validate">
	<div class="width-60 fltlft">
		<fieldset class="adminform">
			<?php if ($this->item->lang_id) : ?>
				<legend><?php echo MolajoText::sprintf('JGLOBAL_RECORD_NUMBER', $this->item->lang_id); ?></legend>
			<?php else : ?>
				<legend><?php echo MolajoText::_('COM_LANGUAGES_VIEW_LANGUAGE_EDIT_NEW_TITLE'); ?></legend>
			<?php endif; ?>

			<?php echo $this->form->getLabel('title'); ?>
			<?php echo $this->form->getInput('title'); ?>

			<?php echo $this->form->getLabel('title_native'); ?>
			<?php echo $this->form->getInput('title_native'); ?>

			<?php echo $this->form->getLabel('sef'); ?>
			<?php echo $this->form->getInput('sef'); ?>

			<?php echo $this->form->getLabel('image'); ?>
			<?php echo $this->form->getInput('image'); ?>

			<?php echo $this->form->getLabel('lang_code'); ?>
			<?php echo $this->form->getInput('lang_code'); ?>

			<?php if ($canDo->get('core.edit.state')) : ?>
				<?php echo $this->form->getLabel('published'); ?>
				<?php echo $this->form->getInput('published'); ?>
			<?php endif; ?>

			<?php echo $this->form->getLabel('description'); ?>
			<?php echo $this->form->getInput('description'); ?>

			<?php echo $this->form->getLabel('lang_id'); ?>
			<?php echo $this->form->getInput('lang_id'); ?>
		</fieldset>
	</div>
	<div class="width-40 fltrt">
		<?php echo JHtml::_('sliders.start','language-sliders-'.$this->item->lang_code, array('useCookie'=>1)); ?>

		<?php echo JHtml::_('sliders.panel',MolajoText::_('JGLOBAL_FIELDSET_METADATA_OPTIONS'), 'metadata'); ?>
			<fieldset class="adminform">
				<?php foreach($this->form->getFieldset('metadata') as $field): ?>
					<?php if (!$field->hidden): ?>
						<?php echo $field->label; ?>
					<?php endif; ?>
					<?php echo $field->input; ?>
				<?php endforeach; ?>
			</fieldset>

		<?php echo JHtml::_('sliders.end'); ?>
		<input type="hidden" name="task" value="" />
		<?php echo JHtml::_('form.token'); ?>
	</div>
	<div class="clr"> </div>
</form>
