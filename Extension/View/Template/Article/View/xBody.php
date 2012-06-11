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
<div class="grid">
	<div class="row">
		<div class="slot-6">
			<include:wrap name=Aside value=content_text_pullquote/>
		</div>
		<div class="slot-7-8-9">
			<include:wrap name=Header value=post_header/>
				<?php echo $this->row->content_text; ?>
				<include:wrap name=Footer value=post_footer/>

		</div>
	</div>
</div>
