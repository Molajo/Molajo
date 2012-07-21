<?php
use Molajo\Service\Services;
/**
 * @package     Molajo
 * @copyright   2012 Amy Stephen. All rights reserved.
 * @license     GNU GPL v 2, or later and MIT, see License folder
 */
defined('MOLAJO') or die;
?>
<form>
	<div class="row">
		<div class="eight columns">
			<dl class="tabs contained">
				<dd class="active"><a href="<?php echo Services::Registry()->get('Triggerdata', 'full_page_url'); ?>#edit">Edit</a></dd>
				<dd><a href="<?php echo Services::Registry()->get('Triggerdata', 'full_page_url'); ?>#options">Options</a></dd>
				<dd><a href="<?php echo Services::Registry()->get('Triggerdata', 'full_page_url'); ?>#fields">Fields</a></dd>
			</dl>
		</div>
		<div class="four columns">
			<include:template name=Editbuttons/>
		</div>
		<ul class="tabs-content contained">
			<li class="active" id="editTab">
				<include:template name=Edititem/>
			</li>
			<li id="optionsTab">
				<include:template name=Editoptions/>
			</li>
			<li id="fieldsTab">
				<include:template name=Editfields/>
			</li>
		</ul>
	</div>
</form>

