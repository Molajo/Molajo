<?php
/**
 * Pagination Template View
 *
 * @package      Niambie
 * @license      MIT
 * @copyright    2013 Amy Stephen. All rights reserved.
 */
use Molajo\Service\Services;

defined('NIAMBIE') or die;

if ($this->row->current === 0) {
    $class   = '';
    $link    = '<a href="' . $this->row->link . '">';
    $linkend = '</a>';
} else {
    $class   = 'class="unavailable"';
    $link    = '';
    $linkend = '';
}
?>
<li <?php echo $class; ?>>
    <?php echo $link; ?><?php echo $this->row->link_text; ?><?php echo $linkend; ?>
</li>
