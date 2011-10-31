<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>TinyAjax - Simple AJAX Function</title>
<link rel="stylesheet" type="text/css" href="style.css" />
<script type="text/javascript" src="tinyajax.js"></script>
</head>
<body>
<div id="wrapper">
	<div id="output">
		<div id="content"></div>
		<div class="close" onclick="hide()">X</div>
	</div>
	<div id="buttons">
		<div class="button" onclick="TINY.ajax.call('get.php?id=32', 'content', 'display(\'red\')')">Get Data</div>
		<div class="button floatright" onclick="TINY.ajax.call('post.php', 'content', 'display(\'green\')', 'id=32')">Post Data</div>
	</div>
	<p>For more information visit <a href="http://www.leigeber.com">leigeber.com</a>.</p>
</div>
</body>
</html>