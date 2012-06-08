<?php
/**
 * @package     Molajo
 * @copyright   2012 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die;
/** Note: Used as custom with $this->query_results[0] to avoid looping for embedded view */
?>
<img src="<?php echo $this->query_results[0]->author_email_gravatar; ?>"/>
<h3><?php echo $this->query_results[0]->author_full_name; ?></h3>
<?php echo $this->query_results[0]->author_content_text_snippet; ?>
<h4><a href="<?php echo $this->query_results[0]->author_twitter; ?>">Follow me on Twitter</a></h4>
<h4><?php echo $this->query_results[0]->author_email_obfuscated; ?></h4>
<?php echo $this->query_results[0]->author_about_me; ?>
