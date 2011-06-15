<?php header("Content-type: text/css"); 
//this sets up the colors for the core missioncontrol template
require('../../css/color-vars.php');
?>
#folderview .path {margin-top:25px;}
#folderview .path input#folderpath {width:40%;background:#f6f6f6; margin-right:4px;}
#folderview .path input#foldername {margin-right:4px;}
#folderview .path button {padding:6px 10px;}
.view iframe {border:1px solid #f0f0f0;margin:15px 0;}
#mc-component2 .manager {overflow:hidden;}

.mooTree_img[style *="mootree.gif"] {background-image:url(../../images/mootree.gif) !important;margin-right:4px;}
.mooTree_text {margin-top:3px !important;padding-top:0px !important;}
.mooTree_node.mooTree_selected {background:#F4F8FB;}

img[src $="remove.png"] {background:url(../../images/icon-delete.png) no-repeat 0 0;height: 0;padding-top: 16px;width: 16px;margin-left:10px;}
img[src $="com_media/images/folderup_32.png"] {background:url(../../images/media-up.png) no-repeat 0 0;height: 0;padding-top: 80px;width: 80px;}
img[src $="com_media/images/folder.png"] {background:url(../../images/media-folder.png) no-repeat 0 0;height: 0;padding-top: 80px;width: 80px;}

.option-com-media fieldset legend {margin-top:0;}

.manager div.controls {background:#f9f9f9;color:#666;border-top:1px solid #f0f0f0;}
.manager div.imgOutline {border-bottom:1px solid #e9e9e9;}
.manager div.imgBorder {width:auto;}
.manager div.imginfoBorder {background:#f3f3f3;color:#333;width:auto;}
.manager div.imginfoBorder a {color:#333;}
.manager div.imgBorder a:hover {background:#F4F8FB;}

.option-com-media .manager table th {border:0;background:<?php echo $active_bg_color; ?>;color:<?php echo $active_text_color;?>;font-weight:normal;}

a.delete-item img {background-position:0 6px;padding-top:18px;}
div.imgBorder a.img-preview img {margin-top: 10px;}
