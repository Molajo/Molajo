<?php
/**
 * @package    Molajo
 * @copyright  2012 Amy Stephen. All rights reserved.
 * @license    GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
use Molajo\Service\Services;
defined('MOLAJO') or die;
?>
<select <?php echo $this->row->multiple; ?> name="<?php echo $this->row->listname; ?>" class="inputbox">
	<option value=""><?php echo Services::Language()->translate('SELECT_' . strtoupper($this->row->listname)); ?></option>
