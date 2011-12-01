<?php
/**
 * @package     Molajo
 * @subpackage  Layouts
 * @copyright   Copyright (C) 2011 Chris Rault. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die;
var_dump($this->setup);
?>
<div class="inner">
    <h2><?php echo MolajoTextHelper::_('Review') ?></h2>

    <p><?php echo MolajoTextHelper::_('You have provided all of the information needed to install Molajo. Press Install when ready to proceed.') ?></p>

    <div class="summary">
        <h3><?php echo MolajoTextHelper::_('Site information') ?></h3>
        <ul class="list-reset">
            <li><strong><?php echo MolajoTextHelper::_('Site name:') ?></strong>
                <span><?php echo $this->setup['sitename'] ?></span></li>
            <li><strong><?php echo MolajoTextHelper::_('Your name:') ?></strong>
                <span><?php echo $this->setup['name'] ?></span></li>
            <li><strong><?php echo MolajoTextHelper::_('Your email:') ?></strong>
                <span><?php echo $this->setup['admin_email'] ?></span></li>
            <li><strong><?php echo MolajoTextHelper::_('Your password:') ?></strong>
                <span><?php echo $this->setup['admin_password'] ?></span></li>
        </ul>
    </div>

    <form action="<?php echo JUri::current() ?>" method="post">

        <input type="hidden" name="language" value="<?php echo $this->setup['language'] ?>">
        <input type="hidden" name="sitename" value="<?php echo $this->setup['sitename'] ?>">
        <input type="hidden" name="name" value="<?php echo $this->setup['name'] ?>">
        <input type="hidden" name="admin_email" value="<?php echo $this->setup['admin_email'] ?>">
        <input type="hidden" name="admin_password" value="<?php echo $this->setup['admin_password'] ?>">
        <input type="hidden" name="db_host" value="<?php echo $this->setup['hostname'] ?>">
        <input type="hidden" name="db_scheme" value="<?php echo $this->setup['db_scheme'] ?>">
        <input type="hidden" name="db_username" value="<?php echo $this->setup['db_username'] ?>">
        <input type="hidden" name="db_password" value="<?php echo $this->setup['db_password'] ?>">
        <input type="hidden" name="db_prefix" value="<?php echo $this->setup['db_prefix'] ?>">
        <input type="hidden" name="db_type" value="<?php echo $this->setup['db_type'] ?>">
        <input type="hidden" name="remove_tables" value="<?php echo $this->setup['remove_tables'] ?>">
        <input type="hidden" name="sample_data" value="<?php echo $this->setup['sample_data'] ?>">

        <div id="actions">
            <!--a href="<?php echo JURI::base(); ?>index.php?option=installer&view=display&layout=step3" class="btn-secondary">&laquo; <strong>P</strong>revious</a
            -->
            <!--a href="<?php echo JURI::base(); ?>index.php?option=installer&task=install" class="btn-primary alt">Install &raquo;</a
            -->
            <button type="submit" class="btn-secondary" name="layout"
                    value="step3"><?php echo MolajoTextHelper::_('Previous') ?></button>
            <button type="submit" class="btn-primary" name="task"
                    value="install"><?php echo MolajoTextHelper::_('Install') ?></button>
        </div>

        <form>

</div>
