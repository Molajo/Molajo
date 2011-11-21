<?php
/**
 * @version		$Id: default.php 20899 2011-03-07 20:56:09Z ian $
 * @package		Joomla.Site
 * @subpackage	mod_login
 * @copyright	Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('JPATH_PLATFORM') or die;
MolajoHTML::_('behavior.keepalive');
?>
<?php if ($type == 'logout') : ?>
<form action="<?php echo MolajoRoute::_('index.php', true, $parameters->get('usesecure')); ?>" method="post" id="login-form">
<?php if ($parameters->get('greeting')) : ?>
	<div class="login-greeting">
	<?php if($parameters->get('name') == 0) : {
		echo MolajoText::sprintf('MOD_LOGIN_HINAME', $user->get('name'));
	} else : {
		echo MolajoText::sprintf('MOD_LOGIN_HINAME', $user->get('username'));
	} endif; ?>
	</div>
<?php endif; ?>
	<div class="logout-button">
		<input type="submit" name="Submit" class="button" value="<?php echo MolajoText::_('JLOGOUT'); ?>" />
		<input type="hidden" name="option" value="com_users" />
		<input type="hidden" name="task" value="user.logout" />
		<input type="hidden" name="return" value="<?php echo $return; ?>" />
		<?php echo MolajoHTML::_('form.token'); ?>
	</div>
</form>
<?php else : ?>
<form action="<?php echo MolajoRoute::_('index.php', true, $parameters->get('usesecure')); ?>" method="post" id="login-form" >
	<?php if ($parameters->get('pretext')): ?>
		<div class="pretext">
		<p><?php echo $parameters->get('pretext'); ?></p>
		</div>
	<?php endif; ?>
	<fieldset class="userdata">
	<p id="form-login-username">
		<label for="modlgn-username"><?php echo MolajoText::_('MOD_LOGIN_VALUE_USERNAME') ?></label>
		<input id="modlgn-username" type="text" name="username" class="inputbox"  size="18" />
	</p>
	<p id="form-login-password">
		<label for="modlgn-passwd"><?php echo MolajoText::_('JGLOBAL_PASSWORD') ?></label>
		<input id="modlgn-passwd" type="password" name="password" class="inputbox" size="18"  />
	</p>
	<?php if (JPluginHelper::isEnabled('system', 'remember')) : ?>
	<p id="form-login-remember">
		<label for="modlgn-remember"><?php echo MolajoText::_('MOD_LOGIN_REMEMBER_ME') ?></label>
		<input id="modlgn-remember" type="checkbox" name="remember" class="inputbox" value="yes"/>
	</p>
	<?php endif; ?>
	<input type="submit" name="Submit" class="button" value="<?php echo MolajoText::_('JLOGIN') ?>" />
	<input type="hidden" name="option" value="com_users" />
	<input type="hidden" name="task" value="user.login" />
	<input type="hidden" name="return" value="<?php echo $return; ?>" />
	<?php echo MolajoHTML::_('form.token'); ?>
	</fieldset>
	<ul>
		<li>
			<a href="<?php echo MolajoRoute::_('index.php?option=com_users&view=reset'); ?>">
			<?php echo MolajoText::_('MOD_LOGIN_FORGOT_YOUR_PASSWORD'); ?></a>
		</li>
		<li>
			<a href="<?php echo MolajoRoute::_('index.php?option=com_users&view=remind'); ?>">
			<?php echo MolajoText::_('MOD_LOGIN_FORGOT_YOUR_USERNAME'); ?></a>
		</li>
		<?php
		$usersConfig = JComponentHelper::getParameters('com_users');
		if ($usersConfig->get('allowUserRegistration')) : ?>
		<li>
			<a href="<?php echo MolajoRoute::_('index.php?option=com_users&view=registration'); ?>">
				<?php echo MolajoText::_('MOD_LOGIN_REGISTER'); ?></a>
		</li>
		<?php endif; ?>
	</ul>
	<?php if ($parameters->get('posttext')): ?>
		<div class="posttext">
		<p><?php echo $parameters->get('posttext'); ?></p>
		</div>
	<?php endif; ?>
</form>
<?php endif; ?>
