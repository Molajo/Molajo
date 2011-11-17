<?php
/**
 * @version		$Id: default_batch.php 21447 2011-06-04 17:39:55Z dextercowley $
 * @package		Joomla.Administrator
 * @subpackage	com_menus
 * @copyright	Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;

$options = array(
	MolajoHTML::_('select.option', 'c', MolajoText::_('MOLAJO_HTML_BATCH_COPY')),
	MolajoHTML::_('select.option', 'm', MolajoText::_('MOLAJO_HTML_BATCH_MOVE'))
);
$published = $this->state->get('filter.published');
?>
<fieldset class="batch">
	<legend><?php echo MolajoText::_('MENU_BATCH_OPTIONS');?></legend>
	<?php echo MolajoHTML::_('batch.access');?>

	<?php if ($published >= 0) : ?>
		<label id="batch-choose-action-lbl" for="batch-choose-action">
			<?php echo MolajoText::_('MENU_BATCH_MENU_LABEL'); ?>
		</label>
		<fieldset id="batch-choose-action" class="combo">
			<select name="batch[menu_id]" class="inputbox" id="batch-menu-id">
				<option value=""><?php echo MolajoText::_('JSELECT') ?></option>
				<?php echo MolajoHTML::_('select.options', MolajoHTML::_('menu.menuitems', array('published' => $published)));?>
			</select>
			<?php echo MolajoHTML::_( 'select.radiolist', $options, 'batch[move_copy]', '', 'value', 'text', 'm'); ?>
		</fieldset>
	<?php endif; ?>
	<button type="submit" onclick="Joomla.submitbutton('item.batch');">
		<?php echo MolajoText::_('JGLOBAL_BATCH_PROCESS'); ?>
	</button>
	<button type="button" onclick="document.id('batch-menu-id').value='';document.id('batch-access').value=''">
		<?php echo MolajoText::_('JSEARCH_FILTER_CLEAR'); ?>
	</button>
</fieldset>
