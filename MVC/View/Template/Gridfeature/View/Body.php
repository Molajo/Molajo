<?php
use Molajo\Service\Services;

/**
 *
 * @package    Niambie
 * @copyright  2012 Amy Stephen. All rights reserved.
 * @license    MIT, see License folder
 */
defined('NIAMBIE') or die;
$action = Services::Registry()->get(PAGE_LITERAL, 'page_url');
if ($this->row->enable == 1) { ?>
<div class="grid-feature grid-batch">
    <ol class="grid-batch">
        <li><strong><?php echo Services::Language()->translate('Feature Assignment'); ?></strong></li>
        <li><input type="submit" class="submit button small radius" name="submit" id="Feature" value="Feature"/></li>
        <li><input type="submit" class="submit button small radius" name="submit" id="Unfeature" value="Unfeature"/></li>
    </ol>
</div>
<?php }
