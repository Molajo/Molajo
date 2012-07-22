<?php
use Molajo\Service\Services;
/**
 * @package    Molajo
 * @copyright  2012 Amy Stephen. All rights reserved.
 * @license    GNU GPL v 2, or later and MIT, see License folder
 */
defined('MOLAJO') or die; ?>
<div class="row">
	<div class="twelve columns">

		<div id="container-filters">
			<div class="row">
				<div class="nine columns">
					<include:template name=Adminresourcemenu/>
				</div>
				<div class="one columns">
					<div id="t-filters"><h5><a href="#">Filters</a></h5></div>
				</div>
				<div class="one columns">
					<div id="t-batch"><h5><a href="#">Batch</a></h5></div>
				</div>
				<div class="one columns">
					<div id="t-options"><h5><a href="#">Options</a></h5></div>
				</div>
			</div>
		</div>

		<div id="m-filters" style="display: none;">
			<h3><?php echo Services::Language()->translate('Filters'); ?></h3>
			<include:template name=Admingridfilters/>
		</div>
		<div id="b-filters"></div>

		<div id="m-options" style="display: none;">
			<h3><?php echo Services::Language()->translate('Options'); ?></h3>
			<ol>
				<li><a href="#" data-reveal-id="options-list-columns"
					   data-animation="fadeAndPop"
					   data-animationspeed="300"
					   data-closeonbackgroundclick="true"
					   data-dismissmodalclass="close-reveal-modal">
					<?php echo Services::Language()->translate('Change Page Title'); ?>
				</a></li>

				<li><a href="#" data-reveal-id="options-list-columns"
					   data-animation="fadeAndPop"
					   data-animationspeed="300"
					   data-closeonbackgroundclick="true"
					   data-dismissmodalclass="close-reveal-modal">
					<?php echo Services::Language()->translate('Change Status Menu Items'); ?>
				</a></li>

				<li><a href="#" data-reveal-id="options-list-columns"
					   data-animation="fadeAndPop"
					   data-animationspeed="300"
					   data-closeonbackgroundclick="true"
					   data-dismissmodalclass="close-reveal-modal">
					<?php echo Services::Language()->translate('Change List and/or Search Filters'); ?>
				</a></li>

				<li><a href="#" data-reveal-id="options-list-columns"
					   data-animation="fadeAndPop"
					   data-animationspeed="300"
					   data-closeonbackgroundclick="true"
					   data-dismissmodalclass="close-reveal-modal">
					<?php echo Services::Language()->translate('Change List Columns'); ?>
				</a></li>

				<li><a href="#" data-reveal-id="options-list-columns"
					   data-animation="fadeAndPop"
					   data-animationspeed="300"
					   data-closeonbackgroundclick="true"
					   data-dismissmodalclass="close-reveal-modal">
					<?php echo Services::Language()->translate('Change List Length and Ordering'); ?>
				</a></li>
			</ol>
		</div>
		<div id="b-options"></div>

		<div id="m-batch" style="display: none;">
			<h3><?php echo Services::Language()->translate('Batch'); ?></h3>
			<a href="#"
			   data-reveal-id="resourceOptions"
			   data-animation="fadeAndPop"
			   data-animationspeed="300"
			   data-closeonbackgroundclick="true"
			   data-dismissmodalclass="close-reveal-modal">Login</a>
			<include:template name=Admingridbatch/>
		</div>
		<div id="b-batch"></div>

	</div>
</div>
