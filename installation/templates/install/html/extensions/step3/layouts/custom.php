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

    <h2>Database Setup</h2>

    <p>Enter your database connection details below. Contact your host if you are not sure what these are.</p>

    <form action="<?php echo JUri::current() ?>" method="post">

        <input type="hidden" name="language"       value="<?php echo $this->setup['language'] ?>">
        <input type="hidden" name="sitename"       value="<?php echo $this->setup['sitename'] ?>">
        <input type="hidden" name="name"           value="<?php echo $this->setup['name'] ?>">
        <input type="hidden" name="admin_email"    value="<?php echo $this->setup['admin_email'] ?>">
        <input type="hidden" name="admin_password" value="<?php echo $this->setup['admin_password'] ?>">
        <input type="hidden" name="hostname"       value="<?php echo $this->setup['hostname'] ?>">
        <input type="hidden" name="db_scheme"      value="<?php echo $this->setup['db_scheme'] ?>">
        <input type="hidden" name="db_username"    value="<?php echo $this->setup['db_username'] ?>">
        <input type="hidden" name="db_password"    value="<?php echo $this->setup['db_password'] ?>">
        <input type="hidden" name="db_prefix"      value="<?php echo $this->setup['db_prefix'] ?>">
        <input type="hidden" name="db_type"        value="<?php echo $this->setup['db_type'] ?>">
        <input type="hidden" name="remove_tables"  value="<?php echo $this->setup['remove_tables'] ?>">
        <input type="hidden" name="sample_data"    value="<?php echo $this->setup['sample_data'] ?>">

        <ol class="list-reset forms">
            <li>
                <span class="inner-wrap">
                    <label for="hostname" class="inlined"><?php echo MolajoText::_('Host name') ?></label>
                    <input type="text" class="input-text" required="required" id="hostname" name="hostname" placeholder="<?php echo MolajoText::_('Host name') ?>" value="<?php echo $this->setup['hostname'] ?>" />
                    <span class="note"><?php echo MolajoText::_('This is usually <b>localhost</b>.') ?></span>
                </span>
            </li>
            <li>
                <span class="inner-wrap">
                    <label for="db_scheme" class="inlined"><?php echo MolajoText::_('Database name') ?></label>
                    <input type="text" class="input-text" required="required" id="db_scheme" name="db_scheme" placeholder="<?php echo MolajoText::_('Database name') ?>" value="<?php echo $this->setup['db_scheme'] ?>" />
                    <span class="note"><?php echo MolajoText::_('The name of the database you are installing Molajo on.') ?></span>
                </span>
            </li>
            <li>
                <span class="inner-wrap">
                    <label for="db_username" class="inlined"><?php echo MolajoText::_('Username') ?></label>
                    <input type="text" class="input-text" required="required" id="db_username" name="db_username" placeholder="Username" value="<?php echo $this->setup['db_username'] ?>" />
                    <span class="note"><?php echo MolajoText::_('Your MySQL database username.') ?></span>
                </span>
            </li>
            <li>
                <span class="inner-wrap">
                    <label for="db_password" class="inlined"><?php echo MolajoText::_('Password') ?></label>
                    <input type="text" class="input-text" required="required" id="db_password" name="db_password" placeholder="<?php echo MolajoText::_('Password') ?>" value="<?php echo $this->setup['db_password'] ?>" />
                    <span class="note"><?php echo MolajoText::_('Your MySQL database password.') ?></span>
                </span>
            </li>
        </ol>

        <ol class="list-rest radios">
            <li>
                <span class="label"><?php echo MolajoText::_('Database type') ?></span>
                <label class="radio-left<?php echo $this->setup['db_type']=='MySQL'?' label-selected':''; ?>" for="MySQL">
                    <input name="db_type" id="MySQL" value="MySQL" type="radio"<?php echo $this->setup['db_type']=='MySQL'?' checked="checked"':''; ?> /><?php echo MolajoText::_('MySQL') ?></label>
                <label class="radio-middle<?php echo $this->setup['db_type']=='MySQLi'?' label-selected':''; ?>" for="MySQLi">
                    <input name="db_type" id="MySQLi" value="MySQLi" type="radio"<?php echo $this->setup['db_type']=='MySQLi'?' checked="checked"':''; ?> /><?php echo MolajoText::_('MySQLi') ?></label>
                <label class="radio-right<?php echo $this->setup['db_type']=='Doctrine'?' label-selected':''; ?>" for="Doctrine">
                    <input name="db_type" id="Doctrine" value="Doctrine" type="radio"<?php echo $this->setup['db_type']=='Doctrine'?' checked="checked"':''; ?> /><?php echo MolajoText::_('Doctrine') ?></label>
            </li>
            <li>
                <span class="label"><?php echo MolajoText::_('Sample Data') ?></span>
                <label class="radio-left<?php echo $this->setup['sample_data']=='none'?' label-selected':''; ?>" for="none">
                    <input name="sample_data" id="none" value="none" type="radio"<?php echo $this->setup['sample_data']=='none'?' checked="checked"':''; ?> /><?php echo MolajoText::_('None') ?></label>
                <label class="radio-middle<?php echo $this->setup['sample_data']=='blog'?' label-selected':''; ?>" for="blog">
                    <input name="sample_data" id="blog" value="blog" type="radio"<?php echo $this->setup['sample_data']=='blog'?' checked="checked"':''; ?> /><?php echo MolajoText::_('Blog') ?></label>
                <label class="radio-middle<?php echo $this->setup['sample_data']=='news'?' label-selected':''; ?>" for="news">
                    <input name="sample_data" id="news" value="news" type="radio"<?php echo $this->setup['sample_data']=='news'?' checked="checked"':''; ?> /><?php echo MolajoText::_('News') ?></label>
                <label class="radio-right<?php echo $this->setup['sample_data']=='etc'?' label-selected':''; ?>" for="etc">
                    <input name="sample_data" id="etc" value="etc" type="radio"<?php echo $this->setup['sample_data']=='etc'?' checked="checked"':''; ?> /><?php echo MolajoText::_('Etc.') ?></label>
            </li>
            <li>
                <span class="label"><?php echo MolajoText::_('Existing tables') ?></span>
                <label class="radio-left<?php echo $this->setup['remove_tables']==1?' label-selected':''; ?>" for="remove">
                    <input name="remove_tables" id="remove" value="1" type="radio"<?php echo $this->setup['remove_tables']==1?' checked="checked"':''; ?> /><?php echo MolajoText::_('Remove') ?>
                </label>
                <label class="radio-right<?php echo $this->setup['remove_tables']==0?' label-selected':''; ?>" for="backup">
                    <input name="remove_tables" id="backup" value="0" type="radio"<?php echo $this->setup['remove_tables']==0?' checked="checked"':''; ?> /><?php echo MolajoText::_('Backup') ?>
                </label>
            </li>
        </ol>

        <p>
            Babs: maybe have tooltips that explain the various options? agree
        </p>
    <div id="actions">
        <!--a href="<?php echo JURI::base(); ?>index.php?option=com_installer&view=display&layout=step2" class="btn-secondary">&laquo; <strong>P</strong>revious</a-->
        <!--a href="<?php echo JURI::base(); ?>index.php?option=com_installer&view=display&layout=step4" class="btn-primary"><strong>N</strong>ext &raquo;</a-->
        <button type="submit" class="btn-secondary" name="layout" value="step2"><?php echo MolajoText::_('Previous') ?></button>
        <button type="submit" class="btn-primary" name="layout" value="step4"><?php echo MolajoText::_('Next') ?></button>
    </div>
    </form>
</div>