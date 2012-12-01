<?php
use Molajo\Service\Services;
/**
 *
 * @package    Molajo
 * @copyright  2012 Amy Stephen. All rights reserved.
 * @license    GNU GPL v 2, or later and MIT, see License folder
 */
defined('MOLAJO') or die;
if ($this->row->enable == 1) { ?>
<div class="grid-tags grid-batch">
    <ol class="grid-batch">
        <li><strong><?php echo Services::Language()->translate('Tags Assignment'); ?></strong></li>
        <li><include:template name=formselectlist datalist=tags/></li>
        <li><input type="submit" class="submit button small radius" name="submit" id="AssignTags" value="Assign"></li>
        <li><input type="submit" class="submit button small radius" name="submit" id="RemoveTags" value="Remove"></li>
    </ol>
</div>
<?php }
