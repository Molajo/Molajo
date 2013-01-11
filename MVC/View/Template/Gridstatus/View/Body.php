<?php
/**
 * Gridstatus Template View
 *
 * @package      Niambie
 * @license      MIT
 * @copyright    2013 Amy Stephen. All rights reserved.
 */
use Molajo\Service\Services;

defined('NIAMBIE') or die;
if ($this->row->enable == 1) {
    ?>
<div class="grid-status grid-batch">
    <ol class="grid-batch">
        <li><strong><?php echo Services::Language()->translate('Update Status'); ?></strong></li>
        <li>
            <include:template name=formselectlist datalist=Statuschange/>
        </li>
        <li><input type="submit" class="submit button small radius" name="submit" id="AssignStatus" value="Assign"></li>
    </ol>
</div>
<?php }
