<?php
/**
 * @package   Molajo
 * @subpackage  View
 * @copyright 2012 Amy Stephen. All rights reserved.
 * @license   GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die;
$action = 'index.php?option=' . Molajo::Request()->parameters->get('display_extension_option');
?>
<form action="<?php echo $action; ?>" method="post" name="managerForm">
    <fieldset id="filter-bar">
        <?php if (Molajo::Request()->parameters->get('search', 1) == 1) { ?>
        <div class="filter-search fltlft">
            <label class="filter-search-lbl" for="filter_search">
                <?php echo Services::Language()->translate('FORM_SEARCH'); ?>
            </label>
            <input type="text" name="filter_search" id="filter_search"
                   value="<?php //echo $this->state->get('filter.search'); ?>"
                   title="<?php echo Services::Language()->translate('FORM_SEARCH_DESC'); ?>"
                />
            <button type="submit" class="btn">
                <?php echo Services::Language()->translate('FORM_SEARCH_BUTTON'); ?>
            </button>
            <button type="button">
                <?php echo Services::Language()->translate('FORM_CLEAR_BUTTON'); ?>
            </button>
        </div>
        <?php } ?>
        <div class="filter-select fltrt">
