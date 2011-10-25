<?php
/**
 * @version		$Id: default_params.php 20214 2011-01-09 20:25:57Z chdemko $
 * @package		Joomla.Site
 * @subpackage	com_users
 * @copyright	Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 * @since		1.6
 */
defined('_JEXEC') or die;

JLoader::register('MolajoHTMLUsers', JPATH_COMPONENT . '/helpers/html/users.php');
MolajoHTML::register('users.spacer', array('MolajoHTMLUsers','spacer'));
MolajoHTML::register('users.helpsite', array('MolajoHTMLUsers','helpsite'));
MolajoHTML::register('users.templatestyle', array('MolajoHTMLUsers','templatestyle'));
MolajoHTML::register('users.admin_language', array('MolajoHTMLUsers','admin_language'));
MolajoHTML::register('users.language', array('MolajoHTMLUsers','language'));
MolajoHTML::register('users.editor', array('MolajoHTMLUsers','editor'));

?>
<?php $fields = $this->form->getFieldset('params'); ?>
<?php if (count($fields)): ?>
<fieldset id="users-profile-custom">
	<legend><?php echo JText::_('COM_USERS_SETTINGS_FIELDSET_LABEL'); ?></legend>
	<dl>
	<?php foreach ($fields as $field):
		if (!$field->hidden) :?>
		<dt><?php echo $field->title; ?></dt>
		<dd>
			<?php if (MolajoHTML::isRegistered('users.'.$field->id)):?>
				<?php echo MolajoHTML::_('users.'.$field->id, $field->value);?>
			<?php elseif (MolajoHTML::isRegistered('users.'.$field->fieldname)):?>
				<?php echo MolajoHTML::_('users.'.$field->fieldname, $field->value);?>
			<?php elseif (MolajoHTML::isRegistered('users.'.$field->type)):?>
				<?php echo MolajoHTML::_('users.'.$field->type, $field->value);?>
			<?php else:?>
				<?php echo MolajoHTML::_('users.value', $field->value);?>
			<?php endif;?>
		</dd>
		<?php endif;?>
	<?php endforeach;?>
	</dl>
</fieldset>
<?php endif;?>

