<html>
<head>
	<meta charset="UTF-8">
	<title>Raww2</title>
	<link rel="stylesheet" type="text/css" href="<?php $this->url("/public/css/base.css");?>" />
	<link rel="stylesheet" type="text/css" href="<?php $this->url("/public/css/app.css");?>" />
	<script type="text/javascript" src="<?php $this->url("/public/js/jquery.js");?>"></script>
</head>
<body>
	<div id="header">
		<div class="wrapper">
			<span id="logo">
				<h1>Raww2</h1>
			</span>
		</div>
	</div>
	<div id="content">
		<div class="wrapper">
			<?php echo $content_for_layout;?>
		</div>
	</div>
</body>
</html>