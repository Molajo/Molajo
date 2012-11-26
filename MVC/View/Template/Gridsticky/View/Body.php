<?php
use Molajo\Service\Services;

/**
 *
 * @package    Molajo
 * @copyright  2012 Individual Molajo Contributors. All rights reserved.
 * @license    GNU GPL v 2, or later and MIT, see License folder
 */
defined('MOLAJO') or die;
$action = Services::Registry()->get('Page', 'page_url');
//if ($this->row->enable == 1) { ?>
<div class="grid-sticky grid-batch">
    <ol class="grid-batch">
        <li><strong><?php echo Services::Language()->translate('Sticky Assignment'); ?></strong></li>
        <li><input type="submit" class="submit button small radius" name="submit" id="Sticky" value="Sticky"></li>
        <li><input type="submit" class="submit button small radius" name="submit" id="Unsticky" value="Unsticky"></li>
    </ol>
</div>
<?php //}
