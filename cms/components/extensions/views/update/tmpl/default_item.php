<?php
/**
 * @version		$Id: default_item.php 20196 2011-01-09 02:40:25Z ian $
 * @package		Joomla.Administrator
 * @subpackage	installer
 * @copyright	Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 * * * @since		1.0
 */

// no direct access
defined('_JEXEC') or die;
?>
<tr class="<?php echo "row".$this->item->index % 2; ?>" >
	<td><?php echo $this->pagination->getRowOffset($this->item->index); ?></td>
	<td>
		<input type="checkbox" id="cb<?php echo $this->item->index;?>" name="uid[]" value="<?php echo $this->item->update_id; ?>" onclick="isChecked(this.checked);" />
		<span class="editlinktip hasTip" title="<?php echo MolajoText::_('INSTALLER_TIP_UPDATE_DESCRIPTION');?>::<?php echo $this->item->description ? $this->item->description : MolajoText::_('INSTALLER_MSG_UPDATE_NODESC'); ?>">
		<?php echo $this->item->name; ?>
		</span>
	</td>
	<td class="center">
		<?php echo $this->item->extension_id ? MolajoText::_('INSTALLER_MSG_UPDATE_UPDATE') : MolajoText::_('INSTALLER_MSG_UPDATE_NEW') ?>
	</td>
	<td>
		<?php echo MolajoText::_($this->item->type) ?>
	</td>
	<td class="center">
		<?php echo $this->item->version ?>
	</td>
	<td class="center"><?php echo @$this->item->folder != '' ? $this->item->folder : 'N/A'; ?></td>
	<td class="center"><?php echo @$this->item->application != '' ? MolajoText::_($this->item->application) : 'N/A'; ?></td>
	<td>
		<?php echo $this->item->details_url ?>
	</td>
</tr>