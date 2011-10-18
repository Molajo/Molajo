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
    <h2>Review</h2>
    <p>You have provided all of the information needed to install Molajo. Press Install when ready to proceed.</p>
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
