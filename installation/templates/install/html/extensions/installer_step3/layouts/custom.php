<?php
/**
 * @package     Molajo
 * @subpackage  Layouts
 * @copyright   Copyright (C) 2011 Chris Rault. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die;
?>
<div class="inner">
    <h2>Site Information</h2>
    <p>Enter your site information. All fields marked with a <strong>*</strong> are required.</p>

    <form action="">
        <ol class="list-reset forms">
            <li>
                <span class="inner-wrap">
                    <label for="site" class="inlined">Site name</label>
                    <input type="text" class="input-text" id="site" name="site" title="Site name" />
                    <span class="note"><strong>*</strong> Your site name.</span>
                </span>
            </li>
            <li>
                <span class="inner-wrap">
                    <label for="email" class="inlined">Your email address</label>
                    <input type="text" class="input-text" id="email" name="email" title="Your email address" />
                    <span class="note"><strong>*</strong> Enter a valid email address. This is where your login info will be sent.</span>
                </span>
            </li>
            <li>
                <span class="inner-wrap">
                    <label for="username" class="inlined">Username</label>
                    <input type="text" class="input-text" id="username" name="username" title="Username" />
                    <span class="note"><strong>*</strong> Enter your admin username.</span>
                </span>
            </li>
            <li>
                <span class="inner-wrap">
                    <label for="password" class="inlined">Password</label>
                    <input type="password" class="password" id="password" name="password" title="Password" />
                    <span class="note"><strong>*</strong> Enter your admin password.</span>
                </span>
            </li>
        </ol>
        <div class="sample-data">
            <a href="#" id="sample-data" class="btn-secondary">Install sample data</a>
            <span class="note">Installing sample data is strongly recommended for beginners.
            This will install sample content that is included in the Joomla! installation package. <a href="#">Learn more</a>.</span>
        </div>
    </form>

    <div id="actions">
        <a href="<?php echo JURI::base(); ?>index.php?option=com_installer&view=display&layout=installer_step2" class="btn-secondary">&laquo; <strong>P</strong>revious</a>
        <a href="<?php echo JURI::base(); ?>index.php?option=com_installer&view=display&layout=installer_step4" class="btn-primary"><strong>N</strong>ext &raquo;</a>
    </div>

</div>