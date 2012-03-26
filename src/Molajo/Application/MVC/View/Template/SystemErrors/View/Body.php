<?php
/**
 * @package   Molajo
 * @copyright 2012 Amy Stephen. All rights reserved.
 * @license   GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die; ?>
<li><?php echo '<strong>' . $this->row->code . ' ' . $this->row->title . ':</strong> ' . $this->row->content_text; ?>
    <?php
    if (Services::Configuration()->get('debug', 0) == 1) {
        if (trim($this->row->debug_location) == '') {
        } else {
            echo 'Location: ' . $this->row->debug_location . '<br />';
        }
        if (is_object($this->row->debug_object)) {
            echo 'Debug Object: <br />';
            echo '<pre>';
            var_dump($this->row->debug_object);
            echo '</pre>';
        }
    } ?>
</li>
