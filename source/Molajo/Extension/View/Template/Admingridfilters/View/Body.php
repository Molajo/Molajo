<?php
use Molajo\Service\Services;
/**
 * @package   Molajo
 * @copyright 2012 Amy Stephen. All rights reserved.
 * @license   GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die;
$listname = 'list_' . $this->row->listname . '*'; ?>
<li class="filter">
	<include:template name=formselect wrap=div wrap-class=filter value=<?php echo $listname; ?>/>
</li>
