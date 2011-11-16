<html>
<head>
	<meta charset="UTF-8">
	<title>Raww - framework</title>
	<link rel="stylesheet" type="text/css" href="<?php $this->url("/assets/main.css");?>" />
	<script type="text/javascript" src="<?php $this->url("/assets/main.js");?>"></script>
</head>
<body>
	<div id="header">
		<div class="row">
			<div class="five columns">
				<h1 id="logo">
					Raww2 <span class="soft">Less enterprise more fun</span>
				</h1>
				
			</div>
			<div class="seven columns">
				<ul id="topnav">
					<li><a href="https://github.com/aheinze/Raww2">Documentation</a></li>
					<li><a href="https://github.com/aheinze/Raww2">Github</a></li>
				</ul>
			</div>
		</div>
	</div>
	<div id="content">
		<div id="output"><?php echo $content_for_layout;?></div>
	</div>
</body>
</html>