<?php
/**
 * @package     Molajo
 * @copyright   2012 Amy Stephen. All rights reserved.
 * @license     GNU GPL v 2, or later and MIT, see License folder
 */
use Molajo\Service\Services;

defined('MOLAJO') or die;
?>
<img class="gravatar" src="<?php echo $this->row->author_email_gravatar; ?>"/>
<h3><?php echo $this->row->author_full_name; ?></h3>
<?php if (trim($this->row->author_twitter) == '') {
} else {
    ?>
<h4><a href="<?php echo $this->row->author_twitter; ?>">
    <?php echo Services::Language()->translate('Follow me on Twitter'); ?>
</a></h4>
<?php } ?>
<h4><?php echo $this->row->author_email_obfuscated; ?></h4>
<?php echo $this->row->author_about_me;
