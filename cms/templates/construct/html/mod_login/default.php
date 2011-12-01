<?php defined('_JEXEC') or die;
/**
* @package		Unified HTML5 Template Framework for Joomla!
* @author		Cristina Solana http://nightshiftcreative.com
* @author		Matt Thomas http://construct-framework.com | http://betweenbrain.com
* @copyright	Copyright (C) 2009 - 2011 Matt Thomas. All rights reserved.
* @license		GNU/GPL v2 or later http://www.gnu.org/licenses/gpl-2.0.html
*/

if (substr(JVERSION, 0, 3) >= '1.6') {
// Joomla 1.6+ ?>

    <?php JHtml::_('behavior.keepalive');?>

    <?php if ($type == 'logout') : ?>
        <form action="<?php echo JRoute::_('index.php', true, $params->get('usesecure')); ?>" method="post" id="login-form">
        <?php if ($params->get('greeting')) : ?>
            <p class="login-greeting">
            <?php if($params->get('name') == 0) : {
	            echo JText::sprintf('LOGIN_HINAME', $user->get('name'));
            } else : {
	            echo JText::sprintf('LOGIN_HINAME', $user->get('username'));
            } endif; ?>
            </p>
        <?php endif; ?>
	        <input type="submit" name="Submit" class="button logout-button" value="<?php echo JText::_('JLOGOUT'); ?>" />
            <input type="hidden" name="option" value="com_users" />
            <input type="hidden" name="task" value="user.logout" />
            <input type="hidden" name="return" value="<?php echo $return; ?>" />
            <?php echo JHtml::_('form.token'); ?>	    
        </form>
    <?php else : ?>
        <form action="<?php echo JRoute::_('index.php', true, $params->get('usesecure')); ?>" method="post" id="login-form" >
            <?php if ($params->get('pretext')): ?>
	            <p class="pretext"><?php echo $params->get('pretext'); ?></p>
            <?php endif; ?>
            <fieldset class="userdata">

	            <label id="form-login-username" for="modlgn-username"><?php echo JText::_('LOGIN_VALUE_USERNAME') ?>
	                <input id="modlgn-username" type="text" name="username" class="inputbox"  size="18" />
	           </label>
	           
	            <label id="form-login-password" for="modlgn-passwd"><?php echo JText::_('JGLOBAL_PASSWORD') ?>
	                <input id="modlgn-passwd" type="password" name="password" class="inputbox" size="18"  />
	            </label>		    

            <?php if (JPluginHelper::isEnabled('system', 'remember')) : ?>

                <label id="form-login-remember" for="modlgn-remember"><?php echo JText::_('LOGIN_REMEMBER_ME') ?>
                    <input id="modlgn-remember" type="checkbox" name="remember" class="inputbox" value="yes"/>
                </label>		    

            <?php endif; ?>
            <input type="submit" name="Submit" class="button" value="<?php echo JText::_('JLOGIN') ?>" />
            <input type="hidden" name="option" value="com_users" />
            <input type="hidden" name="task" value="user.login" />
            <input type="hidden" name="return" value="<?php echo $return; ?>" />
            <?php echo JHtml::_('form.token'); ?>
            </fieldset>
            <ul>
	            <li>
		            <a href="<?php echo JRoute::_('index.php?option=com_users&view=reset'); ?>">
		            <?php echo JText::_('LOGIN_FORGOT_YOUR_PASSWORD'); ?></a>
	            </li>
	            <li>
		            <a href="<?php echo JRoute::_('index.php?option=com_users&view=remind'); ?>">
		            <?php echo JText::_('LOGIN_FORGOT_YOUR_USERNAME'); ?></a>
	            </li>
	            <?php
	            $usersConfig = JComponentHelper::getParams('com_users');
	            if ($usersConfig->get('allowUserRegistration')) : ?>
	            <li>
		            <a href="<?php echo JRoute::_('index.php?option=com_users&view=registration'); ?>">
			            <?php echo JText::_('LOGIN_REGISTER'); ?></a>
	            </li>
	            <?php endif; ?>
            </ul>
            <?php if ($params->get('posttext')): ?>
	            <p class="posttext"><?php echo $params->get('posttext'); ?></p>
            <?php endif; ?>
        </form>
    <?php endif; ?>

<?php
}
else {
// Joomla 1.5 ?>

<?php
$return = base64_encode(base64_decode($return).'#content');

if ($type == 'logout') : ?>
	<form action="index.php" method="post" id="login-form">
		<fieldset>
			<?php if ($params->get('greeting')) : ?>
			<div class="login-greeting">
					<?php if ($params->get('name')) : {
						echo JText::sprintf( 'HINAME', $user->get('name') );
					} else : {
						echo JText::sprintf( 'HINAME', $user->get('username') );
					} endif; ?>
			</div>
			<?php endif; ?>	
			<button type="submit" name="submit" class="button">
				<?php echo JText::_('BUTTON_LOGOUT'); ?>
			</button>		
		</fieldset>		
		<input type="hidden" name="option" value="com_user">
		<input type="hidden" name="task" value="logout">
		<input type="hidden" name="return" value="<?php echo $return; ?>">		
	</form>
<?php else : ?>
<form action="<?php echo JRoute::_( 'index.php', true, $params->get('usesecure')); ?>" method="post" id="login-form">
	<?php if ($params->get('pretext')) : ?>
		<p class="pretext">
			<?php echo $params->get('pretext'); ?>
		</p>
	<?php endif; ?>
	<fieldset class="userdata">
		<label id="form-login-username" for="mod_login_username">
			<?php echo JText::_('Username'); ?>
			<input name="username" id="mod_login_username" type="text" class="inputbox" alt="<?php echo JText::_('Username'); ?>">
		</label>
		<label id="form-login-password" for="mod_login_password">
			<?php echo JText::_('Password'); ?>
			<input type="password" id="mod_login_password" name="passwd" class="inputbox"  alt="<?php echo JText::_('Password'); ?>">
		</label>
		<label id="form-login-remember" for="mod_login_remember" class="remember">
			<?php echo JText::_('Remember me'); ?>
			<input type="checkbox" name="remember" id="mod_login_remember" class="checkbox" value="yes" alt="<?php echo JText::_('Remember me'); ?>" />
		</label>
		<button type="submit" name="Submit" class="button">
			<?php echo JText::_('BUTTON_LOGIN'); ?>
		</button>
	</fieldset>
	<ul>
		<li>
			<a href="<?php echo JRoute::_('index.php?option=com_user&view=reset#content'); ?>">
				<?php echo JText::_('FORGOT_YOUR_PASSWORD'); ?>
			</a>
		</li>
		<li>
			<a href="<?php echo JRoute::_('index.php?option=com_user&view=remind#content'); ?>">
				<?php echo JText::_('FORGOT_YOUR_USERNAME'); ?>
			</a>
		</li>
		<?php $usersConfig =& JComponentHelper::getParams('com_users'); ?>			
		<?php if ($usersConfig->get('allowUserRegistration')) : ?>
			<li>
				<?php echo JText::_('No account yet?'); ?>
				<a href="<?php echo JRoute::_('index.php?option=com_user&view=register#content'); ?>">
					<?php echo JText::_('Register'); ?>
				</a>
			</li>
		<?php endif; ?>
	</ul>
	<p class="posttext">
		<?php echo $params->get('posttext'); ?>
	</p>
	<input type="hidden" name="option" value="com_user">
	<input type="hidden" name="task" value="login">
	<input type="hidden" name="return" value="<?php echo $return; ?>">
	<?php echo JHTML::_( 'form.token' ); ?>
</form>
<?php endif; ?>
<?php }
