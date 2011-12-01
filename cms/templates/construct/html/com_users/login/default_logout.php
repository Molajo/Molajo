<?php defined('_JEXEC') or die;
/**
 * @version		$Id: default_logout.php 20196 2011-01-09 02:40:25Z ian $
 * @package		Joomla.Site
 * @subpackage	com_users
 * @copyright	Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 * @since		1.5
 */

// Joomla 1.6+ only

?>

<section class="logout<?php echo $this->pageclass_sfx?>">
	<?php if ($this->params->get('show_page_heading')) : ?>
	<h2>
		<?php echo $this->escape($this->params->get('page_heading')); ?>
	</h2>
	<?php endif; ?>

	<?php if ($this->params->get('logout_description') != '' || $this->params->get('logout_image') != '') : ?>
	<p class="logout-description">
	<?php endif ; ?>

		<?php if ($this->params->get('logout_description') != '') : ?>
			<?php echo $this->params->get('logout_description'); ?>
		<?php endif; ?>

		<?php if (($this->params->get('logout_image')!='')) :?>
			<img src="<?php echo $this->escape($this->params->get('logout_image')); ?>" class="logout-image" alt="<?php echo JTEXT::_('USER_LOGOUT_IMAGE_ALT')?>"/>
		<?php endif; ?>

	<?php if ($this->params->get('logout_description') != '' || $this->params->get('logout_image') != '') : ?>
	</p>
	<?php endif ; ?>

	<form action="<?php echo JRoute::_('index.php?option=com_users&task=user.logout'); ?>" method="post">
			<button type="submit" class="button"><?php echo JText::_('JLOGOUT'); ?></button>
			<input type="hidden" name="return" value="<?php echo base64_encode($this->params->get('logout_redirect_url',$this->form->getValue('return'))); ?>" />
			<?php echo JHtml::_('form.token'); ?>
	</form>
</section>