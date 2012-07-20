<?php
/**
 * @package    Molajo
 * @copyright  2012 Amy Stephen. All rights reserved.
 * @license    GNU GPL v 2, or later and MIT, see License folder
 */
defined('MOLAJO') or die; ?>
<div class="row">
	<div class="twelve columns">

		<div id='header' class="ui-widget-header ui-corner-all ui-widget">
			<div>Dashboard test</div>
			<div id="menu2" class="ui-icon ui-icon-wrench"></div>
			<br class='clear'/>
		</div>
		<div class="demo">
			<div id="window_dialog" class="hidden">
				<fieldset>
					<legend>Column Widths</legend>
					<label>Left:</label><input type='text' id='c1-width'>
					<label>Middle:</label><input type='text' id='c2-width'>
					<label>Right:</label><input type='text' id='c3-width'>
				</fieldset>
				<fieldset>
					<legend>Window Visibility</legend>
					<input type="checkbox" id="feeds-visible"/><label>Feeds</label><br/>
					<input type="checkbox" id="shopping-visible"/><label>Shopping</label><br/>
					<input type="checkbox" id="news-visible"/><label>News</label><br/>
					<input type="checkbox" id="links-visible"/><label>Links</label><br/>
					<input type="checkbox" id="images-visible"/><label>Images</label><br/>
				</fieldset>
			</div>
			<div class="column1">
				<div id='feeds-portlet' class="portlet">
					<div class="portlet-header">Feeds</div>
					<div class="portlet-content">Lorem ipsum dolor sit amet, consectetuer adipiscing elit</div>
				</div>

				<div id="news-portlet" class="portlet">
					<div class="portlet-header">News</div>
					<div class="portlet-content">Lorem ipsum dolor sit amet, consectetuer adipiscing elit</div>
				</div>
			</div>
			<div class="column2">
				<div class="portlet" id='shopping-portlet'>
					<div class="portlet-header">Shopping</div>
					<div class="portlet-content">Lorem ipsum dolor sit amet, consectetuer adipiscing elit</div>
				</div>
			</div>
			<div class="column3">
				<div id="links-portlet" class="portlet">
					<div class="portlet-header">Links</div>
					<div class="portlet-content">Lorem ipsum dolor sit amet, consectetuer adipiscing elit</div>
				</div>
				<div id="images-portlet" class="portlet">
					<div class="portlet-header">Images</div>
					<div class="portlet-content">Lorem ipsum dolor sit amet, consectetuer adipiscing elit Lorem ipsum
						dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et
						dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi
					</div>
				</div>
			</div>
		</div>
		<!-- End demo -->
	</div>
</div>
