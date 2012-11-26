<?php
use Molajo\Service\Services;

/**
 *
 * @package    Molajo
 * @copyright  2012 Individual Molajo Contributors. All rights reserved.
 * @license    GNU GPL v 2, or later and MIT, see License folder
 */
defined('MOLAJO') or die;
//if ($this->row->enable == 1) { ?>
<div class="grid-categories grid-batch">
    <ol class="grid-batch">
        <li><strong><?php echo Services::Language()->translate('Category Assignment'); ?></strong></li>
        <li><include:template name=formselectlist datalist=categories/> </li>
        <li><input type="submit" class="submit button small radius" name="submit" id="AssignCategories" value="Assign"/></li>
        <li><input type="submit" class="submit button small radius" name="submit" id="RemoveCategories" value="Remove"/></li>
    </ol>
</div>
<?php //}
