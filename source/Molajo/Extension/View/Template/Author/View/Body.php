<?php
/**
 * @package     Molajo
 * @subpackage  Views
 * @copyright   2012 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die; ?>
<img src="<?php echo $this->row->author_email_gravatar; ?>"/>
<h3><?php echo $this->row->author_full_name; ?></h3>
<?php echo $this->row->author_content_text_snippet; ?>
<h4><a href="<?php echo $this->row->author_twitter; ?>">Follow me on Twitter</a></h4>
<h4><?php echo $this->row->author_email_obfuscated; ?></h4>
<?php echo $this->row->author_about_me; ?>
