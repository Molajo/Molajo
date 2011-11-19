<?php
/**
 * @version		$Id: default.php 20724 2011-02-16 08:41:47Z infograf768 $
 * @package		Joomla.Administrator
 * @subpackage	com_config
 * @copyright	Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access
defined('_JEXEC') or die;

$template = MolajoFactory::getApplication()->getTemplate();

// Load the tooltip behavior.
MolajoHTML::_('behavior.tooltip');
MolajoHTML::_('behavior.formvalidation');
?>
<script type="text/javascript">
	Joomla.submitbutton = function(task)
	{
		if (document.formvalidator.isValid(document.id('component-form'))) {
			Joomla.submitform(task, document.getElementById('component-form'));
		}
	}
</script>
<form action="<?php echo MolajoRoute::_('index.php?option=com_config');?>" id="component-form" method="post" name="adminForm" autocomplete="off" class="form-validate">
	<fieldset>
		<div class="fltrt">
			<button type="button" onclick="Joomla.submitform('component.apply', this.form);">
				<?php echo MolajoText::_('JAPPLY');?></button>
			<button type="button" onclick="Joomla.submitform('component.save', this.form);">
				<?php echo MolajoText::_('JSAVE');?></button>
			<button type="button" onclick="window.parent.SqueezeBox.close();">
				<?php echo MolajoText::_('JCANCEL');?></button>
		</div>
		<div class="configuration" >
			<?php echo MolajoText::_($this->component->option.'_configuration') ?>
		</div>
	</fieldset>

	<?php
	echo MolajoHTML::_('tabs.start','config-tabs-'.$this->component->option.'_configuration', array('useCookie'=>1));
		$fieldSets = $this->form->getFieldsets();
		foreach ($fieldSets as $name => $fieldSet) :
			$label = empty($fieldSet->label) ? 'COM_CONFIG_'.$name.'_FIELDSET_LABEL' : $fieldSet->label;
			echo MolajoHTML::_('tabs.panel',MolajoText::_($label), 'publishing-details');
			if (isset($fieldSet->description) && !empty($fieldSet->description)) :
				echo '<p class="tab-description">'.MolajoText::_($fieldSet->description).'</p>';
			endif;
	?>
			<ul class="config-option-list">
			<?php
			foreach ($this->form->getFieldset($name) as $field):
			?>
				<li>
				<?php if (!$field->hidden) : ?>
				<?php echo $field->label; ?>
				<?php endif; ?>
				<?php echo $field->input; ?>
				</li>
			<?php
			endforeach;
			?>
			</ul>


	<div class="clr"></div>
	<?php
		endforeach;
	echo MolajoHTML::_('tabs.end');
	?>
	<div>
		<input type="hidden" name="id" value="<?php echo $this->component->id;?>" />
		<input type="hidden" name="component" value="<?php echo $this->component->option;?>" />
		<input type="hidden" name="task" value="" />
		<?php echo MolajoHTML::_('form.token'); ?>
	</div>
</form>
