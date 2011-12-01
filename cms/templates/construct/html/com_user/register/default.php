<?php defined('_JEXEC') or die;
/**
 * @package        Unified HTML5 Template Framework for Joomla!
 * @author        Cristina Solana http://nightshiftcreative.com
 * @author        Matt Thomas http://construct-framework.com | http://betweenbrain.com
 * @copyright    Copyright (C) 2009 - 2011 Matt Thomas. All rights reserved.
 * @license        GNU/GPL v2 or later http://www.gnu.org/licenses/gpl-2.0.html
 */

// Joomla 1.5 only

?>

<section class="registration<?php echo $this->escape($this->params->get('pageclass_sfx')) ?>">

    <?php if ($this->params->get('show_page_title', 1)) : ?>
    <header>
        <h2>
            <?php echo $this->escape($this->params->get('page_title')) ?>
        </h2>
    </header>
    <?php endif; ?>

    <script type="text/javascript">
        Window.onDomReady(function() {
            document.formvalidator.setHandler('passverify', function (value) {
                return ($('password').value == value);
            });
        });
    </script>

    <form action="<?php echo JRoute::_('index.php?option=com_user#content'); ?>" method="post" id="member-registration"
          name="josForm" class="form-validate user">
        <?php if (isset($this->message)) : ?>
        <?php $this->display('message'); ?>
        <?php endif; ?>
        <fieldset>
            <p><?php echo JText::_('REGISTER_REQUIRED'); ?></p>
            <label id="namemsg" for="name">
                <?php echo JText::_('Name'); ?>: *
                <input type="text" name="name" id="name" value="<?php echo $this->escape($this->user->get('name')); ?>"
                       class="inputbox validate required none namemsg" maxlength="50">
            </label>
            <label id="usernamemsg" for="username">
                <?php echo JText::_('Username'); ?>: *
                <input type="text" id="username" name="username"
                       value="<?php echo $this->escape($this->user->get('username')); ?>"
                       class="inputbox validate required username usernamemsg" maxlength="25">
            </label>
            <label id="emailmsg" for="email">
                <?php echo JText::_('Email'); ?>: *
                <input type="email" id="email" name="email"
                       value="<?php echo $this->escape($this->user->get('email')); ?>"
                       class="inputbox validate required email emailmsg" maxlength="100">
            </label>
            <label id="pwmsg" for="password">
                <?php echo JText::_('Password'); ?>: *
                <input type="password" id="password" name="password" value=""
                       class="inputbox required validate-password">
            </label>
            <label id="pw2msg" for="password2">
                <?php echo JText::_('Verify Password'); ?>: *
                <input type="password" id="password2" name="password2" value=""
                       class="inputbox required validate-passverify">
            </label>
        </fieldset>
        <button class="button validate" type="submit"><?php echo JText::_('Register'); ?></button>
        <input type="hidden" name="task" value="register_save">
        <input type="hidden" name="id" value="0">
        <input type="hidden" name="gid" value="0">
        <?php echo JHTML::_('form.token'); ?>
    </form>
</section>
