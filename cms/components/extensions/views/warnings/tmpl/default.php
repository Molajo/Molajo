<?php
/**
 * @version		$Id: default.php 21020 2011-03-27 06:52:01Z infograf768 $
 * @package		Joomla.Administrator
 * @subpackage	installer
 * @copyright	Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 * * * @since		1.0
 */

// no direct access
defined('_JEXEC') or die;
?>
<form action="<?php echo MolajoRouteHelper::_('index.php?option=installer&view=warnings');?>" method="post" name="adminForm" id="adminForm">
<?php

if (!count($this->messages)) {
	echo '<p class="nowarning">'. MolajoText::_('INSTALLER_MSG_WARNINGS_NONE').'</p>';
} else {
	echo MolajoHTML::_('sliders.start', 'warning-sliders', array('useCookie'=>1));
	foreach($this->messages as $message) {
		echo MolajoHTML::_('sliders.panel', $message['message'], str_replace(' ','', $message['message']));
		echo '<div style="padding: 5px;" >'.$message['description'].'</div>';
	}
	echo MolajoHTML::_('sliders.panel', MolajoText::_('INSTALLER_MSG_WARNINGFURTHERINFO'),'furtherinfo-pane');
	echo '<div style="padding: 5px;" >'. MolajoText::_('INSTALLER_MSG_WARNINGFURTHERINFODESC') .'</div>';
	echo MolajoHTML::_('sliders.end');
}
?>
<div class="clr"> </div>
<div>
	<input type="hidden" name="boxchecked" value="0" />
	<?php echo MolajoHTML::_('form.token'); ?>
</div>
</form>