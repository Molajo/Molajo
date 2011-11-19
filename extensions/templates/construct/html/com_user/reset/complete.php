<?php defined('_JEXEC') or die;
/**
* @package		Unified HTML5 Template Framework for Joomla!
* @author		Cristina Solana http://nightshiftcreative.com
* @author		Matt Thomas http://construct-framework.com | http://betweenbrain.com
* @copyright	Copyright (C) 2009 - 2011 Matt Thomas. All rights reserved.
* @license		GNU/GPL v2 or later http://www.gnu.org/licenses/gpl-2.0.html
*/

// Joomla 1.5 only

?>

<section class="reset-complete<?php echo $this->escape($this->params->get('pageclass_sfx')) ?>">
	<h2>
		<?php echo JText::_('Reset your Password'); ?>
	</h2>
	<form action="<?php echo JRoute::_( 'index.php?option=com_user&task=completereset' ); ?>" method="post" class="josForm form-validate">
		<fieldset>
			<p>
				<?php echo JText::_('RESET_PASSWORD_COMPLETE_DESCRIPTION'); ?>
			</p>
			<label for="password1" class="hasTip" title="<?php echo JText::_('RESET_PASSWORD_PASSWORD1_TIP_TITLE'); ?>::<?php echo JText::_('RESET_PASSWORD_PASSWORD1_TIP_TEXT'); ?>">
				<?php echo JText::_('Password'); ?>:
				<input id="password1" name="password1" type="password" class="required validate-password" />
			</label>
			<label for="password2" class="hasTip" title="<?php echo JText::_('RESET_PASSWORD_PASSWORD2_TIP_TITLE'); ?>::<?php echo JText::_('RESET_PASSWORD_PASSWORD2_TIP_TEXT'); ?>">
				<?php echo JText::_('Verify Password'); ?>:
				<input id="password2" name="password2" type="password" class="required validate-password" />
			</label>
		</fieldset>
		<button type="submit" class="validate"><?php echo JText::_('Submit'); ?></button>
		<?php echo JHTML::_( 'form.token' ); ?>
	</form>
</section>