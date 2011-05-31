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
					Raww2 <span>the tiny framework</span>
				</span>
				<ul id="topnav">
					<li><a href="https://github.com/aheinze/Raww2">Documentation</a></li>
					<li><a href="https://github.com/aheinze/Raww2">Github</a></li>
				</ul>
			</div>
		</div>
		<div id="content">
			<div class="wrapper">
				<?php echo $content_for_layout;?>
			</div>
		</div>
	</div>
</body>
</html>