<?php
/**
 * @version     $id: layout
 * @package     Molajo
 * @subpackage  Multiple View
 * @copyright   Copyright (C) 2012 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die;

if ($this->render['class'] == '') {
    $displayclass = '';
} else {
    $displayclass = 'class="' . $this->render['class'] . '" ';
}
if ($this->render['link_value'] === false) {
    $linkbegin = '';
    $linkend = '';
} else {
    $linkbegin = '<a href="' . $this->render['link_value'] . '">';
    $linkend = '</a>';
}
?>
<td <?php echo $displayclass; ?>valign="<?php echo $this->render['valign']; ?>"
    align="<?php echo $this->render['align']; ?>">
    <?php echo $linkbegin; ?><?php echo $this->render['print_value']; ?><?php echo $linkend; ?>
</td>