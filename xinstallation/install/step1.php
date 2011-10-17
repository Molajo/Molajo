<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en">
<head>
	<meta http-equiv="Content-Type" content="text/html;charset=UTF-8" />
	<title>Molajo Installer - Step 2 of 4</title>
	<?php include_once('lib/functions.php'); ?>
	<?php include_once('lib/head.php'); ?>
</head>
<body class="<?php echo $lcbrowser . ' ' . $lcbrowser.$ver . ' ' . strtolower($browser->getPlatform()); ?>">
	<div id="wrap">
		<div id="top">
			<h1>
				<a href="http://www.molajo.org" title="Click here to open the Molajo website in a new window" target="_blank">Molajo 
				<span>Click here to view the Molajo website</span></a>
			</h1>
			<strong>Version 1.6.1 <span>Step 2 of 4</span></strong>
		</div>
		<div id="main" class="step2">
			<div class="inner">
				<h2>Database Setup</h2>
				<p>Enter your database connection details below. Contact your host if you are not sure what these are.<br />
				All fields marked with a <strong>*</strong> are required.</p>

				<form action="">
					<ol class="list-reset forms">
						<li>
							<span class="inner-wrap">
								<label for="host" class="inlined">Host name</label>
								<input type="text" class="input-text" id="host" name="host" title="Host name" />
								<span class="note"><strong>*</strong> This is usually <b>localhost</b>.</span>
							</span>
						</li>
						<li>
							<span class="inner-wrap">
								<label for="name" class="inlined">Database name</label>
								<input type="text" class="input-text" id="name" name="name" title="Database name" />
								<span class="note"><strong>*</strong> The name of the database you are installing Molajo on.</span>
							</span>
						</li>
						<li>
							<span class="inner-wrap">
								<label for="username" class="inlined">Username</label>
								<input type="text" class="input-text" id="username" name="username" title="Username" />
								<span class="note"><strong>*</strong> Your MySQL database username.</span>
							</span>
						</li>
						<li>
							<span class="inner-wrap">
								<label for="password" class="inlined">Password</label>
								<input type="text" class="input-text" id="password" name="password" title="Password" />
								<span class="note"><strong>*</strong> Your MySQL database password.</span>
							</span>
						</li>
						<li>
							<span class="inner-wrap">
								<label for="prefix" class="inlined">Table prefix</label>
								<input type="text" class="input-text" id="prefix" name="prefix" title="Table prefix" />
								<span class="note"><strong>*</strong> By default this is set to jos_ but we recommended that you change this.</span>
							</span>
						</li>
					</ol>

					<ol class="list-rest radios">
						<li>
							<span class="label">Database type</span>
							<label class="radio-left" for="mysql"><input name="dbtype" id="mysql" value="myql" type="radio">MySQL</label>
							<label class="radio-right label-selected" for="mysqli"><input name="dbtype" id="mysqli" value="mysqli" type="radio" checked="checked">MySQLi</label>
							<span class="note">MySQLi is recommended, but not all hosts support it. <a href="#">Learn more</a>.</span>
						</li>
						<li>
							<span class="label">Existing database</span>
							<label class="radio-left" for="remove"><input name="existingdb" id="remove" value="remove" type="radio">Remove</label>
							<label class="radio-right label-selected" for="backup"><input name="existingdb" id="backup" value="backup" type="radio" checked="checked">Backup</label>
							<span class="note alt">If you have an existing database with the same name, would you like it to be replaced or backed up.</span>
						</li>
					</ol>
				</form>

				<div id="actions">
					<a href="index.php" class="btn-secondary">&laquo; <strong>P</strong>revious</a>
					<a href="step2.php" class="btn-primary"><strong>N</strong>ext &raquo;</a>
				</div>
			</div>
		</div>
	</div>
</body>
</html>