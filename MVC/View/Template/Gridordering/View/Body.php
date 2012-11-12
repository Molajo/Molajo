<?php
use Molajo\Service\Services;
/**
 * @package    Molajo
 * @copyright  2012 Individual Molajo Contributors. All rights reserved.
 * @license    GNU GPL v 2, or later and MIT, see License folder
 */
defined('MOLAJO') or die; ?>
<div class="grid-ordering grid-batch">
    <ol class="grid-batch">
        <li><strong><?php echo Services::Language()->translate('Change Ordering of Results'); ?></strong></li>
        <li><include:template name=formselectlist datalist=Fields selected=<?php echo Services::Registry()->get('Plugindata', 'GridTableOrdering'); ?>/></li>
        <li><include:template name=formselectlist datalist=OrderingDirection selected=<?php echo Services::Registry()->get('Plugindata', 'GridTableOrderingDirection'); ?>/></li>
        <li><include:template name=formselectlist datalist=Itemsperpage selected=<?php echo Services::Registry()->get('Plugindata', 'GridTableItemsPerPage'); ?>/></li>
        <li><input type="submit" class="submit button small radius" name="submit" id="Sort" value="Sort"></li>
    </ol>
</div>
