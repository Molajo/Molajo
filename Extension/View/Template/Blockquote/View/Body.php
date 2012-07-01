<?php
/**
 * @package     Molajo
 * @copyright   2012 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die; ?>
<blockquote>
<?php echo $this->row->blockquote; ?>
	<?php if ($this->row->cite == '') {
	} else { ?>
	<cite><?php echo $this->row->cite; ?></cite>
	<?php } ?>
</blockquote>
