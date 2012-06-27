<?php
/**
 * @package     Molajo
 * @copyright   2012 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
use Molajo\Service\Services;
defined('MOLAJO') or die;
/** Note: Used as custom with $this->query_results[0] to avoid looping for embedded view */
?>
<img class="gravatar" src="<?php echo $this->query_results[0]->author_email_gravatar; ?>"/>
<h3><?php echo $this->query_results[0]->author_full_name; ?></h3>
<?php if (trim($this->query_results[0]->author_twitter) == '') {
} else { ?>
<h4><a href="<?php echo $this->query_results[0]->author_twitter; ?>">
	<?php echo Services::Language()->translate('Follow me on Twitter'); ?>
</a></h4>
<?php } ?>
<h4><?php echo $this->query_results[0]->author_email_obfuscated; ?></h4>
<?php echo $this->query_results[0]->author_about_me; ?>
