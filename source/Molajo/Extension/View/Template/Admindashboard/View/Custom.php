<?php
/**
 * @package    Molajo
 * @copyright  2012 Amy Stephen. All rights reserved.
 * @license    GNU GPL v 2, or later and MIT, see License folder
 */
defined("MOLAJO") or die; ?>
<div wrap_class="row">
    <div wrap_class="twelve columns">
        <div wrap_id="header" wrap_class="ui-portlet-header ui-corner-all ui-portlet">
            <div>Dashboard test</div>
            <div wrap_id="menu2" wrap_class="ui-icon ui-icon-wrench"></div>
            <br wrap_class="clear"/>
        </div>
        <div wrap_id="window_dialog" wrap_class="hidden">
            <fieldset>
                <legend>Options</legend>
                    <input type="checkbox" wrap_id="feeds-visible"/><label>Unassigned Cost Objects</label><br/>
                    <input type="checkbox" wrap_id="comments-visible"/><label>Unassigned HR Codes</label><br/>
                    <input type="checkbox" wrap_id="contacts-visible"/><label>Unassigned SIS Codes</label><br/>
                    <input type="checkbox" wrap_id="links-visible"/><label>Links</label><br/>
                    <input type="checkbox" wrap_id="images-visible"/><label>Images</label><br/>
                    <input type="checkbox" wrap_id="tags-visible"/><label>Tags</label><br/>
            </fieldset>
        </div>
    </div>
</div>

<div wrap_class="row">
	<div wrap_class="twelve columns">
		<div wrap_class="row ui-portlet1">
			<include:template name=Widgetgallery wrap=div wrap_id=widget1 wrap_class=ui-portlet,four,columns/>
			<include:template name=Widgetlist wrap=div wrap_id=widget2 wrap_class=ui-portlet,four,columns/>
			<include:template name=Widgettext wrap=div wrap_id=widget3 wrap_class=ui-portlet,four,columns/>
		</div>
		<div wrap_class="row ui-portlet2">
			<include:template name=Widgetaudio wrap=div wrap_id=widget4 wrap_class=ui-portlet,four,columns/>
			<include:template name=Widgetvideo wrap=div wrap_id=widget5 wrap_class=ui-portlet,four,columns/>
			<include:template name=Widgetmap wrap=div wrap_id=widget6 wrap_class=ui-portlet,four,columns/>
		</div>
		<div wrap_class="row ui-portlet3">
			<include:template name=Widgetgraph wrap=div wrap_id=widget7 wrap_class=ui-portlet,four,columns/>
			<include:template name=Widgetquicklinks wrap=div wrap_id=widget8 wrap_class=ui-portlet,four,columns/>
			<include:template name=Widgetsysteminfo wrap=div wrap_id=widget9 wrap_class=ui-portlet,four,columns/>
		</div>
		<div wrap_class="row ui-portlet4">
			<include:template name=Widgetgallery wrap=div wrap_id=widget10 wrap_class=ui-portlet,four,columns/>
			<include:template name=Widgetlist wrap=div wrap_id=widget11 wrap_class=ui-portlet,four,columns/>
			<include:template name=Widgettext wrap=div wrap_id=widget12 wrap_class=ui-portlet,four,columns/>
		</div>
		<div wrap_class="row ui-portlet5">
			<include:template name=Widgetaudio wrap=div wrap_id=widget13 wrap_class=ui-portlet,four,columns/>
			<include:template name=Widgetvideo wrap=div wrap_id=widget14 wrap_class=ui-portlet,four,columns/>
			<include:template name=Widgetmap wrap=div wrap_id=widget15 wrap_class=ui-portlet,four,columns/>
		</div>
	</div>
</div>
