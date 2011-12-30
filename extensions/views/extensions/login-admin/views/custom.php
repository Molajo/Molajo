<?php
/**
 * @package     Molajo
 * @subpackage  Mojito
 * @copyright   Copyright (C) 2012 Cristina Solana. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die;
//MolajoHTML::_('behavior.keepalive');

$langs = MolajoLoginHelper::getLanguageList();
$return = MolajoLoginHelper::getReturnURI();
?>
<form action="<?php echo MolajoRouteHelper::_('index.php', true, $this->parameters->get('usesecure')); ?>" method="post"
      id="form-login">
    <fieldset class="loginform">
        <label id="" class="" for="login-username"><?php echo MolajoTextHelper::_('JGLOBAL_USERNAME'); ?>
            <input name="username" id="login-username" class="" type="text" size="15"/>
        </label>
        <label id="" class="" for="login-password"><?php echo MolajoTextHelper::_('JGLOBAL_PASSWORD'); ?>
            <input name="password" id="login-password" class="" type="password" size="15"/>
        </label>
        <label id="" class=""
               for="lang"><?php echo MolajoTextHelper::_('VIEW_EXTENSION_ADMINISTER_LOGIN_LANGUAGE'); ?>
            <?php echo $langs; ?>
        </label>
        <?php /* TODO: add forgot username + password */ ?>
        <a href="<?php echo JURI::root(); ?>"><?php echo MolajoTextHelper::_('LOGIN_RETURN_TO_SITE_HOME_PAGE') ?></a>
        <?php /* <a href="#" onclick="document.getElementById('form-login').submit();">
			<?php echo MolajoTextHelper::_('VIEW_EXTENSION_ADMINISTER_LOGIN_LOGIN'); ?></a> */ ?>
        <input type="submit" value="<?php echo MolajoTextHelper::_('VIEW_EXTENSION_ADMINISTER_LOGIN_LOGIN'); ?>"/>
        <input type="hidden" name="option" value="login"/>
        <input type="hidden" name="task" value="login"/>
        <input type="hidden" name="return" value="<?php echo $return; ?>"/>
        <?php echo MolajoHTML::_('form.token'); ?>
    </fieldset>
</form>