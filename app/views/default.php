<html>
<head>
	<meta charset="UTF-8">
	<title>Raww2</title>
	<link rel="stylesheet" type="text/css" href="<?php $this->url("/assets/main.css");?>" />
	<script type="text/javascript" src="<?php $this->url("/assets/main.js");?>"></script>
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