<?php header("Content-type: text/css"); 
//this sets up the colors for the core missioncontrol template
//require('../../css/color-vars.php');
?>
#mc-menu-key {overflow:hidden;}
#mc-menu-key li {width:50%;float:left;height:24px;}
#mc-menu-key .mc-bigger-field {padding-right:150px;}
#mc-menu-key .mc-bigger-field input.inputbox {width:450px !important;}
#mc-menu-key .inputbox {margin-left:0;}

#mc-menu {background:#fff;border:1px solid #EDEDED;padding:15px 15px 25px 15px;}

#mc-menu .show {display:block;}
#mc-menu .hide {display:none;}

#page-options, #page-modules {overflow:hidden;}
#mc-details, #mc-options, #mc-assignments, #mc-metadata {width: 50%;float:left;}
#mc-details .mc-block, #mc-assignments .mc-block {padding-right:20px;}

#mc-metadata label {display:block;clear:left;width:150px;height:20px;}
#mc-metadata textarea, #mc-metadata select, #mc-metadata input {float:left;margin:0 0 10px 0;}

.mc-menu-type {width: 222px !important;margin-right:3px;}

.mc-block h3 {line-height:26px;height:26px;font-size:20px;border-bottom:3px solid #E3E3E3;color:#333;font-weight:normal;margin-bottom:5px;}
.mc-list-table td.left ul {margin:2px;}

h3.title a {color:#333;text-decoration:none;cursor:default;}
label[id*="_spacer"] {color:#333;}

/* module options */
#mc-assignments table.adminlist {width:100%;}
#mc-assignments table.adminlist th {background:#f5f5f5;color:#333;border-bottom:1px solid #e6e6e6;font-size:13px;padding:4px;}
#mc-assignments td {border-bottom:1px solid #f3f3f3;}
#mc-assignments td small {color:#666;}


body #sbox-window {padding: 15px 25px;}