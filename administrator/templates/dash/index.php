<!DOCTYPE html>
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
		<title>Molajito: Molajo's Admin Drunk with Power</title>
		<link type="text/css" href="css/jquery.ui.all.css" rel="stylesheet" />
		<link type="text/css" href="css/custom.css" rel="stylesheet" />
		<script type="text/javascript" src="js/jquery-1.6.2.js"></script>
		<script type="text/javascript" src="js/jquery-ui-1.8.15.custom.js"></script>
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
				<li><a href="#" class="icon_configure"></a></li>
				<li><a href="#" class="icon_access"></a></li>
				<li><a href="#" class="icon_create"></a></li>
				<li><a href="#" class="icon_build"></a></li>
				<li><a href="#" class="icon_search"></a></li>
			</ul>
			<!-- TODO: Get rid of the jUI icons, possibly add east facing arrowhead on hover/active -->
			<div id="accordion" class="menu_sub">
				<div>
					<h2><a href="?dash">Articles</a></h2>
					<ul>
						<li><a href="?edit">Add</a></li>
						<li><a href="?edit">Edit</a></li>
						<li><a href="?list">Whatever</a></li>
					</ul>
				</div>
				<div>
					<h2><a href="?dash">Media</a></h2>
					<ul>
						<li><a href="?edit">Add</a></li>
						<li><a href="?edit">Edit</a></li>
						<li><a href="?list">Etcetera</a></li>
					</ul>
				</div>
				<div>
					<h2><a href="?dash">Other Com</a></h2>
					<ul>
						<li><a href="?edit">Add</a></li>
						<li><a href="?edit">Edit</a></li>
						<li><a href="?list">Etc</a></li>
					</ul>
				</div>
			</div>
		</nav>
		<section>
			<div id="">
				<?php
					if(isset($_GET['edit'])){
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