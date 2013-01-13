<?php
/**
 * Errors Template View
 *
 * @package      Molajo
 * @license      MIT
 * @copyright    2013 Amy Stephen. All rights reserved.
 */
defined('MOLAJO') or die; ?>
<li>
    <?php echo '<strong>' . $this->row->code . ' ' . $this->row->title . ':</strong> ' . $this->row->content_text; ?>
</li>
