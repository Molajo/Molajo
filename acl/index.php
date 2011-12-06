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
	<link type="text/css" rel="stylesheet" href="common/css/checkbox.css" />
	<script type="text/javascript" src="common/js/jquery-1.7.1.min.js"></script>
</head>
<body>
<dl class="acl-widget">
	<lh>
		Access Control Lists
	<ul>
		<?php foreach(array_shift(array_values($userGroup)) as $action=>$value) : ?>
		<li>
		<?php echo $action; ?>
		</li>
		<?php endforeach;?>
	</ul>
	</lh>

	<?php foreach($userGroup as $group=>$permissions) : ?>
	<dt>
	<?php echo $group; ?>
	</dt>
	<dd>
		<ul>
		<?php foreach($permissions as $action=>$value) : ?>
			<li>
				<p>
				<input type="checkbox" class="<?php echo $action;?>" id="<?php echo $action . '-' . $group;?>" <?php if ($value == 1){ echo 'checked="checked"'; } ?>>
				<label class="<?php echo $action;?>" for="<?php echo $action . '-' . $group;?>"><?php echo $action; ?></label>
				</p>
			</li>
		<?php endforeach;?>
		</ul>
	</dd>
	<?php endforeach;?>
</dl>
</body>
</html>
