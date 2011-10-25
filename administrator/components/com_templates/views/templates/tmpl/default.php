<?php
/**
 * @version		$Id: default.php 21576 2011-06-19 16:14:23Z chdemko $
 * @package		Joomla.Administrator
 * @subpackage	com_templates
 * @copyright	Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 * * * @since		1.0
 */

// No direct access.
defined('_JEXEC') or die;

// Include the component HTML helpers.
MolajoHTML::addIncludePath(JPATH_COMPONENT.'/helpers/html');
MolajoHTML::_('behavior.tooltip');
MolajoHTML::_('behavior.modal');
MolajoHTML::_('behavior.multiselect');

$user		= MolajoFactory::getUser();
$listOrder	= $this->escape($this->state->get('list.ordering'));
$listDirn	= $this->escape($this->state->get('list.direction'));
?>

<form action="<?php echo MolajoRoute::_('index.php?option=com_templates&view=templates'); ?>" method="post" name="adminForm" id="adminForm">
	<fieldset id="filter-bar">
		<div class="filter-search fltlft">
			<label class="filter-search-lbl" for="filter_search"><?php echo MolajoText::_('JSEARCH_FILTER_LABEL'); ?></label>
			<input type="text" name="filter_search" id="filter_search" value="<?php echo $this->escape($this->state->get('filter.search')); ?>" title="<?php echo MolajoText::_('COM_TEMPLATES_TEMPLATES_FILTER_SEARCH_DESC'); ?>" />
			<button type="submit"><?php echo MolajoText::_('JSEARCH_FILTER_SUBMIT'); ?></button>
			<button type="button" onclick="document.id('filter_search').value='';this.form.submit();"><?php echo MolajoText::_('JSEARCH_FILTER_CLEAR'); ?></button>
		</div>
		<div class="filter-select fltrt">
			<select name="filter_application_id" class="inputbox" onchange="this.form.submit()">
				<option value="*"><?php echo MolajoText::_('JGLOBAL_FILTER_CLIENT'); ?></option>
				<?php echo MolajoHTML::_('select.options', TemplatesHelper::getApplicationOptions(), 'value', 'text', $this->state->get('filter.application_id'));?>
			</select>
		</div>
	</fieldset>
	<div class="clr"> </div>

	<table class="adminlist" id="template-mgr">
		<thead>
			<tr>
				<th class="col1template">
					&#160;
				</th>
				<th>
					<?php echo MolajoHTML::_('grid.sort', 'COM_TEMPLATES_HEADING_TEMPLATE', 'a.element', $listDirn, $listOrder); ?>
				</th>
				<th width="10%">
					<?php echo MolajoHTML::_('grid.sort', 'JCLIENT', 'a.application_id', $listDirn, $listOrder); ?>
				</th>
				<th width="10%" class="center">
					<?php echo MolajoText::_('MOLAJOVERSION'); ?>
				</th>
				<th width="15%">
					<?php echo MolajoText::_('JDATE'); ?>
				</th>
				<th width="25%" >
					<?php echo MolajoText::_('JAUTHOR'); ?>
				</th>
			</tr>
		</thead>
		<tfoot>
			<tr>
				<td colspan="8">
					<?php echo $this->pagination->getListFooter(); ?>
				</td>
			</tr>
		</tfoot>
		<tbody>
		<?php foreach ($this->items as $i => $item) : ?>
			<tr class="row<?php echo $i % 2; ?>">
				<td class="center">
					<?php echo MolajoHTML::_('templates.thumb', $item->element, $item->application_id); ?>
				</td>
				<td class="template-name">
					<a href="<?php echo MolajoRoute::_('index.php?option=com_templates&view=template&id='.(int) $item->extension_id); ?>">
						<?php echo  MolajoText::sprintf( 'COM_TEMPLATES_TEMPLATE_DETAILS', $item->name) ;?></a>
					<p>
					<?php if($this->preview && $item->application_id == '0'): ?>
						<a href="<?php echo JURI::root().'index.php?tp=1&template='.$item->element; ?>" target="_blank">
							<?php echo  MolajoText::sprintf('COM_TEMPLATES_TEMPLATE_PREVIEW'); ?></a>
					<?php elseif ($item->application_id == '1'): ?>
						<?php echo  MolajoText::sprintf('COM_TEMPLATES_TEMPLATE_NO_PREVIEW_ADMIN'); ?>
					<?php else: ?>
						<span class="hasTip" title="<?php echo MolajoText::sprintf('COM_TEMPLATES_TEMPLATE_NO_PREVIEW'); ?>::<?php echo MolajoText::sprintf('COM_TEMPLATES_TEMPLATE_NO_PREVIEW_DESC'); ?>">
							<?php echo  MolajoText::sprintf('COM_TEMPLATES_TEMPLATE_NO_PREVIEW'); ?></span>
					<?php endif; ?>
					</p>
				</td>
				<td class="center">
					<?php echo $item->application_id == 0 ? MolajoText::_('JSITE') : MolajoText::_('JADMINISTRATOR'); ?>
				</td>
				<td class="center">
					<?php echo $this->escape($item->xmldata->get('version')); ?>
				</td>
				<td class="center">
					<?php echo $this->escape($item->xmldata->get('creationDate')); ?>
				</td>
				<td>
					<?php if ($author = $item->xmldata->get('author')) : ?>
						<p><?php echo $this->escape($author); ?></p>
					<?php else : ?>
						&mdash;
					<?php endif; ?>
					<?php if ($email = $item->xmldata->get('authorEmail')) : ?>
						<p><?php echo $this->escape($email); ?></p>
					<?php endif; ?>
					<?php if ($url = $item->xmldata->get('authorUrl')) : ?>
						<p><a href="<?php echo $this->escape($url); ?>">
							<?php echo $this->escape($url); ?></a></p>
					<?php endif; ?>
				</td>
			</tr>
			<?php endforeach; ?>
		</tbody>
	</table>

	<div>
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="boxchecked" value="0" />
		<input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>" />
		<input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>" />
		<?php echo MolajoHTML::_('form.token'); ?>
	</div>
</form>
