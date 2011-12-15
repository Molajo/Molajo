<?php
/**
 * @package     Molajo
 * @subpackage  Items
 * @copyright   Copyright (C) 2012 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die; ?>
<h3>
    <a href="<?php echo $this->row->url; ?>">
        <?php echo $this->row->title; ?>
    </a>
</h3>
<p>
    <?php echo $this->row->snippet; ?>
</p>
<p class="small">
    <?php echo MolajoTextHelper::_('MOLAJO_WRITTEN_BY') . ' ' . $this->row->display_author_name; ?>
</p>
<p class="small">
    <?php echo $this->row->published_pretty_date; ?>
</p>
<li>
    <a href="<?php echo $this->row->url; ?>"><?php echo $this->row->title; ?></a>
</li>