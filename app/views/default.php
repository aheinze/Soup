<html>
<head>
	<meta charset="UTF-8">
	<title>Raww2</title>
	<link rel="stylesheet" type="text/css" href="<?php $this->url("/assets/main.css");?>" />
	<script type="text/javascript" src="<?php $this->url("/assets/main.js");?>"></script>
</head>
<body>
	<div id="frame">
		<div id="header">
			<div class="wrapper">
				<span id="logo">
					Raww2 <span>framework</span>
				</span>
				<ul id="topnav">
					<li><a href="https://github.com/aheinze/Raww2">Documentation</a></li>
					<li><a href="https://github.com/aheinze/Raww2">Github</a></li>
				</ul>
			</div>
		</div>
		<div id="content">
			<div class="wrapper">
				<div id="topbox">
					<div class="p-20">
						<div class="h1">Less Enterprise</div>
						<div class="h2">more fun!</div>
					</div>
					<div id="ribbonbar">
						&mdash; because we &hearts; it simple &mdash;
						<span class="left"></span>
						<span class="right"></span>
					</div>
				</div>
				<div id="output"><?php echo $content_for_layout;?></div>
			</div>
		</div>
	</div>
</body>
</html>