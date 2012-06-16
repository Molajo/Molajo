<?php
/**
 * @package    Molajo
 * @copyright  2012 Amy Stephen. All rights reserved.
 * @license    GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
use Molajo\Service\Services;
defined('MOLAJO') or die;
$action = Services::Registry()->get('Trigger', 'PageURL'); ?>
<form action="<?php echo $action; ?>" method="post" name="Adminsearch">
	<input type="search" placeholder="Search..." name="s" >
	<input type="submit" value="Search" />
</form>
