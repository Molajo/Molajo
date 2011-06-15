<?php header("Content-type: text/css"); 
//this sets up the colors for the core missioncontrol template
require('color-vars.php');
?>
/* body text */
body {color:<?php echo $body_text_color;?>;}
/* body link */
a {color:<?php echo $body_link_color;?>;}
/* header background */
#mc-header {background:<?php echo $header_bg_color;?>;}
body #mc-status .mc-dropdown, body #mc-status .mc-dropdown li.divider {border-top: 1px solid <?php echo $header_bg_color;?>;}
body #mc-status .select-arrow {border-left: 1px solid <?php echo $header_bg_color;?>;}
/* header text */
#mc-header, #mc-userinfo .userinfo {color:<?php echo $header_text_color;?>;}
/* header link */
#mc-header h1,#mc-userinfo .userinfo b, #mc-userinfo .userinfo a {color:<?php echo $header_link_color;?>;}
/* header shadow */
#mc-header h1 {text-shadow:1px 1px 0px <?php echo $header_shadow_color;?>;}
/* tab background */
#mc-status li,.disabled .mc-button a, #mc-frame .disabled .mc-button a:hover,#mc-status ul.disabled li:hover, #mc-status ul.disabled li.action:hover, #mc-status ul.disabled ul.disabled li.standard:hover, #mc-status ul.disabled li:hover .select-list,#mc-status li.dropdown,#mc-submenu ul,.menutop li .item,.menutop.disabled li.root > span.item:hover,.menutop.disabled li:hover > .item {background:<?php echo $tab_bg_color;?>;}
#mc-userinfo .gravatar {border:1px solid <?php echo $tab_bg_color;?>;}
/* tab text */
#mc-status a,#mc-header .disabled .mc-button a,#mc-header .disabled .mc-button a:hover, #mc-status ul.disabled a,#mc-submenu a, #mc-submenu span.nolink,.menutop li .item,.menutop.disabled li.root > span.item:hover,.menutop.disabled li:hover > .item {color:<?php echo $tab_text_color;?>;}
/* special background */
#mc-status li.action,#mc-status li.action {background:<?php echo $special_bg_color;?>;}
/* special text */
#mc-status li.action,#mc-status li.action a {color:<?php echo $special_text_color;?>;}
/* active background */
.mc-button a, #editor-xtd-buttons a, .button2-left a,#mc-login #form-login .button,#help.mc-toolbar a,.mc-toolbar .special a,#mc-submenu .active,.mc-list-table thead,.pane-sliders .panel h3.jpane-toggler-down,.pane-sliders .panel h3.pane-toggler-down, .mc-pagination-container .pages,input[type="submit"]{background:<?php echo $active_bg_color;?>;}
.menutop.disabled li.root.active > span.item:hover,.menutop.disabled li.active:hover > .item,.menutop li.active > .item, .mc-module-standard table.adminlist td.title, .mc-module-standard table.adminlist th, #mc-cpanel .pane-sliders .panel h3.jpane-toggler-down, #mc-cpanel .pane-sliders .panel h3.pane-toggler-down {background-color:<?php echo $active_bg_color;?>;}
#mc-header {border-bottom:10px solid <?php echo $active_bg_color;?>;}
/* active text */
#mc-standard .mc-button a, #editor-xtd-buttons a, .button2-left a,#mc-login #form-login .button,.mc-list-table thead,.pane-sliders .panel h3.jpane-toggler-down,.pane-sliders .panel h3.pane-toggler-down,.mc-pagination-container .pages a,.mc-pagination-container .pages span,.menutop.disabled li.root.active > span.item:hover,.menutop.disabled li.active:hover > .item,.menutop li.active > .item, .mc-module-standard table.adminlist td.title, .mc-module-standard table.adminlist th, .pane-sliders .panel h3.jpane-toggler-down, .pane-sliders .panel h3.pane-toggler-down a, #mc-header .menutop .active a, #mc-submenu .active,#mc-submenu li span.nolink.active {color:<?php echo $active_text_color;?>;}
/* hover background */
.mc-button a:hover, input#upload-submit:hover, #editor-xtd-buttons a:hover, .button2-left a:hover,#mc-login #form-login .button:hover, #mc-status li:hover, #mc-status li.action:hover, #mc-status li.standard:hover,#mc-status li:hover .mc-dropdown li:hover,.mc-toolbar a:hover,#help.mc-toolbar a:hover, .mc-toolbar .special a:hover,body #toolbar .mc-dropdown li:hover, .mc-dropdown li:hover,#mc-submenu a:hover, .pane-sliders .panel h3:hover,.mc-pagination-container .pages:hover, .mc-pagination .page-button a:hover,#mc-cpanel .pane-sliders .panel h3.title:hover  {background:<?php echo $hover_bg_color;?>;}
.menutop li:hover .item:hover, .menutop li.active .item:hover,.mc-update-check a:hover,button:hover, input[type="button"]:hover, input[type="submit"]:hover {background-color:<?php echo $hover_bg_color;?>;}
/* hover text */
#mc-standard .mc-button a:hover, #editor-xtd-buttons a:hover, .button2-left a:hover,#mc-login #form-login .button:hover,#mc-status li select, .pane-sliders .panel h3:hover, .pane-sliders .panel h3:hover a, .menutop li:hover .item:hover, .menutop li.active .item:hover,#mc-cpanel .pane-sliders .panel h3.title:hover,#mc-cpanel .pane-sliders .panel h3.title:hover a, #mc-cpanel .pane-sliders .panel h3.title a:hover,.mc-update-check a:hover,button:hover, .mc-button a:hover, #mc-status a:hover, #mc-header .menutop a:hover, #mc-submenu li:hover a, #mc-status li.action a:hover,#mc-header .menutop li:hover li:hover a:hover, #mc-status li:hover li:hover a,#toolbar li:hover a, .mc-toolbar a:hover,body #toolbar li:hover li:hover a,.mc-pagination-container .pages:hover a,.mc-pagination-container .pages:hover span, .mc-pagination .page-button a:hover {color:<?php echo $hover_text_color;?>;}
/* tabs */
.mc-form-tabs {list-style:none;margin:0 15px;padding:0;overflow:hidden;margin-top:30px;}
.mc-form-tabs li {float:left;margin-right:2px;}
.mc-form-tabs a {display:block;float:left;padding:4px 20px;background:#999;color:#fff;text-decoration:none;cursor:pointer;}
.mc-form-tabs a.active {background:<?php echo $active_bg_color; ?>;color:<?php echo $active_text_color; ?>;}
.mc-form-tabs a:hover, .mc-form-tabs a.active:hover {background:<?php echo $hover_bg_color; ?>;color:<?php echo $hover_text_color; ?>;}