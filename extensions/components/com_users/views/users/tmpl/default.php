<?php
/**
 * @version		$Id: default.php 21595 2011-06-21 02:51:29Z dextercowley $
 * @package		Joomla.Administrator
 * @subpackage	com_users
 * @copyright	Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access.
defined('_JEXEC') or die;

// Include the component HTML helpers.
MolajoHTML::addIncludePath(JPATH_COMPONENT.'/helpers/html');

// Load the tooltip behavior.
MolajoHTML::_('behavior.tooltip');
MolajoHTML::_('behavior.multiselect');

$canDo 		= UsersHelper::getActions();
$listOrder	= $this->escape($this->state->get('list.ordering'));
$listDirn	= $this->escape($this->state->get('list.direction'));
$loggeduser = MolajoFactory::getUser();
?>

<form action="<?php echo MolajoRouteHelper::_('index.php?option=com_users&view=users');?>" method="post" name="adminForm" id="adminForm">
	<fieldset id="filter-bar">
		<div class="filter-search fltlft">
			<label class="filter-search-lbl" for="filter_search"><?php echo MolajoText::_('COM_USERS_SEARCH_USERS'); ?></label>
			<input type="text" name="filter_search" id="filter_search" value="<?php echo $this->escape($this->state->get('filter.search')); ?>" title="<?php echo MolajoText::_('COM_USERS_SEARCH_USERS'); ?>" />
			<button type="submit"><?php echo MolajoText::_('JSEARCH_FILTER_SUBMIT'); ?></button>
			<button type="button" onclick="document.id('filter_search').value='';this.form.submit();"><?php echo MolajoText::_('JSEARCH_RESET'); ?></button>
		</div>
		<div class="filter-select fltrt">
			<label for="filter_state">
				<?php echo MolajoText::_('COM_USERS_FILTER_LABEL'); ?>
			</label>

			<select name="filter_state" class="inputbox" onchange="this.form.submit()">
				<option value="*"><?php echo MolajoText::_('COM_USERS_FILTER_STATE');?></option>
				<?php echo MolajoHTML::_('select.options', UsersHelper::getStateOptions(), 'value', 'text', $this->state->get('filter.state'));?>
			</select>

			<select name="filter_active" class="inputbox" onchange="this.form.submit()">
				<option value="*"><?php echo MolajoText::_('COM_USERS_FILTER_ACTIVE');?></option>
				<?php echo MolajoHTML::_('select.options', UsersHelper::getActiveOptions(), 'value', 'text', $this->state->get('filter.active'));?>
			</select>

			<select name="filter_group_id" class="inputbox" onchange="this.form.submit()">
				<option value=""><?php echo MolajoText::_('COM_USERS_FILTER_USERGROUP');?></option>
				<?php echo MolajoHTML::_('select.options', UsersHelper::getGroups(), 'value', 'text', $this->state->get('filter.group_id'));?>
			</select>
		</div>
	</fieldset>
	<div class="clr"> </div>

	<table class="adminlist">
		<thead>
			<tr>
				<th width="1%">
					<input type="checkbox" name="checkall-toggle" value="" title="<?php echo MolajoText::_('JGLOBAL_CHECK_ALL'); ?>" onclick="Joomla.checkAll(this)" />
				</th>
				<th class="left">
					<?php echo MolajoHTML::_('grid.sort', 'COM_USERS_HEADING_NAME', 'a.name', $listDirn, $listOrder); ?>
				</th>
				<th class="nowrap" width="10%">
					<?php echo MolajoHTML::_('grid.sort', 'JGLOBAL_USERNAME', 'a.username', $listDirn, $listOrder); ?>
				</th>
				<th class="nowrap" width="5%">
					<?php echo MolajoHTML::_('grid.sort', 'COM_USERS_HEADING_ENABLED', 'a.block', $listDirn, $listOrder); ?>
				</th>
				<th class="nowrap" width="5%">
					<?php echo MolajoHTML::_('grid.sort', 'COM_USERS_HEADING_ACTIVATED', 'a.activated', $listDirn, $listOrder); ?>
				</th>
				<th class="nowrap" width="10%">
					<?php echo MolajoText::_('COM_USERS_HEADING_GROUPS'); ?>
				</th>
				<th class="nowrap" width="15%">
					<?php echo MolajoHTML::_('grid.sort', 'JGLOBAL_EMAIL', 'a.email', $listDirn, $listOrder); ?>
				</th>
				<th class="nowrap" width="10%">
					<?php echo MolajoHTML::_('grid.sort', 'COM_USERS_HEADING_LAST_VISIT_DATE', 'a.last_visit_datetime', $listDirn, $listOrder); ?>
				</th>
				<th class="nowrap" width="10%">
					<?php echo MolajoHTML::_('grid.sort', 'COM_USERS_HEADING_REGISTRATION_DATE', 'a.register_datetime', $listDirn, $listOrder); ?>
				</th>
				<th class="nowrap" width="3%">
					<?php echo MolajoHTML::_('grid.sort', 'JGRID_HEADING_ID', 'a.id', $listDirn, $listOrder); ?>
				</th>
			</tr>
		</thead>
		<tfoot>
			<tr>
				<td colspan="15">
					<?php echo $this->pagination->getListFooter(); ?>
				</td>
			</tr>
		</tfoot>
		<tbody>
		<?php foreach ($this->items as $i => $item) :
			$canEdit	= $canDo->get('core.edit');
			$canChange	= $loggeduser->authorise('core.edit.state',	'com_users');
			// If this group is super admin and this user is not super admin, $canEdit is false
			if ((!$loggeduser->authorise('core.admin')) && JAccess::check($item->id, 'core.admin')) {
				$canEdit	= false;
				$canChange	= false;
			}
		?>
			<tr class="row<?php echo $i % 2; ?>">
				<td class="center">
					<?php if ($canEdit) : ?>
						<?php echo MolajoHTML::_('grid.id', $i, $item->id); ?>
					<?php endif; ?>
				</td>
				<td>
					<?php if ($canEdit) : ?>
					<a href="<?php echo MolajoRouteHelper::_('index.php?option=com_users&task=user.edit&id='.(int) $item->id); ?>" title="<?php echo MolajoText::sprintf('COM_USERS_EDIT_USER', $this->escape($item->name)); ?>">
						<?php echo $this->escape($item->name); ?></a>
					<?php else : ?>
						<?php echo $this->escape($item->name); ?>
					<?php endif; ?>
					<?php if (JDEBUG) : ?>
						<div class="fltrt"><div class="button2-left smallsub"><div class="blank"><a href="<?php echo MolajoRouteHelper::_('index.php?option=com_users&view=debuguser&user_id='.(int) $item->id);?>">
						<?php echo MolajoText::_('COM_USERS_DEBUG_USER');?></a></div></div></div>
					<?php endif; ?>
				</td>
				<td class="center">
					<?php echo $this->escape($item->username); ?>
				</td>
				<td class="center">
					<?php if ($canChange) : ?>
						<?php if ($loggeduser->id != $item->id) : ?>
							<?php echo MolajoHTML::_('grid.boolean', $i, !$item->block, 'users.unblock', 'users.block'); ?>
						<?php else : ?>
							<?php echo MolajoHTML::_('grid.boolean', $i, !$item->block, 'users.block', null); ?>
						<?php endif; ?>
					<?php else : ?>
						<?php echo MolajoText::_($item->block ? 'JNO' : 'JYES'); ?>
					<?php endif; ?>
				</td>
				<td class="center">
					<?php echo MolajoHTML::_('grid.boolean', $i, !$item->activated, 'users.activate', null); ?>
				</td>
				<td class="center">
					<?php if (substr_count($item->group_names,"\n") > 1) : ?>
						<span class="hasTip" title="<?php echo MolajoText::_('COM_USERS_HEADING_GROUPS').'::'.nl2br($item->group_names); ?>"><?php echo MolajoText::_('COM_USERS_USERS_MULTIPLE_GROUPS'); ?></span>
					<?php else : ?>
						<?php echo nl2br($item->group_names); ?>
					<?php endif; ?>
				</td>
				<td class="center">
					<?php echo $this->escape($item->email); ?>
				</td>
				<td class="center">
					<?php if ($item->last_visit_datetime!='0000-00-00 00:00:00'):?>
						<?php echo MolajoHTML::_('date',$item->last_visit_datetime, 'Y-m-d H:i:s'); ?>
					<?php else:?>
						<?php echo MolajoText::_('JNEVER'); ?>
					<?php endif;?>
				</td>
				<td class="center">
					<?php echo MolajoHTML::_('date',$item->register_datetime, 'Y-m-d H:i:s'); ?>
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
