<?php
/**
 * @package     Molajo
 * @subpackage  Wrap
 * @copyright   Copyright (C)  2011 Amy Stephen, Cristina Solana. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die;
?>
<table cellspacing="1" cellpadding="0" width="100%">
    <tr>
        <td>
        <table cellpadding="0" cellspacing="0"
               class="moduletable<?php echo $this->parameters->get('layout_class_suffix'); ?>">
        <?php if ($this->parameters->get('showtitle', true) === true) : ?>
    <tr>
        <th>
            <?php echo $this->escape($this->row->title); ?>
        </th>
    </tr>
<?php endif;