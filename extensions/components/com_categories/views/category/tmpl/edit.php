<?php
/**
 * @version		$Id: edit.php 21503 2011-06-09 22:58:13Z dextercowley $
 * @package		Joomla.Administrator
 * @subpackage	com_categories
 * @copyright	Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;

// Include the component HTML helpers.
MolajoHTML::addIncludePath(JPATH_COMPONENT.'/helpers/html');

// Load the tooltip behavior.
MolajoHTML::_('behavior.tooltip');
MolajoHTML::_('behavior.formvalidation');
MolajoHTML::_('behavior.keepalive');
?>

<script type="text/javascript">
	Joomla.submitbutton = function(task)
	{
		if (task == 'category.cancel' || document.formvalidator.isValid(document.id('item-form'))) {
			<?php echo $this->form->getField('description')->save(); ?>
			Joomla.submitform(task, document.getElementById('item-form'));
		} else {
			alert('<?php echo $this->escape(MolajoText::_('JGLOBAL_VALIDATION_FORM_FAILED'));?>');
		}
	}
</script>

<form action="<?php echo MolajoRouteHelper::_('index.php?option=com_categories&extension='.JRequest::getCmd('extension', 'com_articles').'&layout=edit&id='.(int) $this->item->id); ?>" method="post" name="adminForm" id="item-form" class="form-validate">
	<div class="width-60 fltlft">
		<fieldset class="adminform">
			<legend><?php echo MolajoText::_('COM_CATEGORIES_FIELDSET_DETAILS');?></legend>
			<ul class="adminformlist">
				<li><?php echo $this->form->getLabel('title'); ?>
				<?php echo $this->form->getInput('title'); ?></li>

				<li><?php echo $this->form->getLabel('alias'); ?>
				<?php echo $this->form->getInput('alias'); ?></li>

				<li><?php echo $this->form->getLabel('extension'); ?>
				<?php echo $this->form->getInput('extension'); ?></li>

				<li><?php echo $this->form->getLabel('parent_id'); ?>
				<?php echo $this->form->getInput('parent_id'); ?></li>

				<li><?php echo $this->form->getLabel('published'); ?>
				<?php echo $this->form->getInput('published'); ?></li>

				<li><?php echo $this->form->getLabel('access'); ?>
				<?php echo $this->form->getInput('access'); ?></li>

				<?php if ($this->canDo->get('core.admin')): ?>
					<li><span class="faux-label"><?php echo MolajoText::_('JGLOBAL_ACTION_PERMISSIONS_LABEL'); ?></span>
					<div class="button2-left"><div class="blank">
		      			<button type="button" onclick="document.location.href='#access-rules';">
		      			<?php echo MolajoText::_('JGLOBAL_PERMISSIONS_ANCHOR'); ?></button>
		      		</div></div>
		    		</li>
				<?php endif; ?>

				<li><?php echo $this->form->getLabel('language'); ?>
				<?php echo $this->form->getInput('language'); ?></li>

				<li><?php echo $this->form->getLabel('id'); ?>
				<?php echo $this->form->getInput('id'); ?></li>
			</ul>
			<div class="clr"></div>
			<?php echo $this->form->getLabel('description'); ?>
			<div class="clr"></div>
			<?php echo $this->form->getInput('description'); ?>
		</fieldset>
	</div>

	<div class="width-40 fltrt">

		<?php echo MolajoHTML::_('sliders.start','categories-sliders-'.$this->item->id, array('useCookie'=>1)); ?>
			<?php echo $this->loadTemplate('options'); ?>
			<div class="clr"></div>

			<?php echo MolajoHTML::_('sliders.panel',MolajoText::_('JGLOBAL_FIELDSET_METADATA_OPTIONS'), 'meta-options'); ?>
			<fieldset class="panelform">
				<?php echo $this->loadTemplate('metadata'); ?>
			</fieldset>

		<?php echo MolajoHTML::_('sliders.end'); ?>
	</div>
	<div class="clr"></div>

	<?php if ($this->canDo->get('core.admin')): ?>
		<div  class="width-100 fltlft">

			<?php echo MolajoHTML::_('sliders.start','permissions-sliders-'.$this->item->id, array('useCookie'=>1)); ?>

			<?php echo MolajoHTML::_('sliders.panel',MolajoText::_('COM_CATEGORIES_FIELDSET_RULES'), 'access-rules'); ?>
			<fieldset class="panelform">
				<?php echo $this->form->getLabel('rules'); ?>
				<?php echo $this->form->getInput('rules'); ?>
			</fieldset>

			<?php echo MolajoHTML::_('sliders.end'); ?>
		</div>
	<?php endif; ?>
	<div>
		<input type="hidden" name="task" value="" />
		<?php echo MolajoHTML::_('form.token'); ?>
	</div>
</form>
