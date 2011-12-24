<?php
/**
 * @version        $Id: offline.php 20717 2011-02-15 16:50:33Z infograf768 $
 * @package        Joomla.Site
 * @copyright    Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license        GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('MOLAJO') or die;

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
        "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?php echo $this->language; ?>"
      lang="<?php echo $this->language; ?>" dir="<?php echo $this->direction; ?>">
<head>
    <doc:include type="head"/>
    <link rel="stylesheet" href="<?php echo $this->baseurl ?>/templates/system/css/offline.css" type="text/css"/>
    <?php if ($this->direction == 'rtl') : ?>
    <link rel="stylesheet" href="<?php echo $this->baseurl ?>/templates/system/css/offline_rtl.css" type="text/css"/>
    <?php endif; ?>
    <link rel="stylesheet" href="<?php echo $this->baseurl ?>/templates/system/css/general.css" type="text/css"/>
</head>
<body>
<doc:include type="message"/>
<div id="frame" class="outline">
    <img src="images/joomla_logo_black.jpg" alt="Molajo Logo"/>

    <h1>
        <?php echo MolajoFactory::getApplication()->get('sitename'); ?>
    </h1>

    <p>
        <?php echo MolajoFactory::getApplication()->get('offline_message'); ?>
    </p>

    <form action="<?php echo MolajoRouteHelper::_('index.php', true); ?>" method="post" id="form-login">
        <fieldset class="input">
            <p id="form-login-username">
                <label for="username"><?php echo MolajoTextHelper::_('JGLOBAL_USERNAME') ?></label>
                <input name="username" id="username" type="text" class="inputbox"
                       alt="<?php echo MolajoTextHelper::_('JGLOBAL_USERNAME') ?>" size="18"/>
            </p>

            <p id="form-login-password">
                <label for="passwd"><?php echo MolajoTextHelper::_('JGLOBAL_PASSWORD') ?></label>
                <input type="password" name="password" class="inputbox" size="18"
                       alt="<?php echo MolajoTextHelper::_('JGLOBAL_PASSWORD') ?>" id="passwd"/>
            </p>

            <p id="form-login-remember">
                <label for="remember"><?php echo MolajoTextHelper::_('JGLOBAL_REMEMBER_ME') ?></label>
                <input type="checkbox" name="remember" class="inputbox" value="yes"
                       alt="<?php echo MolajoTextHelper::_('JGLOBAL_REMEMBER_ME') ?>" id="remember"/>
            </p>
            <input type="submit" name="Submit" class="button" value="<?php echo MolajoTextHelper::_('JLOGIN') ?>"/>
            <input type="hidden" name="option" value="users"/>
            <input type="hidden" name="task" value="user.login"/>
            <input type="hidden" name="return" value="<?php echo base64_encode(JURI::base()) ?>"/>
            <?php echo MolajoHTML::_('form.token'); ?>
        </fieldset>
    </form>
</div>
</body>
</html>