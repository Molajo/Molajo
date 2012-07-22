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
			<div class="four columns portlet1">

				<div id="articles-portlet" class="portlet">
					<div class="portlet-header">
						Recent Articles
					</div>
					<div class="portlet-content">
						<ul>
							<li><a href="#">Name of this article</a></li>
							<li><a href="#">Name of this article</a></li>
						</ul>
					</div>
				</div>

				<div id="comments-portlet" class="portlet">
					<div class="portlet-header">
						Recent Comments
					</div>
					<div class="portlet-content">
						<ul>
							<li><a href="#">Snippet from Comment</a></li>
							<li><a href="#">Snippet from Comment</a></li>
						</ul>
					</div>
				</div>

			</div>

			<div class="eight columns portlet2">
				<div id="media-portlet" class="portlet">
					<div class="portlet-header">
						<h5>Media</h5>
					</div>
					<div class="portlet-content">
						<ul>
							<li><a href="#">Snippet from Comment</a></li>
							<li><a href="#">Snippet from Comment</a></li>
						</ul>
					</div>
				</div>
			</div>

		</div>

		<div class="row">
			<div class="four columns portlet3">
				<div id="activity-portlet" class="portlet">
					<div class="portlet-header">
						<h4>Recent Activity</h4>
					</div>
					<div class="portlet-content">
						<ul>
							<li><a href="#">Activity</a></li>
							<li><a href="#">Activitye</a></li>
						</ul>
					</div>
				</div>
			</div>

			<div class="four columns portlet4">
				<div id="portlet-online" class="portlet">
					<div class="portlet-header">
						<h4>Users Online</h4>
					</div>
					<div class="portlet-content">
						<ul>
							<li><a href="#">User 1</a></li>
							<li><a href="#">User 2</a></li>
						</ul>
					</div>
				</div>
			</div>

			<div class="four columns portlet5">
				<div id="portlet-scheduled" class="portlet">
					<div class="portlet-header">
						<h4>Scheduled for Publication</h4>
					</div>
					<div class="portlet-content">
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
