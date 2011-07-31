<?php
/**
 * @version     $id: layout
 * @package     Molajo
 * @subpackage  Multiple View
 * @copyright   Copyright (C) 2011 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die; ?>
<div class="filter-search fltlft">
        <label class="filter-search-lbl" for="filter_search"><?php echo JText::_('MOLAJO_SEARCH_FILTER_LABEL'); ?></label>
        <input type="text" name="filter_search" id="filter_search" value="<?php echo $this->escape($this->state->get('filter.search')); ?>" title="<?php echo JText::_('MOLAJO_SEARCH_FILTER_DESC'); ?>"/>
        <button type="submit" class="btn"><?php echo JText::_('MOLAJO_SEARCH_FILTER_SUBMIT'); ?></button>
        <button type="button" onclick="document.id('filter_search').value='';this.form.submit();"><?php echo JText::_('MOLAJO_SEARCH_FILTER_CLEAR'); ?></button>
</div>