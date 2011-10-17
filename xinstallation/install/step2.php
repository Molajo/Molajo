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
			<strong>Version 1.6.1 <span>Step 3 of 4</span></strong>
		</div>
		<div id="main" class="step2">
			<div class="inner">
				<h2>Site Information</h2>
				<p>Enter your site information. All fields marked with a <strong>*</strong> are required.</p>
				
				<form action="">
					<ol class="list-reset forms">
						<li>
							<span class="inner-wrap">
								<label for="site" class="inlined">Site name</label>
								<input type="text" class="input-text" id="site" name="site" title="Site name" />
								<span class="note"><strong>*</strong> Your site name.</span>
							</span>
						</li>
						<li>
							<span class="inner-wrap">
								<label for="email" class="inlined">Your email address</label>
								<input type="text" class="input-text" id="email" name="email" title="Your email address" />
								<span class="note"><strong>*</strong> Enter a valid email address. This is where your login info will be sent.</span>
							</span>
						</li>
						<li>
							<span class="inner-wrap">
								<label for="username" class="inlined">Username</label>
								<input type="text" class="input-text" id="username" name="username" title="Username" />
								<span class="note"><strong>*</strong> Enter your admin username.</span>
							</span>
						</li>
						<li>
							<span class="inner-wrap">
								<label for="password" class="inlined">Password</label>
								<input type="password" class="password" id="password" name="password" title="Password" />
								<span class="note"><strong>*</strong> Enter your admin password.</span>
							</span>
						</li>
					</ol>
					<div class="sample-data">
						<a href="#" id="sample-data" class="btn-secondary">Install sample data</a>
						<span class="note">Installing sample data is strongly recommended for beginners. 
						This will install sample content that is included in the Joomla! installation package. <a href="#">Learn more</a>.</span>
					</div>					
				</form>
				
				<div id="actions">
					<a href="step1.php" class="btn-secondary">&laquo; <strong>P</strong>revious</a>
					<a href="step3.php" class="btn-primary"><strong>N</strong>ext &raquo;</a>
				</div>
				
			</div>
		</div>
	</div>
</body>
</html>