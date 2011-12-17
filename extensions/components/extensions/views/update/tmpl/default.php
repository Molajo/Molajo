<?php
/**
 * @version        $Id: default.php 21595 2011-06-21 02:51:29Z dextercowley $
 * @package        Joomla.Administrator
 * @subpackage    installer
 * @copyright    Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license        GNU General Public License version 2 or later; see LICENSE.txt
 * * * @since        1.0
 */

// no direct access
defined('_JEXEC') or die;

MolajoHTML::_('behavior.multiselect');

$listOrder = $this->escape($this->state->get('list.ordering'));
$listDirn = $this->escape($this->state->get('list.direction'));
?>
<form action="<?php echo MolajoRouteHelper::_('index.php?option=installer&view=update');?>" method="post"
      name="adminForm" id="adminForm">
    <?php if ($this->showMessage) : ?>
    <?php echo $this->loadTemplate('message'); ?>
    <?php endif; ?>

    <?php if ($this->ftp) : ?>
    <?php echo $this->loadTemplate('ftp'); ?>
    <?php endif; ?>

    <?php if (count($this->items)) : ?>
    <table class="adminlist" cellspacing="1">
        <thead>
        <tr>
            <th width="20"><input type="checkbox" name="checkall-toggle" value=""
                                  title="<?php echo MolajoTextHelper::_('JGLOBAL_CHECK_ALL'); ?>"
                                  onclick="Joomla.checkAll(this)"/></th>
            <th class="nowrap"><?php echo MolajoHTML::_('grid.sort', 'INSTALLER_HEADING_NAME', 'name', $listDirn, $listOrder); ?></th>
            <th class="nowrap"><?php echo MolajoHTML::_('grid.sort', 'INSTALLER_HEADING_INSTALLTYPE', 'extension_id', $listDirn, $listOrder); ?></th>
            <th><?php echo MolajoHTML::_('grid.sort', 'INSTALLER_HEADING_TYPE', 'type', $listDirn, $listOrder); ?></th>
            <th width="10%" class="center"><?php echo MolajoTextHelper::_('MOLAJOVERSION'); ?></th>
            <th><?php echo MolajoHTML::_('grid.sort', 'INSTALLER_HEADING_FOLDER', 'folder', $listDirn, $listOrder); ?></th>
            <th><?php echo MolajoHTML::_('grid.sort', 'INSTALLER_HEADING_CLIENT', 'application_id', $listDirn, $listOrder); ?></th>
            <th width="25%"><?php echo MolajoTextHelper::_('INSTALLER_HEADING_DETAILSURL'); ?></th>
        </tr>
        </thead>
        <tfoot>
        <tr>
            <td colspan="9"><?php echo $this->pagination->getListFooter(); ?></td>
        </tr>
        </tfoot>
        <tbody>
            <?php foreach ($this->items as $i => $item): ?>
        <tr class="row<?php echo $i % 2; ?>">
            <td><?php echo MolajoHTML::_('grid.id', $i, $item->update_id); ?></td>
            <td>
					<span class="editlinktip hasTip"
                          title="<?php echo MolajoTextHelper::_('JGLOBAL_DESCRIPTION');?>::<?php echo $item->description
                                  ? $item->description : MolajoTextHelper::_('INSTALLER_MSG_UPDATE_NODESC'); ?>">
					<?php echo $item->name; ?>
					</span>
            </td>
            <td class="center">
                <?php echo $item->extension_id ? MolajoTextHelper::_('INSTALLER_MSG_UPDATE_UPDATE')
                    : MolajoTextHelper::_('INSTALLER_NEW_INSTALL') ?>
            </td>
            <td><?php echo MolajoTextHelper::_('INSTALLER_TYPE_' . $item->type) ?></td>
            <td class="center"><?php echo $item->version ?></td>
            <td class="center"><?php echo @$item->folder != '' ? $item->folder
                    : MolajoTextHelper::_('INSTALLER_TYPE_NONAPPLICABLE'); ?></td>
            <td class="center"><?php echo @$item->application != ''
                    ? MolajoTextHelper::_('INSTALLER_TYPE_' . $item->application)
                    : MolajoTextHelper::_('INSTALLER_TYPE_NONAPPLICABLE'); ?></td>
            <td><?php echo $item->details_url ?></td>
        </tr>
            <?php endforeach;?>
        </tbody>
    </table>
    <?php else : ?>
    <p class="nowarning"><?php echo MolajoTextHelper::_('INSTALLER_MSG_UPDATE_NOUPDATES'); ?></p>
    <?php endif; ?>

    <div>
        <input type="hidden" name="task" value=""/>
        <input type="hidden" name="boxchecked" value="0"/>
        <input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>"/>
        <input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>"/>
        <?php echo MolajoHTML::_('form.token'); ?>
    </div>
</form>
