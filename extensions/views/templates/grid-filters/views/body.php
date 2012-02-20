<?php
/**
 * @package     Molajo
 * @subpackage  View
 * @copyright   Copyright (C) 2012 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die; ?>
<select name="filter_<?php echo $this->row->name; ?>" class="inputbox">
    <option value=""><?php echo Services::Language()->translate('SELECT_'.strtoupper($this->row->name)); ?></option>
<?php
$currentSelection = Services::User()->get($this->row->name);
foreach ($this->row->list as $l) {
    if ($currentSelection == $l->value) {
        $selected = ' selected="selected"';
    } else {
        $selected = '';
    }
?>
    <option value="<?php echo $l->key; ?>"<?php echo $selected; ?>><?php echo $l->value; ?></option>
<?php } ?>
</select>
