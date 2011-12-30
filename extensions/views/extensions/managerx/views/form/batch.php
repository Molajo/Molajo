<?php
/**
 * @version     $id: view
 * @package     Molajo
 * @subpackage  Batch Copy
 * @copyright   Copyright (C) 2012 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die; ?>
<fieldset class="batch">

    <legend><?php echo MolajoTextHelper::_('MOLAJO_BATCH_OPTIONS');?></legend>

    <fieldset id="batch-choose-action" class="combo">
        <select name="batch_category_id" class="inputbox" id="batch-category-id">
            <option value=""><?php echo MolajoTextHelper::_('MOLAJO_BATCH_CATEGORY_LABEL') ?></option>
            <?php if ($this->request['option'] == 'categories') { ?>
            <?php echo MolajoHTML::_('select.options', MolajoHTML::_('category.categories', $this->request['option'], array('published' => 1))); ?>
            <?php } else { ?>
            <?php echo MolajoHTML::_('select.options', MolajoHTML::_('category.options', $this->request['option']), 'value', 'text', $this->state->get('filter.batch_category_id')); ?>
            <?php } ?>
        </select>

        <button type="submit" onclick="submitbutton('<?php echo $this->state->get('request.DefaultView'); ?>.copy');">
            <?php echo MolajoTextHelper::_('MOLAJO_BATCH_COPY_PROCESS'); ?>
        </button>

        <button type="submit" onclick="submitbutton('<?php echo $this->state->get('request.DefaultView'); ?>.move');">
            <?php echo MolajoTextHelper::_('MOLAJO_BATCH_MOVE_PROCESS'); ?>
        </button>
    </fieldset>
</fieldset>