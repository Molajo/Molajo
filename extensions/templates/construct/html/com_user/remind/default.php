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

<section class="remind<?php echo $this->escape($this->params->get('pageclass_sfx')) ?>">

	<?php if($this->params->get('show_page_title',1)) : ?>
		<h2>
			<?php echo $this->escape($this->params->get('page_title')) ?>
		</h2>
	<?php endif; ?>
	
	<form id="user-registration" action="<?php echo JRoute::_( 'index.php?option=com_user&task=remindusername' ); ?>" method="post" class="josForm form-validate">
		<p>
			<?php echo JText::_('REMIND_USERNAME_DESCRIPTION'); ?>
		</p>
		<fieldset>
			<label for="email" class="hasTip" title="<?php echo JText::_('REMIND_USERNAME_EMAIL_TIP_TITLE'); ?>::<?php echo JText::_('REMIND_USERNAME_EMAIL_TIP_TEXT'); ?>">
				<?php echo JText::_('Email Address'); ?>:
				<input id="email" name="email" type="email" class="required validate-email">
			</label>
		</fieldset>
		<button type="submit" class="validate">
			<?php echo JText::_('Submit'); ?>
		</button>
		<?php echo JHTML::_( 'form.token' ); ?>
	</form>
</section>