<?php
/**
 * @package    Molajo
 * @copyright  2012 Amy Stephen. All rights reserved.
 * @license    GNU GPL v 2, or later and MIT, see License folder
 */
defined("MOLAJO") or die; ?>
<div class="row">
	<div class="twelve columns">
		<div id="header" class="ui-widget-header ui-corner-all ui-widget">
			<div>Dashboard test</div>
			<div id="menu2" class="ui-icon ui-icon-wrench"></div>
			<br class="clear"/>
		</div>
		<div id="window_dialog" class="hidden">
			<fieldset>
				<legend>Options</legend>
					<input type="checkbox" id="feeds-visible"/><label>Articles List</label><br/>
					<input type="checkbox" id="comments-visible"/><label>Recent Comments</label><br/>
					<input type="checkbox" id="contacts-visible"/><label>Contacts</label><br/>
					<input type="checkbox" id="links-visible"/><label>Links</label><br/>
					<input type="checkbox" id="images-visible"/><label>Images</label><br/>
					<input type="checkbox" id="tags-visible"/><label>Tags</label><br/>
			</fieldset>
		</div>
	</div>
</div>

<div class="row">
	<div class="twelve columns">

		<div class="row">
			<div class="four columns ui-widget1">

				<div id="articles-ui-widget" class="ui-widget">
					<div class="ui-widget-header">
						<h4>Graph of Something</h4>
					</div>
					<div class="ui-widget-content">
						<ul>
							<li><a href="#">Snippet from Comment</a></li>
							<li><a href="#">Snippet from Comment</a></li>
						</ul>
					</div>
				</div>

				<div id="comments-ui-widget" class="ui-widget">
					<div class="ui-widget-header">
						<h4>Recent Comments</h4>
					</div>
					<div class="ui-widget-content">
						<ul>
							<li><a href="#">Snippet from Comment</a></li>
							<li><a href="#">Snippet from Comment</a></li>
						</ul>
					</div>
				</div>

			</div>

			<div class="eight columns ui-widget2">
				<div id="media-ui-widget" class="ui-widget">
					<div class="ui-widget-header">
						<h4>Graph of Something</h4>
					</div>
					<div class="ui-widget-content">
						<dl style="width: 300px">
							<dt>2008</dt>
							<dd><div id="data-one" class="bar" style="width: 60%">60%</div></dd>
							<dt>2009</dt>
							<dd><div id="data-two" class="bar" style="width: 80%">80%</div></dd>
							<dt>2010</dt>
							<dd><div id="data-three" class="bar" style="width: 64%">64%</div></dd>
							<dt>2011</dt>
							<dd><div id="data-four" class="bar" style="width: 97%">97%</div></dd>
						</dl>
					</div>
				</div>
			</div>

		</div>

		<div class="row">
			<div class="four columns ui-widget3">
				<div id="activity-ui-widget" class="ui-widget">
					<div class="ui-widget-header">
						<h4>Recent Activity</h4>
					</div>
					<div class="ui-widget-content">

					</div>
				</div>
			</div>

			<div class="four columns ui-widget4">
				<div id="ui-widget-online" class="ui-widget">
					<div class="ui-widget-header">
						<h4>Users Online</h4>
					</div>
					<div class="ui-widget-content">
						<ul>
							<li><a href="#">User 1</a></li>
							<li><a href="#">User 2</a></li>
						</ul>
					</div>
				</div>
			</div>

			<div class="four columns ui-widget5">
				<div id="ui-widget-scheduled" class="ui-widget">
					<div class="ui-widget-header">
						<h4>Scheduled for Publication</h4>
					</div>
					<div class="ui-widget-content">
						<ul>
							<li><a href="#">Content 2</a></li>
							<li><a href="#">Content 2</a></li>
						</ul>
					</div>
				</div>
			</div>
		</div>

	</div>
</div>
