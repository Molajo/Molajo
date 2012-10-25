<?php
/**
 * @package     Molajo
 * @copyright   2012 Individual Molajo Contributors. All rights reserved.
 * @license     GNU GPL v 2, or later and MIT, see License folder
 */
use Molajo\Service\Services;

defined('MOLAJO') or die; ?>
<img class="gravatar float_left" src="<?php echo $this->row->author_email_gravatar; ?>"/>
<h4>
    <a href="<?php echo $this->row->author_catalog_sef_request; ?>"
       title="<?php echo Services::Language()->translate('Posts by '); ?>
       <?php echo $this->row->author_full_name; ?>" rel="author">
        <?php echo $this->row->author_full_name; ?>
    </a>
</h4>
<h5><?php echo $this->row->author_email; ?></h5>

<?php echo $this->row->author_about_me;
