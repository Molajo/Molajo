<?php
use Molajo\Service\Services;

/**
 *
 * @package    Molajo
 * @copyright  2012 Individual Molajo Contributors. All rights reserved.
 * @license    GNU GPL v 2, or later and MIT, see License folder
 */
defined('MOLAJO') or die;
$action = Services::Registry()->get('Plugindata', 'page_url');
if ($this->row->enable == 1) { ?>
<div class="grid-checkin grid-batch">
    <ol class="grid-batch">
        <li><strong><?php echo Services::Language()->translate('Checkin Content'); ?></strong></li>
        <li><input type="submit" class="submit button small radius" name="submit" id="Checkin" value="Checkin"></li>
    </ol>
</div>
<?php }
