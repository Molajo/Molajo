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
					<input type="checkbox" id="feeds-visible"/><label>Unassigned Cost Objects</label><br/>
					<input type="checkbox" id="comments-visible"/><label>Unassigned HR Codes</label><br/>
					<input type="checkbox" id="contacts-visible"/><label>Unassigned SIS Codes</label><br/>
					<input type="checkbox" id="links-visible"/><label>Links</label><br/>
					<input type="checkbox" id="images-visible"/><label>Images</label><br/>
					<input type="checkbox" id="tags-visible"/><label>Tags</label><br/>
			</fieldset>
		</div>
	</div>
</div>

<div class="row ui-widget1">

	<include:template name=Widgetgallery wrap=div id=widget-gallery1 class=ui-widget,four,columns/>

	<include:template name=Widgetlist wrap=div id=widget-list1 class=ui-widget,four,columns/>

	<include:template name=Widgettext wrap=div id=widget-text1 class=ui-widget,four,columns/>

	<include:template name=Widgetaudio wrap=div id=widget-audio1 class=ui-widget,four,columns/>

	<include:template name=Widgetvideo wrap=div id=widget-video1 class=ui-widget,four,columns/>

	<include:template name=Widgetmap wrap=div id=widget-map1 class=ui-widget,four,columns/>

	<include:template name=Widgetgraph wrap=div id=widget-graph1 class=ui-widget,four,columns/>

	<include:template name=Widgetquicklinks wrap=div id=widget-qucklinks1 class=ui-widget,four,columns/>

	<div id="media-ui-widget" class="ui-widget four columns">
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

	<div id="activity-ui-widget" class="ui-widget four columns">
		<div class="ui-widget-header">
			<h4>Recent Activity</h4>
		</div>
		<div class="ui-widget-content">
			<li><a href="#">Person1 assigned Cost Object to Department B</a></li>
			<li><a href="#">Person2 updated Data Cost Object to Department B</a></li>
		</div>
	</div>

	<div id="ui-widget-online" class="ui-widget four columns">
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

	<div id="ui-widget-scheduled" class="ui-widget four columns">
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
