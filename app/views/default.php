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
						<div class="p-20" style="text-align:center;font-size:40px;font-weight:bold;">Less Enterprise</div>
						<div style="text-align:center;font-size:24px;font-family:Georgia;font-style:italic;">more fun!</div>
					</div>
					<div id="topbar">
						&mdash; because we &hearts; it simple.
					</div>
				</div>
				<div id="output"><?php echo $content_for_layout;?></div>
			</div>
		</div>
	</div>
</body>
</html>