<?php
/**
 * @package     Molajo
 * @subpackage  Mojito
 * @copyright   Copyright (C) 2011 Cristina Solana. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die;
//MolajoHTML::_('behavior.keepalive');

$langs	= MolajoLoginHelper::getLanguageList();
$return	= MolajoLoginHelper::getReturnURI();
?>
<form action="<?php echo MolajoRoute::_('index.php', true, $this->params->get('usesecure')); ?>" method="post" id="form-login">
	<fieldset class="loginform">
        <label id="" class="" for="login-username"><?php echo MolajoText::_('JGLOBAL_USERNAME'); ?>
			<input name="username" id="login-username" class="" type="text" size="15" />
		</label>
        <label id="" class="" for="login-password"><?php echo MolajoText::_('JGLOBAL_PASSWORD'); ?>
			<input name="password" id="login-password" class="" type="password" size="15" />
		</label>
        <label id="" class="" for="lang"><?php echo MolajoText::_('LAYOUT_EXTENSION_ADMIN_LOGIN_LANGUAGE'); ?>
        <?php echo $langs; ?>
		</label>
		<?php /* TODO: add forgot username + password */ ?>
		<a href="<?php echo JURI::root(); ?>"><?php echo MolajoText::_('COM_LOGIN_RETURN_TO_SITE_HOME_PAGE') ?></a>
		<?php /* <a href="#" onclick="document.getElementById('form-login').submit();">
			<?php echo MolajoText::_('LAYOUT_EXTENSION_ADMIN_LOGIN_LOGIN'); ?></a> */ ?>
		<input type="submit" value="<?php echo MolajoText::_( 'LAYOUT_EXTENSION_ADMIN_LOGIN_LOGIN' ); ?>" />
		<input type="hidden" name="option" value="com_login" />
		<input type="hidden" name="task" value="login" />
		<input type="hidden" name="return" value="<?php echo $return; ?>" />
		<?php echo MolajoHTML::_('form.token'); ?>
	</fieldset>
</form>