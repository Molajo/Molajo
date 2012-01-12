<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8" />
	<title>Edit View</title>

	<script src="foundation/javascripts/foundation.js"></script>
	<script src="foundation/javascripts/app.js"></script>
	<script src="http://js.nicedit.com/nicEdit-latest.js" type="text/javascript"></script>
	<script type="text/javascript">bkLib.onDomLoaded(nicEditors.allTextAreas);
						new nicEditor({maxHeight : 100}).panelInstance('body');</script>

	
	<link rel="stylesheet" href="./foundation/stylesheets/foundation.css">
	<link rel="stylesheet" href="./custom/css/datepicker.css"">
	<link rel="stylesheet" href="./custom/css/custom.css">
	
	<meta name="viewport"
	content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0">
</head>
<body>

<?php require('./snippets/menu.php') ?>
<?php require('./snippets/toolbar.php') ?>

	<div class="alert-box error">
		<a class="close" href="#">×</a>
		<p><strong>Shoot!</strong> The article could not be saved.</p>
	</div>
	
	<form class="nice custom">
	
	<!-- the tabs -->
	<dl class="tabs">
		<dd><a href="#content" class="active">Content</a></dd>
		<dd><a href="#access">Access</a></dd>
		<dd><a href="#props">Properties</a></dd>
	</dl>
	
	<!-- tab "panes" -->
	<ul class="tabs-content contained">
		<li class="active" id="contentTab">
		
			<fieldset>
				<h2>Page details</h2>
				<div class="input-row">
					<label for="title">Title</label>
					<input type="text" class="oversize input-text" id="title" name="title" />
				</div>
				<div class="input-row">
					<label for="alias">Alias</label>
					<input type="text" class="input-text" id="alias" name="alias" />
				</div>
				<div class="input-row">
					<label for="date">Date</label>
					<input type="date" class="small input-text" id="date" name="date" />
				</div>
				<div class="input-row">
					<span>Enabled</span>
					<label for="publish1"><input type="radio" id="publish1" name="publish" value="1" />yes</label>
					<label for="publish0"><input type="radio" id="publish0" name="publish" value="0" />no</label>
				</div>
				<div class="input-row">
					<label for="body">Body</label>
				</div>
				
			</fieldset>
			<fieldset>
				<h2>Page content</h2>
				<textarea cols="50" rows="10" id="body"><##></textarea>
			</fieldset>
						
		</li>
		<li id="accessTab">
		</li>
		<li id="propsTab">
		
			<fieldset>
				<h2>Parameter Set</h2>
				<div class="input-row">
					<label for="options">Some options</label>
					<select id="options" name="options">
						<option value="">Please select</option>
						<option value="1">Option one</option>
						<option value="2">Option two</option>
						<option value="3">Option three</option>
					</select>
				</div>
				<div class="input-row">
					<span class="group-label">Some boolean setting</span>
					<label for="value1"><input type="radio" id="value1" name="value" value="1" />yes</label>
					<label for="value0"><input type="radio" id="value0" name="value" value="0" />no</label>
				</div>
			</fieldset>

			<fieldset>
				<h2>Parameter Set</h2>
				<div class="input-row">
					<span class="group-label">Some multiple-choice setting</span>
					<label for="valuea"><input type="checkbox" id="valuea" name="value" value="a" />Option a</label>
					<label for="valueb"><input type="checkbox" id="valueb" name="value" value="b" />Option b</label>
					<label for="valuec"><input type="checkbox" id="valuec" name="value" value="c" />Option c</label>
					<label for="valued"><input type="checkbox" id="valued" name="value" value="d" />Option d</label>
				</div>
			</fieldset>
			
		</li>
	</ul>
	
	</form>

</body>
</html>
