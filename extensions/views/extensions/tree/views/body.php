<?php
/**
 * @version     $id: item_body.php
 * @package     Molajo
 * @subpackage  Latest News View
 * @copyright   Copyright (C) 2012 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die; ?>
<h3>
    <a href="<?php echo $this->row->url; ?>"><?php echo $this->row->title; ?></a>
</h3>
<p>
    <?php echo '<p>' . $this->row->snippet . '</p>'; ?>
</p>
<p class="small">
    <?php echo MolajoTextHelper::_('MOLAJO_WRITTEN_BY') . ' ' . $this->row->display_author_name; ?>
</p>
<p class="small">
    <?php echo $this->row->published_pretty_date; ?>
</p>