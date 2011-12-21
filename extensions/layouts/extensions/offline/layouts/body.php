<?php
/**
 * @package     Molajo
 * @subpackage  Error
 * @copyright   Copyright (C) 2012 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die; ?>

<jdoc:include type="message"/>
<div id="frame" class="outline">
    <?php if ($app->getCfg('offline_image')) : ?>
    <img src="<?php echo $app->getCfg('offline_image'); ?>" alt="<?php echo $app->getCfg('sitename'); ?>"/>
    <?php endif; ?>
    <h1>
        <?php echo $app->getCfg('sitename'); ?>
    </h1>
    <?php if ($app->getCfg('display_offline_message', 1) == 1 && str_replace(' ', '', $app->getCfg('offline_message')) != ''): ?>
    <p>
        <?php echo $app->getCfg('offline_message'); ?>
    </p>
    <?php elseif ($app->getCfg('display_offline_message', 1) == 2 && str_replace(' ', '', JText::_('JOFFLINE_MESSAGE')) != ''): ?>
    <p>
        <?php echo JText::_('JOFFLINE_MESSAGE'); ?>
    </p>
    <?php  endif; ?>
    <form action="<?php echo JRoute::_('index.php', true); ?>" method="post" id="form-login">
        <fieldset class="input">
            <p id="form-login-username">
                <label for="username"><?php echo JText::_('JGLOBAL_USERNAME') ?></label>
                <input name="username" id="username" type="text" class="inputbox"
                       alt="<?php echo JText::_('JGLOBAL_USERNAME') ?>" size="18"/>
            </p>

            <p id="form-login-password">
                <label for="passwd"><?php echo JText::_('JGLOBAL_PASSWORD') ?></label>
                <input type="password" name="password" class="inputbox" size="18"
                       alt="<?php echo JText::_('JGLOBAL_PASSWORD') ?>" id="passwd"/>
            </p>

            <p id="form-login-remember">
                <label for="remember"><?php echo JText::_('JGLOBAL_REMEMBER_ME') ?></label>
                <input type="checkbox" name="remember" class="inputbox" value="yes"
                       alt="<?php echo JText::_('JGLOBAL_REMEMBER_ME') ?>" id="remember"/>
            </p>
            <input type="submit" name="Submit" class="button" value="<?php echo JText::_('JLOGIN') ?>"/>
            <input type="hidden" name="option" value="com_users"/>
            <input type="hidden" name="task" value="user.login"/>
            <input type="hidden" name="return" value="<?php echo base64_encode(JURI::base()) ?>"/>
            <?php echo JHtml::_('form.token'); ?>
        </fieldset>
    </form>
</div>