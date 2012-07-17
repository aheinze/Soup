<html>
<head>
	<meta charset="UTF-8">
	<title>&#9832; Soup - framework</title>
	<meta name="viewport" content="width=device-width, initial-scale=1.0" />
	<link rel="stylesheet" href="<?php $this->base_url("/css/fonts.css");?>">
	<?php $this->app["assets"]->style_and_script("system");?>
</head>
<body>
	<div id="header">
		<div class="row">
			<div class="six columns">
				<h1 id="logo">
					&#9832; Soup <span>framework</span>
				</h1>
			</div>
			<div class="six columns">
				<ul id="topnav">
					<li><a href="https://github.com/aheinze/Soup/wiki">Documentation</a></li>
					<li><a href="https://github.com/aheinze/Soup">Github</a></li>
				</ul>
			</div>
		</div>
	</div>
	<div id="content">
		<div class="container">
			<div id="output"><?php echo $content_for_layout;?></div>
		</div>
	</div>
</body>
</html>