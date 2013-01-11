<?php
/**
 * Errors Template View
 *
 * @package      Niambie
 * @license      MIT
 * @copyright    2013 Amy Stephen. All rights reserved.
 */
defined('NIAMBIE') or die; ?>
<li>
    <?php echo '<strong>' . $this->row->code . ' ' . $this->row->title . ':</strong> ' . $this->row->content_text; ?>
</li>
