<!DOCTYPE html>
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
		<title>Molajito: Molajo's Admin Drunk with Power</title>
		<link type="text/css" href="css/jquery.ui.all.css" rel="stylesheet" />
		<link type="text/css" href="css/custom.css" rel="stylesheet" />
		<script type="text/javascript" src="js/jquery-1.6.2.js"></script>
		<script type="text/javascript" src="js/jquery-ui-1.8.16.custom.js"></script>
		<script type="text/javascript" src="js/scripts.js"></script>
	</head>
	<body>
		<!-- Accordion -->
	<div class="container">
		<header>
			<h1>Molajo Admin CP</h1>
		</header>
		<nav id="launchpad">
			<ul class="menu_main">
				<li><a href="?config" class="icon_configure"></a></li>
				<li><a href="?access" class="icon_access"></a></li>
				<li><a href="?create" class="icon_create"></a></li>
				<li><a href="?build" class="icon_build"></a></li>
				<li><a href="#" class="icon_search"></a></li>
			</ul>
			<?php
				if(isset($_GET['config'])){
					include('includes/lp_item.html');
				}else if(isset($_GET['access'])){
					include('includes/lp_item.html');
				}else if(isset($_GET['create'])){
					include('includes/lp_item.html');
				}else if(isset($_GET['build'])){
					include('includes/lp_item.html');
				}else if(isset($_GET['edit'])){
					include('includes/lp_item.html');
				}else if(isset($_GET['list'])){
					include('includes/lp_item.html');
				} else {
					include('includes/lp_item.html');
				}
			?>
		</nav>
		<section class="dash">
			<div id="">
				<?php
					if(isset($_GET['config'])){
						include('includes/config.html');
					}else if(isset($_GET['access'])){
						include('includes/access.html');
					}else if(isset($_GET['create'])){
						include('includes/create.html');
					}else if(isset($_GET['build'])){
						include('includes/build.html');
					}else if(isset($_GET['edit'])){
						include('includes/edit.html');
					}else if(isset($_GET['list'])){
						include('includes/list.html');
					} else {
						include('includes/dash.html');
					}
				?>
			</div>
		</section>
		<footer>
		</footer>
	</div>
	</body>
</html>