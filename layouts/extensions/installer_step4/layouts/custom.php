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
    <h2>Congratulations, you have successfully installed Molajo!</h2>
    <p>For security reasons, you now need to remove the installation directory from your Molajo install. Simply click the "Remove installation directory" button below &amp; we'll attempt to do it for you. </p>
    <div class="remove-install">
        <a href="#" id="remove-installation" class="btn-secondary">Remove installation directory</a>
    </div>
    <div class="summary">
        <h3>Site information</h3>
        <ul class="list-reset">
            <li><strong>Site name:</strong> <span>Molajito</span></li>
            <li><strong>Your name:</strong> <span>Chris Rault</span></li>
            <li><strong>Your email:</strong> <span>chris@prothemer.com</span></li>
            <li><strong>Admin username:</strong> <span>connectr</span></li>
        </ul>
    </div>

    <div id="actions">
        <a href="<?php echo JURI::base(); ?>index.php?option=com_installer&view=display&layout=installer_step3" class="btn-secondary">&laquo; <strong>P</strong>revious</a>
        <a href="<?php echo JURI::base(); ?>index.php?option=com_installer&task=install" class="btn-primary alt">Install &raquo;</a>
    </div>

</div>
