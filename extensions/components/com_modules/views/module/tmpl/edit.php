<?php
/**
 * @version		$Id: edit.php 21529 2011-06-11 22:17:15Z chdemko $
 * @package		Joomla.Administrator
 * @subpackage	com_modules
 * @copyright	Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access.
defined('_JEXEC') or die;

MolajoHTML::addIncludePath(JPATH_COMPONENT.'/helpers/html');
MolajoHTML::_('behavior.tooltip');
MolajoHTML::_('behavior.formvalidation');
MolajoHTML::_('behavior.combobox');
$hasContent = empty($this->item->module) || $this->item->module == 'custom' || $this->item->module == 'mod_custom';

$script = "Joomla.submitbutton = function(task)
	{
			if (task == 'module.cancel' || document.formvalidator.isValid(document.id('module-form'))) {";
if ($hasContent) {
	$script .= $this->form->getField('content')->save();
}
$script .= "	Joomla.submitform(task, document.getElementById('module-form'));
				if (self != top) {
					window.top.setTimeout('window.parent.SqueezeBox.close()', 1000);
				}
			} else {
				alert('".$this->escape(MolajoText::_('JGLOBAL_VALIDATION_FORM_FAILED'))."');
			}
	}";

MolajoFactory::getDocument()->addScriptDeclaration($script);
?>
<form action="<?php echo MolajoRoute::_('index.php?option=com_modules&layout=edit&id='.(int) $this->item->id); ?>" method="post" name="adminForm" id="module-form" class="form-validate">
	<div class="width-60 fltlft">
		<fieldset class="adminform">
			<legend><?php echo MolajoText::_('JDETAILS'); ?></legend>
			<ul class="adminformlist">

			<li><?php echo $this->form->getLabel('title'); ?>
			<?php echo $this->form->getInput('title'); ?></li>

			<li><?php echo $this->form->getLabel('showtitle'); ?>
			<?php echo $this->form->getInput('showtitle'); ?></li>

			<li><?php echo $this->form->getLabel('position'); ?>
			<?php echo $this->form->getInput('position'); ?></li>

			<?php if ((string) $this->item->xml->name != 'Login Form'): ?>
			<li><?php echo $this->form->getLabel('published'); ?>
			<?php echo $this->form->getInput('published'); ?></li>
			<?php endif; ?>

			<li><?php echo $this->form->getLabel('access'); ?>
			<?php echo $this->form->getInput('access'); ?></li>

			<li><?php echo $this->form->getLabel('ordering'); ?>
			<?php echo $this->form->getInput('ordering'); ?></li>

			<?php if ((string) $this->item->xml->name != 'Login Form'): ?>
			<li><?php echo $this->form->getLabel('start_publishing_datetime'); ?>
			<?php echo $this->form->getInput('start_publishing_datetime'); ?></li>

			<li><?php echo $this->form->getLabel('stop_publishing_datetime'); ?>
			<?php echo $this->form->getInput('stop_publishing_datetime'); ?></li>
			<?php endif; ?>

			<li><?php echo $this->form->getLabel('language'); ?>
			<?php echo $this->form->getInput('language'); ?></li>

			<li><?php echo $this->form->getLabel('note'); ?>
			<?php echo $this->form->getInput('note'); ?></li>

			<?php if ($this->item->id) : ?>
				<li><?php echo $this->form->getLabel('id'); ?>
				<?php echo $this->form->getInput('id'); ?></li>
			<?php endif; ?>

			<li><?php echo $this->form->getLabel('module'); ?>
			<?php echo $this->form->getInput('module'); ?>
			<input type="text" size="35" value="<?php if ($this->item->xml) echo ($text = (string) $this->item->xml->name) ? MolajoText::_($text) : $this->item->module;else echo MolajoText::_(COM_MODULES_ERR_XML);?>" class="readonly" readonly="readonly" /></li>

			<li><?php echo $this->form->getLabel('application_id'); ?>
			<input type="text" size="35" value="<?php echo $this->item->application_id == 0 ? MolajoText::_('JSITE') : MolajoText::_('JADMINISTRATOR'); ?>	" class="readonly" readonly="readonly" />
			<?php echo $this->form->getInput('application_id'); ?></li>
			</ul>
			<div class="clr"></div>
			<?php if ($this->item->xml) : ?>
				<?php if ($text = trim($this->item->xml->description)) : ?>
					<label>
						<?php echo MolajoText::_('COM_MODULES_MODULE_DESCRIPTION'); ?>
					</label>
					<span class="readonly mod-desc"><?php echo MolajoText::_($text); ?></span>
				<?php endif; ?>
			<?php else : ?>
				<p class="error"><?php echo MolajoText::_('COM_MODULES_ERR_XML'); ?></p>
			<?php endif; ?>
			<div class="clr"></div>
		</fieldset>
	</div>

	<div class="width-40 fltrt">
	<?php echo MolajoHTML::_('sliders.start', 'module-sliders'); ?>
		<?php echo $this->loadTemplate('options'); ?>
		<div class="clr"></div>
	<?php echo MolajoHTML::_('sliders.end'); ?>
	</div>

	<?php if ($hasContent) : ?>
	<div class="width-60 fltlft">
		<fieldset class="adminform">
			<legend><?php echo MolajoText::_('COM_MODULES_CUSTOM_OUTPUT'); ?></legend>
			<ul class="adminformlist">
			<div class="clr"></div>
			<li><?php echo $this->form->getLabel('content'); ?>
			<div class="clr"></div>
			<?php echo $this->form->getInput('content'); ?></li>
			</ul>
		</fieldset>
	</div>

	<?php endif; ?>
	<?php if ($this->item->application_id == 0) :?>
	<div class="width-60 fltlft">
		<?php echo $this->loadTemplate('assignment'); ?>
	</div>
	<?php endif; ?>

	<div class="clr"></div>

	<div>
		<input type="hidden" name="task" value="" />
		<?php echo MolajoHTML::_('form.token'); ?>
	</div>
</form>
