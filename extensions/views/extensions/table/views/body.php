<?php
/**
 * @version     $id: body.php
 * @package     Molajo
 * @subpackage  System View
 * @copyright   Copyright (C) 2012 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die; ?>

<td align="left" valign="top" nowrap>
    <?php echo $this->row->title; ?>
</td>
<td align="left" valign="top" nowrap>
    <?php echo $this->row->display_author_name; ?>
</td>
<td align="left" valign="top" nowrap>
    <?php echo $this->row->created_date; ?>
</td>
<td align="left" valign="top">
    <?php echo $this->row->snippet; ?>
</td>
