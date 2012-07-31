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
			<include:template name=Portletgallery wrap=div wrap_id=portlet1 wrap_class=ui-portlet,four,columns/>
			<include:template name=Portletlist wrap=div wrap_id=portlet2 wrap_class=ui-portlet,four,columns/>
			<include:template name=Portlettext wrap=div wrap_id=portlet3 wrap_class=ui-portlet,four,columns/>
		</div>
		<div wrap_class="row ui-portlet2">
			<include:template name=Portletaudio wrap=div wrap_id=portlet4 wrap_class=ui-portlet,four,columns/>
			<include:template name=Portletvideo wrap=div wrap_id=portlet5 wrap_class=ui-portlet,four,columns/>
			<include:template name=Portletmap wrap=div wrap_id=portlet6 wrap_class=ui-portlet,four,columns/>
		</div>
		<div wrap_class="row ui-portlet3">
			<include:template name=Portletgraph wrap=div wrap_id=portlet7 wrap_class=ui-portlet,four,columns/>
			<include:template name=Portletquicklinks wrap=div wrap_id=portlet8 wrap_class=ui-portlet,four,columns/>
			<include:template name=Portletsysteminfo wrap=div wrap_id=portlet9 wrap_class=ui-portlet,four,columns/>
		</div>
		<div wrap_class="row ui-portlet4">
			<include:template name=Portletgallery wrap=div wrap_id=portlet10 wrap_class=ui-portlet,four,columns/>
			<include:template name=Portletlist wrap=div wrap_id=portlet11 wrap_class=ui-portlet,four,columns/>
			<include:template name=Portlettext wrap=div wrap_id=portlet12 wrap_class=ui-portlet,four,columns/>
		</div>
		<div wrap_class="row ui-portlet5">
			<include:template name=Portletaudio wrap=div wrap_id=portlet13 wrap_class=ui-portlet,four,columns/>
			<include:template name=Portletvideo wrap=div wrap_id=portlet14 wrap_class=ui-portlet,four,columns/>
			<include:template name=Portletmap wrap=div wrap_id=portlet15 wrap_class=ui-portlet,four,columns/>
		</div>
	</div>
</div>
