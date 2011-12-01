<?php
/**
 * @package     Molajo
 * @subpackage  Layouts
 * @copyright   Copyright (C) 2011 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die; ?>
<table class="adminlist">
<thead>
    <tr>
<?php
if (count($this->rowset[0]->columncount) > 0) :
    for ($i=1; $i < count($this->rowset[0]->columncount + 1); $i++) {
        echo '<th>'.$this->rowset[0]->columnheading.$i.'</th>';
    }
?>
    </tr>
<?php else : ?>
</thead>
	<tbody>
		<tr>
			<td colspan="<?php echo $this->rowset[0]->columncount; ?>">
				<p class="noresults"><?php echo MolajoText::_('LATEST_NO_MATCHING_RESULTS');?></p>
			</td>
		</tr>
	</tbody>
<?php endif;
