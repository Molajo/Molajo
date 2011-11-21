<?php
/**
 * @version		$Id: modal.php 21020 2011-03-27 06:52:01Z infograf768 $
 * @package		Joomla.Administrator
 * @subpackage	com_articles
 * @copyright	Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('MOLAJO') or die;

$function	= JRequest::getCmd('function', 'jSelect');

$this->escape($this->state->get('list.ordering'))	= $this->escape($this->state->get('list.ordering'));
$this->escape($this->state->get('list.direction'))	= $this->escape($this->state->get('list.direction'));
?>
<form action="<?php echo MolajoRouteHelper::_('index.php?option='.$this->request['option'].'&view='.$this->state->get('request.view').$function); ?>" method="post" name="adminForm" id="adminForm">
	<fieldset class="filter clearfix">
		<div class="left">
			<label for="filter_search">
				<?php echo MolajoText::_('JSearch_Filter_Label'); ?>
			</label>
			<input type="text" name="filter_search" id="filter_search" value="<?php echo $this->escape($this->queryState->get('filter.search')); ?>" size="30" title="<?php echo MolajoText::_('MOLAJO_FILTER_SEARCH_DESC'); ?>" />

			<button type="submit">
				<?php echo MolajoText::_('JSEARCH_FILTER_SUBMIT'); ?></button>
			<button type="button" onclick="document.id('filter_search').value='';this.form.submit();">
				<?php echo MolajoText::_('JSEARCH_FILTER_CLEAR'); ?></button>
		</div>

		<div class="right">
			<select name="filter_published" class="inputbox" onchange="this.form.submit()">
				<option value=""><?php echo MolajoText::_('JOPTION_SELECT_PUBLISHED');?></option>
				<?php echo MolajoHTML::_('select.options', MolajoHTML::_('jgrid.publishedOptions'), 'value', 'text', $this->queryState->get('filter.published'), true);?>
			</select>

			<select name="filter_category_id" class="inputbox" onchange="this.form.submit()">
				<option value=""><?php echo MolajoText::_('JOPTION_SELECT_CATEGORY');?></option>
				<?php echo MolajoHTML::_('select.options', MolajoHTML::_('category.options', 'com_articles'), 'value', 'text', $this->queryState->get('filter.category_id'));?>
			</select>

		</div>
	</fieldset>

	<table class="adminlist">
		<thead>
			<tr>
				<th class="title">
					<?php echo MolajoHTML::_('grid.sort', 'JGLOBAL_TITLE', 'a.title', $this->escape($this->state->get('list.direction')), $this->escape($this->state->get('list.ordering'))); ?>
				</th>
				<th width="20%">
					<?php echo MolajoHTML::_('grid.sort', 'JCATEGORY', 'a.catid', $this->escape($this->state->get('list.direction')), $this->escape($this->state->get('list.ordering'))); ?>
				</th>
				<th width="5%">
					<?php echo MolajoHTML::_('grid.sort',  'JDATE', 'a.created', $this->escape($this->state->get('list.direction')), $this->escape($this->state->get('list.ordering'))); ?>
				</th>
				<th width="5%">
					<?php echo MolajoHTML::_('grid.sort',  'JAUTHOR', 'a.author', $this->escape($this->state->get('list.direction')), $this->escape($this->state->get('list.ordering'))); ?>
				</th>
				<th width="1%" class="nowrap">
					<?php echo MolajoHTML::_('grid.sort', 'JGRID_HEADING_ID', 'a.id', $this->escape($this->state->get('list.direction')), $this->escape($this->state->get('list.ordering'))); ?>
				</th>
			</tr>
		</thead>
		<tfoot>
			<tr>
				<td colspan="15">
					<?php //echo $this->pagination->getListFooter(); ?>
				</td>
			</tr>
		</tfoot>
		<tbody>
		<?php foreach ($this->recordset as $i => $item) : ?>
			<tr class="row<?php echo $i % 2; ?>">
				<td>
					<a class="pointer" onclick="if (window.parent) window.parent.<?php echo $function;?>('<?php echo $item->id; ?>', '<?php echo $this->escape(addslashes($item->title)); ?>', '<?php echo $this->escape($item->catid); ?>');">
						<?php echo $this->escape($item->title); ?></a>
				</td>
				<td class="center">
					<?php echo $this->escape($item->category_title); ?>
				</td>
				<td class="center nowrap">
					<?php echo MolajoHTML::_('date',$item->created, MolajoText::_('DATE_FORMAT_LC4')); ?>
				</td>
				<td class="center">
					<?php echo $this->escape($item->author); ?>
				</td>
				<td class="center">
					<?php echo (int) $item->id; ?>
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
