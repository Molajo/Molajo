<?php defined('_JEXEC') or die;
/**
 * @package        Unified HTML5 Template Framework for Joomla!
 * @author        Cristina Solana http://nightshiftcreative.com
 * @author        Matt Thomas http://construct-framework.com | http://betweenbrain.com
 * @copyright    Copyright (C) 2009 - 2011 Matt Thomas. All rights reserved.
 * @license        GNU/GPL v2 or later http://www.gnu.org/licenses/gpl-2.0.html
 */

// Joomla 1.6+ only

JHtml::_('behavior.keepalive');
?>

<section class="login<?php echo $this->pageclass_sfx?>">
    <?php if ($this->params->get('show_page_heading')) : ?>
    <h2>
        <?php echo $this->escape($this->params->get('page_heading')); ?>
    </h2>
    <?php endif; ?>

    <?php if ($this->params->get('login_description') != '' || $this->params->get('login_image') != '') : ?>
	<p class="login-description">
	<?php endif; ?>

    <?php if ($this->params->get('login_description') != '') : ?>
    <?php echo $this->params->get('login_description'); ?>
    <?php endif; ?>

    <?php if (($this->params->get('login_image') != '')) : ?>
    <img src="<?php echo $this->escape($this->params->get('login_image')); ?>" class="login-image"
         alt="<?php echo JTEXT::_('USER_LOGIN_IMAGE_ALT')?>"/>
    <?php endif; ?>

    <?php if (($this->params->get('login_description') != '') || $this->params->get('login_image') != '') : ?>
	</p>
	<?php endif; ?>

    <form action="<?php echo JRoute::_('index.php?option=com_users&task=user.login'); ?>" method="post">
        <fieldset>
            <?php foreach ($this->form->getFieldset('credentials') as $field): ?>
            <?php if (!$field->hidden): ?>
                <p class="login-fields"><?php echo $field->label; ?>
                    <?php echo $field->input; ?></p>
                <?php endif; ?>
            <?php endforeach; ?>
            <button type="submit" class="button"><?php echo JText::_('JLOGIN'); ?></button>
            <input type="hidden" name="return"
                   value="<?php echo base64_encode($this->params->get('login_redirect_url', $this->form->getValue('return'))); ?>"/>
            <?php echo JHtml::_('form.token'); ?>
        </fieldset>
    </form>
</section>
<section>
    <ul>
        <li>
            <a href="<?php echo JRoute::_('index.php?option=com_users&view=reset'); ?>">
                <?php echo JText::_('USERS_LOGIN_RESET'); ?></a>
        </li>
        <li>
            <a href="<?php echo JRoute::_('index.php?option=com_users&view=remind'); ?>">
                <?php echo JText::_('USERS_LOGIN_REMIND'); ?></a>
        </li>
        <?php
                $usersConfig = JComponentHelper::getParams('com_users');
        if ($usersConfig->get('allowUserRegistration')) : ?>
            <li>
                <a href="<?php echo JRoute::_('index.php?option=com_users&view=registration'); ?>">
                    <?php echo JText::_('USERS_LOGIN_REGISTER'); ?></a>
            </li>
            <?php endif; ?>
    </ul>
</section>