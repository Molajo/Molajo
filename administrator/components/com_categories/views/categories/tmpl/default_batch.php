<?php
/**
 * @version		$Id: default_batch.php 21663 2011-06-23 13:51:35Z chdemko $
 * @package		Joomla.Administrator
 * @subpackage	com_categories
 * @copyright	Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;

$options = array(
	JHtml::_('select.option', 'c', MolajoText::_('MOLAJO_HTML_BATCH_COPY')),
	JHtml::_('select.option', 'm', MolajoText::_('MOLAJO_HTML_BATCH_MOVE'))
);
$published	= $this->state->get('filter.published');
$extension	= $this->escape($this->state->get('filter.extension'));
?>
<fieldset class="batch">
	<legend><?php echo MolajoText::_('COM_CATEGORIES_BATCH_OPTIONS');?></legend>
	<?php echo JHtml::_('batch.access');?>

	<?php if ($published >= 0) : ?>
		<label id="batch-choose-action-lbl" for="batch-category-id">
			<?php echo MolajoText::_('COM_CATEGORIES_BATCH_CATEGORY_LABEL'); ?>
		</label>
		<select name="batch[category_id]" class="inputbox" id="batch-category-id">
			<option value=""><?php echo MolajoText::_('JSELECT') ?></option>
			<?php echo JHtml::_('select.options', JHtml::_('category.categories', $extension, array('published' => $published)));?>
		</select>
		<?php echo JHtml::_( 'select.radiolist', $options, 'batch[move_copy]', '', 'value', 'text', 'm'); ?>
	<?php endif; ?>

	<button type="submit" onclick="submitbutton('category.batch');">
		<?php echo MolajoText::_('JGLOBAL_BATCH_PROCESS'); ?>
	</button>
	<button type="button" onclick="document.id('batch-category-id').value='';document.id('batch-access').value=''">
		<?php echo MolajoText::_('JSEARCH_FILTER_CLEAR'); ?>
	</button>
</fieldset>