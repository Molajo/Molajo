<?php

/**
 * @package    Molajo
 * @copyright  2012 Amy Stephen. All rights reserved.
 * @license    GNU GPL v 2, or later and MIT, see License folder
 */
defined('MOLAJO') or die;

if ($this->row->last_row == 1) {
    $liclass = ' class="last-in-row"';
} else {
    $liclass = '';
}
?>
<li<?php echo $liclass; ?>><a
    href="<?php echo $this->row->link; ?>"><span<?php echo $this->row->css_id ?><?php echo $this->row->css_class; ?>><?php echo $this->row->link_text; ?></span></a>
</li>
