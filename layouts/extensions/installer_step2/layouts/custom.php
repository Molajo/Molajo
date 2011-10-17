<?php
/**
 * @package     Molajo
 * @subpackage  Layouts
 * @copyright   Copyright (C) 2011 Cristina Solana. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die;
?>
<div class="inner">

    <h2>Database Setup</h2>

    <p>Enter your database connection details below. Contact your host if you are not sure what these are.<br />
    All fields marked with a <strong>*</strong> are required.</p>

    <form action="">
        <ol class="list-reset forms">
            <li>
                <span class="inner-wrap">
                    <label for="host" class="inlined">Host name</label>
                    <input type="text" class="input-text" id="host" name="host" title="Host name" />
                    <span class="note"><strong>*</strong> This is usually <b>localhost</b>.</span>
                </span>
            </li>
            <li>
                <span class="inner-wrap">
                    <label for="name" class="inlined">Database name</label>
                    <input type="text" class="input-text" id="name" name="name" title="Database name" />
                    <span class="note"><strong>*</strong> The name of the database you are installing Molajo on.</span>
                </span>
            </li>
            <li>
                <span class="inner-wrap">
                    <label for="username" class="inlined">Username</label>
                    <input type="text" class="input-text" id="username" name="username" title="Username" />
                    <span class="note"><strong>*</strong> Your MySQL database username.</span>
                </span>
            </li>
            <li>
                <span class="inner-wrap">
                    <label for="password" class="inlined">Password</label>
                    <input type="text" class="input-text" id="password" name="password" title="Password" />
                    <span class="note"><strong>*</strong> Your MySQL database password.</span>
                </span>
            </li>
            <li>
                <span class="inner-wrap">
                    <label for="prefix" class="inlined">Table prefix</label>
                    <input type="text" class="input-text" id="prefix" name="prefix" title="Table prefix" />
                    <span class="note"><strong>*</strong> By default this is set to jos_ but we recommended that you change this.</span>
                </span>
            </li>
        </ol>

        <ol class="list-rest radios">
            <li>
                <span class="label">Database type</span>
                <label class="radio-left" for="mysql"><input name="dbtype" id="mysql" value="myql" type="radio">MySQL</label>
                <label class="radio-right label-selected" for="mysqli"><input name="dbtype" id="mysqli" value="mysqli" type="radio" checked="checked">MySQLi</label>
                <span class="note">MySQLi is recommended, but not all hosts support it. <a href="#">Learn more</a>.</span>
            </li>
            <li>
                <span class="label">Existing database</span>
                <label class="radio-left" for="remove"><input name="existingdb" id="remove" value="remove" type="radio">Remove</label>
                <label class="radio-right label-selected" for="backup"><input name="existingdb" id="backup" value="backup" type="radio" checked="checked">Backup</label>
                <span class="note alt">If you have an existing database with the same name, would you like it to be replaced or backed up.</span>
            </li>
        </ol>
    </form>

    <div id="actions">
        <a href="<?php echo JURI::base(); ?>index.php?option=com_installer&view=display&layout=installer_step1" class="btn-secondary">&laquo; <strong>P</strong>revious</a>
        <a href="<?php echo JURI::base(); ?>index.php?option=com_installer&view=display&layout=installer_step3" class="btn-primary"><strong>N</strong>ext &raquo;</a>
    </div>
</div>
