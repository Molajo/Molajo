<?php
/**
 * @package    Molajo
 * @copyright  2012 Individual Molajo Contributors. All rights reserved.
 * @license    GNU GPL v 2, or later and MIT, see License folder
 */
defined("MOLAJO") or die; ?>
<section class="dashboard">

	<div wrap_id="header" class="ui-portlet-header ui-corner-all ui-portlet">
		<div>Dashboard test</div>
		<div wrap_id="menu2" class="ui-icon ui-icon-wrench"></div>
		<br class="clear"/>
	</div>

	<div wrap_id="window_dialog" class="hidden">
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

	<div class="sortable">
		<include:template name=Portletgallery wrap=section wrap_id=portlet1 wrap_class=portlet/>
		<include:template name=Portletlist wrap=section wrap_id=portlet2 wrap_class=portlet/>
		<include:template name=Portlettext wrap=section wrap_id=portlet3 wrap_class=portlet/>
		<include:template name=Portletfeed wrap=section wrap_id=portlet4 wrap_class=portlet/>
		<include:template name=Portletvideo wrap=section wrap_id=portlet5 wrap_class=portlet/>
		<include:template name=Portletmap wrap=section wrap_id=portlet6 wrap_class=portlet/>
		<include:template name=Portletbargraph wrap=section wrap_id=portlet7 wrap_class=portlet/>
		<include:template name=Portletquicklinks wrap=section wrap_id=portlet8 wrap_class=portlet/>
		<include:template name=Portletsysteminfo wrap=section wrap_id=portlet9 wrap_class=portlet/>
		<include:template name=Portletgallery wrap=section wrap_id=portlet10 wrap_class=portlet/>
		<include:template name=Portletweather wrap=section wrap_id=portlet11 wrap_class=portlet/>
		<include:template name=Portlettext wrap=section wrap_id=portlet12 wrap_class=portlet/>
		<include:template name=Portletaudio wrap=section wrap_id=portlet13 wrap_class=portlet/>
		<include:template name=Portletmap wrap=section wrap_id=portlet14 wrap_class=portlet/>
		<include:template name=Portletmap wrap=section wrap_id=portlet15 class=portlet/>
	</div>
</section>
