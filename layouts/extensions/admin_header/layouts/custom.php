<?php
/**
 * @package     Molajo
 * @subpackage  Layouts
 * @copyright   Copyright (C) 2011 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die;
?>
<span class="logo">
    <?php echo $this->rowset[0]->link; ?>
        <img src="<?php echo dirname(__FILE__); ?>/images/logo.png" alt="<?php $this->rowset[0]->site_title; ?>" />
    <?php echo $this->rowset[0]->endlink; ?>
</span>
<span class="title">
    <a href="index.php">
        <?php echo $this->rowset[0]->site_title; ?>
    </a>
</span>