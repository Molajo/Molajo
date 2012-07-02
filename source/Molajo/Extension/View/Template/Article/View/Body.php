<?php
use Molajo\Service\Services;
/**
 * @package    Molajo
 * @copyright  2012 Amy Stephen. All rights reserved.
 * @license    GNU GPL v 2, or later and MIT, see license folder
 */
defined('MOLAJO') or die;
?>
<div class="row">
	<div class="twelve columns">
		<?php echo $this->row->content_text; ?>
		<include:template name=Author wrap=Section value=author/>
	</div>
</div>
