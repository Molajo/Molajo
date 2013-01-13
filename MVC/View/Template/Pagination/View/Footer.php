<?php
/**
 * Pagination Template View
 *
 * @package      Molajo
 * @license      MIT
 * @copyright    2013 Amy Stephen. All rights reserved.
 */
use Molajo\Service\Services;

defined('MOLAJO') or die;

if ($this->row->next_page === 0) {
    $class   = 'class="arrow unavailable"';
    $link    = '';
    $linkend = '';
} else {
    $class   = 'class="arrow"';
    $link    = '<a href="' . $this->row->next_link . '">';
    $linkend = '</a>';
}
?>
<li <?php echo $class; ?>>
    <?php echo $link; ?>&raquo;<?php echo $linkend; ?>
</li>
</ul>
