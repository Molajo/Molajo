<?php header("Content-type: text/css"); 
//this sets up the colors for the core missioncontrol template
require('../../css/color-vars.php');
?>
.k2Date {white-space:normal !important;}
.K2AdminViewItems .mc-list-table td {font-size:11px;}

#adminFormK2Sidebar {width:250px !important;}
textarea[name=metadesc],textarea[name=metakey] {width:145px !important;}
#adminFormK2Sidebar table.sidebarDetails {padding:5px;border:1px solid #D9D7AD !important;background:#FFFCD5 !important;}
div.simpleTabsContent {margin-top:2px !important;padding-bottom:30px !important;}
.k2ItemFormEditor {padding:0 !important;margin:0 !important;background:none !important;border:0 !important;}
div.dummyHeight {background:none !important;border:0 !important;}
.mc-form-frame #system-message {margin-bottom:15px !important;}
input.fileUpload {border:0 !important;}
ul.simpleTabsNavigation li a {font-family: Helvetica, Arial, sans-serif !important;}
div.simpleTabs {padding-top:25px !important;}
.paramlist_value div[style*='background:'] {background:<?php echo $active_bg_color;?> !important;color:<?php echo $active_text_color;?> !important;}
ul.tags {border:0 !important;}
ul.tags li.tagAdd input {border:1px solid #dedede !important;}
.K2AdminViewUser div[style*='margin-top:-5px'] {margin-top:5px !important;}
textarea[name=embedVideo] {background:none !important;}
#k2ToggleSidebarContainer {position:absolute;top:-25px; right:0px;}
a#k2ToggleSidebar {border:0 !important;padding: 4px 10px !important;background:#999 !important;color:#fff !important;}
a#k2ToggleSidebar:hover {background:<?php echo $hover_bg_color;?> !important;color:<?php echo $hover_text_color;?> !important;}
#mc-submenu a, #mc-submenu span.nolink {padding:2px 10px !important;}