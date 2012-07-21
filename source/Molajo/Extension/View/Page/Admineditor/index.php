<?php
use Molajo\Service\Services;
/**
 * @package     Molajo
 * @copyright   2012 Amy Stephen. All rights reserved.
 * @license     GNU GPL v 2, or later and MIT, see License folder
 */
defined('MOLAJO') or die; ?>
<div class="row">
	<div class="two columns">
		<include:template name=Adminsectionmenu/>
	</div>
	<div class="ten columns">
		<dl class="tabs pill">
			<dd class="active"><a href="<?php echo Services::Registry()->get('Triggerdata', 'full_page_url'); ?>#edit">Edit</a></dd>
			<dd><a href="<?php echo Services::Registry()->get('Triggerdata', 'full_page_url'); ?>#options">Options</a></dd>
			<dd><a href="<?php echo Services::Registry()->get('Triggerdata', 'full_page_url'); ?>#fields">Fields</a></dd>
		</dl>
		<form>
			<ul class="tabs-content">
				<li class="active" id="editTab">
					<include:request/>
				</li>
				<li id="optionsTab">
					<include:template name=Editoptions/>
				</li>
				<li id="fieldsTab">
					<include:template name=Editfields/>
				</li>
			</ul>
		</form>
	</div>
</div>
