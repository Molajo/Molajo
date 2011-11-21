<?php
/**
 * @version		$Id: default.php 21576 2011-06-19 16:14:23Z chdemko $
 * @package		Joomla.Administrator
 * @subpackage	com_templates
 * @copyright	Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access.
defined('_JEXEC') or die;

// Include the component HTML helpers.
MolajoHTML::addIncludePath(JPATH_COMPONENT.'/helpers/html');
MolajoHTML::_('behavior.tooltip');
MolajoHTML::_('behavior.multiselect');

$user		= MolajoFactory::getUser();
$listOrder	= $this->escape($this->state->get('list.ordering'));
$listDirn	= $this->escape($this->state->get('list.direction'));
?>
<form action="<?php echo MolajoRouteHelper::_('index.php?option=com_templates&view=styles'); ?>" method="post" name="adminForm" id="adminForm">
	<fieldset id="filter-bar">
		<div class="filter-search fltlft">
			<label class="filter-search-lbl" for="filter_search"><?php echo MolajoText::_('JSEARCH_FILTER_LABEL'); ?></label>
			<input type="text" name="filter_search" id="filter_search" value="<?php echo $this->escape($this->state->get('filter.search')); ?>" title="<?php echo MolajoText::_('COM_TEMPLATES_STYLES_FILTER_SEARCH_DESC'); ?>" />
			<button type="submit"><?php echo MolajoText::_('JSEARCH_FILTER_SUBMIT'); ?></button>
			<button type="button" onclick="document.id('filter_search').value='';this.form.submit();"><?php echo MolajoText::_('JSEARCH_FILTER_CLEAR'); ?></button>
		</div>
		<div class="filter-select fltrt">
			<select name="filter_template" class="inputbox" onchange="this.form.submit()">
				<option value="0"><?php echo MolajoText::_('COM_TEMPLATES_FILTER_TEMPLATE'); ?></option>
				<?php echo MolajoHTML::_('select.options', TemplatesHelper::getTemplateOptions($this->state->get('filter.application_id')), 'value', 'text', $this->state->get('filter.template'));?>
			</select>
		</div>
		<div class="filter-select fltrt">
			<select name="filter_application_id" class="inputbox" onchange="this.form.submit()">
				<option value="*"><?php echo MolajoText::_('JGLOBAL_FILTER_CLIENT'); ?></option>
				<?php echo MolajoHTML::_('select.options', TemplatesHelper::getAppOptions(), 'value', 'text', $this->state->get('filter.application_id'));?>
			</select>
		</div>
	</fieldset>
	<div class="clr"> </div>

	<table class="adminlist">
		<thead>
			<tr>
				<th width="5">
					&#160;
				</th>
				<th>
					<?php echo MolajoHTML::_('grid.sort', 'COM_TEMPLATES_HEADING_STYLE', 'a.title', $listDirn, $listOrder); ?>
				</th>
				<th width="10%">
					<?php echo MolajoHTML::_('grid.sort', 'JCLIENT', 'a.application_id', $listDirn, $listOrder); ?>
				</th>
				<th>
					<?php echo MolajoHTML::_('grid.sort', 'COM_TEMPLATES_HEADING_TEMPLATE', 'a.template', $listDirn, $listOrder); ?>
				</th>
				<th width="5%">
					<?php echo MolajoHTML::_('grid.sort', 'COM_TEMPLATES_HEADING_DEFAULT', 'a.home', $listDirn, $listOrder); ?>
				</th>
				<th width="5%">
					<?php echo MolajoText::_('COM_TEMPLATES_HEADING_ASSIGNED'); ?>
				</th>
				<th width="1%" class="nowrap">
					<?php echo MolajoHTML::_('grid.sort', 'JGRID_HEADING_ID', 'a.id', $listDirn, $listOrder); ?>
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
			<?php foreach ($this->items as $i => $item) :
				$canCreate	= $user->authorise('core.create',		'com_templates');
				$canEdit	= $user->authorise('core.edit',			'com_templates');
				$canChange	= $user->authorise('core.edit.state',	'com_templates');
			?>
			<tr class="row<?php echo $i % 2; ?>">
				<td width="1%" class="center">
					<?php echo MolajoHTML::_('grid.id', $i, $item->id); ?>
				</td>

				<td>
					<?php if ($canEdit) : ?>
					<a href="<?php echo MolajoRouteHelper::_('index.php?option=com_templates&task=style.edit&id='.(int) $item->id); ?>">
						<?php echo $this->escape($item->title);?></a>
					<?php else : ?>
						<?php echo $this->escape($item->title);?>
					<?php endif; ?>
				</td>
				<td class="center">
					<?php echo $item->application_id == 0 ? MolajoText::_('JSITE') : MolajoText::_('JADMINISTRATOR'); ?>
				</td>
				<td>
					<label for="cb<?php echo $i;?>">
						<?php echo $this->escape($item->template);?>
					</label>
				</td>
				<td class="center">
					<?php if ($item->home=='0' || $item->home=='1'):?>
						<?php echo MolajoHTML::_('jgrid.isdefault', $item->home!='0', $i, 'styles.', $canChange && $item->home!='1');?>
					<?php elseif ($canChange):?>
						<a href="<?php echo MolajoRouteHelper::_('index.php?option=com_templates&task=styles.unsetDefault&cid[]='.$item->id.'&'.JUtility::getToken().'=1');?>">
							<?php echo MolajoHTML::_('image', 'mod_languages/'.$item->image.'.gif', $item->language_title, array('title'=>MolajoText::sprintf('COM_TEMPLATES_GRID_UNSET_LANGUAGE', $item->language_title)), true);?>
						</a>
					<?php else:?>
						<?php echo MolajoHTML::_('image', 'mod_languages/'.$item->image.'.gif', $item->language_title, array('title'=>$item->language_title), true);?>
					<?php endif;?>
				</td>
				<td class="center">
					<?php if ($item->assigned > 0) : ?>
							<?php echo MolajoHTML::_('image','admin/tick.png', MolajoText::plural('COM_TEMPLATES_ASSIGNED',$item->assigned), array('title'=>MolajoText::plural('COM_TEMPLATES_ASSIGNED',$item->assigned)), true); ?>
					<?php else : ?>
							&#160;
					<?php endif; ?>
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
