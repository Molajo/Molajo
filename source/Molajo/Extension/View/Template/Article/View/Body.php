<?php
use Molajo\Service\Services;
/**
 * @package    Molajo
 * @copyright  2012 Amy Stephen. All rights reserved.
 * @license    GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die;

$blockquote = Services::Registry()->exists('Trigger', 'Blockquote');
if ((int) $blockquote > 0) {
	$columns = '.nine ';
} else {
	$columns = '.twelve ';
}
?>
<div class="row">
	<div class="<?php echo $columns; ?>columns">
		<?php echo $this->row->content_text; ?>
		<include:template name=Author wrap=Section value=author/>
	</div>
	<?php if ((int) $blockquote > 0) { ?>
		<div class="three columns">
			<include:template name=Blockquote/>
		</div>
	<?php } ?>
</div>
