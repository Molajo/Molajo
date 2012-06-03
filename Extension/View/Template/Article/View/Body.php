<?php
/**
 * @package    Molajo
 * @copyright  2012 Amy Stephen. All rights reserved.
 * @license    GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
use Molajo\Service\Services;

defined('MOLAJO') or die;

Services::Registry()->set('Trigger', 'post_header', '<h2>' . $this->row->title . '</h2>');
?>
<article>
	<include:wrap name=Header value=post_header/>
	<include:wrap name=Aside value=content_text_pullquote/>
	<?php echo $this->row->content_text; ?>
	<include:wrap name=Footer value=post_footer/>
	<include:template name=Author wrap=Section value=author*/>
</article>
