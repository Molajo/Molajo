<?php
/**
 * @package   Molajo
 * @subpackage  Views
 * @copyright 2012 Amy Stephen. All rights reserved.
 * @license   GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die; ?>
<dl class="sub-nav">
	<dt>Article Options:</dt>
	<dd>
	<dd><a href="<?php echo $row; ?>"><?php echo 'List'; ?></a></dd>
	<dd><a href="<?php echo $row; ?>"><?php echo 'New'; ?></a></dd>
	<dd><a href="<?php echo $row; ?>"><?php echo 'Edit'; ?></a></dd>
	<dd><a href="<?php echo $row; ?>"><?php echo 'Configure'; ?></a></dd>
	<dd><form id="searchform" method="get" action="?">
		<input type="search" placeholder="Search..." name="s" >
		<input type="submit" value="Search" />
	</form>
	</dd>
</dl>
