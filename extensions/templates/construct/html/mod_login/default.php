<?php defined('_JEXEC') or die;
/**
* @package		Template Framework for Molajo 1.6
* @author		Joomla Engineering http://joomlaengineering.com
* @copyright	Copyright (C) 2010 Matt Thomas | Joomla Engineering. All rights reserved.
* @license		GNU/GPL v2 or later http://www.gnu.org/licenses/gpl-2.0.html
*/

MolajoHTML::_('behavior.keepalive');
?>
<?php if ($type == 'logout') : ?>
<form action="index.php" method="post" name="form-login" id="form-login">
<?php if ($params->get('greeting')) : ?>
	<div>
	<?php if($params->get('name') == 0) : {
		echo JText::sprintf('MOD_LOGIN_HINAME', $user->get('name'));
	} else : {
		echo JText::sprintf('MOD_LOGIN_HINAME', $user->get('username'));
	} endif; ?>
	</div>
<?php endif; ?>
	<div class="logout-button">
		<input type="submit" name="Submit" class="button" value="<?php echo JText::_('JLOGOUT'); ?>" />
	</div>
	<input type="hidden" name="option" value="com_users" />
	<input type="hidden" name="task" value="user.logout" />
	<input type="hidden" name="return" value="<?php echo $return; ?>" />
</form>
<?php else : ?>
<form action="<?php echo JRoute::_('index.php', true, $params->get('usesecure')); ?>" method="post" name="form-login" id="form-login" >
	<?php echo $params->get('pretext'); ?>
	<fieldset class="input">
		<label id="form-login-username" for="modlgn_username"><?php echo JText::_('MOD_LOGIN_VALUE_USERNAME') ?>
			<input id="modlgn_username" type="text" name="username" class="inputbox"  size="18" />
		</label>
		<label id="form-login-password" for="modlgn_passwd"><?php echo JText::_('JGLOBAL_PASSWORD') ?>
			<input id="modlgn_passwd" type="password" name="password" class="inputbox" size="18"  />
		</label>
	<?php if (JPluginHelper::isEnabled('system', 'remember')) : ?>
		<label id="form-login-remember" for="modlgn_remember"><?php echo JText::_('MOD_LOGIN_REMEMBER_ME') ?>
			<input id="modlgn_remember" type="checkbox" name="remember" class="inputbox" value="yes"/>
		</label>
	<?php endif; ?>
	<input type="submit" name="Submit" class="button" value="<?php echo JText::_('JLOGIN') ?>" />
	<input type="hidden" name="option" value="com_users" />
	<input type="hidden" name="task" value="user.login" />
	<input type="hidden" name="return" value="<?php echo $return; ?>" />
	<?php echo MolajoHTML::_('form.token'); ?>
	</fieldset>
	<ul>
		<li>
			<a href="<?php echo JRoute::_('index.php?option=com_users&view=reset'); ?>">
			<?php echo JText::_('MOD_LOGIN_FORGOT_YOUR_PASSWORD'); ?></a>
		</li>
		<li>
			<a href="<?php echo JRoute::_('index.php?option=com_users&view=remind'); ?>">
			<?php echo JText::_('MOD_LOGIN_FORGOT_YOUR_USERNAME'); ?></a>
		</li>
		<?php
		$usersConfig = JComponentHelper::getParams('com_users');
		if ($usersConfig->get('allowUserRegistration')) : ?>
		<li>
			<a href="<?php echo JRoute::_('index.php?option=com_users&view=registration'); ?>">
				<?php echo JText::_('MOD_LOGIN_REGISTER'); ?></a>
		</li>
		<?php endif; ?>
	</ul>
	<?php echo $params->get('posttext'); ?>
</form>
<?php endif; ?>
