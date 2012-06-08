<?php
/**
 * @package   Molajo
 * @copyright 2012 Amy Stephen. All rights reserved.
 * @license   GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
use Molajo\Service\Services;

defined('MOLAJO') or die;

$name = $this->row;
$listselectedname = 'list_' . $name . '_selected';
$listname = 'list_' . $name;
$list = Services::Registry()->get('Trigger', $listname);
?>
<select name="<?php echo $listname; ?>" class="inputbox">
	<option value=""><?php echo Services::Language()->translate('SELECT_' . strtoupper($name)); ?></option>
	<?php
	$currentSelection = Services::Registry()->get('Trigger', $listselectedname);
	foreach ($list as $l) {
		if ($currentSelection == $l->id) {
			$selected = ' selected="selected"';
		} else {
			$selected = '';
		}
		?>
		<option value="<?php echo $l->id; ?>"<?php echo $selected; ?>><?php echo $l->value; ?></option>
		<?php } ?>
</select>
