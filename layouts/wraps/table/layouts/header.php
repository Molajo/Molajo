<?php
/**
 * @package     Molajo
 * @subpackage  Wrap
 * @copyright   Copyright (C)  2011 Amy Stephen, Cristina Solana. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die;
?>
<table cellpadding="0" cellspacing="0" class="moduletable<?php echo $this->params->get('moduleclass_sfx'); ?>">
<?php if ($this->params->get('showtitle', true) === true) :  ?>
    <tr>
        <th>
            <?php echo $this->escape($this->row->title); ?>
        </th>
    </tr>
<?php endif;