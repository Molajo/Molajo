<?php
/**
 * @version     $id: layout
 * @package     Molajo
 * @subpackage  Multiple View
 * @copyright   Copyright (C) 2011 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die;
?>
<select name="filter_<?php echo strtolower($this->tempColumnName); ?>" class="inputbox" onchange="this.form.submit()">
    <option value=""><?php echo JText::_('MOLAJO_SELECT_'.strtoupper($this->tempColumnName)); ?></option>
    <?php echo JHtml::_('select.options', $this->tempArray, 'value', 'text', $this->selectedValue); ?>
</select>
