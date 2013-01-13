<?php
/**
 * Gridcheckin Template View
 *
 * @package      Molajo
 * @license      MIT
 * @copyright    2013 Amy Stephen. All rights reserved.
 */
use Molajo\Service\Services;

defined('MOLAJO') or die;
$action = Services::Registry()->get(PAGE_LITERAL, 'page_url');
if ($this->row->enable == 1) {
    ?>
<div class="grid-checkin grid-batch">
    <ol class="grid-batch">
        <li><strong><?php echo Services::Language()->translate('Checkin Content'); ?></strong></li>
        <li><input type="submit" class="submit button small radius" name="submit" id="Checkin" value="Checkin"/></li>
    </ol>
</div>
<?php }
