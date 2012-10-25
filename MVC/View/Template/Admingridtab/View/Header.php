<?php
use Molajo\Service\Services;
/**
 * @package    Molajo
 * @copyright  2012 Individual Molajo Contributors. All rights reserved.
 * @license    GNU GPL v 2, or later and MIT, see License folder
 */
defined('MOLAJO') or die;
$printheaders = 0;
$this->row->new_fieldset = '0';
Services::Registry()->set('Parameters', 'application_help', 0);

if ($printheaders == 1) { ?>
<h2>
    <span>
        <em><?php echo Services::Registry()->get('Plugindata', 'heading1'); ?> <?php echo Services::Registry()->get('Plugindata', 'heading2'); ?></em>
        <strong><?php echo $this->row->tab_title; ?></strong>
    </span>
</h2>
<?php } ?>
<?php if (Services::Registry()->get('Parameters', 'application_help') == 1) { ?>
    <p class="tab-description"><?php echo $this->row->tab_fieldset_description; ?></p>
<?php }
