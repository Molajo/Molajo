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
    <h2>Welcome to the Molajo Installer</h2>
    <p>Before we get started, please ensure you have your database connection information handy, as you'll need it to complete the installation process.
    Contact your hosting provider if you do not know your database connection information.</p>
    <ul id="system-check">
        <li id="language" class="valid">
            <span>We have detected that your language is set to <strong class="en-uk">UK English</strong> <a href="#" id="select-language"><span>Change Language</span></a>
                <ul style="display:none;">
                    <li class="en-us"><a href="#">US English</a></li>
                    <li class="en-uk"><a href="#">UK English</a></li>
                    <li class="de-de"><a href="#">German</a></li>
                    <li class="it-it"><a href="#">Italian</a></li>
                    <li class="po-br"><a href="#">Brazilian Portuguese</a></li>
                </ul>
            </span>
        </li>
        <li id="version" class="valid">
            <span>You are installing the latest version of Molajo.</span>
        </li>
        <!--<li id="version" class="invalid">
            <span>It seems you are installing an outdated version of Molajo. Please update before installing. <a href="#" id="update-installer"><span>Update Installer</span></a></span>
        </li>-->
        <li id="requirements" class="valid">
            <span>Your server meets all of the system requirements.</span>
        </li>
        <!--<li id="requirements" class="invalid">
            <span>There seems to an issue with your server configuration. Please address the issues listed below  <a href="#" id="check-requirements"><span>Check Again</span></a></span>
        </li>-->
    </ul>
    <div id="actions">
        <a href="<?php echo JURI::base(); ?>index.php?option=com_installer&view=display&layout=installer_step2" class="btn-primary"><strong>Ready?</strong> Lets get started! &raquo;</a>
    </div>
</div>