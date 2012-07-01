<?php
/**
 * @package    Molajo
 * @copyright  2012 Amy Stephen. All rights reserved.
 * @license    GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
use Molajo\Service\Services;
defined('MOLAJO') or die;
$blockquote = Services::Registry()->exists('Trigger', 'Blockquote');
if ((int) $blockquote > 0) {
	$columns = 'nine ';
} else {
	$columns = 'twelve ';
} ?>
<section class="row">
	<section class="<?php echo $columns; ?> columns">
		<?php echo $this->row->content_text; ?>
		<include:template name=Author wrap=Section value=author/>
	</section>
	<?php if ((int) $blockquote > 0) { ?>
		<aside class="three columns">
			<include:template name=Blockquote/>
		</aside>
	<?php } ?>
</section>
