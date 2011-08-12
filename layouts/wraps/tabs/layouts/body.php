<?php
/**
 * @version     $id: item_body.php
 * @package     Molajo
 * @subpackage  Latest News Layout
 * @copyright   Copyright (C) 2011 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die;?>

	<dt>
		<a href="#"><?php echo $this->row->title; ?></a>
	</dt>
	<dd>
		<?php echo $this->row->content; ?>
	</dd>