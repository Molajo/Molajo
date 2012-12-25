<?php
use Molajo\Service\Services;
/**
 * @package    Niambie
 * @copyright  2013 Amy Stephen. All rights reserved.
 * @license    MIT
 */
defined('NIAMBIE') or die; ?>
<div class="grid-ordering grid-batch">
    <ol class="grid-batch">
        <li><strong><?php echo Services::Language()->translate('Order Results'); ?></strong></li>
        <li><include:template name=formselectlist datalist=Fields selected=<?php echo Services::Registry()->get('Grid', 'Tableordering'); ?>/></li>
        <li><include:template name=formselectlist datalist=OrderingDirection selected=<?php echo Services::Registry()->get('Grid', 'Orderingdirection'); ?>/></li>
        <li><include:template name=formselectlist datalist=Itemsperpage selected=<?php echo Services::Registry()->get('Grid', 'ItemsPerPage'); ?>/></li>
        <li><input type="submit" class="submit button small radius" name="submit" id="Sort" value="Sort"></li>
    </ol>
</div>
