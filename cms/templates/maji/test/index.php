<?php
// sample data
require('incoming-data.php');

defined('MOLAJO') or die;
?>
<!DOCTYPE html>
<html>
<head>
	<title>Molajo ACL Widget</title>
	<link type="text/css" rel="stylesheet" href="common/css/normalize.css" />
	<link type="text/css" rel="stylesheet" href="common/css/acl-widget.css" />
	<script type="text/javascript" src="common/js/jquery-1.7.1.min.js"></script>
</head>
<body>
<header class="access">
	<h2>
		Access Control Lists
	</h2>
	<ul>
		<?php foreach(array_shift(array_values($userGroup)) as $action=>$value) : ?>
		<li>
		<?php echo $action; ?>
		</li>
		<?php endforeach;?>
	</ul>
</header>

<dl class="access control">
	<?php foreach($userGroup as $group=>$permissions) : ?>
	<dt>
	<?php echo $group; ?>
	</dt>
	<dd>
		<ul>
		<?php foreach($permissions as $action=>$value) : ?>
			<li>
				<input type="checkbox" class="<?php echo $action;?>" id="<?php echo $action . '-' . $group;?>" <?php if ($value == 1){ echo 'checked'; } ?> style="border: 0;">
				<label class="<?php echo $action;?>" for="<?php echo $action . '-' . $group;?>"><?php echo $action; ?></label>
			</li>
		<?php endforeach;?>
		</ul>
	</dd>
	<?php endforeach;?>
</dl>
</body>
</html>
