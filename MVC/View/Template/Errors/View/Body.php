<?php
/**
 * @package    Niambie
 * @copyright  2013 Amy Stephen. All rights reserved.
 * @license    MIT
 */
defined('NIAMBIE') or die; ?>
<li>
    <?php echo '<strong>' . $this->row->code . ' ' . $this->row->title . ':</strong> ' . $this->row->content_text; ?>
</li>
