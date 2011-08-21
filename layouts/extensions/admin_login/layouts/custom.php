<?php
/**
 * @package     Molajo
 * @subpackage  Login
 * @copyright   Copyright (C) 2011 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die;

JHtml::_('behavior.keepalive');
$langs	= MolajoLoginHelper::getLanguageList();
$return	= MolajoLoginHelper::getReturnURI();
?>
<header>
	<h1>Molajo Admin CP</h1>
</header>
<form action="<?php echo JRoute::_('index.php', true, $this->params->get('usesecure')); ?>" method="post" id="form-login">
	<fieldset class="loginform">
        <label id="mod-login-username-lbl" for="mod-login-username"><?php echo JText::_('JGLOBAL_USERNAME'); ?>
			<input name="username" id="mod-login-username" type="text" class="inputbox" size="15" />
		</label>
        <label id="mod-login-password-lbl" for="mod-login-password"><?php echo JText::_('JGLOBAL_PASSWORD'); ?>
			<input name="passwd" id="mod-login-password" type="password" class="inputbox" size="15" />
		</label>
        <label id="mod-login-language-lbl" for="lang"><?php echo JText::_('LAYOUT_EXTENSION_ADMIN_LOGIN_LANGUAGE'); ?>
        <?php echo $langs; ?>
		</label>
		<!--
		<a href="#" onclick="document.getElementById('form-login').submit();">
			<?php echo JText::_('LAYOUT_EXTENSION_ADMIN_LOGIN_LOGIN'); ?>
		</a> -->
		<input type="submit" class="hidebtn" value="<?php echo JText::_( 'LAYOUT_EXTENSION_ADMIN_LOGIN_LOGIN' ); ?>" />
		<input type="hidden" name="option" value="com_login" />
		<input type="hidden" name="task" value="login" />
		<input type="hidden" name="return" value="<?php echo $return; ?>" />
		<?php echo JHtml::_('form.token'); ?>
	</fieldset>
</form>