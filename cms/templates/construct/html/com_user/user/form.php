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

<section class="profile-edit<?php echo $this->escape($this->params->get('pageclass_sfx')) ?>">

    <?php if ($this->params->get('show_page_title', 1)) : ?>
    <h2>
        <?php echo $this->escape($this->params->get('page_title')) ?>
    </h2>
    <?php endif; ?>

    <script type="text/javascript">
        <!--
        Window.onDomReady(function() {
            document.formvalidator.setHandler('passverify', function (value) {
                return ($('password').value == value);
            });
        });
        // -->
    </script>

    <form id="member-profile" action="<?php echo JRoute::_('index.php'); ?>" method="post" name="userform"
          autocomplete="off" class="user">
        <fieldset>
            <label for="username">
                <?php echo JText::_('User Name'); ?>:
                <?php echo $this->escape($this->user->get('username')); ?>
            </label>
            <label for="name">
                <?php echo JText::_('Your Name'); ?>:
                <input class="inputbox" type="text" id="name" name="name"
                       value="<?php echo $this->escape($this->user->get('name')); ?>" size="40">
            </label>
            <label for="email">
                <?php echo JText::_('email'); ?>:
                <input class="inputbox required validate-email" type="email" id="email" name="email"
                       value="<?php echo $this->escape($this->user->get('email'));?>" size="40">
            </label>
            <?php if ($this->user->get('password')) : ?>
            <label for="password">
                <?php echo JText::_('Password'); ?>:
                <input class="inputbox validate-password" type="password" id="password" name="password" value=""
                       size="40">
            </label>
            <label for="verifyPass">
                <?php echo JText::_('Verify Password'); ?>:
                <input class="inputbox validate-passverify" type="password" id="password2" name="password2" size="40">
            </label>
            <?php endif; ?>
            <?php if (isset($this->params)) : ?>
            <?php echo $this->params->render('params'); ?>
            <?php endif; ?>
            <button class="button validate" type="submit" onclick="submitbutton( this.form );return false;">
                <?php echo JText::_('Save'); ?>
            </button>
        </fieldset>
        <input type="hidden" name="username" value="<?php echo $this->escape($this->user->get('username'));?>">
        <input type="hidden" name="id" value="<?php echo (int)$this->user->get('id');?>">
        <input type="hidden" name="gid" value="<?php echo (int)$this->user->get('gid');?>">
        <input type="hidden" name="option" value="com_user">
        <input type="hidden" name="task" value="save">
        <?php echo JHTML::_('form.token'); ?>
    </form>
</section>